<?php
/*
 * Mailbox - an e107 plugin by Tijn Kuyper
 *
 * Copyright (C) 2015-2016 Tijn Kuyper (http://www.tijnkuyper.nl)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 */

require_once('../../class2.php');

if (!e107::isInstalled('mailbox')) 
{
	e107::redirect();
	exit;
}

require_once(HEADERF);

$text = '

<div class="panel panel-primary">
	<div class="panel-heading clearfix">
		<div class="row">
			<div class="col-md-4">
				<h2 class="panel-title pull-left messaging-title">Inbox</h3>
			</div>
			<!-- /.col-md-4 -->
			<div class="col-md-8">
				<form method="get" action="">
				<div class="input-group">
					<input class="form-control" type="text" id="messaging-searchform" placeholder="Search in inbox...">
			        <span class="input-group-btn">
			        	<button class="btn btn-default" type="submit" name="s">'.e107::getParser()->toGlyph("search").'</button>
			        </span>
				</div>
				</form>
			</div>
			<!-- /.col-md-8 -->
		</div>
		<!-- /.row -->
	</div>
	<!-- /.panel-heading -->

	<div class="panel-body">
		<div class="mailbox-controls">
		    <!-- Check all button -->
		    <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
		    </button>
		    <div class="btn-group">
				<button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
				<button type="button" class="btn btn-default btn-sm"><i class="fa fa-floppy-o"></i></button>
		    </div>
	   		<!-- /.btn-group -->
 			<button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
		    <div class="pull-right">
		    	1-50/200
		      	<div class="btn-group">
		        	<button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
		        	<button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
		      	</div>
		      	<!-- /.btn-group -->
		    </div>
		    <!-- /.pull-right -->
		</div>

		<div class="table-responsive mailbox-messages">
			<table class="table table-hover table-striped">
				<tbody>
			  		<tr>
					    <td><input type="checkbox"></td>
					    <td class="mailbox-star hidden-xs"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
					    <td class="mailbox-name"><a href="read-mail.html">John Doe</a></td>
					    <td class="mailbox-subject">Test subject</td>
					    <td class="mailbox-attachment hidden-xs"></td>
					    <td class="mailbox-date">5 mins ago</td>
			  		</tr>
			  		<tr>
					    <td><input type="checkbox"></td>
					    <td class="mailbox-star hidden-xs"><a href="#"><i class="fa fa-star-o text-yellow"></i></a></td>
					    <td class="mailbox-name"><a href="read-mail.html">John Doe</a></td>
					    <td class="mailbox-subject">Test subject</td>
					    <td class="mailbox-attachment hidden-xs"><i class="fa fa-paperclip"></i></td>
					    <td class="mailbox-date">5 mins ago</td>
			  		</tr>
			  		<tr>
					    <td><input type="checkbox"></td>
					    <td class="mailbox-star hidden-xs"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
					    <td class="mailbox-name"><a href="read-mail.html">John Doe</a></td>
					    <td class="mailbox-subject">Test subject</td>
					    <td class="mailbox-attachment hidden-xs"></td>
					    <td class="mailbox-date">5 mins ago</td>
			  		</tr>
			  		<tr>
					    <td><input type="checkbox"></td>
					    <td class="mailbox-star hidden-xs"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
					    <td class="mailbox-name"><a href="read-mail.html">John Doe</a></td>
					    <td class="mailbox-subject">Test subject</td>
					    <td class="mailbox-attachment hidden-xs"><i class="fa fa-paperclip"></i></td>
					    <td class="mailbox-date">5 mins ago</td>
			  		</tr>
			  		<tr>
					    <td><input type="checkbox"></td>
					    <td class="mailbox-star hidden-xs"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
					    <td class="mailbox-name"><a href="read-mail.html">John Doe</a></td>
					    <td class="mailbox-subject">Test subject</td>
					    <td class="mailbox-attachment hidden-xs"></td>
					    <td class="mailbox-date">5 mins ago</td>
			  		</tr>
			  	</tbody>
			</table>
		<!-- /.table -->
		</div>
		<!-- /.mail-box-messages -->
		
	
		<div class="mailbox-controls">
			<!-- Check all button -->
			<button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i></button>
		    <div class="btn-group">
				<button type="button" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i></button>
				<button type="button" class="btn btn-default btn-sm"><i class="fa fa-floppy-o"></i></button>
		    </div>
			<!-- /.btn-group -->
			<button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>	
			<div class="pull-right">
	  		1-50/200
	      		<div class="btn-group">
			        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
			        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
			    </div>
	      		<!-- /.btn-group -->
	   		</div>
	    	<!-- /.pull-right -->
		</div>
		<!-- /.mailbox-controls -->
	</div>
	<!-- /.panel-body -->
</div>
<!-- /.panel -->

';

$ns->tablerender(" ", $text);
require_once(FOOTERF);
exit;