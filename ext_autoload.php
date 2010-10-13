<?php
/*
 * Register necessary class names with autoloader
 *
 */
return array(
	'tx_simpleshoutbox_api'		=> t3lib_extMgm::extPath('simpleshoutbox', 'class.tx_simpleshoutbox_api.php'),
	'tx_simpleshoutbox_ajax'	=> t3lib_extMgm::extPath('simpleshoutbox', 'class.tx_simpleshoutbox_ajax.php'),
	'tx_simpleshoutbox_smilie'	=> t3lib_extMgm::extPath('simpleshoutbox', 'hooks/class.tx_simpleshoutbox_smilie.php'),
	'tx_simpleshoutbox_pi1'		=> t3lib_extMgm::extPath('simpleshoutbox', 'pi1/class.tx_simpleshoutbox_pi1.php'),
);
?>