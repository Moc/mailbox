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

$MAILBOX_TEMPLATE['container']['start'] = '<div class="row">';

$MAILBOX_TEMPLATE['container']['end'] = '</div>';



$MAILBOX_TEMPLATE['tablelist']['start'] = '
	<div class="col-lg-9">          
';

$MAILBOX_TEMPLATE['tablelist']['header'] = '
		<div class="mail-box-header">
			<form method="get" action="index.html" class="float-right mail-search">
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Search email">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-primary">
                            Search
                        </button>
                    </div>
                </div>
            </form>

			<h2>{MAILBOX_BOXTITLE} (#)</h2>

            <div class="mail-tools tooltip-demo m-t-md">
                <div class="btn-group float-right">
                    <button class="btn btn-white btn-sm"><i class="fa fa-arrow-left"></i></button>
                    <button class="btn btn-white btn-sm"><i class="fa fa-arrow-right"></i></button>

                </div>
                <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="left" title="Refresh inbox"><i class="fa fa-refresh"></i> Refresh</button>
                <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Mark as read"><i class="fa fa-eye"></i> </button>
                <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Mark with star"><i class="fa fa-star"></i> </button>
                <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" title="Move to trash"><i class="fa fa-trash-o"></i> </button>

            </div>
        </div> 
			
        <div class="mail-box">
			<table class="table table-hover table-mail">
				<thead>
				    <tr>
				      <th></th>
				      <th>{LAN=LAN_MAILBOX_STARRED}</th>
				      <th>{LAN=LAN_MAILBOX_FROMTO}</th>
				      <th>{LAN=LAN_CATEGORY}</th>
				      <th>{LAN=LAN_SUBJECT}</th>
				      <th>{LAN=LAN_MAILBOX_ATTACHMENT}</th>
				      <th>{LAN=LAN_DATESTAMP}</th>
				      <th>{LAN=LAN_MAILBOX_TAGS}</th>
				    </tr>
			  	</thead>
		        <tbody>
';

/*$MAILBOX_TEMPLATE['tablelist']['messages'] = '
				<tr>
					<td><input type="checkbox"></td>
					<td class="mailbox-star hidden-xs">{MAILBOX_MESSAGE_STAR}</td>
					<td class="mailbox-name">{SETIMAGE: w=40&h=40&crop=1} {MAILBOX_MESSAGE_AVATAR} {MAILBOX_MESSAGE_FROMTO}</td>
					<td class="mailbox-subject">{MAILBOX_MESSAGE_SUBJECT}</td>
					<td class="mailbox-attachment hidden-xs">{MAILBOX_MESSAGE_ATTACHMENT}</td>
					<td class="mailbox-date">{MAILBOX_MESSAGE_DATESTAMP=relative}</td>
				</tr>
';*/

$MAILBOX_TEMPLATE['tablelist']['messages'] = '
					<tr class="{MAILBOX_MESSAGE_READUNREAD}">
			            <td class="check-mail">
			                <input type="checkbox" class="i-checks">
			            </td>
			            <td class="hidden-xs">{MAILBOX_MESSAGE_STAR}</td>
			            <td class="mail-ontact"><a href="mail_detail.html">{SETIMAGE: w=30&h=30&crop=1} {MAILBOX_MESSAGE_AVATAR: shape=circle} {MAILBOX_MESSAGE_FROMTO}</a>  </td>
			            <td class=""><span class="label label-warning">Clients</span></td>
			            <td class="mail-subject"><a href="mail_detail.html">{MAILBOX_MESSAGE_SUBJECT}</a></td>
			            <td class="hidden-xs">{MAILBOX_MESSAGE_ATTACHMENT}</td>
			            <td class="mail-date">{MAILBOX_MESSAGE_DATESTAMP=relative}</td>
			            <td class=""> 
			            	<ul class="tag-list" style="padding: 0">
						        <li><a href=""><i class="fa fa-tag"></i> Family</a></li>
						        <li><a href=""><i class="fa fa-tag"></i> Work</a></li>
						        <li><a href=""><i class="fa fa-tag"></i> Family</a></li>
						        <li><a href=""><i class="fa fa-tag"></i> Work</a></li>
						    </ul>
						</td>
			        </tr>
';

$MAILBOX_TEMPLATE['tablelist']['footer'] = '
				</tbody>
			</table>
		</div>
		<!-- /.mail-box -->
';

$MAILBOX_TEMPLATE['tablelist']['end'] = '
	</div>
	<!-- /.col-lg-9 -->
';

$MAILBOX_TEMPLATE['box_navigation']['start'] = '
<div class="col-lg-3">
    <div class="ibox ">
		<div class="ibox-content mailbox-content">
';

$MAILBOX_TEMPLATE['box_navigation']['content'] = '
<div class="file-manager">
	<a class="btn btn-block btn-primary compose-mail" href="{MAILBOX_COMPOSELINK}">{LAN=LAN_MAILBOX_COMPOSE}</a>
	<div class="space-25"></div>

    <h5>{LAN=LAN_MAILBOX_MAILBOXES}</h5>

    <ul class="folder-list m-b-md" style="padding: 0">       		
    	<li {MAILBOX_BOXLINK_ACTIVE=inbox}>
    		<a href="{MAILBOX_BOXLINK=inbox}">{MAILBOX_BOXGLYPH=inbox} {LAN=LAN_MAILBOX_INBOX} <span class="label label-primary float-right">{MAILBOX_BOXCOUNT=inbox}</span></a>
       	</li>
       	<li {MAILBOX_BOXLINK_ACTIVE=outbox}>
       		<a href="{MAILBOX_BOXLINK=outbox}">{MAILBOX_BOXGLYPH=outbox} {LAN=LAN_MAILBOX_OUTBOX} <span class="label label-primary float-right">{MAILBOX_BOXCOUNT=outbox}</span></a>
       	</li>
       	<li {MAILBOX_BOXLINK_ACTIVE=draftbox}>
       		<a href="{MAILBOX_BOXLINK=draftbox}">{MAILBOX_BOXGLYPH=draftbox} {LAN=LAN_MAILBOX_DRAFTBOX} <span class="label label-primary float-right">{MAILBOX_BOXCOUNT=draftbox}</span></a>
       	</li>
       	<li {MAILBOX_BOXLINK_ACTIVE=starbox}>
       		<a href="{MAILBOX_BOXLINK=starbox}">{MAILBOX_BOXGLYPH=starbox} {LAN=LAN_MAILBOX_STARBOX} <span class="label label-primary float-right">{MAILBOX_BOXCOUNT=starbox}</span></a>
       	</li>
        <li {MAILBOX_BOXLINK_ACTIVE=trashbox}>
        	<a href="{MAILBOX_BOXLINK=trashbox}">{MAILBOX_BOXGLYPH=trashbox} {LAN=LAN_MAILBOX_TRASHBOX} <span class="label label-primary float-right">{MAILBOX_BOXCOUNT=trashbox}</span></a>
        </li>
    </ul>
 
	<h5>{LAN=LAN_CATEGORIES}</h5>
	<ul class="category-list" style="padding: 0">
	    <li><a href="#"> <i class="fa fa-circle text-navy"></i> Work </a></li>
	    <li><a href="#"> <i class="fa fa-circle text-danger"></i> Documents</a></li>
	    <li><a href="#"> <i class="fa fa-circle text-primary"></i> Social</a></li>
	    <li><a href="#"> <i class="fa fa-circle text-info"></i> Advertising</a></li>
	    <li><a href="#"> <i class="fa fa-circle text-warning"></i> Clients</a></li>
	</ul>

    <h5 class="tag-title">{LAN=LAN_MAILBOX_TAGS}</h5>
    <ul class="tag-list" style="padding: 0">
        <li><a href=""><i class="fa fa-tag"></i> Family</a></li>
        <li><a href=""><i class="fa fa-tag"></i> Work</a></li>
        <li><a href=""><i class="fa fa-tag"></i> Home</a></li>
        <li><a href=""><i class="fa fa-tag"></i> Children</a></li>
        <li><a href=""><i class="fa fa-tag"></i> Holidays</a></li>
        <li><a href=""><i class="fa fa-tag"></i> Music</a></li>
        <li><a href=""><i class="fa fa-tag"></i> Photography</a></li>
        <li><a href=""><i class="fa fa-tag"></i> Film</a></li>
    </ul>
    <div class="clearfix"></div>
</div>
';

$MAILBOX_TEMPLATE['box_navigation']['end'] = '
					</div>
                </div>
            </div>
';

$MAILBOX_TEMPLATE['compose_message'] = '

<div class="mail-box-header">
	<form method="post">
	    <div class="float-right">
	    	<button name="compose" type="submit" class="btn btn-primary" value="send"><i class="fa fa-envelope-o"></i> Send</button>
	        <button name="compose" type="submit" class="btn btn-success" value="draft"><i class="fa fa-floppy-o"></i> Save as draft</button>
	        <button name="compose" type="submit" class="btn btn-danger" value="discard"><i class="fa fa-times"></i> Discard</button>
	    </div>

	    <h2>
	        {LAN=LAN_MAILBOX_COMPOSE}
	    </h2>
</div>
<div class="mail-box">
	<div class="mail-body">
		<div class="form-group font-bold">{MAILBOX_COMPOSE_TO}</div>
	    <div class="form-group font-bold">{MAILBOX_COMPOSE_SUBJECT}</div>
	    <div class="form-group font-bold">{MAILBOX_COMPOSE_TEXT}</div>

	    <div class="form-group">
			<div class="btn btn-default btn-file">
	        	<i class="fa fa-paperclip"></i> Attachment
	        </div>
	        <p class="help-block">Max. 32MB</p>
	    </div>

	    <div class="text-right">
	  		<button name="compose" type="submit" class="btn btn-primary" value="send"><i class="fa fa-envelope-o"></i> Send</button>
	  		<button name="compose" type="submit" class="btn btn-success" value="draft"><i class="fa fa-floppy-o"></i> Save as draft</button>
			<button name="compose" type="submit" class="btn btn-danger" value="discard"><i class="fa fa-times"></i> Discard</button>
	    </div>
    </form>
    </div>
</div>

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
				        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
				        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
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
					<div class="mailbox-controls hidden-xs pull-right">
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