<?php
// sls imports
require_once(SLS_DIR.'lib/data/library/Library.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');

/**
 * Shows the library page.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	page
 * @category 	Story Library System
 */

class LibraryPage extends SortablePage {
	// default values
	public $defaultSortField = LIBRARY_DEFAULT_SORT_FIELD;
	public $defaultSortOrder = LIBRARY_DEFAULT_SORT_ORDER;
	public $defaultDaysPrune = LIBRARY_DEFAULT_DAYS_PRUNE;
	public $itemsPerPage = LIBRARY_STORIES_PER_PAGE;

	// library data
	public $libraryID = 0;
	public $library;
	public $enableRating = STORY_ENABLE_RATING;

	// parameters
	public $daysPrune;
	public $status = '';
	public $languageID = 0;

	// system
	public $templateName = 'library';
	public $libraryList = null;
	public $storyList = null;
	public $markedChapters = 0, $markedStories = 0;
	public $normalStoriesStatus = 1;
	public $libraryModerators = array();
	public $tags = array();

	/**
	 * tag id
	 *
	 * @var integer
	 */
	public $tagID = 0;

	/**
	 * tag object
	 *
	 * @var Tag
	 */
	public $tag = null;

	/**
	 * Reads the given parameters.
	 */
	public function readParameters() {
		parent::readParameters();

		// get library id
		if (isset($_REQUEST['libraryID'])) $this->libraryID = intval($_REQUEST['libraryID']);
		else if (isset($_REQUEST['libraryid'])) $this->libraryID = intval($_REQUEST['libraryid']);
		if (isset($_REQUEST['status'])) $this->status = $_REQUEST['status'];
		if (isset($_REQUEST['languageID'])) $this->languageID = intval($_REQUEST['languageID']);
		if (isset($_REQUEST['tagID'])) $this->tagID = intval($_REQUEST['tagID']);

		// get library
		$this->library = new Library($this->libraryID);

		// stories per page
		if ($this->library->storiesPerPage) $this->itemsPerPage = $this->library->storiesPerPage;
		if (WCF::getUser()->storiesPerPage) $this->itemsPerPage = WCF::getUser()->storiesPerPage;

		// enter library
		$this->library->enter();

		// redirect to external url if given
		if ($this->library->isExternalLink()) {
			if (!WCF::getSession()->spiderID) {
				// count redirects
				$sql = "UPDATE	sls".SLS_N."_library
					SET	clicks = clicks + 1
					WHERE	libraryID = ".$this->libraryID;
				WCF::getDB()->registerShutdownUpdate($sql);

				// reset cache
				WCF::getCache()->clearResource('libraryData');
			}

			// do redirect
			HeaderUtil::redirect($this->library->externalURL, false);
			exit;
		}

		// get sorting values
		if ($this->library->sortField) $this->defaultSortField = $this->library->sortField;
		if ($this->library->sortOrder) $this->defaultSortOrder = $this->library->sortOrder;
		if ($this->library->daysPrune) $this->defaultDaysPrune = $this->library->daysPrune;
		if (WCF::getUser()->storyDaysPrune) $this->defaultDaysPrune = WCF::getUser()->storyDaysPrune;

		// story rating
		if ($this->library->enableRating != -1) $this->enableRating = $this->library->enableRating;

		// days prune
		if (isset($_REQUEST['daysPrune'])) $this->daysPrune = intval($_REQUEST['daysPrune']);
		if ($this->daysPrune < 1) $this->daysPrune = $this->defaultDaysPrune;

		// status filter
		if (!empty($this->status)) {
			switch ($this->status) {
				case 'read':
				case 'unread':
				case 'open':
				case 'closed':
				case 'deleted':
				case 'hidden':
				case 'done':
				case 'undone': break;
				default: $this->status = '';
			}
		}

		if ($this->library->isLibrary()) {
			if (MODULE_TAGGING && $this->tagID) {
				require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
				$this->tag = TagEngine::getInstance()->getTagByID($this->tagID);
				if ($this->tag === null) {
					throw new IllegalLinkException();
				}
				require_once(SLS_DIR.'lib/data/story/TaggedLibraryStoryList.class.php');
				$this->storyList = new TaggedLibraryStoryList($this->tagID, $this->library, $this->daysPrune,  $this->status, $this->languageID);
			}
			else {
				require_once(SLS_DIR.'lib/data/story/LibraryStoryList.class.php');
				$this->storyList = new LibraryStoryList($this->library, $this->daysPrune,  $this->status, $this->languageID);
			}
		}
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		// generate list of sublibraries
		$this->renderLibraries();

		// get stories
		if ($this->storyList != null) {
			$this->storyList->limit = $this->itemsPerPage;
			$this->storyList->offset = ($this->pageNo - 1) * $this->itemsPerPage;
			$this->storyList->sqlOrderBy = ($this->sortField != 'ratingResult' ? 'story.' : '').$this->sortField." ".$this->sortOrder.
							(($this->sortField == 'ratingResult') ? (", story.ratings ".$this->sortOrder) : ('')).
							(($this->sortField != 'lastChapterTime') ? (", story.lastChapterTime DESC") : (''));
			$this->storyList->readStories();
		}

		// show moderators
		if (LIBRARY_ENABLE_MODERATORS) {
			$this->renderModerators();
		}

		// update subscription
		if (WCF::getUser()->userID) {
			WCF::getUser()->updateLibrarySubscription($this->libraryID);
		}

		// get marked chapters
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedChapters'])) {
			$this->markedChapters = count($sessionVars['markedChapters']);
		}
		if (isset($sessionVars['markedStories'])) {
			$this->markedStories = count($sessionVars['markedStories']);
		}

