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
require_once(t3lib_extMgm::extPath('simpleshoutbox').'class.tx_simpleshoutbox_api.php');

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
	var $ts;				//TimeStamp
	
	/**
	 * Initiates configuration variables
	 *
	 * @param	array	$conf: Configuration Array
	 */
	function init($conf) {
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!

		$this->ts = mktime();
				
		$temp_tsfe = get_object_vars($GLOBALS["TSFE"] -> fe_user);
		$this->conf['user']['uid'] = $temp_tsfe['user']['uid'];
		$this->conf['user']['username'] = $temp_tsfe['user']['username'];
		
		if (empty($this->conf['template'])) {
			$this->conf['template'] = 'EXT:simpleshoutbox/res/tmpl.tmpl';
		}		
		$this->templateCode = $this->cObj->fileResource($this->conf['template']);
		
		$GLOBALS['TSFE']->additionalHeaderData['tx_simpleshoutbox_js'] = '	<script type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/simpleshoutbox.php"></script>';
		$GLOBALS['TSFE']->additionalHeaderData['tx_simpleshoutbox_conf'] = '	<script type="text/javascript">var conf = \''.serialize($this->conf).'\';</script>';
		$GLOBALS['TSFE']->additionalHeaderData['prototype_js'] = '	<script src="typo3/contrib/prototype/prototype.js" type="text/javascript"></script>';
		
		$this->api = t3lib_div::makeInstance('tx_simpleshoutbox_api');
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
		
		$actionLink = $this->cObj->typoLink_URL(array(
			'parameter' => $GLOBALS['TSFE']->id,
			'addQueryString' => 1,
			'addQueryString.' => array(
				'exclude' => 'cHash,no_cache',
			),
			'additionalParams' => '&no_cache=1',
			'useCacheHash' => false,
		));
		
		$markers = array(
			'###ACTION_LINK###' => $actionLink,
			'###JS_LINK###' => 'txShoutBoxSendForm(); return false;',
			'###L_MESSAGE###' => $this->pi_getLL('L_MESSAGE'),
			'###L_SUBMIT###' => $this->pi_getLL('L_SUBMIT'),
		);
		
		$content = $this->cObj->substituteMarkerArray($template, $markers);
		$content .= t3lib_div::wrapJS('txShoutBoxStartPeriodicalUpdate(20);');
		return $content;
	}
	
	/**
	 * Generates output of plugin
	 *
	 * @return	string	content to be presented on website
	 */
	function generateOutput() {
		if ($this->conf['user']['uid']) { 
			$content = $this->api->messages();
			$content .= "\n".$this->form();
		} else {
			$content = $this->pi_getLL('error_login');
		}
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/pi1/class.tx_simpleshoutbox_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/pi1/class.tx_simpleshoutbox_pi1.php']);
}

?>