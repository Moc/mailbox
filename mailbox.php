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
e107::lan('mailbox', false, true); 

// Load the header
require_once(HEADERF);

// Load template and shortcodes
$sc 		= e107::getScBatch('mailbox', TRUE);
$template 	= e107::getTemplate('mailbox'); 
$template 	= array_change_key_case($template);

// Define variables
$sql 	= e107::getDb();
$tp 	= e107::getParser();
$text 	= ''; 

// Construct the database queries depending on which box the user is viewing 
switch ($_GET['page']) 
{
	case 'inbox':
	default:
		$query_getmessages = $sql->retrieve("mailbox_messages", "*", "message_to=".USERID." AND message_to_deleted=0", true); 
		break;
	case 'outbox':
		$query_getmessages = $sql->retrieve("mailbox_messages", "*", "message_from=".USERID." AND message_to_deleted=0", true);
		break;
	case 'draftbox':
		$query_getmessages = $sql->retrieve("mailbox_messages", "*", "message_from=".USERID." AND message_draft=1 AND message_sent=0 AND message_to_deleted=0", true);
		break;
	case 'starbox': // no, not Starbucks ;)
		$query_getmessages = $sql->retrieve("mailbox_messages", "*", "message_to=".USERID." AND message_starred_to=1 AND message_to_deleted=0", true);
		break;
	case 'trashbox':
		$query_getmessages = $sql->retrieve("mailbox_messages", "*", "message_to=".USERID." AND message_to_deleted=1", true);
		break;
	case 'compose':
		$text .= "compose page";
		break;
}

/* Let's render some things now */ 
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
	// Load right content
		// Header
		$text .= $tp->parseTemplate($template['tablelist']['header'], true, $sc);

		// Body
			// Check if there messages to display 
			if($query_getmessages)
			{
				// Messages found, loop through 
				foreach($query_getmessages as $message)
				{
					$sc->setVars($message); // pass query values on so they can be used in the shortcodes 
					$text .= $tp->parseTemplate($template['tablelist']['body'], true, $sc);
				}
			}
			else
			{
				$text .='<div class="mailbox-infomessage">'.LAN_MAILBOX_NOMESSAGESTODISPLAY.'</div>';
			} 		
		// Footer
		$text .= $tp->parseTemplate($template['tablelist']['footer'], true, $sc);
	// Close right content
	$text .= '</div>';
// Close container
$text .= '</div>';

$ns->tablerender(LAN_MAILBOX_NAME, $text);
require_once(FOOTERF);
exit;