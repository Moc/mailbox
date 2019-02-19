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

   function sc_mailbox_boxlink_active($parm='')
   {
      // always default back to inbox
      if(!$parm) { $parm = 'inbox'; }

      // Check if current page is the active page
      if(e107::getParser()->filter($_GET['page']) == $parm)
      {
         return 'class="active"';
      }

      return;
   }

   function sc_mailbox_boxtitle($parm='')
   {
      switch(e107::getParser()->filter($_GET['page']))
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
      // Draft messages cannot be starred
      if(e107::getParser()->filter($_GET['page']) == 'draftbox')
      {
         return;
      }

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
      $PREFERENCE = 2; // TODO

      switch(e107::getParser()->filter($_GET['page']))
      {
         case 'inbox':
         case 'starbox':
         case 'trashbox':
         default:
            $userinfo = e107::user($this->var['message_from']);
            break;
         case 'outbox':
         case 'draftbox':
            // Check if there are multiple recipients (outbox, draftbox)
            if(strrpos($this->var['message_to'], ','))
            {
               $options = array(
                  'w' => '20',
                  'h' => '20',
               );

               $recipients = explode(',', $this->var['message_to']);
               $avatars = '';

               $max = $PREFERENCE + 1; // Set maximum depending on preference
               $count = 0;

               foreach($recipients as $recipient)
               {
                  $count++;
                  $userinfo = e107::user($recipient);

                  // Check for maximum amount of avatars
                  if ($count >= $max)
                  {
                     $avatars .= "..."; // Add text after ommitting other avatars
                     break;
                  }

                  $avatars .= e107::getParser()->toAvatar($userinfo, $options);
               }

               return $avatars;
            }

            $userinfo = e107::user($this->var['message_to']);
            break;
      }

      return e107::getParser()->toAvatar($userinfo);
   }

   function sc_mailbox_message_fromto($parm='')
   {
      $PREFERENCE = 2; // TODO

      switch(e107::getParser()->filter($_GET['page']))
      {
         case 'inbox':
         case 'starbox':
         case 'trashbox':
         default:
            $userinfo = e107::user($this->var['message_from']);
            break;
         case 'outbox':
         case 'draftbox':
            if(strrpos($this->var['message_to'], ','))
            {
               $recipients = explode(',', $this->var['message_to']);
               $output = '';

               $max = $PREFERENCE + 1; // Set maximum depending on preference
               $count = 0;

               foreach($recipients as $recipient)
               {
                  $count++;

                  $userinfo = e107::user($recipient);
                  $profile_link = e107::getUrl()->create('user/profile/view', array('id' => $userinfo['user_id'], 'name' => $userinfo['user_name']));
                  $output .= "<a href='".$profile_link."'>".$userinfo['user_name']."</a>, ";

                  // Check for maximum amount of recipients
                  if ($count >= $max)
                  {
                     $output .= "..."; // Add text after ommitting other recipients
                     break;
                  }

               }

               return $output;
            }

            $userinfo = e107::user($this->var['message_to']);
            break;
      }

      $profile_link = e107::getUrl()->create('user/profile/view', array('id' => $userinfo['user_id'], 'name' => $userinfo['user_name']));
      return "<a href='".$profile_link."'>".$userinfo['user_name']."</a>";
   }

   function sc_mailbox_message_subject($parm='')
   {
      // Check for either mailboxes section or reading an individual message
      if(e107::getParser()->filter($_GET['id']))
      {
         // Reading an individual message, does not require a link
         return $this->var['message_subject'];
      }
      // In one of the mailboxes
      else
      {
         $urlparms = array(
            'id' => $this->var['message_id'],
         );

         // Check if draft, because then it requires a link to continue writing the message (compose)
         if(e107::getParser()->filter($_GET['page']) == 'draftbox')
         {
            $url = e107::url('mailbox', 'composeid', $urlparms);
         }
         else
         {
            $url = e107::url('mailbox', 'read', $urlparms);
         }

         return "<a href='".$url."'>".$this->var['message_subject']."</a>";
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
      if(e107::getParser()->filter($_GET['page']) == 'draftbox'){ return; }
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
   function sc_mailbox_compose_id($parm='')
   {
      return $this->var['message_id'];
   }

   function sc_mailbox_compose_to($parm='')
   {
      // Set options
      $options = array(
         'limit' => 10, // TODO: change into preference
      );

      if($this->var['message_to'])
      {
         $message_to = $this->var['message_to'];
      }

      return e107::getForm()->userpicker('message_to', $message_to, $options);
   }

   function sc_mailbox_compose_subject($parm='')
   {
      if($this->var['message_subject'])
      {
         $message_subject = $this->var['message_subject'];
      }

      return e107::getForm()->text('message_subject', $message_subject, '', array('placeholder' => 'Subject'));
   }

   function sc_mailbox_compose_text($parm='')
   {
      if($this->var['message_text'])
      {
         $message_text = $this->var['message_text'];
      }

      return e107::getForm()->bbarea('message_text', $message_text);
   }

   function sc_mailbox_quickform($parm='')
   {
      return '
         <div class="ibox">
            <div class="ibox-content">
                <h3>Private message</h3>

                <p class="small">
                    Send private message to Alex Smith
                </p>

                <div class="form-group">
                    <label>Subject</label>
                    <input type="email" class="form-control" placeholder="Message subject">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea class="form-control" placeholder="Your message" rows="3"></textarea>
                </div>
                <button class="btn btn-primary btn-block">Send</button>

            </div>
        </div>
      ';
   }

}