		// get list status
		if (WCF::getUser()->userID) {
			$this->normalStoriesStatus = intval(WCF::getUser()->normalStoriesStatus);
		}
		else {
			if (WCF::getSession()->getVar('normalStoriesStatus') !== null) $this->normalStoriesStatus = WCF::getSession()->getVar('normalStoriesStatus');
		}

		// tags
		if (MODULE_TAGGING && STORY_ENABLE_TAGS && LIBRARY_ENABLE_TAGS && $this->library->isLibrary()) {
			$this->readTags();
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'permissions' => $this->library->getModeratorPermissions(),
			'selfLink' => 'index.php?page=Library&libraryID=' . $this->libraryID . SID_ARG_2ND_NOT_ENCODED,
			'daysPrune' => $this->daysPrune,
			'markedChapters' => $this->markedChapters,
			'markedStories' => $this->markedStories,
			'library' => $this->library,
			'libraryID' => $this->libraryID,
			'libraryQuickJumpOptions' => Library::getLibrarySelect(),
			'status' => $this->status,
			'libraryModerators' => $this->libraryModerators,
			'normalStories' => ($this->storyList != null ? $this->storyList->stories : null),
			'newNormalStories' => ($this->storyList != null ? $this->storyList->newStories : 0),
			'normalStoriesStatus' => $this->normalStoriesStatus,
			'allowSpidersToIndexThisPage' => true,
			'defaultSortField' => $this->defaultSortField,
			'defaultSortOrder' => $this->defaultSortOrder,
			'defaultDaysPrune' => $this->defaultDaysPrune,
			'languageID' => $this->languageID,
			'contentLanguages' => Language::getContentLanguages(),
			'enableRating' => $this->enableRating,
			'tags' => $this->tags,
			'tagID' => $this->tagID,
			'tag' => $this->tag
		));

		if (WCF::getSession()->spiderID) {
			if ($this->storyList != null && $this->storyList->maxLastChapterTime) {
				@header('Last-Modified: '.gmdate('D, d M Y H:i:s', $this->storyList->maxLastChapterTime).' GMT');
			}
		}
	}

	/**
	 * Renders the moderators of this library for template output.
	 */
	protected function renderModerators() {
		$moderators = WCF::getCache()->get('library', 'moderators');
		if (isset($moderators[$this->libraryID])) {
			$this->libraryModerators = $moderators[$this->libraryID];
		}
	}

	/**
	 * Wrapper for LibraryList->renderLibraries()
	 * @see LibraryList::renderLibraries()
	 */
	protected function renderLibraries() {
		if ($this->libraryList === null) {
			require_once(SLS_DIR.'lib/data/library/LibraryList.class.php');
			$this->libraryList = new LibraryList($this->libraryID);
		}
		$this->libraryList->maxDepth = LIBRARY_LIBRARY_LIST_DEPTH;
		$this->libraryList->renderLibraries();
	}


	/**
	 * @see MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();

		if ($this->storyList == null) return 0;
		return $this->storyList->countStories();
	}

	/**
	 * @see SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();

		switch ($this->sortField) {
			case 'topic':
			case 'username':
			case 'time':
			case 'views':
			case 'chapters':
			case 'lastChapterTime': break;
			case 'ratingResult': if ($this->enableRating) break;
			default: $this->sortField = $this->defaultSortField;
		}
	}

	/**
	 * Reads the tags of this library.
	 */
	protected function readTags() {
		// include files
		require_once(SLS_DIR.'lib/data/library/LibraryTagCloud.class.php');

		// get tags
		$tagCloud = new LibraryTagCloud($this->libraryID, WCF::getSession()->getVisibleLanguageIDArray());
		$this->tags = $tagCloud->getTags();
	}
}
?>