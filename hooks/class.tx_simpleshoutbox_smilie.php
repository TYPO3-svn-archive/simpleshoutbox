<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Peter Schuster <typo3@peschuster.de>
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
 * hooks/class.tx_simpleshoutbox_smilie.php
 *
 * $Id$
 *
 * @author Peter Schuster <typo3@peschuster.de>
 */

/**
 * Class with function for replacing smilie strings
 * in message body with img-tags
 *
 * @author Peter Schuster <typo3@peschuster.de>
 * @package TYPO3
 * @subpackage simpleshoutbox
 */
class tx_simpleshoutbox_smilie {

	/**
	 * instance of tx_smilie
	 *
	 * @var tx_smilie
	 */
	public $smilie = NULL;

	/**
	 * Calls tx_smilie->replaceSmilies with
	 * $params['markers']['###MESSAGETEXT###'] as paramter
	 * and returns result in new markers array
	 *
	 * @param array $params array of paramters
	 * @return array $params['marker']
	 */
	public function replaceSmilies(&$params) {
		if (t3lib_extMgm::isLoaded('smilie') && class_exists('tx_smilie', TRUE)) {
			if ($this->smilie === NULL) {
				$this->smilie = t3lib_div::makeInstance('tx_smilie');
			}
			$params['markers']['###MESSAGETEXT###'] = $this->smilie->replaceSmilies($params['markers']['###MESSAGETEXT###']);
			return $params['markers'];
		}
		return false;
	}

}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/hooks/class.tx_simpleshoutbox_smilie.php'])	{
	require_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/simpleshoutbox/hooks/class.tx_simpleshoutbox_smilie.php']);
}
?>