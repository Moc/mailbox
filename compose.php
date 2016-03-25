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

// Check if the compose form is filled in
if($_POST)
{
	switch ($_POST['compose']) 
	{
		// Message should be send to the receiver
		case 'send':
		default:
			$mailbox_class->process_compose("send", $_POST); 
			//print_a("The message should be send");
			break;
		// Message should be saved as a draft
		case 'draft':
			$mailbox_class->process_compose("draft", $_POST); 
			//print_a("The message should be saved as a draft");
			break;
		case 'discard':
			print_a("The message should be discarded");
			break;
	}
}
// Form is not filled in yet, show form
else
{
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
			$text .= $tp->parseTemplate($template['compose_message'], true, $sc);
		// Close right content
		$text .= '</div>';
	// Close container
	$text .= '</div>';

	$ns->tablerender(LAN_MAILBOX_NAME, $text);
	require_once(FOOTERF);
	exit;
}