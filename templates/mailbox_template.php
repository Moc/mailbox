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
					<td class="mailbox-star hidden-xs">{MAILBOX_BOX_STAR}</td>
					<td class="mailbox-name">{SETIMAGE: w=50&h=50&crop=1} {MAILBOX_BOX_AVATAR} {MAILBOX_BOX_FROM}</td>
					<td class="mailbox-subject">{MAILBOX_BOX_SUBJECT}</td>
					<td class="mailbox-attachment hidden-xs">{MAILBOX_BOX_ATTACHMENT}</td>
					<td class="mailbox-date">{MAILBOX_BOX_DATESTAMP=relative}</td>
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
	<div class="panel-heading">Folders</div>
		<div class="panel-body">
 		<ul class="nav nav-pills nav-stacked mailbox-nav">
	        <li class="active"><a href="{MAILBOX_BOXLINK=inbox}">
	        	'.e107::getParser()->toGlyph("inbox").' '.LAN_MAILBOX_INBOX.' 
	        	<span class="label label-primary pull-right">12</span></a>
	        </li>
	        <li><a href="{MAILBOX_BOXLINK=outbox}">
	        	'.e107::getParser()->toGlyph("envelope-o").' Outbox</a>
	        </li>
	        <li><a href="{MAILBOX_BOXLINK=draftbox}">
	        	'.e107::getParser()->toGlyph("pencil-square-o").' Drafts</a>
	        </li>
	        <li><a href="{MAILBOX_BOXLINK=starbox}">
	        '.e107::getParser()->toGlyph("star").' Starred 
	        <span class="label label-warning pull-right">65</span></a>
	        </li>
    		<li><a href="{MAILBOX_BOXLINK=trashbox}">
    			'.e107::getParser()->toGlyph("trash-o").' Trash</a>
    		</li>
  		</ul>
	 </div>
<!-- /.panel-body -->
</div>
<!-- /. panel --> 
';

$MAILBOX_TEMPLATE['compose'] = '
<div class="panel panel-primary">
	<div class="panel-heading">
	  <h3 class="panel-title">'.LAN_MAILBOX_COMPOSE.'</h3>
	</div>
	<!-- /.box-header -->
	
	<div class="panel-body">
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
    <!-- /.box-body -->

    <div class="panel-footer">
    	<div class="pull-right">
        	<button type="button" class="btn btn-default">'.e107::getParser()->toGlyph("floppy-o").' Draft</button>
        	<button type="submit" class="btn btn-primary">'.e107::getParser()->toGlyph("envelope-o").' Send</button>
      	</div>
      	<button type="reset" class="btn btn-default">'.e107::getParser()->toGlyph("times").' Discard</button>
    </div>
    <!-- /.panel-footer -->

</div>
<!-- /. panel -->
';