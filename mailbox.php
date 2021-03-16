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
$text 	= '';
$page   = $tp->filter($_GET['page']);

// Load mailbox class and initiate
require_once(e_PLUGIN."mailbox/mailbox_class.php");
$mailbox_class 		= new Mailbox;

// Check if AJAX calls were made
if(e_AJAX_REQUEST)
{	
	// Check if 'mark as read/unread' button was pressed
	if(varset($_POST['action']) == 'readunread')
	{
		$mailbox_class->ajaxReadUnread();
	}

	// Check if 'mark as star' button was pressed, or individual star
	if(varset($_POST['action']) == 'star')
	{
		$mailbox_class->ajaxStar();
	}

	// Check if trash button was pressed 
	if(varset($_POST['action']) == 'trash')
	{
		$mailbox_class->ajaxTrash();
	}
}

// Get some basic info 
$current_mailbox 	= $mailbox_class->get_current_mailbox($page);
$queryargs 			= $mailbox_class->get_database_queryargs($current_mailbox);

// Set pagetitles
define('PAGE_NAME', LAN_MAILBOX_NAME);
$pagetitle = $mailbox_class->get_pagetitle($page); 
define('e_PAGETITLE', $pagetitle);

// Load the header and mailbox class
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
	// Notify user that messages in trashbox are permanently deleted after 14 days in the trashbox
	if($current_mailbox == 'trashbox')
	{
		e107::getMessage()->addInfo(LAN_MAILBOX_TRASHDELETED);
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
		// Load tablelist table (contents)
			// Header
			$text .= $tp->parseTemplate($template['tablelist']['header'], true, $sc);

			// Body (= messages)
				// Construct query
				$query_getmessages = $sql->retrieve('mailbox_messages', '*', $queryargs, true);

				// Check if there messages to display
				if($query_getmessages)
				{
					// Messages found, loop through
					foreach($query_getmessages as $message)
					{
						$sc->setVars($message); // pass query values on so they can be used in the shortcodes
						$text .= $tp->parseTemplate($template['tablelist']['messages'], true, $sc);
					}
				}
				else
				{
					$nomessages = e107::getParser()->lanVars(LAN_MAILBOX_NOMESSAGESTODISPLAY, $current_mailbox, true);
					$text .= e107::getMessage()->addInfo($nomessages);
				}
			// Footer
			$text .= $tp->parseTemplate($template['tablelist']['footer'], true, $sc);
		// Close tabellist
		$text .= $tp->parseTemplate($template['tablelist']['end'], true, $sc);
	// Close container
	$text .= $tp->parseTemplate($template['container']['end'], true, $sc);
}

$ns->tablerender(LAN_MAILBOX_NAME, e107::getMessage()->render().$text, 'mailbox');
require_once(FOOTERF);
exit;