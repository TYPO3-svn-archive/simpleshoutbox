#
# Table structure for table 'tx_simpleshoutbox_messages'
#
CREATE TABLE tx_simpleshoutbox_messages (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	userid tinyint(4) DEFAULT '0' NOT NULL,
	name tinytext DEFAULT '' NOT NULL,
	message tinytext DEFAULT '' NOT NULL,
	doublecheck tinytext DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);