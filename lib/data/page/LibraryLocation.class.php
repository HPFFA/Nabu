<?php
// sls imports
require_once(SLS_DIR.'lib/data/libary/Library.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/page/location/Location.class.php');

/**
 * LibraryLocation is an implementation of Location for the libary page.
 *
  * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.page.location
 * @category 	Burning Library
 */
class LibraryLocation implements Location {
	public $libaries = null;

	/**
	 * @see Location::cache()
	 */
	public function cache($location, $requestURI, $requestMethod, $match) {}

	/**
	 * @see Location::get()
	 */
	public function get($location, $requestURI, $requestMethod, $match) {
		if ($this->libaries == null) {
			$this->readLibraries();
		}

		$libaryID = $match[1];
		if (!isset($this->libaries[$libaryID]) || !$this->libaries[$libaryID]->getPermission()) {
			return '';
		}

		return WCF::getLanguage()->get($location['locationName'], array('$libary' => '<a href="index.php?page=Library&amp;libaryID='.$this->libaries[$libaryID]->libaryID.SID_ARG_2ND.'">'.WCF::getLanguage()->get(StringUtil::encodeHTML($this->libaries[$libaryID]->title)).'</a>'));
	}

	/**
	 * Gets libaries from cache.
	 */
	protected function readLibraries() {
		$this->libaries = WCF::getCache()->get('libary', 'libaries');
	}
}
?>
