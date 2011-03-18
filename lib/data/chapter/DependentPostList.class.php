<?php
// sls imports
require_once(SLS_DIR.'lib/data/story/Story.class.php');
require_once(SLS_DIR.'lib/data/library/Library.class.php');
require_once(SLS_DIR.'lib/data/chapter/ChapterList.class.php');

/**
 * Shows a list of dependent chapters.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.Chapter
 * @category 	Sotry Library System
 */
class DependentChapterList extends ChapterList {
	public $library;
	public $sqlConditionVisible = '';

	/**
	 * Creates a new DependentChapterList object.
	 *
	 * @param	Story		$story
	 * @param	Library		$library
	 */
	public function __construct(Story $story, Library $library) {
		$this->story = $story;
		$this->library = $library;

		parent::__construct();
	}

	/**
	 * @see ChapterList::initDefaultSQL();
	 */
	protected function initDefaultSQL() {
		parent::initDefaultSQL();

		// default sql conditions
		$this->sqlConditions = "storyID = ".$this->story->storyID;
		if (!$this->library->getModeratorPermission('canReadDeletedChapter') && !STORY_ENABLE_DELETED_CHAPTER_NOTE) {
			$this->sqlConditionVisible .= ' AND isDeleted = 0';
		}
		if (!$this->library->getModeratorPermission('canEnableChapter')) {
			$this->sqlConditionVisible .= ' AND isDisabled = 0';
		}
		$this->sqlConditions .= $this->sqlConditionVisible;
	}
}
?>