<?php
/*
 * Mailbox - an e107 plugin by Tijn Kuyper
 *
 * Copyright (C) 2016-2017 Tijn Kuyper (http://www.tijnkuyper.nl)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Class including all generic functions
 *
 */

/* NOTES

- Deleting messages from database 
=> check for starred status first

- Unsaving
=> check if message is ready to be deleted from database completely 
1) should not be starred
2) should be deleted by both to and from 

- Emty trash 
=> last empty date updated each time when trash is emptied
=> only messages displayed where the message_to_deleted < latest_emptytrash_datestamp of user
*/

if (!defined('e107_INIT')) { exit; }

class mailbox_class
{
	protected $plugprefs = array();
	protected $db;

	public function __construct($prefs)
	{
		$this->db = e107::getDb();
		$this->plugprefs = e107::getPlugPref('messaging');
	}
	
	/*
	 *	Send a message
	 *
	 *	@param	array $vars	- message information
	 *		['options'] - array of options
	 *		['attachments'] - list of attachments (if any) - each is an array('size', 'name')
	 *		['to_userclass'] - set TRUE if sending to a user class
	 *		['to_array'] = array of recipients
	 *		['pm_userclass'] = target user class
	 *		['to_info'] = recipients array of array('user_id', 'user_class')
	 *
	 *		May also be an array as received from the generic table, if sending via a cron job
	 *			identified by the existence of $vars['pm_from']
	 *
	 *	@return	string - text detailing result
	 */
	function send_message($vars)
	{
		$tp = e107::getParser();
		$sql = e107::getDb();
		$pmsize = 0;
		$attachlist = '';
		$pm_options = '';
		$ret = '';
		$maxSendNow = varset($this->plugprefs'pm_max_send'], 100) // Max # of messages before having to queue
		
		if (isset($vars['pm_from']))
		{	// Doing bulk send off cron task
			$info = array();
			foreach ($vars as $k => $v)
			{
				if (strpos($k, 'pm_') === 0)
				{
					$info[$k] = $v;
					unset($vars[$k]);
				}
			}
		}
		else
		{	// Send triggered by user - may be immediate or bulk dependent on number of recipients
			$vars['options'] = '';
			if(isset($vars['receipt']) && $vars['receipt']) {$pm_options .= '+rr+';	}
			if(isset($vars['uploaded']))
			{
				foreach($vars['uploaded'] as $u)
				{
					if (!isset($u['error']) || !$u['error'])
					{
						$pmsize += $u['size'];
						$a_list[] = $u['name'];
					}
				}
				$attachlist = implode(chr(0), $a_list);
			}
			$pmsize += strlen($vars['pm_message']);

			$pm_subject = trim($tp->toDB($vars['pm_subject']));
			$pm_message = trim($tp->toDB($vars['pm_message']));
			
			if (!$pm_subject && !$pm_message && !$attachlist)
			{  // Error - no subject, no message body and no uploaded files
				return LAN_PM_65;
			}
			
			// Most of the pm info is fixed - just need to set the 'to' user on each send
			$info = array(
				'pm_from' => $vars['from_id'],
				'pm_sent' => time(),					/* Date sent */
				'pm_read' => 0,							/* Date read */
				'pm_subject' => $pm_subject,
				'pm_text' => $pm_message,
				'pm_sent_del' => 0,						/* Set when can delete */
				'pm_read_del' => 0,						/* set when can delete */
				'pm_attachments' => $attachlist,
				'pm_option' => $pm_options,				/* Options associated with PM - '+rr' for read receipt */
				'pm_size' => $pmsize
				);
		}

