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

$MAILBOX_TEMPLATE['tablelist']['header'] = '
<div class="panel panel-primary">
	<div class="panel-heading clearfix">
		<div class="row">
			<div class="col-md-4">
				<h2 class="panel-title pull-left mailbox-title">Inbox</h3>
			</div>
			<!-- /.col-md-4 -->
			<div class="col-md-8">
				<form method="get" action="">
				<div class="input-group">
					<input class="form-control" type="text" id="mailbox-searchform" placeholder="Search in inbox...">
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
';

$MAILBOX_TEMPLATE['tablelist']['body'] = '	    
				<tr>
					<td><input type="checkbox"></td>
					<td class="mailbox-star hidden-xs"><a href="#"><i class="fa fa-star text-yellow"></i></a></td>
					<td class="mailbox-name"><a href="#">{SETIMAGE: w=50&h=50&crop=1} {USER_AVATAR: shape=circle} John Doe</a></td>
					<td class="mailbox-subject">Test subject</td>
					<td class="mailbox-attachment hidden-xs"></td>
					<td class="mailbox-date">5 mins ago</td>
				</tr>
';

$MAILBOX_TEMPLATE['tablelist']['footer'] = '
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

$MAILBOX_TEMPLATE['box_navigation'] = '
<div class="form-group">
	<a href="compose.php" class="btn btn-primary btn-block">Compose</a>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">Folders</div>
		<div class="panel-body">
 		<ul class="nav nav-pills nav-stacked mailbox-nav">
	        <li class="active"><a href="#">
	        	<i class="fa fa-inbox"></i> Inbox 
	        	<span class="label label-primary pull-right">12</span></a>
	        </li>
	        <li><a href="#">
	        	<i class="fa fa-envelope-o"></i> Outbox</a>
	        </li>
	        <li><a href="#">
	        	<i class="fa fa-pencil-square-o"></i> Drafts</a>
	        </li>
	        <li><a href="#"><i class="fa fa-floppy-o">
	        	</i> Saved <span class="label label-warning pull-right">65</span></a>
	        </li>
    		<li><a href="#">
    			<i class="fa fa-trash-o"></i> Trash</a>
    		</li>
  		</ul>
	 </div>
<!-- /.panel-body -->
</div>
<!-- /. panel --> 
';