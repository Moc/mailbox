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

require_once(HEADERF);

/* STRUCTURE
*
* BOXES
* 1) Inbox
* 2) Outbox
* 3) Draftbox
* 4) Savedbox
* 5) Trashbox
*
* COMPOSE
*
*/
$text = ''; 

switch ($_GET['page']) 
{
	case 'inbox':
		# code...
		break;
	case 'outbox':
		break;
	case 'draftbox':
		# code...
		break;
	case 'savedbox':
		# code...
		break;
	case 'trashbox':
		# code...
		break;
	case 'compose':
		$text .= "compose page";
		break;
	default:
		# code... inbox
		break;
}
/* {SETIMAGE: w=20} {USER_AVATAR} */

$text .= '
<div class="row">
	<div class="col-md-3">

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
	</div>
	<!-- /. col-md-3 --> 
	<div class="col-md-9">
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
				    	1-20/200
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
							    <td class="mailbox-avatar hidden-xs"><a href="#"><img class="img-circle user-avatar" alt="" src="/e107/thumb.php?src=%7Be_IMAGE%7Dgeneric%2Fblank_avatar.jpg&amp;w=40&amp;h=0" width="40"  /></a></td>
							    <td class="mailbox-namedate">
							    	<a href="#">John Doe</a> 
							    	<br />
							    	<div class="mailbox-datestamp">5 mins ago</div>
							    </td>
							    <td class="mailbox-subject">Testing a longer subject</td>
							    <td class="mailbox-attachment hidden-xs"><i class="fa fa-paperclip"></i></td>
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
			  		1-20/200
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
	</div>
	<!-- /.col-md-9 -->
</div>
';

$ns->tablerender("Mailbox", $text);
require_once(FOOTERF);
exit;