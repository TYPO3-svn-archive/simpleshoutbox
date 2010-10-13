<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_simpleshoutbox_messages'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:simpleshoutbox/locallang_db.xml:tx_simpleshoutbox_messages',
		'label'     => 'message',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'default_sortby' => 'ORDER BY crdate DESC',
		'delete'	=> 'deleted',
		'iconfile'	=> t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_simpleshoutbox_messages.gif',
		'hideTable'	=> 1,
	),
	'columns' => array(),
);


t3lib_extMgm::addPlugin(array('LLL:EXT:simpleshoutbox/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY . '_pi1'), 'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY, 'pi1/static/', 'Simple Shoutbox');
?>