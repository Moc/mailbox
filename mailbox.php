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

// Load the header and mailbox class
require_once(HEADERF);
require_once(e_PLUGIN."mailbox/mailbox_class.php");

// Load template and shortcodes
$sc 		= e107::getScBatch('mailbox', TRUE);
$template 	= e107::getTemplate('mailbox'); 
$template 	= array_change_key_case($template);

// Define variables
$sql 	= e107::getDb();
$tp 	= e107::getParser();
$text 	= ''; 

$mailbox_class 		= new Mailbox; 
$current_mailbox 	= $mailbox_class->get_current_mailbox($_GET['page']);
$queryargs 			= $mailbox_class->get_database_queryargs($current_mailbox); 

if(!USERID)
{
	e107::getMessage()->addError("Please login to view your mailbox!");
}
else
{
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
				// Construct query  
				$query_getmessages = $sql->retrieve('mailbox_messages', '*', ''.$queryargs.'', true);
				
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
}

$ns->tablerender(LAN_MAILBOX_NAME, e107::getMessage()->render().$text);
require_once(FOOTERF);
exit;