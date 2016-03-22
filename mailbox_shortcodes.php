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
   function sc_mailbox_boxlink($parm='')
   {
      if(!$parm) { $parm = 'inbox'; }
      //print_a($parm);

      $urlparms = array(
         'boxname' => $parm,
      );
      //print_a($urlparms);

      $url = e107::url('mailbox', 'box', $urlparms);
      //print_a($url);

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

   // BOX
   function sc_mailbox_box_star($parm='')
   {
      if($this->var['message_starred'])
      {
         return '<a href="#">'.e107::getParser()->toGlyph("star").'</a>';
      }
      else
      {
         return '<a href="#">'.e107::getParser()->toGlyph("star-o").'</a>';
      }
   }

   function sc_mailbox_box_avatar($parm='')
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

   function sc_mailbox_box_fromto($parm='')
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

   function sc_mailbox_box_subject($parm='')
   {
      $urlparms = array(
         'id' => $this->var['message_id'], 
      );

      $url = e107::url('mailbox', 'read', $urlparms);

      return "<a href='".$url."'>".$this->var['message_subject']."</a>"; 
   }

   function sc_mailbox_box_attachment($parm='')
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

   function sc_mailbox_box_datestamp($parm='')
   {
      // No need for a date when message is not send yet
      if($_GET['page'] == 'draftbox'){ return; }
      
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

      return e107::getForm()->userpicker('to', 'to_id', '', '', $userpicker_options);
   }

   function sc_mailbox_compose_subject($parm='')
   { 
      return e107::getForm()->text('subject', $subject, '', array('placeholder' => 'Subject'));
   }

   function sc_mailbox_compose_content($parm='')
   {
      return e107::getForm()->bbarea('message_content', $message_content);
   }
}