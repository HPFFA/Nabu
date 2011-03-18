<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/TagCloud.class.php');

/**
 * his class holds a list of tags that can be used for creating a tag cloud.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.library
 * @category 	Story Library System
 */
class LibraryTagCloud extends TagCloud {
	/**
	 * Contructs a new LibraryTagCloud.
	 *
	 * @param	integer		$libraryID
	 * @param	array<integer>	$languageIDArray
	 */
	public function __construct($libraryID, $languageIDArray = array()) {
		$this->libraryID = $libraryID;
		$this->languageIDArray = $languageIDArray;
		if (!count($this->languageIDArray)) $this->languageIDArray = array(0);

		// init cache
		$this->cacheName = 'tagCloud-'.$this->libraryID.'-'.implode(',', $this->languageIDArray);
		$this->loadCache();
	}

	/**
	 * Loads the tag cloud cache.
	 */
	public function loadCache() {
		if ($this->tags !== null) return;

		// get cache
		WCF::getCache()->addResource($this->cacheName, SLS_DIR.'cache/cache.tagCloud-'.$this->libraryID.'-'.StringUtil::getHash(implode(',', $this->languageIDArray)).'.php', SLS_DIR.'lib/system/cache/CacheBuilderLibraryTagCloud.class.php', 0, 86400);
		$this->tags = WCF::getCache()->get($this->cacheName);
	}
}
?>