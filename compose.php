<?php
/*
 * Mailbox - an e107 plugin by Tijn Kuyper
 *
 * Copyright (C) 2016-2017 Tijn Kuyper (http://www.tijnkuyper.nl)
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

// Load the header and the mailbox class
require_once(HEADERF);
require_once(e_PLUGIN."mailbox/mailbox_class.php");

// Load template and shortcodes
$sc 		= e107::getScBatch('mailbox', TRUE);
$template 	= e107::getTemplate('mailbox');
$template 	= array_change_key_case($template);

// Define variables
$sql 	= e107::getDb();
$tp 	= e107::getParser();
$frm 	= e107::getForm();
$text 	= '';

$mailbox_class = new Mailbox;

// Check if the user has just submitted a message
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// Get message id when updating existing draft
	if($tp->filter($_GET['cid']))
	{
		$_POST['id'] = $tp->filter($_GET['cid']);
	}

	// Determine whether we are sending, saving as a draft or discarding the message
	switch($_POST['compose'])
	{
		// Message should be send to the receiver
		case 'send':
		default:
			$text .= $mailbox_class->process_compose("send", $_POST);
			break;
		// Message should be saved as a draft
		case 'draft':
			$text .= $mailbox_class->process_compose("draft", $_POST);
			break;
		case 'discard':
			print_a("The message should be discarded");
			break;
	}
}

// Open container
$text .= '<div class="row">';
	// Open left sidebar
	$text .= '<div class="col-md-3">';
		// Load left sidebar
		$text .= $tp->parseTemplate($template['box_navigation'], true, $sc);
	// Close left sidebar
	$text .= '</div>';
	// Open right content
	$text .= '<div class="col-md-9">';

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
			$draftvalues['message_draft']	== 1 &&
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

	// Close right content
	$text .= '</div>';
// Close container
$text .= '</div>';

$ns->tablerender(LAN_MAILBOX_NAME, e107::getMessage()->render().$text);
require_once(FOOTERF);
exit;