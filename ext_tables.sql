#
# Table structure for table 'tx_simpleshoutbox_messages'
#
CREATE TABLE tx_simpleshoutbox_messages (
	uid int(11) NOT NULL auto_increment,
	tstamp int(11) NOT NULL default '0',
	crdate int(11) NOT NULL default '0',
	deleted tinyint(4) NOT NULL default '0',
	userid int(11) default '0',
	`name` tinytext,
	message tinytext,
	doublecheck tinytext,

	PRIMARY KEY  (uid)
);