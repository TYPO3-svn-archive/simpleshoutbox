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
		$this->where = 'deleted=0';
		
		if (empty($this->conf['template'])) {
			$this->conf['template'] = 'EXT:simpleshoutbox/res/tmpl.tmpl';
		}		
		$this->templateCode = $this->cObj->fileResource($this->conf['template']);
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
	
		//DEBUG-CONFIG
		$GLOBALS['TYPO3_DB']->debugOutput = true;
		t3lib_div::debug($this->conf,'conf');
		
		
		$content = $this->generateOutput();
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	/**
	 * Returns wrapped list of shoutbox messages
	 *
	 * @return	string	wrapped list of shoutbox messages
	 */
	function messages() {
		$template = $this->cObj->getSubpart($this->templateCode, '###LIST###'); 
		
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,crdate,userid,name,message',
					'tx_simpleshoutbox_messages', $this->where, '', 'crdate DESC', '0,'.$this->conf['limit']);
		
		$content = $this->cObj->substituteSubpart($template, '###MESSAGE###', $this->messages_getMessages($rows));
		return $content;
	}
	
	/**
	 * Returns list of shoutbox messages
	 *
	 * @param	array	$rows: database result rows (table: tx_simpleshoutbox_messages)
	 * @return	string	shoutbox messages
	 */
	function messages_getMessages(&$rows) {
		if (count($rows) == 0 ) {
			$content = $this->pi_getLL('error_nomessages');		
		} else {
			$content = '';
			$template = $this->cObj->getSubpart($this->templateCode, '###MESSAGE###'); 
			
			foreach ($rows as $row) {
				$markers = array(
					'###USERNAME###' => $this->messages_getUsername($row['userid'],$row['name']),
					'###DATETIME###' => date($this->conf['dateformat'], $row['crdate']),
					'###MESSAGETEXT###' => htmlspecialchars($row['message']),
				);
				
				$content .= $this->cObj->substituteMarkerArray($template, $markers);
			}
		}
		return $content;
	}
	
	/**
	 * Returns username w/o link to profile
	 *
	 * @param	integer	$uid: uid of user to be shown
	 * @param	string	$name: name of user to be shown (if blank: username is read form database)
	 * @return	username w/o link to profile
	 */
	function messages_getUsername($uid, $name='') {
		$uid = intval($uid);
		
		if (!$name) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,username',
					'fe_users', 'uid='.$uid);
			if ($rows[0]['username']) {
				$name = $rows[0]['username'];		
			}
		}
		
		if ($this->conf['userProfilePID']) {
			$content = $this->pi_linkToPage(
				$name,
				intval($this->conf['userProfilePID']),'',
				array($this->conf['userProfileParam'] => $uid)
			);
		} else {
			$content = $name;
		}
		return $content;
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
			'###JS_LINK###' => '',
			'###L_MESSAGE###' => $this->pi_getLL('L_MESSAGE'),
			'###L_SUBMIT###' => $this->pi_getLL('L_SUBMIT'),
		);
		
		return $this->cObj->substituteMarkerArray($template, $markers);
	}
	
	/**
	 * Generates output of plugin
	 *
	 * @return	string	content to be presented on website
	 */
	function generateOutput() {
		$content = $this->messages();
		$content .= "\n".$this->form();
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/pi1/class.tx_simpleshoutbox_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/pi1/class.tx_simpleshoutbox_pi1.php']);
}

?>