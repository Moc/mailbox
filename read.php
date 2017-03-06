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
$frm 	= e107::getForm();
$text 	= '';
$mid 	= (int) $_GET['id'];

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
			// Construct query
			$query_getmessage = $sql->retrieve('mailbox_messages', '*', 'message_id='.$mid.'');

			// Check if the message is there
			if($query_getmessage)
			{
				if($query_getmessage['message_to'] == USERID || $query_getmessage['message_from'] == USERID)
				{
					$sc->setVars($query_getmessage);
					$text .= $tp->parseTemplate($template['read_message'], true, $sc);
				}
				else
				{
					$text .= e107::getMessage()->addError(LAN_MAILBOX_MESSAGENOTYOURS);
				}
			}
			else
			{
				$text .= e107::getMessage()->addError(LAN_MAILBOX_MESSAGENOTFOUND); // may only happen when message has been deleted > 14 days ago
			}
	// Close right content
	$text .= '</div>';
// Close container
$text .= '</div>';

$ns->tablerender(LAN_MAILBOX_NAME, e107::getMessage()->render().$text);
require_once(FOOTERF);
exit;