		if(isset($vars['to_userclass']) || isset($vars['to_array']))
		{
			if(isset($vars['to_userclass']))
			{
				$toclass = e107::getUserClass()->uc_get_classname($vars['pm_userclass']);
				$tolist = $this->get_users_inclass($vars['pm_userclass']);
				$ret .= LAN_PM_38.": {$toclass}<br />";
				$class = TRUE;
			}
			else
			{
				$tolist = $vars['to_array'];
				$class = FALSE;
			}
			// Sending multiple PMs here. If more than some number ($maxSendNow), need to split into blocks.
			if (count($tolist) > $maxSendNow)
			{
				$totalSend = count($tolist);
				$targets = array_chunk($tolist, $maxSendNow);		// Split into a number of lists, each with the maximum number of elements (apart from the last block, of course)
				unset($tolist);
				$array = new ArrayData;
				$pmInfo = $info;
				$genInfo = array(
					'gen_type' => 'pm_bulk',
					'gen_datestamp' => time(),
					'gen_user_id' => USERID,
					'gen_ip' => ''
					);
				for ($i = 0; $i < count($targets) - 1; $i++)
				{	// Save the list in the 'generic' table
					$pmInfo['to_array'] = $targets[$i];			// Should be in exactly the right format
					$genInfo['gen_intdata'] = count($targets[$i]);
					$genInfo['gen_chardata'] = $array->WriteArray($pmInfo,TRUE);
					$sql->insert('generic', array('data' => $genInfo, '_FIELD_TYPES' => array('gen_chardata' => 'string')));	// Don't want any of the clever sanitising now
				}
				$toclass .= ' ['.$totalSend.']';
				$tolist = $targets[count($targets) - 1];		// Send the residue now (means user probably isn't kept hanging around too long if sending lots)
				unset($targets);
			}
			foreach($tolist as $u)
			{
				set_time_limit(30);
				$info['pm_to'] = intval($u['user_id']);		// Sending to a single user now

				if($pmid = $sql->insert('private_msg', $info))
				{
					$info['pm_id'] = $pmid;
					e107::getEvent()->trigger('user_pm_sent', $info);

					unset($info['pm_id']); // prevent it from being used on the next record.

					if($class == FALSE)
					{
						$toclass .= $u['user_name'].', ';
					}
					if(check_class($this->plugprefs'notify_class'], $u['user_class']))
					{
						$vars['to_info'] = $u;
						$this->pm_send_notify($u['user_id'], $vars, $pmid, count($a_list));
					}
				}
				else
				{
					$ret .= LAN_PM_39.": {$u['user_name']} <br />";
					e107::getMessage()->addDebug($sql->getLastErrorText());
				}
			}
			if ($addOutbox)
			{
				$info['pm_to'] = $toclass;		// Class info to put into outbox
				$info['pm_sent_del'] = 0;
				$info['pm_read_del'] = 1;
				if(!$pmid = $sql->insert('private_msg', $info))
				{
					$ret .= LAN_PM_41.'<br />';
				}
			}
			
		}
		else
		{	// Sending to a single person
			$info['pm_to'] = intval($vars['to_info']['user_id']);		// Sending to a single user now
			if($pmid = $sql->insert('private_msg', $info))
			{
				$info['pm_id'] = $pmid;
				e107::getEvent()->trigger('user_pm_sent', $info);
				if(check_class($this->plugprefs'notify_class'], $vars['to_info']['user_class']))
				{
					set_time_limit(30);
					$this->pm_send_notify($vars['to_info']['user_id'], $vars, $pmid, count($a_list));
				}
				$ret .= LAN_PM_40.": {$vars['to_info']['user_name']}<br />";
			}
		}
		return $ret;
	}

	/**
	 *	Mark a PM as read
	 *	If flag set, send read receipt to sender
	 *
	 *	@param	int $pm_id - ID of PM
	 *	@param	array $pm_info - PM details
	 *
	 *	@return	none
	 *	
	 *	@todo - 'read_delete' pref doesn't exist - remove code? Or support?
	 */
	function pm_mark_read($pm_id, $pm_info)
	{
		$now = time();
		if($this->plugprefs'read_delete'])
		{
			$this->del($pm_id);
		}
		else
		{
			e107::getDb()->gen("UPDATE `#private_msg` SET `pm_read` = {$now} WHERE `pm_id`=".intval($pm_id)); // TODO does this work properly?
			if(strpos($pm_info['pm_option'], '+rr') !== FALSE)
			{
				$this->pm_send_receipt($pm_info);
			}
		  	e107::getEvent()->trigger('user_pm_read', $pm_id);
		}
	}


	/*
	 *	Get an existing PM
	 *
	 *	@param	int $pmid - ID of PM in DB
	 *
	 *	@return	boolean|array - FALSE on error, array of PM info on success
	 */
	function pm_get($pmid)
	{
		$qry = "
		SELECT pm.*, ut.user_image AS sent_image, ut.user_name AS sent_name, uf.user_image AS from_image, uf.user_name AS from_name, uf.user_email as from_email, ut.user_email as to_email  FROM #private_msg AS pm
		LEFT JOIN #user AS ut ON ut.user_id = pm.pm_to
		LEFT JOIN #user AS uf ON uf.user_id = pm.pm_from
		WHERE pm.pm_id='".intval($pmid)."'
		";
		if (e107::getDb()->gen($qry))
		{
			$row = e107::getDb()->fetch();
			return $row;
		}
		return FALSE;
	}


	



	/**
	 *	Delete a PM from a user's inbox/outbox.
	 *	PM is only actually deleted from DB once both sender and recipient have marked it as deleted
	 *	When physically deleted, any attachments are deleted as well
	 *
	 *	@param integer $pmid - ID of the PM
	 *	@param boolean $force - set to TRUE to force deletion of unread PMs
	 *	@return boolean|string - FALSE if PM not found, or other DB error. String if successful
	 */
	function del($pmid, $force = FALSE)
	{
		$sql = e107::getDb();
		$pmid = (int)$pmid;
		$ret = '';
		$newvals = '';
		if($sql->select('private_msg', '*', 'pm_id = '.$pmid.' AND (pm_from = '.USERID.' OR pm_to = '.USERID.')'))
		{
			$row = $sql->fetch();

			// if user is the receiver of the PM
			if (!$force && ($row['pm_to'] == USERID))
			{
				$newvals = 'pm_read_del = 1';
				$ret .= LAN_PM_42.'<br />';
				if($row['pm_sent_del'] == 1) { $force = TRUE; } // sender has deleted as well, set force to true so the DB record can be deleted
			}

			// if user is the sender of the PM
			if (!$force && ($row['pm_from'] == USERID))
			{
				if($newvals != '') { $force = TRUE; }
				$newvals = 'pm_sent_del = 1';
				$ret .= LAN_PM_43."<br />";
				if($row['pm_read_del'] == 1) { $force = TRUE; } // receiver has deleted as well, set force to true so the DB record can be deleted
			}

			if($force == TRUE)
			{
				// Delete any attachments and remove PM from db
				$attachments = explode(chr(0), $row['pm_attachments']);
				$aCount = array(0,0);
				foreach($attachments as $a)
				{
					$a = trim($a);
					if ($a)
					{
						$filename = e_PLUGIN.'pm/attachments/'.$a;
						if (unlink($filename)) $aCount[0]++; else $aCount[1]++;
					}
				}
				if ($aCount[0] || $aCount[1])
				{

				//	$ret .= str_replace(array('--GOOD--', '--FAIL--'), $aCount, LAN_PM_71).'<br />';
					$ret .= e107::getParser()->lanVars(LAN_PM_71, $aCount);
				}
				$sql->delete('private_msg', 'pm_id = '.$pmid);
			}
			else
			{
				$sql->update('private_msg', $newvals.' WHERE pm_id = '.$pmid);
			}
			return $ret;
		}
		return FALSE;
	}

	/**
	 * Convinient url assembling shortcut
	 */
	public function url($action, $params = array(), $options = array())
	{
		if(strpos($action, '/') === false) $action = 'view/'.$action;
		e107::getUrl()->create('pm/'.$action, $params, $options);
	}

	/**
	 *	Send an email to notify of a PM
	 *
	 *	@param int $uid - not used
	 *	@param array $pmInfo - PM details
	 *	@param int $pmid - ID of PM in database
	 *	@param int $attach_count - number of attachments
	 *
	 *	@return none
	 */
	function pm_send_notify($uid, $pmInfo, $pmid, $attach_count = 0)
	{
		require_once(e_HANDLER.'mail.php');
		$subject = LAN_PM_100.SITENAME;
	//	$pmlink = $this->url('show', 'id='.$pmid, 'full=1&encode=0'); //TODO broken - replace with e_url.php configuration.
		$pmlink = SITEURLBASE.e_PLUGIN_ABS."pm/pm.php?show.".$pmid;
		$txt = LAN_PM_101.SITENAME."\n\n";
		$txt .= LAN_PM_102.USERNAME."\n";
		$txt .= LAN_PM_103.$pmInfo['pm_subject']."\n";
		if($attach_count > 0)
		{
			$txt .= LAN_PM_104.$attach_count."\n";
		}
		$txt .= LAN_PM_105."\n".$pmlink."\n";
		sendemail($pmInfo['to_info']['user_email'], $subject, $txt, $pmInfo['to_info']['user_name']);
	}


	/**
	 *	Send PM read receipt
	 *
	 *	@param array $pmInfo - PM details
	 *
	 * 	@return none
	 */
	function pm_send_receipt($pmInfo)
	{
		require_once(e_HANDLER.'mail.php');
		$subject = LAN_PM_106.$pmInfo['sent_name'];
	//	$pmlink = $this->url('show', 'id='.$pmInfo['pm_id'], 'full=1&encode=0');
		$pmlink = SITEURLBASE.e_PLUGIN_ABS."pm/pm.php?show.".$pmInfo['pm_id'];
		$txt = str_replace("{UNAME}", $pmInfo['sent_name'], LAN_PM_107).date('l F dS Y h:i:s A')."\n\n";
		$txt .= LAN_PM_108.date('l F dS Y h:i:s A', $pmInfo['pm_sent'])."\n";
		$txt .= LAN_PM_103.$pmInfo['pm_subject']."\n";
		$txt .= LAN_PM_105."\n".$pmlink."\n";
		sendemail($pmInfo['from_email'], $subject, $txt, $pmInfo['from_name']);
	}


	/**
	 *	Get list of users blocked from sending to a specific user ID.
	 *
	 *	@param integer $to - user ID
	 *
	 *	@return array of blocked users as user IDs
	 */
	function block_get($to = USERID)
	{
		$sql = e107::getDb();
		$ret = array();
		$to = intval($to);		// Precautionary
		if ($sql->select('private_msg_block', 'pm_block_from', 'pm_block_to = '.$to))
		{
			while($row = $sql->fetch())
			{
				$ret[] = $row['pm_block_from'];
			}
		}
		return $ret;
	}


	/**
	 *	Get list of users blocked from sending to a specific user ID.
	 *
	 *	@param integer $to - user ID
	 *
	 *	@return array of blocked users, including specific user info
	 */
	function block_get_user($to = USERID)
	{
		$sql = e107::getDb();
		$ret = array();
		$to = intval($to);		// Precautionary
		if ($sql->gen('SELECT pm.*, u.user_name FROM `#private_msg_block` AS pm LEFT JOIN `#user` AS u ON `pm`.`pm_block_from` = `u`.`user_id` WHERE pm_block_to = '.$to))
		{
			while($row = $sql->fetch())
			{
				$ret[] = $row;
			}
		}
		return $ret;
	}


	/**
	 *	Add a user block
	 *
	 *	@param int $from - sender to block
	 *	@param int $to - user doing the blocking
	 *
	 *	@return string result message
	 */
	function block_add($from, $to = USERID)
	{
		$sql = e107::getDb();
		$from = intval($from);
		if($sql->select('user', 'user_name, user_perms', 'user_id = '.$from))
		{
			$uinfo = $sql->fetch();
			if (($uinfo['user_perms'] == '0') || ($uinfo['user_perms'] == '0.'))
			{  // Don't allow block of main admin
				return LAN_PM_64;
			}
		  
			if(!$sql->count('private_msg_block', '(*)', 'WHERE pm_block_from = '.$from." AND pm_block_to = '".e107::getParser()->toDB($to)."'"))
			{
				if($sql->insert('private_msg_block', array(
						'pm_block_from' => $from,
						'pm_block_to' => $to,
						'pm_block_datestamp' => time()
					)))
				{
					return str_replace('{UNAME}', $uinfo['user_name'], LAN_PM_47);
				}
				else
				{
					return LAN_PM_48;
				}
			}
			else
			{
				return str_replace('{UNAME}', $uinfo['user_name'], LAN_PM_49);
			}
		}
		else
		{
			return LAN_PM_17;
		}
	}



	/**
	 *	Delete user block
	 *
	 *	@param int $from - sender to block
	 *	@param int $to - user doing the blocking
	 *
	 *	@return string result message
	 */
	function block_del($from, $to = USERID)
	{
		$sql = e107::getDb();
		$from = intval($from);
		if($sql->select('user', 'user_name', 'user_id = '.$from))
		{
			$uinfo = $sql->fetch();
			if($sql->select('private_msg_block', 'pm_block_id', 'pm_block_from = '.$from.' AND pm_block_to = '.intval($to)))
			{
				$row = $sql->fetch();
				if($sql->delete('private_msg_block', 'pm_block_id = '.intval($row['pm_block_id'])))
				{
					return str_replace('{UNAME}', $uinfo['user_name'], LAN_PM_44);
				}
				else
				{
					return LAN_PM_45;
				}
			}
			else
			{
				return str_replace('{UNAME}', $uinfo['user_name'], LAN_PM_46);
			}
		}
		else
		{
			return LAN_PM_17;
		}
	}


	/**
	 *	Get user ID matching a name
	 *
	 *	@param string var - name to match
	 *
	 *	@return boolean|array - FALSE if no match, array of user info if found
	 */
	function pm_getuid($var)
	{
		$sql = e107::getDb();
		$var = strip_if_magic($var);
		$var = str_replace("'", '&#039;', trim($var));		// Display name uses entities for apostrophe
		if($sql->select('user', 'user_id, user_name, user_class, user_email', "user_name LIKE '".$sql->escape($var, FALSE)."'"))
		{
			$row = $sql->fetch();
			return $row;
		}
		return FALSE;
	}


	/**
	 *	Get list of users in class
	 *
	 *	@param int $class - class ID
	 *
	 *	@return boolean|array - FALSE on error/none found, else array of user information arrays
	 */
	function get_users_inclass($class)
	{
		$sql = e107::getDb();
		if($class == e_UC_MEMBER)
		{
			$qry = "SELECT user_id, user_name, user_email, user_class FROM `#user` WHERE 1";
		}
		elseif($class == e_UC_ADMIN)
		{
			$qry = "SELECT user_id, user_name, user_email, user_class FROM `#user` WHERE user_admin = 1";
		}
		elseif($class)
		{
			$regex = "(^|,)(".e107::getParser()->toDB($class).")(,|$)";
			$qry = "SELECT user_id, user_name, user_email, user_class FROM `#user` WHERE user_class REGEXP '{$regex}'";
		}
		if($sql->gen($qry))
		{
			$ret = $sql->db_getList();
			return $ret;
		}
		return FALSE;
	}


	/**
	 *	Get inbox - up to $limit messages from $from
	 *
	 *	@param int $uid - user ID
	 *	@param int $from - first message
	 *	@param int $limit - number of messages
	 *
	 *	@return boolean|array - FALSE if none found or error, array of PMs if available
	 */
	function pm_get_inbox($uid = USERID, $from = 0, $limit = 10)
	{
		$sql = e107::getDb();
		$ret = array();
		$total_messages = 0;
		$uid = intval($uid);
		$limit = intval($limit);
		if ($limit < 2) { $limit = 10; }
		$from = intval($from);
		$qry = "
		SELECT SQL_CALC_FOUND_ROWS pm.*, u.user_image, u.user_name FROM `#private_msg` AS pm
		LEFT JOIN `#user` AS u ON u.user_id = pm.pm_from
		WHERE pm.pm_to='{$uid}' AND pm.pm_read_del=0
		ORDER BY pm.pm_sent DESC
		LIMIT ".$from.", ".$limit."
		";
		if($sql->gen($qry))
		{
			$total_messages = $sql->total_results;		// Total number of messages
			$ret['messages'] = $sql->db_getList();
		}
		$ret['total_messages'] = $total_messages;		// Should always be defined
		return $ret;
	}


	/**
	 *	Get outbox - up to $limit messages from $from
	 *
	 *	@param int $uid - user ID
	 *	@param int $from - first message
	 *	@param int $limit - number of messages
	 *
	 *	@return boolean|array - FALSE if none found or error, array of PMs if available
	 */
	function pm_get_outbox($uid = USERID, $from = 0, $limit = 10)
	{
		$sql = e107::getDb();
		$ret = array();
		$total_messages = 0;
		$uid = intval($uid);
		$limit = intval($limit);
		if ($limit < 2) { $limit = 10; }
		$from = intval($from);
		$qry = "
		SELECT SQL_CALC_FOUND_ROWS pm.*, u.user_image, u.user_name FROM #private_msg AS pm
		LEFT JOIN #user AS u ON u.user_id = pm.pm_to
		WHERE pm.pm_from='{$uid}' AND pm.pm_sent_del = '0'
		ORDER BY pm.pm_sent DESC
		LIMIT ".$from.', '.$limit;
		
		if($sql->gen($qry))
		{
			$total_messages = $sql->total_results;		// Total number of messages
			$ret['messages'] = $sql->db_getList();
		}
		$ret['total_messages'] = $total_messages;
		return $ret;
	}

	/**
	 *	Get the box-related information for inbox or outbox - limits, message count etc
	 *	The information read from the DB is cached internally for efficiency
	 *
	 *	@param	string $which = inbox|outbox|clear
	 *
	 *	@return	array
	 *	
	 */
	function pm_getInfo($which = 'inbox')
	{
		static $pm_info;
		if('clear' == $which)
		{
			unset($pm_info['inbox']);
			unset($pm_info['outbox']);
			return;
		}
		if('inbox' == $which)
		{
			$qry = "SELECT count(pm.pm_id) AS total, SUM(pm.pm_size)/1024 size, SUM(pm.pm_read = 0) as unread FROM `#private_msg` as pm WHERE pm.pm_to = ".USERID." AND pm.pm_read_del = 0";
		}
		else
		{
			$qry = "SELECT count(pm.pm_from) AS total, SUM(pm.pm_size)/1024 size, SUM(pm.pm_read = 0) as unread FROM `#private_msg` as pm WHERE pm.pm_from = ".USERID." AND pm.pm_sent_del = 0";
		}
		if(!isset($pm_info[$which]['total']))
		{
			$this->pmDB->gen($qry);
			$pm_info[$which] = $this->pmDB->fetch();
			if ($which == 'inbox' && ($this->plugprefs'animate'] == 1 || $this->plugprefs'popup'] == 1))
			{
				if($new = $this->pmDB->db_Count('private_msg', '(*)', "WHERE pm_sent > '".USERLV."' AND pm_read = 0 AND pm_to = '".USERID."' AND pm_read_del != 1"))
				{
					$pm_info['inbox']['new'] = $new;
				}
				else
				{
					$pm_info['inbox']['new'] = 0;
				}
			}
		}
		if(!isset($pm_info[$which]['limit']))
		{
			if(varset($this->plugprefs'pm_limits'],0) > 0)
			{
				if($this->plugprefs'pm_limits'] == 1)
				{
					$qry = "SELECT MAX(gen_user_id) AS inbox_limit, MAX(gen_ip) as outbox_limit FROM `#generic` WHERE gen_type='pm_limit' AND gen_datestamp IN (".USERCLASS_LIST.")";
				}
				else
				{
					$qry = "SELECT MAX(gen_intdata) AS inbox_limit, MAX(gen_chardata) as outbox_limit FROM `#generic` WHERE gen_type='pm_limit' AND gen_datestamp IN (".USERCLASS_LIST.")";
				}
				if($this->pmDB->gen($qry))
				{
					$row = $this->pmDB->fetch();
					$pm_info['inbox']['limit'] =  $row['inbox_limit'];
					$pm_info['outbox']['limit'] =  $row['outbox_limit'];
				}
				$pm_info['inbox']['limit_val'] = ($this->plugprefs'pm_limits'] == 1 ? varset($pm_info['inbox']['total'],'') : varset($pm_info['inbox']['size'],''));
				if(!$pm_info['inbox']['limit'] || !$pm_info['inbox']['limit_val'])
				{
					$pm_info['inbox']['filled'] = 0;
				}
				else
				{
					$pm_info['inbox']['filled'] = number_format($pm_info['inbox']['limit_val']/$pm_info['inbox']['limit'] * 100, 2);
				}
				$pm_info['outbox']['limit_val'] = ($this->plugprefs'pm_limits'] == 1 ? varset($pm_info['outbox']['total'],'') : varset($pm_info['outbox']['size'],''));
				if(!$pm_info['outbox']['limit'] || !$pm_info['outbox']['limit_val'])
				{
					$pm_info['outbox']['filled'] = 0;
				}
				else
				{
					$pm_info['outbox']['filled'] = number_format($pm_info['outbox']['limit_val']/$pm_info['outbox']['limit'] * 100, 2);
				}
			}
			else
			{
				$pm_info['inbox']['limit'] = '';
				$pm_info['outbox']['limit'] = '';
				$pm_info['inbox']['filled'] = '';
				$pm_info['outbox']['filled'] = '';
			}
		}
		return $pm_info;
	}
}
?>