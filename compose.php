<?php
/*
 * Mailbox 
 *
 * Copyright (C) 2021 - Tijn Kuyper (Moc)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
*/

if(!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

if(!e107::isInstalled('mailbox'))
{
	e107::redirect();
	exit;
}

// Load the LAN files
e107::lan('mailbox');

// Define variables
$sql 	= e107::getDb();
$tp 	= e107::getParser();
$frm 	= e107::getForm();
$text 	= '';
$page   = $tp->filter($_GET['page']);

// Load mailbox class and initiate
require_once(e_PLUGIN."mailbox/mailbox_class.php");
$mailbox_class 		= new Mailbox;

// Get some basic info 
$current_mailbox 	= $mailbox_class->get_current_mailbox($page);
$queryargs 			= $mailbox_class->get_database_queryargs($current_mailbox);

// Set pagetitles
$pagetitle = $mailbox_class->get_pagetitle($page); 
define('e_PAGETITLE', $pagetitle);

// Load the header a
require_once(HEADERF);

// Load template and shortcodes
$sc 		= e107::getScBatch('mailbox', TRUE);
$template 	= e107::getTemplate('mailbox');
$template 	= array_change_key_case($template);


if(!USERID)
{
	e107::getMessage()->addError(LAN_MAILBOX_NOTLOGGEDIN);
}
else
{
	// Check if the user has just submitted a message
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Get message id when updating existing draft
		if($_GET['cid'])
		{
			$_POST['id'] = $tp->filter($_GET['cid']);
		}

		// Determine whether we are sending, saving as a draft or discarding the message
		switch($_POST['compose'])
		{
			// Message should be send to the recipient(s)
			case 'send':
			default:
				$text .= $mailbox_class->process_compose("send", $_POST);
				break;
			// Message should be saved as a draft
			case 'draft':
				$text .= $mailbox_class->process_compose("draft", $_POST);
				break;
			case 'discard':
				$text .= $mailbox_class->discard_message($_POST);
				break;
		}
	}
	// Open container
	$text .= $tp->parseTemplate($template['container']['start'], true, $sc);
		// Open sidemenu
		$text .= $tp->parseTemplate($template['box_navigation']['start'], true, $sc);
			// Load sidemenu content
			$text .= $tp->parseTemplate($template['box_navigation']['content'], true, $sc);
		// Close sidemenu
		$text .= $tp->parseTemplate($template['box_navigation']['end'], true, $sc);
	// Open tablelist 
	$text .= $tp->parseTemplate($template['tablelist']['start'], true, $sc);

		// Check if we are continuing a draft - in which case we need to retrieve the data from db
		if($tp->filter($_GET['cid']))
		{
			$cid = $tp->filter($_GET['cid']);
			$draftvalues = $sql->retrieve('mailbox_messages', 'message_id, message_from, message_to, message_subject, message_text, message_draft, message_sent', 'message_id='.$cid);

			/* Need to confirm that:
			 - user is indeed the original sender of the message
			 - message is a draft
			 - message has not been sent yet
			*/
			if(
				$draftvalues['message_from'] 	== USERID &&
				$draftvalues['message_draft']	!= 0 &&
				$draftvalues['message_sent'] 	== 0
			  )
			{
				$text .= e107::getMessage()->addInfo("You are continuing your draft message.");
				$sc->setVars($draftvalues);
				$text .= $tp->parseTemplate($template['compose_message'], true, $sc);
			}
			// Message is not from user or message is not a draft/has already been sent.
			else
			{
				$url = e107::url('mailbox', 'compose');
				e107::redirect($url);
			}
		}
		else
		{
			$text .= $tp->parseTemplate($template['compose_message'], true, $sc);
		}

		// Close tabellist
		$text .= $tp->parseTemplate($template['tablelist']['end'], true, $sc);
	// Close container
	$text .= $tp->parseTemplate($template['container']['end'], true, $sc);
}

$ns->tablerender(LAN_MAILBOX_NAME, e107::getMessage()->render().$text);
require_once(FOOTERF);
exit;