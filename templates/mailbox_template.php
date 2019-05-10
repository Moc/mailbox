<?php
/*
 * Mailbox - an e107 plugin by Tijn Kuyper
 *
 * Copyright (C) 2019-2020 Tijn Kuyper (http://www.tijnkuyper.nl)
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
			<form name="search-messages" method="post" action="index." class="float-right mail-search">
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Search message(s)">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

			<h2>{MAILBOX_BOXTITLE} ({MAILBOX_BOXCOUNT: format=countonly})</h2>
            
                <div class="mail-tools m-t-md">
                    <div class="btn-group float-right">
                        <button class="btn btn-white btn-sm"><i class="fa fa-arrow-left"></i></button>
                        <button class="btn btn-white btn-sm"><i class="fa fa-arrow-right"></i></button>
                    </div>
                    <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="left" data-mailbox-action="refresh" title="Refresh inbox"><i class="fa fa-refresh"></i> Refresh</button>
                    <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" data-mailbox-action="readunread" title="Mark as read/unread"><i class="fa fa-eye"></i> </button>
                    <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" data-mailbox-action="star" title="Mark with star"><i class="fa fa-star"></i> </button>
                    <button class="btn btn-white btn-sm" data-toggle="tooltip" data-placement="top" data-mailbox-action="trash" title="Move to trash"><i class="fa fa-trash-o"></i> </button>
                </div>
            
        </div> 
			
        <div class="mail-box">
			<table id="mailbox-messages" class="table table-hover table-mail">
				<thead>
				    <tr>
				      <th></th>
				      <th class="d-none d-sm-table-cell">{LAN=LAN_MAILBOX_STARRED}</th>
				      <th>{LAN=LAN_MAILBOX_FROMTO}</th>
				      <th class="d-none d-sm-table-cell">{LAN=LAN_CATEGORY}</th>
				      <th>{LAN=LAN_SUBJECT}</th>
				      <th class="d-none d-sm-table-cell">{LAN=LAN_MAILBOX_ATTACHMENT}</th>
				      <th>{LAN=LAN_DATESTAMP}</th>
				      <th class="d-none d-sm-table-cell">{LAN=LAN_MAILBOX_TAGS}</th>
				    </tr>
			  	</thead>
		        <tbody>
';


$MAILBOX_TEMPLATE['tablelist']['messages'] = '
					<tr id="message-{MAILBOX_MESSAGE_ID}" class="{MAILBOX_MESSAGE_READUNREAD}">
			            <td class="check-mail">
			                <input data-token="'.e_TOKEN.'" type="checkbox" name="messages[]" id="{MAILBOX_MESSAGE_ID}" value="{MAILBOX_MESSAGE_READUNREAD}" class="i-checks">
			            </td>
			            <td class="d-none d-sm-table-cell">{MAILBOX_MESSAGE_STAR}</td>
			            <td class="mail-ontact"><a href="mail_detail.html">{SETIMAGE: w=30&h=30&crop=1} {MAILBOX_MESSAGE_AVATAR: shape=circle} {MAILBOX_MESSAGE_FROMTO}</a>  </td>
			            <td class="d-none d-sm-table-cell"><span class="label label-warning">Clients</span></td>
			            <td class="mail-subject"><a href="mail_detail.html">{MAILBOX_MESSAGE_SUBJECT}</a></td>
			            <td class="d-none d-sm-table-cell">{MAILBOX_MESSAGE_ATTACHMENT}</td>
			            <td class="mail-date">{MAILBOX_MESSAGE_DATESTAMP=relative}</td>
			            <td class="d-none d-sm-table-cell"> 
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
		<div class="ibox-content">
';

$MAILBOX_TEMPLATE['box_navigation']['content'] = '
<div class="file-manager">
	<a class="btn btn-block btn-primary compose-mail" href="{MAILBOX_COMPOSELINK}">{LAN=LAN_MAILBOX_COMPOSE}</a>
	<div class="space-25"></div>

    <h5>{LAN=LAN_MAILBOX_MAILBOXES}</h5>

    <ul class="folder-list m-b-md" style="padding: 0">       		
    	<li {MAILBOX_BOXLINK_ACTIVE=inbox}>
    		<a href="{MAILBOX_BOXLINK=inbox}">{MAILBOX_BOXGLYPH=inbox} {LAN=LAN_MAILBOX_INBOX} {MAILBOX_BOXCOUNT: box=inbox}</a>
       	</li>
       	<li {MAILBOX_BOXLINK_ACTIVE=outbox}>
       		<a href="{MAILBOX_BOXLINK=outbox}">{MAILBOX_BOXGLYPH=outbox} {LAN=LAN_MAILBOX_OUTBOX} {MAILBOX_BOXCOUNT: box=outbox}</a>
       	</li>
       	<li {MAILBOX_BOXLINK_ACTIVE=draftbox}>
       		<a href="{MAILBOX_BOXLINK=draftbox}">{MAILBOX_BOXGLYPH=draftbox} {LAN=LAN_MAILBOX_DRAFTBOX} {MAILBOX_BOXCOUNT: box=draftbox}</a>
       	</li>
       	<li {MAILBOX_BOXLINK_ACTIVE=starbox}>
       		<a href="{MAILBOX_BOXLINK=starbox}">{MAILBOX_BOXGLYPH=starbox} {LAN=LAN_MAILBOX_STARBOX} {MAILBOX_BOXCOUNT: box=starbox}</a>
       	</li>
        <li {MAILBOX_BOXLINK_ACTIVE=trashbox}>
        	<a href="{MAILBOX_BOXLINK=trashbox}">{MAILBOX_BOXGLYPH=trashbox} {LAN=LAN_MAILBOX_TRASHBOX} {MAILBOX_BOXCOUNT: box=trashbox}</a>
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
            {MAILBOX_COMPOSE_ATTACHMENTS}
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
<div class="mail-box-header">
    <div class="float-right">
		<button name="reply" type="submit" class="btn btn-primary" title="Reply"><i class="fa fa-reply"></i> Reply</button>
	    <button name="forward" type="submit" class="btn btn-success" title="Forward"><i class="fa fa-share"></i> Forward</button>
		<button name="delete" type="button" class="btn btn-danger" title="Delete"><i class="fa fa-trash-o"></i> Delete</button>
    </div>
    <h2>
        View message: <span class="font-italic">{MAILBOX_MESSAGE_SUBJECT}</span>
    </h2>
    <div class="mail-tools m-t-md">

    </div>
</div>
<div class="mail-box">
	<div class="mail-body">
	<div class="row">
		<div class="col-md-2">

			<div class="widget-head-color-box navy-bg p-lg text-center">
	            <div class="m-b-md">
	                <h2 class="font-bold no-margins">
	                    {MAILBOX_MESSAGE_FROMTO=nolink}
	                </h2>
	            </div>

	           	{SETIMAGE: w=80&h=80&crop=1} <a href="{MAILBOX_MESSAGE_FROMTO=linkonly}">{MAILBOX_MESSAGE_AVATAR: shape=circle}</a>    
	        </div>
	        <div class="widget-text-box">
	            <div class="text-center">
	                <a href="" class="btn btn-xs btn-white"><i class="fa fa-thumbs-up"></i> Like </a>
	                <a href="" class="btn btn-xs btn-primary"><i class="fa fa-reply"></i> Reply</a>
	            </div>
	        </div>

        </div> 
        <div class="col-md-10">  
		    <p>
		       {MAILBOX_MESSAGE_TEXT}
		    </p>
		</div>
	</div>
	</div>
    <div class="mail-attachment">
        <p>
            <span><i class="fa fa-paperclip"></i> 3 attachments - </span>
            <a href="#">Download all</a>
        </p>

        <div class="attachment">
            <div class="file-box">
                <div class="file">
                    <a href="#">
                        <span class="corner"></span>

                        <div class="file-icon">
                            <i class="fa fa-file"></i>
                        </div>
                        <div class="file-name">
                            Document_2019.doc
                            <br/>
                            <small>Added: Jan 11, 2019</small>
                        </div>
                    </a>
                </div>
            </div>
            <div class="file-box">
                <div class="file">
                    <a href="#">
                        <span class="corner"></span>

                        <div class="file-image">
                            <img alt="image" class="img-fluid" src="{e_MEDIA_IMAGE}2019-02/test_image_5.jpg">
                        </div>
                        <div class="file-name">
                            Italy street.jpg
                            <br/>
                            <small>Added: Jan 6, 2019</small>
                        </div>
                    </a>

                </div>
            </div>
            <div class="file-box">
                <div class="file">
                    <a href="#">
                        <span class="corner"></span>

                        <div class="file-image">
                            <img alt="image" class="img-fluid" src="http://www.bureauvijftig.nl/wp-content/uploads/2017/09/test-image-5.jpg">
                        </div>
                        <div class="file-name">
                            My feel.png
                            <br/>
                            <small>Added: Jan 7, 2019</small>
                        </div>
                    </a>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="mail-body text-right">
        <button name="reply" type="submit" class="btn btn-primary" title="Reply"><i class="fa fa-reply"></i> Reply</button>
        <button name="forward" type="submit" class="btn btn-success" title="Forward"><i class="fa fa-share"></i> Forward</button>
        <button name="delete" type="button" class="btn btn-danger" title="Delete"><i class="fa fa-trash-o"></i> Delete</button>
    </div>
    <div class="clearfix"></div>
</div>
';