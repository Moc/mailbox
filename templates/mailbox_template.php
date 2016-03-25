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
				<h2 class="panel-title pull-left mailbox-title">{MAILBOX_BOXTITLE}</h3>
			</div>
			<!-- /.col-md-4 -->
			<div class="col-md-8">
				<form method="get" action="">
				<div class="input-group">
					<input class="form-control" type="text" id="mailbox-searchform" placeholder="'.LAN_SEARCH.'">
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
		    <button type="button" class="btn btn-default btn-sm checkbox-toggle">'.e107::getParser()->toGlyph("square-o").'
		    </button>
		    <div class="btn-group">
				<button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("trash-o").'</button>
				<button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("floppy-o").'</button>
		    </div>
	   		<!-- /.btn-group -->
 			<button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("refresh").'</button>
		    <div class="pull-right">
		    	1-50/200
		      	<div class="btn-group">
		        	<button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("chevron-left").'</button>
		        	<button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("chevron-right").'</button>
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
					<td class="mailbox-star hidden-xs">{MAILBOX_MESSAGE_STAR}</td>
					<td class="mailbox-name">{SETIMAGE: w=50&h=50&crop=1} {MAILBOX_MESSAGE_AVATAR} {MAILBOX_MESSAGE_FROMTO}</td>
					<td class="mailbox-subject">{MAILBOX_MESSAGE_SUBJECT}</td>
					<td class="mailbox-attachment hidden-xs">{MAILBOX_MESSAGE_ATTACHMENT}</td>
					<td class="mailbox-date">{MAILBOX_MESSAGE_DATESTAMP=relative}</td>
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
			<button type="button" class="btn btn-default btn-sm checkbox-toggle">'.e107::getParser()->toGlyph("square-o").'</button>
		    <div class="btn-group">
				<button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("trash-o").'</button>
				<button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("floppy-o").'</button>
		    </div>
			<!-- /.btn-group -->
			<button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("refresh").'</button>	
			<div class="pull-right">
	  		1-50/200
	      		<div class="btn-group">
			        <button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("chevron-left").'</button>
			        <button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("chevron-right").'</button>
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
	<a href="{MAILBOX_COMPOSELINK}" class="btn btn-primary btn-block">'.LAN_MAILBOX_COMPOSE.'</a>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">'.LAN_MAILBOX_MAILBOXES.'</div>
	<div class="panel-body">
		<ul class="nav nav-pills nav-stacked mailbox-nav">
        	<li {MAILBOX_BOXLINK_ACTIVE=inbox}>
        		<a href="{MAILBOX_BOXLINK=inbox}">{MAILBOX_BOXGLYPH=inbox} '.LAN_MAILBOX_INBOX.' {MAILBOX_BOXCOUNT=inbox}</a> 
           	</li>
           	<li {MAILBOX_BOXLINK_ACTIVE=outbox}>
           		<a href="{MAILBOX_BOXLINK=outbox}">{MAILBOX_BOXGLYPH=outbox} '.LAN_MAILBOX_OUTBOX.' {MAILBOX_BOXCOUNT=outbox}</a>
           	</li>
           	<li {MAILBOX_BOXLINK_ACTIVE=draftbox}>
           		<a href="{MAILBOX_BOXLINK=draftbox}">{MAILBOX_BOXGLYPH=draftbox} '.LAN_MAILBOX_DRAFTBOX.' {MAILBOX_BOXCOUNT=draftbox}</a>
           	</li>
           	<li {MAILBOX_BOXLINK_ACTIVE=starbox}>
           		<a href="{MAILBOX_BOXLINK=starbox}">{MAILBOX_BOXGLYPH=starbox} '.LAN_MAILBOX_STARBOX.' {MAILBOX_BOXCOUNT=starbox}</a> 
           	</li>
	        <li {MAILBOX_BOXLINK_ACTIVE=trashbox}>
	        	<a href="{MAILBOX_BOXLINK=trashbox}">{MAILBOX_BOXGLYPH=trashbox} '.LAN_MAILBOX_TRASHBOX.' {MAILBOX_BOXCOUNT=starbox}</a>
	        </li>
      	</ul>
 	</div>
	<!-- /.panel-body -->
