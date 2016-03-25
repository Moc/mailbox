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

- Move to trash
=> Remove starred status from to (set message_to_starred to 0)

- Empty trash 
=> last empty date updated each time when trash is emptied
=> only messages displayed where the message_to_deleted < latest_emptytrash_datestamp of user

- Delete draft
=> check for draft status (just to be sure)
=> set message_to_deleted=1


*/

if (!defined('e107_INIT')) { exit; }

class Mailbox
{
	protected $plugprefs = array();

	public function __construct()
	{
		$this->plugprefs = e107::getPlugPref('messaging');
	}
	
	public function get_current_mailbox($parm)
	{
		// All valid mailboxes in an array 
		$mailbox_array = array("inbox", "outbox", "draftbox", "starbox", "trashbox"); 
		// Check user input to see if mailbox matches with array
		if(in_array($parm, $mailbox_array))
		{
			$current_mailbox = $parm; 
		}
		// Invalid mailbox input, so default back to inbox to be sure
		else
		{
			$current_mailbox = 'inbox';
		}

		return $current_mailbox;
	}

	public function get_database_queryargs($box = '')
	{
		// Default back to inbox to be sure 
		if(!$box) { $box = 'inbox'; }

		switch($box) 
		{
			case 'inbox':
			default:
				$args = "message_to=".USERID." AND message_to_deleted=0"; 
				break;
			case 'outbox':
				$args = "message_from=".USERID." AND message_to_deleted=0 AND message_draft=0";
				break;
			case 'draftbox':
				$args = "message_from=".USERID." AND message_draft=1 AND message_sent=0 AND message_to_deleted=0";
				break;
			case 'starbox': // no, not Starbucks ;)
				$args = "message_to=".USERID." AND message_to_starred=1 AND message_to_deleted=0";
				break;
			case 'trashbox':
				$args = "message_to=".USERID." AND message_to_deleted!=0";
				break;
		}

		return $args; 
	}

	public function process_compose($action = 'send', $post_data)
	{
		print_a("Message action: ".$action);
		print_a($post_data);
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
		$maxSendNow = varset($this->plugprefs['pm_max_send'], 100); // Max # of messages before having to queue
		
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
					if(check_class($this->plugprefs['notify_class'], $u['user_class']))
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
				if(check_class($this->plugprefs['notify_class'], $vars['to_info']['user_class']))
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
		if($this->plugprefs['read_delete'])
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
}