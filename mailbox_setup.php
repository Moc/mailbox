<?php
/*
 * Mailbox - an e107 plugin by Tijn Kuyper
 *
 * Copyright (C) 2016-2017 Tijn Kuyper (http://www.tijnkuyper.nl)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Custom install/uninstall/update routines
*/

class mailbox_setup
{

 	function install_pre($var)
	{

	}

	/**
	 * Installs example data after tables have been created
	 */
	function install_post($var)
	{
		$sql = e107::getDb();
		$mes = e107::getMessage();

		// Currencies
		$example_messages = "
		INSERT INTO `#mailbox_messages` (`message_id`, `message_from`, `message_to`, `message_draft`, `message_sent`, `message_read`, `message_subject`, `message_text`, `message_from_starred`, `message_to_starred`, `message_from_deleted`, `message_to_deleted`, `message_attachments`) VALUES

			(1, 1, 1, 1, 0, 0, 'Test draft ', '[html]<p>test draft&nbsp;</p>[/html]', 0, 0, 0, 0, ''),
			(2, 2, 1, 0, 1488722400, 0, 'Message from john', '[html]<p>Hi there!</p><p>Just checking in :)</p>[/html]', 0, 1, 0, 0, ''),
			(3, 1, 1, 1, 0, 0, 'test multiple', '', 0, 0, 0, 0, '');
		";

		$status = ($sql->gen($example_messages)) ? E_MESSAGE_SUCCESS : E_MESSAGE_ERROR;
		$mes->add("Adding example messages", $status);
	}


	function uninstall_options()
	{

	}


	function uninstall_post($var)
	{

	}


	function upgrade_post($var)
	{

	}

}