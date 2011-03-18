<?php
// sls imports
require_once(SLS_DIR.'lib/data/library/LibraryList.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows the start page of the archiv.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	page
 * @category 	Story Library System
 */


/*
   Class: IndexPage
   A class that creat the IndexPage
*/
class IndexPage extends AbstractPage {
	public $templateName = 'index';
	public $tags = array();

	/**
	 * @see Page::assignVariables();
	 */
	public function assignVariables() {

		parent::assignVariables();

		$this->renderLibraries();
		if (MODULE_TAGGING && STORY_ENABLE_TAGS && INDEX_ENABLE_TAGS) {
			$this->readTags();
		}
		if (INDEX_ENABLE_STATS) {
			$this->renderStats();
		}

		WCF::getTPL()->assign(array(
			'selfLink' => 'index.php?page=Index'.SID_ARG_2ND_NOT_ENCODED,
			'allowSpidersToIndexThisPage' => true,
			'tags' => $this->tags
		));

		if (WCF::getSession()->spiderID) {
			if ($lastChangeTime = @filemtime(SLS_DIR.'cache/cache.stat.php')) {
				@header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastChangeTime).' GMT');
			}
		}
	}

	/**
	 * Renders the archiv stats on the index page.
	 */
	protected function renderStats() {
		$stats = WCF::getCache()->get('stat');
		WCF::getTPL()->assign('stats', $stats);
	}

	/**
	 * @see LibraryList::renderLibraries()
	 */
	protected function renderLibraries() {
		$libraryList = new LibraryList();
		$libraryList->maxDepth = LIBRARY_LIST_DEPTH;
		$libraryList->renderLibraries();
	}



	/**
	 * Reads the tags of this library.
	 */
	protected function readTags() {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagCloud.class.php');

		// get tags
		$tagCloud = new TagCloud(WCF::getSession()->getVisibleLanguageIDArray());
		$this->tags = $tagCloud->getTags();
	}
}
?>