<?php
/*
 * Mailbox - an e107 plugin by Tijn Kuyper
 *
 * Copyright (C) 2016-2017 Tijn Kuyper (http://www.tijnkuyper.nl)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 */

if (!defined('e107_INIT')) { exit; }

class mailbox_url
{
	function config()
	{
		$config = array();

		$config['mailbox'] = array(
			'regex'			=> '^mailbox/?$',
			'sef'			=> 'mailbox',
			'redirect'		=> '{e_PLUGIN}mailbox/mailbox.php',
		);
		
		$config['compose'] = array(
			'regex'			=> '^mailbox/compose/?$',
			'sef'			=> 'mailbox/compose',
			'redirect'		=> '{e_PLUGIN}mailbox/compose.php',
		);

		$config['read'] = array(
			'regex'			=> '^mailbox/read/(.*)$',
			'sef'			=> 'mailbox/read/{id}',
			'redirect'		=> '{e_PLUGIN}mailbox/read.php?id=$1',
		);

		$config['box'] = array(
			'regex'			=> '^mailbox/(.*)$',
			'sef'			=> 'mailbox/{boxname}',
			'redirect'		=> '{e_PLUGIN}mailbox/mailbox.php?page=$1',
		);

		return $config;
	}
}