<?php
/*
 * Mailbox 
 *
 * Copyright (C) 2021 - Tijn Kuyper (Moc)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
*/

if (!defined('e107_INIT')) { exit; }

class mailbox_shortcodes extends e_shortcode
{
   function sc_mailbox_boxcount($parm = '')
   {
      require_once(e_PLUGIN."mailbox/mailbox_class.php");
      $mailbox_class = new Mailbox;

      // Filter options (all | unread)
      if(empty($parm['filter']))
      {
         $parm['filter'] = 'all';
      }

      // Format (full | countonly)
      if(empty($parm['format']))
      {
         $parm['format'] = 'full';
      }

      // Default to current mailbox
      if(empty($parm['box']))
      {
         $parm['box'] = $mailbox_class->get_current_mailbox();
      }

      $args  = $mailbox_class->get_database_queryargs($parm['box'], $parm['filter']);
      $count = e107::getDb()->count('mailbox_messages', '(*)', $args);

      // Format 'countonly' returns only the integer (number)
      if($parm['format'] == 'countonly')
      {
         return $count; 
      }

      // If there are no unread messages, and fitler is set to unread, display nothing
      if(!$count && $parm['filter'] == 'unread')
      {
         return '';
      }

      // Default returns styling 
      return '<span class="label label-primary float-right">'.$count.'</span>';
   }

   function sc_mailbox_boxglyph($parm = '')
   {
      if(!$parm) { $parm = 'inbox'; }

      switch($parm)
      {
         case 'inbox':
         default:
            $glyph = e107::getParser()->toGlyph('fa-inbox');
            break;
         case 'outbox':
            $glyph = e107::getParser()->toGlyph('fa-envelope-o');
            break;
         case 'draftbox':
            $glyph = e107::getParser()->toGlyph("fa-pencil-square-o");
            break;
         case 'starbox':
            $glyph = e107::getParser()->toGlyph("fa-star");
            break;
         case 'trashbox':
            $glyph = e107::getParser()->toGlyph("fa-trash-o");
            break;
      }

      return $glyph;
   }

   function sc_mailbox_boxlink($parm = '')
   {
      if(!$parm) { $parm = 'inbox'; }

      $urlparms = array(
         'boxname' => $parm,
      );

      $url = e107::url('mailbox', 'box', $urlparms);

      return $url;
   }

   function sc_mailbox_boxlink_active($parm = '')
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

   function sc_mailbox_boxtitle($parm = '')
   {

      require_once(e_PLUGIN."mailbox/mailbox_class.php");
      $mailbox_class = new Mailbox;

      return $mailbox_class->get_pagetitle(e107::getParser()->filter($_GET['page']));
   }

   function sc_mailbox_composelink($parm = '')
   {
      $url = e107::url('mailbox', 'compose');
      return $url;
   }

   function sc_mailbox_message_id($parm = '')
   {
      return $this->var['message_id'];
   }

   function sc_mailbox_message_star($parm = '')
   {
      // Distinguish between draft or inbox 
      $column = (e107::getParser()->filter($_GET['page']) == 'draftbox') ? 'message_from_starred' : 'message_to_starred';

      // If a draft is starred, starbox should also use message_from_starred
      //print_a($column);
      if($this->var['message_draft'] != '0')
      {
         $column = 'message_from_starred';
      }

      if($this->var[$column])
      {
         return '<span data-mailbox-action="star" data-mailbox-starid="'.$this->var['message_id'].'">'.e107::getParser()->toGlyph("fa-star").'</span>';
      }
      else
      {
         return '<span data-mailbox-action="star" data-mailbox-starid="'.$this->var['message_id'].'">'.e107::getParser()->toGlyph("fa-star-o").'</span>';
      }
   }

   function sc_mailbox_message_avatar($parm = '')
   {
      $PREFERENCE = 2; // MAILBOXPREF TODO

      $options = array(); 

      if($parm['shape'])
      {
         $options['shape'] =  $parm['shape'];
      }

      //print_a($options);

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
               // Decrease size for multiple avatars
               $options['w'] = '20'; 
               $options['h'] = '20'; 

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
                     //$avatars .= "..."; // Add text after ommitting other avatars
                     break;
                  }

