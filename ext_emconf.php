<?php

########################################################################
# Extension Manager/Repository config file for ext "simpleshoutbox".
#
# Auto generated 27-12-2009 21:39
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
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
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.3.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.3.0-0.0.0',
			'cms' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'smilie' => '1.0.0-0.0.0',
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:9:"ChangeLog";s:4:"5cb9";s:32:"class.tx_simpleshoutbox_ajax.php";s:4:"6c34";s:31:"class.tx_simpleshoutbox_api.php";s:4:"6491";s:16:"ext_autoload.php";s:4:"7d25";s:12:"ext_icon.gif";s:4:"0803";s:17:"ext_localconf.php";s:4:"08ae";s:14:"ext_tables.php";s:4:"4f00";s:14:"ext_tables.sql";s:4:"88ce";s:35:"icon_tx_simpleshoutbox_messages.gif";s:4:"475a";s:16:"locallang_db.xml";s:4:"50a7";s:14:"doc/manual.sxw";s:4:"6ac5";s:40:"hooks/class.tx_simpleshoutbox_smilie.php";s:4:"9552";s:35:"pi1/class.tx_simpleshoutbox_pi1.php";s:4:"26df";s:17:"pi1/locallang.xml";s:4:"ee74";s:24:"pi1/static/editorcfg.txt";s:4:"094d";s:20:"pi1/static/setup.txt";s:4:"d294";s:22:"res/simpleshoutbox.css";s:4:"59c5";s:21:"res/simpleshoutbox.js";s:4:"d862";s:17:"res/template.html";s:4:"c86a";}',
	'suggests' => array(
	),
);

?>