<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

abstract class Model_Main {

  protected $_aRequest;
  protected $_aSession;
  protected $_aData;
  protected $_iId;
  protected $_oDb;
  public $oPage;

  public function __construct($aRequest = '', $aSession = '', $aFile = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;

    $this->_oDb = new PDO('mysql:host=' . SQL_HOST . ';port=' . SQL_PORT . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
                PDO::ATTR_PERSISTENT => true));
    $this->_oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function create() {

  }

  protected function update() {

  }

  public function destroy() {

  }

	/**
	 * Remove slashes from content for update purposes.
	 *
	 * @access protected
	 * @param array $aRow array with data to update
	 * @return array $aData data witout slashes
	 *
	 */
	protected function _formatForUpdate($aRow) {
		foreach ($aRow as $sColumn => $sData)
			$aData[$sColumn] = Helper::removeSlahes($sData);

		return $aData;
	}

	/**
	 * Format data correctly.
	 *
	 * @access protected
	 * @param array $aRow array with data to format
	 * @return array $aData rebuild data
	 *
	 */
	protected function _formatForOutput($aRow, $sSection) {
		foreach ($aRow as $sColumn => $sData)
			$aData[$sColumn] = is_int($sData) ? (int) $sData : Helper::formatOutput($sData);

		# Format data
		if (isset($aRow['date'])) {
			$aData['date'] = Helper::formatTimestamp($aRow['date'], true);
			$aData['datetime'] = Helper::formatTimestamp($aRow['date']);
			$aData['date_raw'] = (int) $aRow['date'];
			$aData['date_rss'] = date('D, d M Y H:i:s O', $aRow['date']);
			$aData['date_w3c'] = date('Y-m-d\TH:i:sP', $aRow['date']);
		}

		if (isset($aRow['author_id'])) {
			$aGravatar = array('use_gravatar' => $aRow['use_gravatar'], 'email' => $aRow['email']);

			$aData['avatar_64'] = Helper::getAvatar('user', 64, $aRow['author_id'], $aGravatar);
			$aData['avatar_100'] = Helper::getAvatar('user', 100, $aRow['author_id'], $aGravatar);
			$aData['avatar_popup'] = Helper::getAvatar('user', 'popup', $aRow['author_id'], $aGravatar);
		}

		# Build user name
		$aData['full_name'] = trim($aData['name'] . ' ' . $aData['surname']);

		# Encoded data for SEO
		$aData['encoded_full_name'] = urlencode($aData['full_name']);
		$aData['encoded_title'] = urlencode($aRow['title']);

		# URL to entry
		$aData['url_clean'] = WEBSITE_URL . '/' . $sSection . '/' . $aRow['id'];
		$aData['url'] = $aData['url_clean'] . '/' . $aData['encoded_title'];
		$aData['encoded_url'] = urlencode($aData['url']);

    # Do we need to highlight text?
		$sHighlight = isset($this->_aRequest['highlight']) && !empty($this->_aRequest['highlight']) ?
						$this->_aRequest['highlight'] :
						'';

		# Highlight text for search results
		if(!empty($sHighlight)) {
			$aData['title'] = Helper::formatOutput($aData['title'], $sHighlight);
			$aData['teaser'] = Helper::formatOutput($aData['teaser'], $sHighlight);
			$aData['content'] = Helper::formatOutput($aData['content'], $sHighlight);
		}

		return $aData;
	}
}