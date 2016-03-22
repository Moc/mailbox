CREATE TABLE mailbox_messages (
  message_id int(10) unsigned NOT NULL auto_increment,
  message_from int(10) unsigned NOT NULL default '0',             /* User ID of sender */
  message_to int(10) NOT NULL default '0',                        /* User ID of receiver */
  message_draft int(10) NOT NULL default '0',                     /* Draft status */
  message_sent int(10) unsigned NOT NULL default '0',			        /* Datestamp sent */
  message_read int(10) unsigned NOT NULL default '0',			        /* Datestamp read */
  message_subject varchar(255) NOT NULL,          
  message_text text NOT NULL,
  message_from_starred tinyint(1) unsigned NOT NULL default '0',	/* Set when sender has starred the message */
  message_to_starred tinyint(1) unsigned NOT NULL default '0',    /* Set when receiver has starred the message */
  message_from_deleted int(10) unsigned NOT NULL default '0',     /* Datestamp when sender has deleted the message */
  message_to_deleted int(10) unsigned NOT NULL default '0',       /* Datestamp when receiver has deleted the message */
  message_attachments text,
  /*message_options varchar(250) NOT NULL default '', */	        /* Options - '+rr' for read receipt - UNDER REVIEW*/
  PRIMARY KEY (message_id)
) ENGINE=MyISAM AUTO_INCREMENT=1;