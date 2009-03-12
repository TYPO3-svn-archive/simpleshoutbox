<?php

########################################################################
# Extension Manager/Repository config file for ext: "simpleshoutbox"
#
# Auto generated 12-03-2009 00:57
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Simple AJAX Shoutbox',
	'description' => 'A very simple ajax shoutbox. It is only usable for logged in front-end users, but works also with disabled javascript.',
	'category' => 'plugin',
	'author' => 'Peter Schuster',
	'author_email' => 'typo3@peschuster.de',
	'shy' => '',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'smilie' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:18:{s:9:"ChangeLog";s:4:"d6c2";s:32:"class.tx_simpleshoutbox_ajax.php";s:4:"efc9";s:31:"class.tx_simpleshoutbox_api.php";s:4:"562b";s:12:"ext_icon.gif";s:4:"0803";s:17:"ext_localconf.php";s:4:"08ae";s:14:"ext_tables.php";s:4:"54b3";s:14:"ext_tables.sql";s:4:"3cca";s:35:"icon_tx_simpleshoutbox_messages.gif";s:4:"475a";s:16:"locallang_db.xml";s:4:"50a7";s:22:"res/simpleshoutbox.css";s:4:"59c5";s:21:"res/simpleshoutbox.js";s:4:"2a1e";s:17:"res/template.html";s:4:"6290";s:40:"hooks/class.tx_simpleshoutbox_smilie.php";s:4:"2565";s:35:"pi1/class.tx_simpleshoutbox_pi1.php";s:4:"6419";s:17:"pi1/locallang.xml";s:4:"ee74";s:24:"pi1/static/editorcfg.txt";s:4:"094d";s:20:"pi1/static/setup.txt";s:4:"6d25";s:14:"doc/manual.sxw";s:4:"8f0a";}',
	'suggests' => array(
	),
);

?>