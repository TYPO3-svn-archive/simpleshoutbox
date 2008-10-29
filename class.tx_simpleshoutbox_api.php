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
require_once(PATH_tslib.'class.tslib_content.php');
require_once(PATH_t3lib.'class.t3lib_page.php');

/**
	* [DESCRIPTION]
	*
	* $Id:
	*
	* @author		Peter Schuster <typo3@peschuster.de>
	* @package		TYPO3
	* @subpackage 	simpleshoutbox
	*/
class tx_simpleshoutbox_api extends tslib_pibase {
	var $prefixId		= 'tx_simpleshoutbox_api';		// Same as class name
	var $scriptRelPath	= 'class.tx_simpleshoutbox_api.php';	// Path to this script relative to the extension dir.
	var $extKey			= 'simpleshoutbox';	// The extension key.

	function init($conf='', $piVars='') {
		$this->conf = $conf;
		$this->piVars = $piVars;

		$this->where = 'deleted=0 '.$this->conf['where'];

		$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$GLOBALS['TSFE']->tmpl = t3lib_div::makeInstance('t3lib_tstemplate');
			$GLOBALS['TSFE']->tmpl->init();

		if (!$this->cObj) $this->cObj = t3lib_div::makeInstance('tslib_cObj');
		$this->templateCode = $this->cObj->fileResource($this->conf['template']);
	}

/**
	 * Returns wrapped list of shoutbox messages
	 *
	 * @return	string	wrapped list of shoutbox messages
	 */
	function messages($wrap=true) {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,crdate,userid,name,message',
					'tx_simpleshoutbox_messages', $this->where, '', 'crdate DESC', '0,'.$this->conf['limit']);

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
		if (count($rows) == 0 ) {
			$content = $this->pi_getLL('error_nomessages');
		} else {
			$content = '';
			$template = $this->cObj->getSubpart($this->templateCode, '###MESSAGE###');

			foreach ($rows as $row) {
				$markers = array(
					'###USERNAME###' => $this->messages_getUsername($row['userid'],$row['name']),
					'###DATETIME###' => date($this->conf['dateformat'], $row['crdate']),
					'###MESSAGETEXT###' => $this->messages_replaceSmilies(htmlspecialchars($row['message'])),
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
			$content = $this->cObj->getTypoLink(
				$name,
				intval($this->conf['userProfilePID']),
				array($this->conf['userProfileParam'] => $uid)
			);
		} else {
			$content = $name;
		}
		return $content;
	}

	function messages_replaceSmilies($message) {
		$smiliesPath = t3lib_extMgm::siteRelPath($this->extKey).'res/smilies/';
		$smilies = array(
			':-)' => '0.gif',
			';-)' => '2.gif',
			':D' => '1.gif',
			':-D' => '1.gif',
			'8-)' => '7.gif',
		);

		foreach ($smilies as $smilie => $path) {
			$content = '<img alt="'.$smilie.'" title="'.$smilie.'" src="'.$smiliesPath.$path.'" />';
			$message = str_replace($smilie, $content, $message);
		}
		return $message;
	}

	function doSubmit() {
		if ($this->piVars['submit'] && $this->doSubmit_validate()) {

			// Create record
			$record = array(
				'pid' => intval($this->conf['pid']),
				'userid' => $this->conf['user']['uid'],
				'name' => $this->conf['user']['username'],
				'message' => trim($this->piVars['message']),
			);

			// Check for double post
			$double_post_check = md5(implode(',', $record));
			if ($this->conf['preventDuplicatePosts']) {
				list($info) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('COUNT(*) AS t', 'tx_simpleshoutbox_messages',
						'deleted=0 AND crdate>=' . (time() - 60*60) . ' AND doublecheck=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($double_post_check, 'tx_simpleshoutbox_messages'));
			} else {
				$info = array('t' => 0);
			}

			if ($info['t'] > 0) {
				//
			} else {
				// Add rest of the fields
				$record['crdate'] = $record['tstamp'] = time();
				$record['doublecheck'] = $double_post_check;

				// Insert comment record
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_simpleshoutbox_messages', $record);
				$newUid = $GLOBALS['TYPO3_DB']->sql_insert_id();

			}
		}
	}

	function doSubmit_validate() {
		// trim all
		foreach ($this->piVars as $key => $value) {
			$this->piVars[$key] = trim($value);
		}

		if (!$this->piVars['message']) {
			return false;
		}

		return true;
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/class.tx_simpleshoutbox_api.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/class.tx_simpleshoutbox_api.php']);
}

?>