</div>
<!-- /. panel --> 
';

$MAILBOX_TEMPLATE['compose_message'] = '
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">'.LAN_MAILBOX_COMPOSE.'</h3>
	</div>
	<!-- /.panel-heading -->
	
	<div class="panel-body">
	<form method="post">
		<div class="form-group">{MAILBOX_COMPOSE_TO}</div>
	    <div class="form-group">{MAILBOX_COMPOSE_SUBJECT}</div>
	    <div class="form-group">{MAILBOX_COMPOSE_CONTENT}</div>
            
        <div class="form-group">
			<div class="btn btn-default btn-file">
            	'.e107::getParser()->toGlyph("paperclip").' Attachment
            </div>
            <p class="help-block">Max. 32MB</p>
        </div>
    </div>
    <!-- /.panel-body -->

    <div class="panel-footer">
    	<div class="pull-right">
        	<button name="compose" type="button" class="btn btn-default" value="draft">'.e107::getParser()->toGlyph("floppy-o").' Draft</button>
        	<button name="compose" type="submit" class="btn btn-primary" value="send">'.e107::getParser()->toGlyph("envelope-o").' Send</button>
      	</div>
      	<button name="compose" type="reset" class="btn btn-default" value="discard">'.e107::getParser()->toGlyph("times").' Discard</button>
    </div>
    <!-- /.panel-footer -->
    </form>
</div>
<!-- /. panel -->
';

$MAILBOX_TEMPLATE['read_message'] = '
<div class="panel panel-primary">
	<div class="panel-heading clearfix">
		<div class="row">
			<div class="col-md-8">
				<h2 class="panel-title pull-left mailbox-title">{MAILBOX_MESSAGE_SUBJECT}</h3>
			</div>
			<!-- /.col-md-8 -->
			<div class="col-md-4">
				<div class="pull-right">
		      		<div class="btn-group">
				        <button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("chevron-left").'</button>
				        <button type="button" class="btn btn-default btn-sm">'.e107::getParser()->toGlyph("chevron-right").'</button>
				    </div>
		      		<!-- /.btn-group -->
		   		</div>
		    	<!-- /.pull-right -->
			</div>
			<!-- /.col-md-4 -->
		</div>
		<!-- /.row -->
	</div>
	<!-- /.panel-heading -->
	
	<div class="panel-body">
		<div class="mailbox-read-info">
			<div class="row">
				<div class="col-md-6">
					{SETIMAGE: w=50&h=50&crop=1} {MAILBOX_MESSAGE_AVATAR} {MAILBOX_MESSAGE_FROMTO}
				</div>
				<!-- /.col-md-6 -->

				<div class="col-md-6">
					<span class="mailbox-read-time pull-right">{MAILBOX_MESSAGE_DATESTAMP=long}</span> <br />
					<div class="mailbox-controls pull-right">
	                	<div class="btn-group">
	                  		<button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Delete">
	                    		<i class="fa fa-trash-o"></i></button>
	                  		<button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Reply">
	                    		<i class="fa fa-reply"></i></button>
	                  		<button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Forward">
	                    		<i class="fa fa-share"></i></button>
	                	</div>
	                	<!-- /.btn-group -->
	                	<button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" title="Print">
	                  	<i class="fa fa-print"></i></button>
	              	</div>
	              	<!-- /.mailbox-controls -->
				</div>
				<!-- /.col-md-6 -->
			</div>
			<!-- /.row -->
		</div>
		<!-- /.mailbox-read-info -->

		<div class="mailbox-read-message">	
			{MAILBOX_MESSAGE_TEXT}
		</div>
		<!-- /.mailbox-read-message -->
    </div>
    <!-- /.panel-body -->

    <div class="panel-footer">
		<div class="pull-right">
			<button type="button" class="btn btn-default"><i class="fa fa-reply"></i> Reply</button>
			<button type="button" class="btn btn-default"><i class="fa fa-share"></i> Forward</button>
		</div>
		<button type="button" class="btn btn-default"><i class="fa fa-trash-o"></i> Delete</button>
		<button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button>
	</div>	
    <!-- /.panel-footer -->
</div>
<!-- /. panel -->
';