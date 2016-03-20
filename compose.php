<?php
/*
 * Messaging - an e107 plugin by Tijn Kuyper
 *
 * Copyright (C) 2015-2016 Tijn Kuyper (http://www.tijnkuyper.nl)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Messaging class including all generic functions
 *
 */

require_once('../../class2.php');

if (!e107::isInstalled('messaging')) 
{
	e107::redirect();
	exit;
}

require_once(HEADERF);

$text = "This is the page to send a new message";

$ns->tablerender("Compose New Message", $text);
require_once(FOOTERF);
exit;