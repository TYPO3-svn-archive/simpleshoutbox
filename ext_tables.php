<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addPlugin(array('LLL:EXT:simpleshoutbox/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Simple Shoutbox");
t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/setup.txt","Simple Shoutbox");
?>