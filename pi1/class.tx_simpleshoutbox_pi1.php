<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Peter Schuster <typo3@peschuster.de>
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
/**
 * pi1/class.tx_simpleshoutbox_pi1.php
 *
 * $Id$
 *
 * @author Peter Schuster <typo3@peschuster.de>
 */

/**
 * Plugin 'Simple Shoutbox' for the 'simpleshoutbox' extension.
 *
 * @author	Peter Schuster <typo3@peschuster.de>
 * @package	TYPO3
 * @subpackage	tx_simpleshoutbox
 */
class tx_simpleshoutbox_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_simpleshoutbox_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_simpleshoutbox_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'simpleshoutbox';	// The extension key.

	/**
	 * Initiates configuration variables
	 *
	 * @param	array	$conf: Configuration Array
	 */
	function init($conf) {
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		if (empty($this->conf['template'])) $this->conf['template'] = 'EXT:simpleshoutbox/res/template.html';
		$this->templateCode = $this->cObj->fileResource($this->conf['template']);
		$this->templateCode = $this->cObj->substituteMarker($this->templateCode, '###SITE_REL_PATH###', t3lib_extMgm::siteRelPath($this->extKey));

		$key = 'tx_simpleshoutbox_' . md5($this->templateCode);
		if (!isset($GLOBALS['TSFE']->additionalHeaderData[$key])) {
			$headerParts = $this->cObj->getSubpart($this->templateCode, '###HEADER_ADDITIONS###');
			if ($headerParts) $GLOBALS['TSFE']->additionalHeaderData[$key] = $headerParts;
		}

		$GLOBALS['TSFE']->additionalHeaderData['prototype_js'] = '	<script src="typo3/contrib/prototype/prototype.js" type="text/javascript"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['tx_simpleshoutbox_js'] = '	<script type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/simpleshoutbox.js"></script>';

		$this->api = t3lib_div::makeInstance('tx_simpleshoutbox_api');
		$this->conf['pageId'] = $GLOBALS['TSFE']->id;
		$this->api->init($this->conf, $this->piVars);
	}

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->init($conf);

		if ($this->piVars['submit']) {
			$this->api->doSubmit();
		}

		$content = $this->generateOutput();

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Returns message form
	 *
	 * @return	string	message form
	 */
	function form() {
		$template = $this->cObj->getSubpart($this->templateCode, '###FORM###');

		$markers = array(
			'###ACTION_LINK###' => t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'),
			'###JS_LINK###' => 'txSimpleShoutbox.sendForm(); return false;',
			'###L_MESSAGE###' => $this->pi_getLL('L_MESSAGE'),
			'###L_SUBMIT###' => $this->pi_getLL('L_SUBMIT'),
		);

		$content = $this->cObj->substituteMarkerArray($template, $markers);
		return $content;
	}

	/**
	 * Generates output of plugin
	 *
	 * @return	string	content to be presented on website
	 */
	function generateOutput() {
		$content = $this->api->messages();
		if ($GLOBALS['TSFE']->loginUser) {
			$content .= "\n".$this->form();
		} else {
			$content .= $this->pi_getLL('error_login');
		}
		$content .= t3lib_div::wrapJS('txSimpleShoutbox.init(); txSimpleShoutbox.lastUid = \'' . $this->api->lastUid . '\'; txSimpleShoutbox.pageId = \'' . $GLOBALS['TSFE']->id . '\'; txSimpleShoutbox.startPeriodicalUpdate(30);');
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/pi1/class.tx_simpleshoutbox_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/pi1/class.tx_simpleshoutbox_pi1.php']);
}

?>