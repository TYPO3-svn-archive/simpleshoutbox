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
 * class.tx_simpleshoutbox_ajax.php
 *
 * $Id$
 *
 * @author Peter Schuster <typo3@peschuster.de>
 */

require_once(t3lib_extMgm::extPath('simpleshoutbox').'class.tx_simpleshoutbox_api.php');

/**
 * Class handling AJAX-Requests of tx_simpleshoutbox
 *
 * @author		Peter Schuster <typo3@peschuster.de>
 * @package		TYPO3
 * @subpackage 	simpleshoutbox
 */
class tx_simpleshoutbox_ajax {
	var $prefixId		= 'tx_simpleshoutbox_ajax';		// Same as class name
	var $scriptRelPath	= 'class.tx_simpleshoutbox_ajax.php';	// Path to this script relative to the extension dir.
	var $extKey			= 'simpleshoutbox';	// The extension key.

	/**
	 * Configuration Array
	 *
	 * @var array
	 */
	protected $conf = array();

	/**
	 * tx_simpleshoutbox api
	 *
	 * @var tx_simpleshoutbox_api
	 */
	protected $api;

	/**
	 * Initiates required apis
	 *
	 * @return void
	 */
	protected function init() {
		tslib_eidtools::connectDB();
		$GLOBALS['TSFE']->fe_user = tslib_eidtools::initFeUser();
		if ($GLOBALS['TSFE']->fe_user->user['uid'] > 0) $GLOBALS['TSFE']->loginUser = true;

		$this->conf['where'] = 'AND uid > '.intval(t3lib_div::_GP('lastupdate'));

		$this->piVars['message'] = t3lib_div::_GP('message');

		$this->api = t3lib_div::makeInstance('tx_simpleshoutbox_api');
		$this->api->init($this->conf, $this->piVars);
	}

	/**
	 * Wraps output into xml
	 *
	 * @param string	$messages: messages
	 * @param integer	$messageId: uid of last message
	 * @return string	XML Output
	 */
	protected function xmlWrap($messages, $messageId) {
		$content = '<?xml version="1.0" ?><simpleshoutbox>';
		$content .= '<lastuid>' . $messageId . '</lastuid>';
		$content .= '<messages>' . ($messages ? '<![CDATA[' . $messages . ']]>' : '') . '</messages>';
		$content .= '</simpleshoutbox>';
		return $content;
	}

	/**
	 * Main function of class dispatching request and returning output
	 *
	 * @return string	Output
	 */
	public function main() {
		$this->init();
		if (!t3lib_div::_GP('update')) {
			$this->api->doSubmit();
		}

		$messages = $this->api->messages(false);
		$lastUid = $this->api->lastUid;
		if ($lastUid < intval(t3lib_div::_GP('lastupdate'))) $lastUid = intval(t3lib_div::_GP('lastupdate'));

		$content = $this->xmlWrap($messages, $lastUid);

		header('Content-Type:application/xml');
		return $content;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/class.tx_simpleshoutbox_ajax.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/class.tx_simpleshoutbox_ajax.php']);
}

$SOBE = t3lib_div::makeInstance('tx_simpleshoutbox_ajax');
echo $SOBE->main();

?>