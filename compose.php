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

$frm = e107::getForm();

require_once(HEADERF);

$userpicker_options = "
array(
    'selectize' => array(
        'create' => false,
        'maxItems' => 10,
        'mode' => 'multi', 
        ),
    ),
";

$text = '

<div class="box box-primary">
	<div class="box-header with-border">
	  <h3 class="box-title">Compose New Message</h3>
	</div>
	<!-- /.box-header -->
	
	<div class="box-body">
		<div class="form-group">
			'.$frm->userpicker('author', 'author_field_id', '', '', $userpicker_options).'
			<input class="form-control" placeholder="To">
		</div>
	    
	    <div class="form-group">
	    	<input class="form-control" placeholder="Subject">
	    </div>
	    
	    <div class="form-group">
	    	'.$frm->bbarea('message_content', $message_content).'
	    </div>
            
        <div class="form-group">
			<div class="btn btn-default btn-file">
            	<i class="fa fa-paperclip"></i> Attachment
            </div>
            <p class="help-block">Max. 32MB</p>
        </div>
    </div>
    <!-- /.box-body -->

    <div class="box-footer">
    	<div class="pull-right">
        	<button type="button" class="btn btn-default"><i class="fa fa-pencil"></i> Draft</button>
        	<button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
      	</div>
      	<button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
    </div>
    <!-- /.box-footer -->

</div>
<!-- /. box -->
';

$ns->tablerender("Compose New Message", $text);
require_once(FOOTERF);
exit;