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
 * class.tx_simpleshoutbox_api.php
 *
 * $Id$
 *
 * @author Peter Schuster <typo3@peschuster.de>
 */

require_once(PATH_tslib.'class.tslib_content.php');
require_once(PATH_t3lib.'class.t3lib_page.php');
require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
require_once(PATH_t3lib.'class.t3lib_tsparser_ext.php');

require_once t3lib_extMgm::extPath('lang', 'lang.php');

/**
 * API Class with all common functions for simpleshoutbox extension
 *
 * $Id$
 *
 * @author		Peter Schuster <typo3@peschuster.de>
 * @package		TYPO3
 * @subpackage 	simpleshoutbox
 */
class tx_simpleshoutbox_api {
	var $prefixId		= 'tx_simpleshoutbox_api';		// Same as class name
	var $scriptRelPath	= 'class.tx_simpleshoutbox_api.php';	// Path to this script relative to the extension dir.
	var $extKey			= 'simpleshoutbox';	// The extension key.

	/**
	 * Contains uid of latest message while last read
	 *
	 * @var integer
	 */
	var $lastUid = 0;

	function init($conf=array(), $piVars='') {
		$this->piVars = $piVars;

		$this->conf = array_merge((array)$this->getTS(), $conf);
		$this->where = 'deleted=0 '.$this->conf['where'];

		$this->cObj = t3lib_div::makeInstance('tslib_cObj');

		if (empty($this->conf['template'])) $this->conf['template'] = 'EXT:simpleshoutbox/res/template.html';
		$this->templateCode = $this->cObj->fileResource($this->conf['template']);

		if (!$GLOBALS['LANG']) {
			$GLOBALS['LANG'] = t3lib_div::makeInstance('language');
			$GLOBALS['LANG']->init($GLOBALS['TSFE']->lang);
		}
	}

	/**
	 * Returns TypoScript configuration for plugin
	 *
	 * @return array	config typoscript for tx_simpleshoutbox_pi1
	 */
	function getTS() {
		if (is_array($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_simpleshoutbox_pi1.']))
			return $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_simpleshoutbox_pi1.'];

		$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$GLOBALS['TSFE']->tmpl = t3lib_div::makeInstance('t3lib_tstemplate');
		$GLOBALS['TSFE']->tmpl->init();

		$rootLine = $GLOBALS['TSFE']->sys_page->getRootLine(1);
		$TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
		$TSObj->tt_track = 0;
		$TSObj->init();
		$TSObj->runThroughTemplates($rootLine);
		$TSObj->generateConfig();
		return $TSObj->setup['plugin.']['tx_simpleshoutbox_pi1.'];
	}

	/**
	 * Returns list of shoutbox messages
	 *
	 * @param	boolean		$wrap: if true, messages are wrapped in shoutbox-container
	 * @return	string	list of shoutbox messages
	 */
	function messages($wrap=true) {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_simpleshoutbox_messages', $this->where, '',
						'crdate DESC', '0,'.$this->conf['limit']);
		$messages = $this->messages_getMessages($rows);

		if ($wrap) {
			$template = $this->cObj->getSubpart($this->templateCode, '###LIST###');
			$content = $this->cObj->substituteSubpart($template, '###MESSAGE###', $messages);
		} else {
			$content = $messages;
		}
		return $content;
	}

	/**
	 * Returns list of shoutbox messages
	 *
	 * @param	array	$rows: database result rows (table: tx_simpleshoutbox_messages)
	 * @return	string	shoutbox messages
	 */
	function messages_getMessages(&$rows) {
		if (count($rows) == 0) {
			$content = $GLOBALS['LANG']->sL('LLL:EXT:simpleshoutbox/pi1/locallang.xml:error_nomessages',0);
		} else {
			$content = '';
			$template = $this->cObj->getSubpart($this->templateCode, '###MESSAGE###');

			foreach ($rows as $row) {
				$markers = array(
					'###USERNAME###' => $this->messages_getUsername($row['userid'],$row['name']),
					'###DATETIME###' => date($this->conf['dateformat'], $row['crdate']),
					'###MESSAGETEXT###' => $this->messages_replaceSmilies(htmlspecialchars($row['message'])),
				);

				// Call hook for custom markers
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['simpleshoutbox']['extraMarker'])) {
					foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['simpleshoutbox']['extraMarker'] as $userFunc) {
						$params = array('pObj' => &$this, 'template' => $this->templateCode, 'markers' => $markers, 'row'=>&$row);
						if (is_array($tempMarkers = t3lib_div::callUserFunction($userFunc, $params, $this))) $markers = $tempMarkers;
					}
				}

				$this->lastUid = ($row['uid'] > $this->lastUid ? $row['uid'] : $this->lastUid);
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
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,username','fe_users', 'uid='.$uid);
			$name = $rows[0]['username'];
		}

		$content = $name;
		if ($this->conf['userProfilePID']) {
			$typoLinkConf = array(
					'parameter' => intval($this->conf['userProfilePID']),
					'useCacheHash' => true,
					'additionalParams' => '&'.$this->conf['userProfileParam'].'='.$uid
			);
			$content = $this->cObj->typoLink($name, $typoLinkConf);
		}
		return $content;
	}

	/**
	 * Processes submission of new message
	 *
	 * @return	void
	 */
	function doSubmit() {
		if ($this->doSubmit_validate()) {

			// Create record
			$record = array(
				'userid' => $GLOBALS['TSFE']->fe_user->user['uid'],
				'name' => $GLOBALS['TSFE']->fe_user->user['username'],
				'message' => trim($this->piVars['message']),
			);

			// Call hook for custom data columns
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['simpleshoutbox']['extraColumn'])) {
				foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['simpleshoutbox']['extraColumn'] as $userFunc) {
					$params = array('pObj' => &$this, 'record'=>$record);
					if (is_array($tempRecord = t3lib_div::callUserFunction($userFunc, $params, $this))) $record = $tempRecord;
				}
			}

			// Check for double post
			$double_post_check = md5(implode(',', $record));
			if ($this->conf['preventDuplicatePosts']) {
				list($info) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('COUNT(*) AS count', 'tx_simpleshoutbox_messages',
						'deleted=0 AND crdate>=' . (time() - 60*2) . ' AND doublecheck=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($double_post_check, 'tx_simpleshoutbox_messages'));
			} else {
				$info = array('count' => 0);
			}

			if ($info['count'] = 0) {
				$record['crdate'] = $record['tstamp'] = time();
				$record['doublecheck'] = $double_post_check;

				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_simpleshoutbox_messages', $record);
				$newUid = $GLOBALS['TYPO3_DB']->sql_insert_id();
			}
		}
	}

	/**
	 * Validates input data before submission
	 *
	 * @return boolean		result of validation
	 */
	function doSubmit_validate() {
		foreach ($this->piVars as $key => $value) $this->piVars[$key] = trim($value);
		if (!$this->piVars['message'] || intVal($GLOBALS['TSFE']->fe_user->user['uid']) < 1) return false;
		return true;
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/class.tx_simpleshoutbox_api.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/class.tx_simpleshoutbox_api.php']);
}

?>