                  $avatars .= e107::getParser()->toAvatar($userinfo, $options);
               }

               return $avatars;
            }

            $userinfo = e107::user($this->var['message_to']);
            break;
      }

      return e107::getParser()->toAvatar($userinfo, $options);
   }

   function sc_mailbox_message_readunread($parm = '')
   {
      //print_a($this->var);

      // Draft messages are always 'unread'
      if($this->var['message_draft'] != '0')
      {
         return 'read';
      }

      // Check if read has a datestamp
      if($this->var['message_read'] == 0)
      {
         return 'unread';
      }
      else
      {
         return 'read';
      }

   }


   function sc_mailbox_message_fromto($parm = '')
   {
      $PREFERENCE = 2; // MAILBOXPREF TODO

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
            // Check for multiple recipients
            if(strrpos($this->var['message_to'], ','))
            {
               $recipients = explode(',', $this->var['message_to']);
               $output = '';
               $output_array = array();

               $max = $PREFERENCE +1; // Set maximum depending on preference
               $count = 0;

               //print_a("Count: ".count($recipients));
               //print_a("Max: ".$max);

               foreach($recipients as $recipient)
               {
                  $count++;

                  $userinfo = e107::user($recipient);
                  $profile_link = e107::getUrl()->create('user/profile/view', array('id' => $userinfo['user_id'], 'name' => $userinfo['user_name']));
                  $output = "<a href='".$profile_link."'>".$userinfo['user_name']."</a>";

               
                  // Check for maximum amount of recipients
                  if ($count >= $max)
                  {
                     $output = "..."; // Add text after ommitting other recipients
                     //break;
                     array_push($output_array, $output);
                     break;
                                          ;
                  }
                  {
                     array_push($output_array, $output);
                  }

               }
               
               //print_a($output_array);

               return implode(", ", $output_array);
            }

            // Single recipient
            $userinfo = e107::user($this->var['message_to']);
            break;
      }

      $profile_link = e107::getUrl()->create('user/profile/view', array('id' => $userinfo['user_id'], 'name' => $userinfo['user_name']));

      if($parm == 'linkonly')
      {
         return $profile_link;
      }

      if($parm == 'nolink')
      {
         return $userinfo['user_name'];
      }

      return "<a href='".$profile_link."'>".$userinfo['user_name']."</a>";
   }

   function sc_mailbox_message_subject($parm = '')
   {
      // Reading an individual message, does not require a link
      // if(e107::getParser()->filter($_GET['id']))
      // {
      //    return $this->var['message_subject'];
      // }

      
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

      if($parm['type'] == 'url')
      {
        return $url; 
      }

      return $this->var['message_subject']; 
      
   }

   function sc_mailbox_message_text($parm = '')
   {
      return e107::getParser()->toHTML($this->var['message_text'], true);
   }

   function sc_mailbox_message_attachment($parm = '')
   {
      //print_a($this->var);
      if($this->var['message_attachments'])
      {
         return e107::getParser()->toGlyph("fa-paperclip");
      }
      else
      {
         return '';
      }
   }

   function sc_mailbox_message_datestamp($parm = '')
   {
      
      // Default to datestamp from when message was sent 
      $datestamp = $this->var['message_sent']; 
      
      // If it's a draft, datestamp is 'last saved'
      if(e107::getParser()->filter($_GET['page']) == 'draftbox' || $this->var['message_sent'] == '0')
      {
        $datestamp = $this->var['message_draft'];
      }

      // Default parm to 'short'
      if(!$parm) { $parm = 'short'; }
      
      if($parm == 'relative')
      {
         //return $gen->computeLapse($this->var['message_sent'], time(), false, false, 'short');
         e107::getParser()->toDate($datestamp, "relative");
      }

      return e107::getParser()->toDate($datestamp, $parm);
      //$gen->convert_date($this->var['message_sent'], $parm);
   }

   // COMPOSE
   function sc_mailbox_compose_id($parm = '')
   {
      return $this->var['message_id'];
   }

   function sc_mailbox_compose_to($parm = '')
   {
      $userclass = false; // MAILBOXPREF TODO

      // Set options
      $options = array(
         'limit' => 10, // MAILBOXPREFTODO 
         'required' => true,
      );

      // Continue draft, re-fill the already entered recipients
      if($this->var['message_to'])
      {
         $message_to = $this->var['message_to'];
      }

      $text = '<label for="message_to">Recipient(s)</label>'; // TODO LAN
      $text .= e107::getForm()->userpicker('message_to', $message_to, $options);

      // If userclass selection pref is enabled. 
      if($userclass)
      {
         $text .= '<label for="message_to_userclass">Userclass</label>'; // TODO LAN
         $text .= e107::getUserClass()->uc_dropdown('message_to_userclass', e_UC_NOBODY, $args);
      }

      return $text;

   }

   function sc_mailbox_compose_subject($parm = '')
   {
      if($this->var['message_subject'])
      {
         $message_subject = $this->var['message_subject'];
      }

      $text = '<label for="message_subject">Subject</label>';
      return $text.e107::getForm()->text('message_subject', $message_subject, '', array('placeholder' => 'Subject'));
   }

   function sc_mailbox_compose_text($parm = '')
   {
      if($this->var['message_text'])
      {
         $message_text = $this->var['message_text'];
      }

      $text = '<label for="message_text">Message</label>';

      return $text.e107::getForm()->bbarea('message_text', $message_text);
   }

   function sc_mailbox_compose_attachments($parm = '')
   {
      //return e107::getForm()->mediapicker('mailbox_attachments', '', 'media=mailbox_attachments&dropzone=true');
      return'
      <div class="btn btn-default btn-file">
            <i class="fa fa-paperclip"></i> Attachments TODO
      </div>
      <p class="help-block">Max. 32MB</p>
      ';
   }

   function sc_mailbox_quickform($parm = '')
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