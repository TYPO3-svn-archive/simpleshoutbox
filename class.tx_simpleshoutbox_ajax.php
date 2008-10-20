<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Peter Schuster <typo3@peschuster.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');

 /**
  * [DESCRIPTION]
  *
  * @author		Peter Schuster <typo3@peschuster.de>
  * @package		TYPO3
  * @subpackage 	simpleshoutbox
  */
class tx_simpleshoutbox_ajax extends tslib_pibase {
	var $prefixId		= 'tx_simpleshoutbox_ajax';		// Same as class name
	var $scriptRelPath	= 'class.tx_simpleshoutbox_ajax.php';	// Path to this script relative to the extension dir.
	var $extKey			= 'simpleshoutbox';	// The extension key.
	
	
	
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/class.tx_simpleshoutbox_ajax.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/class.tx_simpleshoutbox_ajax.php']);
}

?>