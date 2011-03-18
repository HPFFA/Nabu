<?php
require_once(SLS_DIR.'lib/data/story/StoryList.class.php');

/**
 * LibraryStoryList provides extended functions for displaying a list of stories.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.story
 * @category 	Story Library System
 */
class LibraryStoryList extends StoryList {
	// parameters
	public $library;
	public $daysPrune = 100;
	public $status = '';
	public $languageID = 0;

	// data
	public $newStories = 0;
	public $maxLastChapterTime = 0;

	// sql
	public $sqlConditionVisible = '';
	public $sqlConditionLanguage = '';

	/**
	 * Creates a new LibraryStoryList object.
	 */
	public function __construct(Library $library, $daysPrune = 100, $status = '', $languageID = 0) {
		$this->library = $library;
		$this->daysPrune = $daysPrune;
		$this->status = $status;
		$this->languageID = $languageID;
		if ($this->library->enableRating != -1) $this->enableRating = $this->library->enableRating;

		parent::__construct();
	}

	/**
	 * @see StoryList::initDefaultSQL()
	 */
	protected function initDefaultSQL() {
		parent::initDefaultSQL();

		$this->sqlConditions = "libraryID = ".$this->library->libraryID;
		// days prune
		if ($this->daysPrune != 1000) {
			$this->sqlConditions .= " AND (( lastChapterTime >= ".(TIME_NOW - ($this->daysPrune * 86400)).") OR  lastChapterTime >= 0)";
		}

		// visible status
		if (!$this->library->getModeratorPermission('canReadDeletedStory') && !LIBRARY_ENABLE_DELETED_STORY_NOTE) {
			$this->sqlConditionVisible .= ' AND isDeleted = 0';
		}
		if (!$this->library->getModeratorPermission('canEnableStory')) {
			$this->sqlConditionVisible .= ' AND isDisabled = 0';
		}
		$this->sqlConditions .= $this->sqlConditionVisible;


		// story language
		if ($this->languageID != 0) {
			$this->sqlConditionLanguage = " AND story.languageID = ".$this->languageID;
			$this->sqlConditions .= $this->sqlConditionLanguage;
		}
		else if (count(WCF::getSession()->getVisibleLanguageIDArray()) && (LIBRARY_STORIES_ENABLE_LANGUAGE_FILTER_FOR_GUESTS == 1 || WCF::getUser()->userID != 0)) {
			$this->sqlConditionLanguage = " AND story.languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")";
			$this->sqlConditions .= $this->sqlConditionLanguage;
		}

		// status filter
		if (!empty($this->status)) {
			switch ($this->status) {
				case 'read':
					if (WCF::getUser()->userID) {
						$this->sqlConditions .= "	AND (story.lastChapterTime <= ".WCF::getUser()->getLibraryVisitTime($this->library->libraryID)."
										OR story.lastChapterTime <= IFNULL((
											SELECT	lastVisitTime
											FROM	sls".SLS_N."_story_visit visit
											WHERE	visit.storyID = story.storyID
												AND visit.userID = ".WCF::getUser()->userID."
										), 0))";
					}
					break;
				case 'unread':
					if (WCF::getUser()->userID) {
						$this->sqlConditions .= "	AND story.lastChapterTime > ".WCF::getUser()->getLibraryVisitTime($this->library->libraryID)."
										AND story.lastChapterTime > IFNULL((
											SELECT	lastVisitTime
											FROM	sls".SLS_N."_story_visit visit
											WHERE	visit.storyID = story.storyID
												AND visit.userID = ".WCF::getUser()->userID."
										), 0)";
					}
					break;
				case 'open':
				case 'closed':
					$this->sqlConditions .= " AND story.isClosed = ".($this->status == 'open' ? 0 : 1);
					break;
				case 'deleted':
					if ($this->library->getModeratorPermission('canReadDeletedStory')) $this->sqlConditions .= " AND story.isDeleted = 1";
					break;
				case 'hidden':
					if ($this->library->getModeratorPermission('canEnableStory')) $this->sqlConditions .= " AND story.isDisabled = 1";
					break;
				case 'done':
				case 'undone':
					if (MODULE_STORY_MARKING_AS_DONE && $this->library->enableMarkingAsDone) {
						$this->sqlConditions .= " AND story.isDone = ".($this->status == 'done' ? 1 : 0);
					}
					break;
			}
		}
	}

	
	/**
	 * @see StoryList::readStories()
	 */
	public function readStories() {
		parent::readStories();

		foreach ($this->stories as $key => $story) {
			if ($story->lastChapterTime > $this->maxLastChapterTime) {
				$this->maxLastChapterTime = $story->lastChapterTime;
			}

				if ($story->isNew()) $this->newStories++;
		}
	}
}
?>