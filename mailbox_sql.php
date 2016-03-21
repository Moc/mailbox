CREATE TABLE mailbox_messages (
  message_id int(10) unsigned NOT NULL auto_increment,
  message_from int(10) unsigned NOT NULL default '0',             /* User ID of sender */
  message_to varchar(250) NOT NULL default '',                    /* User ID of receiver */
  message_draft varchar(250) NOT NULL default '',                 /* Draft status */
  message_sent int(10) unsigned NOT NULL default '0',			        /* Date sent */
  message_read int(10) unsigned NOT NULL default '0',			        /* Date read */
  message_subject text NOT NULL,
  message_text text NOT NULL,
  message_from_saved tinyint(1) unsigned NOT NULL default '0',		/* Set when sender has saved the message */
  message_to_saved tinyint(1) unsigned NOT NULL default '0',		  /* Set when receiver has saved the message */
  message_from_deleted tinyint(1) unsigned NOT NULL default '0',  /* Set when sender has deleted the message */
  message_to_deleted tinyint(1) unsigned NOT NULL default '0',    /* Set when receiver has deleted the message */
  message_attachments text NOT NULL,
  /*message_options varchar(250) NOT NULL default '', */	        /* Options - '+rr' for read receipt - UNDER REVIEW*/
  PRIMARY KEY (message_id)
) ENGINE=MyISAM AUTO_INCREMENT=1;