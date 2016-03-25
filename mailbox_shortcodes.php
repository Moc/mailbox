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

class mailbox_shortcodes extends e_shortcode
{
   function sc_mailbox_boxcount($parm='')
   {
      require_once(e_PLUGIN."mailbox/mailbox_class.php");
      $mailbox_class = new Mailbox;
      $args  = $mailbox_class->get_database_queryargs($parm);
      $count = e107::getDb()->count('mailbox_messages', '(*)', ''.$args.'');
      
      return '<span class="label label-primary pull-right">'.$count.'</span>'; 
   }

   function sc_mailbox_boxglyph($parm='')
   {
      if(!$parm) { $parm = 'inbox'; }

      switch($parm)
      {
         case 'inbox':
         default:
            $glyph = e107::getParser()->toGlyph('inbox');
            break;
         case 'outbox':
            $glyph = e107::getParser()->toGlyph('envelope-o'); 
            break;
         case 'draftbox':
            $glyph = e107::getParser()->toGlyph("pencil-square-o");
            break;
         case 'starbox':
            $glyph = e107::getParser()->toGlyph("star");
            break;
         case 'trashbox':
            $glyph = e107::getParser()->toGlyph("trash-o");
            break;
      }

      return $glyph; 
   } 

   function sc_mailbox_boxlink($parm='')
   {
      if(!$parm) { $parm = 'inbox'; }

      $urlparms = array(
         'boxname' => $parm,
      );

      $url = e107::url('mailbox', 'box', $urlparms);
 
      return $url;
   } 

   function sc_mailbox_boxtitle($parm='')
   {
      switch($_GET['page'])
      {
         case 'inbox':
         default:
            $title = LAN_MAILBOX_INBOX;
            break;
         case 'outbox':
            $title = LAN_MAILBOX_OUTBOX;
            break;
         case 'draftbox':
            $title = LAN_MAILBOX_DRAFTBOX;
            break;
         case 'starbox':
            $title = LAN_MAILBOX_STARBOX;
            break;
         case 'trashbox':
            $title = LAN_MAILBOX_TRASHBOX;
            break;
      }
      
      return $title; 
   }

   function sc_mailbox_composelink($parm='')
   {
      $url = e107::url('mailbox', 'compose'); 
      return $url;
   } 

   function sc_mailbox_message_star($parm='')
   {
      if($this->var['message_to_starred'])
      {
         return '<a href="#">'.e107::getParser()->toGlyph("star").'</a>';
      }
      else
      {
         return '<a href="#">'.e107::getParser()->toGlyph("star-o").'</a>';
      }
   }

   function sc_mailbox_message_avatar($parm='')
   {
      switch($_GET['page'])
      {
         case 'inbox':
         case 'starbox':
         case 'trashbox':
         default:
            $userinfo = e107::user($this->var['message_from']);
            break;
         case 'outbox':
         case 'draftbox':
            $userinfo = e107::user($this->var['message_to']);
            break;
      } 

      return e107::getParser()->toAvatar($userinfo); 
   }

   function sc_mailbox_message_fromto($parm='')
   {
      switch($_GET['page'])
      {
         case 'inbox':
         case 'starbox':
         case 'trashbox':
         default:
            $userinfo = e107::user($this->var['message_from']);
            break;
         case 'outbox':
         case 'draftbox':
            $userinfo = e107::user($this->var['message_to']);
            break;
      } 

      $profile_link = e107::getUrl()->create('user/profile/view', array('id' => $userinfo['user_id'], 'name' => $userinfo['user_name']));
      return "<a href='".$profile_link."'>".$userinfo['user_name']."</a>"; 
   }

   function sc_mailbox_message_subject($parm='')
   {
      // Check for either mailboxes section or reading an individual message 
      if($_GET['page'])
      {
         $urlparms = array(
            'id' => $this->var['message_id'], 
         );

         $url = e107::url('mailbox', 'read', $urlparms);

         return "<a href='".$url."'>".$this->var['message_subject']."</a>";   
      }
      // Reading individual plugin
      else
      {
         return $this->var['message_subject'];
      }
   }

   function sc_mailbox_message_text($parm='')
   {
      return e107::getParser()->toHTML($this->var['message_text']);
   } 

   function sc_mailbox_message_attachment($parm='')
   {
      if($this->var['message_attachment'])
      {
         return '<a href="#">'.e107::getParser()->toGlyph("paperclip").'</a>';
      }
      else
      {
         return '';
      }
   }

   function sc_mailbox_message_datestamp($parm='')
   {
      // No need for a date when message is not send yet or when it is a draft message
      if($_GET['page'] == 'draftbox'){ return; }
      if($this->var['message_draft']) { return; }
            
      $gen = e107::getDateConvert();
      if(!$parm) { $parm = 'short'; }
      
      if($parm == 'relative')
      {
         return $gen->computeLapse($this->var['message_sent'], time(), false, false, 'short');    
      }
      
      return $gen->convert_date($this->var['message_sent'], $parm);
   }

   // COMPOSE
   function sc_mailbox_compose_to($parm='')
   {
      $userpicker_options = 
      array(
          'selectize' => 
             array(
                 'create'  => false,
                 'maxItems'   => 10,
                 'mode'       => 'multi',
             ),
          'placeholder' => 'To',
      );

      return e107::getForm()->userpicker('message_to', 'to_id', '', '', $userpicker_options);
   }

   function sc_mailbox_compose_subject($parm='')
   { 
      return e107::getForm()->text('message_subject', $subject, '', array('placeholder' => 'Subject'));
   }

   function sc_mailbox_compose_content($parm='')
   {
      return e107::getForm()->bbarea('message_content', $message_content);
   }
}