<?php
// sls imports
require_once(SLS_DIR.'lib/data/chapter/ChapterEditor.class.php');
require_once(SLS_DIR.'lib/data/story/StoryEditor.class.php');
require_once(SLS_DIR.'lib/data/story/StoryAction.class.php');

/**
 * Executes moderation actions on Chapters.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.chapter
 * @category 	Story Library System
 */
class ChapterAction {
	/**
	 * chaper editor object
	 *
	 * @var ChapterEditor
	 */
	protected $chaptert = null;

	/**
	 * library editor object
	 *
	 * @var LibraryEditor
	 */
	protected $library = null;

	/**
	 * story editor object
	 *
	 * @var StoryEditor
	 */
	protected $story = null;

	protected $title = '';
	protected $url = '';
	protected $chapterIDs = null;
	protected $reason = '';
	protected $chapterID = 0;

	/**
	 * Creates a new ChapterAction object.
	 *
	 * @param	LibraryEditor	$library
	 * @param	StoryEditor	$story
	 * @param	ChapterEditor	$chapter
	 */
	public function __construct($library = null, $story = null, $chapter = null, $chapterID = 0, $title = '', $forwardURL = '', $reason = '') {
		$this->library = $library;
		$this->story = $story;
		$this->chapter = $chapter;
		$this->title = $title;
		$this->url = $forwardURL;
		$this->chapterID = $chapterID;
		if (empty($this->url) && $this->story) $this->url = 'index.php?page=Story&storyID='.$this->story->storyID.SID_ARG_2ND_NOT_ENCODED;
		$this->reason = $reason;

		// get marked chapters from session
		$this->getMarkedChapters();
	}

	/**
	 * Gets marked chapters from session.
	 */
	public function getMarkedChapters() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedChapters'])) {
			$this->ChapterIDs = implode(',', $sessionVars['markedChapters']);
		}
	}

	/**
	 * Changes the title of the selected chapter.
	 *
	 * @param	string		$title
	 */
	public function setTitle($title) {
		$this->title = $title;
		$this->changeTitle();
	}

	/**
	 * Changes the title of the selected chapter.
	 */
	public function changeTitle() {
		if (!$this->library->getModeratorPermission('canEditChapter')) {
			return;
		}

		if ($this->chapter != null) {
			$this->chapter->setTitle($this->title);

		}
	}

	/**
	 * Marks the selected chapter.
	 */
	public function mark() {
		if ($this->chapter != null) {
			$this->chapter->mark();
		}
		else if (is_array($this->chapterID)) {
			$storyIDs = ChpaterEditor::getStoryIDs(implode(',', $this->chapterID));
			if (!empty($storyIDs)) {
				// check permissions
				$sql = "SELECT	*
					FROM	sls".SLS_N."_story
					WHERE	storyID IN (".$storyIDs.")";
				$result = WCF::getDB()->sendQuery($sql);
				while ($row = WCF::getDB()->fetchArray($result)) {
					$story = new StoryEditor(null, $row);
					$story->enter();
				}

				foreach ($this->chapterID as $chapterID) {
					$chapter = new ChapterEditor($chapterID);
					$chapter->mark();
				}
			}
		}
	}

	/**
	 * Unmarks the selected chapter.
	 */
	public function unmark() {
		if ($this->chapter != null) {
			$this->chapter->unmark();
		}
		else if (is_array($this->chapterID)) {
			$chapterIDs = ChapterEditor::getStoryIDs(implode(',', $this->chapterID));
			if (!empty($storyIDs)) {
				// check permissions
				$sql = "SELECT	*
					FROM	sls".SLS_N."_story
					WHERE	storyID IN (".$storyIDs.")";
				$result = WCF::getDB()->sendQuery($sql);
				while ($row = WCF::getDB()->fetchArray($result)) {
					$story = new StoryEditor(null, $row);
					$story->enter();
				}

				foreach ($this->chapterID as $chapterID) {
					$chapter = new ChapterEditor($chapterID);
					$chapter->unmark();
				}
			}
		}
	}

	/**
	 * Trashes the selected chapter.
	 */
	public function trash($ignorePermission = false) {
		if (!STORY_ENABLE_RECYCLE_BIN || (!$ignorePermission && !$this->library->getModeratorPermission('canDeleteChapter'))) {
			return;
		}

		if ($this->chapter != null && !$this->chapter->isDeleted) {
			$this->chapter->trash($this->reason);
			$this->story->checkVisibility($this->reason);
			$this->removeChapter();
		}
	}

	/**
	 * Deletes the selected chapter.
	 */
	public function delete() {
		if ($this->chapter == null) {
			throw new IllegalLinkException();
		}

		// check permission
		$this->library->checkModeratorPermission('canDeleteChapterCompletely');

		// remove user stats
		StoryEditor::updateUserStats($this->story->storyID, 'delete');
		ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($this->story->storyID), 'delete');

		$this->chapter->unmark();
		$this->chapter->delete(false);

		if ($this->story->hasChapters()) {
			// delete only chapter
			$this->story->checkVisibility();
			if (!$this->chapter->isDeleted || !STORY_ENABLE_RECYCLE_BIN) {
				$this->removeChapter();
			}
			else {
				StoryEditor::refreshFirstChapterIDAll($this->story->storyID);
			}

			// re-add user stats
			StoryEditor::updateUserStats($this->story->storyID, 'enable');
			ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($this->story->storyID), 'enable');

			// forward
			HeaderUtil::redirect($this->url);
			exit;
		}
		else {
			// delete complete story
			$this->story->delete(false, false);
			if (!$this->chapter->isDeleted || !STORY_ENABLE_RECYCLE_BIN) {
				$this->library->refresh();
				if ($this->chapter->time >= $this->library->getLastChapterTime($this->story->languageID)) {
					$this->library->setLastChapters();
				}

				// reset cache
				StoryAction::resetCache();
			}

			HeaderUtil::redirect('index.php?page=Library&libraryID='.$this->library->libraryID.SID_ARG_2ND_NOT_ENCODED);
			exit;
		}
	}

	/**
	 * Restores the selected chapter.
	 */
	public function recover() {
		if (!$this->library->getModeratorPermission('canDeleteChapterCompletely')) {
			return;
		}

		if ($this->chapter != null && $this->chapter->isDeleted) {
			$this->chapter->restore();
			$this->Story->checkVisibility();
			$this->addChapter();
		}
	}

	/**
	 * Disables the selected chapter.
	 */
	public function disable() {
		if (!$this->library->getModeratorPermission('canEnableChapter')) {
			return;
		}

		if ($this->chapter != null && !$this->chapter->isDisabled) {
			$this->chapter->disable();
			$this->story->checkVisibility();
			$this->removeChapter();
		}
	}

	/**
	 * Enables the selected chapter.
	 */
	public function enable() {
		if (!$this->library->getModeratorPermission('canEnableChapter')) {
			return;
		}

		if ($this->chapter != null && $this->chapter->isDisabled) {
			$this->chapter->enable();
			$this->story->checkVisibility();
			$this->addChapter();
		}
	}

	/**
	 * Closes the selected chapter.
	 */
	public function close() {
		if (!$this->library->getModeratorPermission('canCloseChapter')) {
			return;
		}

		if ($this->chapter != null && !$this->chapter->isClosed) {
			$this->chapter->close();
		}
	}

	/**
	 * Opens the selected chapter.
	 */
	public function open() {
		if (!$this->library->getModeratorPermission('canCloseChaper')) {
			return;
		}

		if ($this->chapter != null && $this->chapter->isClosed) {
			$this->chapter->open();
		}
	}

	/**
	 * Unmarks all marked chapters.
	 */
	public static function unmarkAll() {
		ChapterEditor::unmarkAll();
	}

	/**
	 * Deletes all marked chapters.
	 */
	public function deleteAll() {
		if (!empty($this->chapterIDs)) {
			// get storyids
			$storyIDs = ChapterEditor::getStoryIDs($this->chapterIDs);

			// get libraries
			list($libraries, $libraryIDs) = StoryEditor::getLibrarys($storyIDs);

			// check permissions
			foreach ($libraries as $library) {
				$library->checkModeratorPermission('canDeleteChapter');
			}

			// get story ids of deleted chapters
			$storyIDs2 = '';
			$sql = "SELECT 	DISTINCT storyID
				FROM 	sls".SLS_N."_chapter
				WHERE 	chapterID IN (".$this->chapterIDs.")
					AND isDeleted = 1";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!empty($storyIDs2)) $storyIDs2 .= ',';
				$storyIDs2 .= $row['storyID'];
			}

			// get libraries of deleted chapters
			list($libraries2, $libraryIDs2) = StoryEditor::getLibraries($storyIDs2);

			// check permissions (delete completely)
			foreach ($libraries2 as $library2) {
				$library2->checkModeratorPermission('canDeleteChapterCompletely');
			}

			// remove user stats
			StoryEditor::updateUserStats($storyIDs, 'delete');
			ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($storyIDs), 'delete');

			// delete chapters
			ChapterEditor::deleteAll($this->chapterIDs, false, $this->reason);
			ChapterEditor::unmarkAll();

			// handle stories (check for empty, deleted and hidden stories)
			StoryEditor::checkVisibilityAll($storyIDs);

			// refresh last chapters in stories
			StoryEditor::refreshAll($storyIDs);

			// re-add user stats
			StoryEditor::updateUserStats($storyIDs, 'enable');
			ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($storyIDs), 'enable');

			// refresh counts
			LibraryEditor::refreshAll($libraryIDs);

			// refresh last chapter in libraries
			foreach ($libraries as $library) {
				$library->setLastChapters();
			}

			// reset cache
			StoryAction::resetCache();
		}

		// check whether the enable exists and forward
		if ($this->story != null && $this->story->hasChapters()) {
			HeaderUtil::redirect($this->url);
		}
		else if ($this->library != null) {
			HeaderUtil::redirect('index.php?page=Library&libraryID='.$this->library->libraryID.SID_ARG_2ND_NOT_ENCODED);
		}
		else {
			HeaderUtil::redirect('index.php'.SID_ARG_1ST);
		}
		exit;
	}

	/**
	 * Recovers all marked chapters.
	 */
	public function recoverAll() {
		// get storyids
		$storyIDs = ChapterEditor::StorygetdIDs($this->chapterIDs);

		// get libraries
		list($libraries, $libraryIDs) = StoryEditor::getLibraries($storyIDs);

		// check permissions
		foreach ($libraries as $library) {
			$library->checkModeratorPermission('canDeleteChapterCompletely');
		}

		// recover chapters
		ChapterEditor::restoreAll($this->chapterIDs);
		ChapterEditor::unmarkAll();

		// handle stories (check for empty, deleted and hidden stories)
		StoryEditor::checkVisibilityAll($storyIDs);

		// refresh last chapters in stories
		StoryEditor::refreshAll($storyIDs);

		// refresh counts
		LibraryEditor::refreshAll($libraryIDs);

		// refresh last chapter in libraries
		foreach ($libraries as $library) {
			$library->setLastChapters();
		}

		// reset cache
		StoryAction::resetCache();

		// forward
		HeaderUtil::redirect($this->url);
		exit;
	}

	/**
	 * Copies the marked chapters.
	 */
	public function copy() {
		if ($this->story == null) {
			throw new IllegalLinkException();
		}

		$this->library->checkModeratorPermission('canCopyChapter');

		// get storyids
		$storyIDs = ChapterEditor::getStoryIDs($this->chapterIDs);

		// remove user stats
		StoryEditor::updateUserStats($this->story->storyID, 'delete');
		ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($this->story->storyID), 'delete');

		// copy chapters
		ChapterEditor::copyAll($this->chapterIDs, $this->story->storyID, null, $this->story->libraryID, false);
		ChapterEditor::unmarkAll();

		// refresh story
		$this->story->refresh();

		// re-add user stats
		StoryEditor::updateUserStats($this->story->storyID, 'enable');
		ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($this->story->storyID), 'enable');

		// refresh counts
		$this->library->refresh();

		// set last chapter in library
		$this->library->setLastChapters();

		// reset cache
		StoryAction::resetCache();

		HeaderUtil::redirect('index.php?page=Story&storyID='.$this->story->storyID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}

	/**
	 * Moves the marked chapters.
	 */
	public function move() {
		if ($this->story == null) {
			throw new IllegalLinkException();
		}

		$this->library->checkModeratorPermission('canMoveChapter');

		// get storyids
		$storyIDs = ChapterEditor::getStoryIDs($this->chapterIDs);

		// get libraries
		list($libraries, $libraryIDs) = StoryEditor::getLibraries($storyIDs);

		// check permissions
		foreach ($libraries as $library) {
			$library->checkModeratorPermission('canMoveChapter');
		}

		// remove user stats
		StoryEditor::updateUserStats($storyIDs.','.$this->story->storyID, 'delete');
		ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($storyIDs.','.$this->story->storyID), 'delete');

		// move chapters
		ChapterEditor::moveAll($this->chapterIDs, $this->story->storyID, $this->story->libraryID, false);
		ChapterEditor::unmarkAll();

		// handle stories (check for empty, deleted and hidden stories)
		StoryEditor::checkVisibilityAll($storyIDs);

		// refresh last chapter, replies, attachments, polls in stories
		StoryEditor::refreshAll($storyIDs.','.$this->story->storyID);

		// re-add user stats
		StoryEditor::updateUserStats($storyIDs.','.$this->story->storyID, 'enable');
		ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($storyIDs.','.$this->story->storyID), 'enable');

		// refresh counts
		LibraryEditor::refreshAll($libraryIDs.','.$this->library->libraryID);

		// refresh last chapter in libraries
		$this->library->setLastChapters();
		foreach ($libraries as $library) {
			$library->setLastChapters();
		}

		// reset cache
		StoryAction::resetCache();

		HeaderUtil::redirect('index.php?page=Story&storyID='.$this->story->storyID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}

	/**
	 * Adds a chapter.
	 */
	public function addChapter() {
		// reset cache
		StoryAction::resetCache();

		// refresh story
		$this->story->refresh(false);
		if ($this->chapter->time > $this->story->lastChapterTime) {
			$this->story->setLastChapter($this->chapter);
		}

		// refresh library
		$this->library->refresh();
		if ($this->chapter->time > $this->library->getLastChapterTime($this->story->languageID)) {
			$this->library->setLastChapters();
		}
	}

	/**
	 * Removes a chapter.
	 */
	public function removeChapter() {
		// reset cache
		StoryAction::resetCache();

		// refresh story
		$this->story->refresh(false);
		if ($this->chapter->time >= $this->story->lastChapterTime) {
			$this->story->setLastChapter();
		}

		// refresh library
		$this->library->refresh();
		if ($this->chapter->time >= $this->library->getLastChapterTime($this->story->languageID)) {
			$this->library->setLastChapters();
		}
	}

	/**
	 * Deletes the report of a chapter.
	 */
	public function removeReport() {
		if ($this->chapter == null) {
			throw new IllegalLinkException();
		}

		$this->library->checkModeratorPermission('canEditChapter');

		ChapterEditor::removeReportData($this->chapter->chapterID);

		HeaderUtil::redirect($this->url);
		exit;
	}

	/**
	 * Deletes reports of marked chapters.
	 */
	public function removeReports() {
		// get storyids
		$storyIDs = ChapterEditor::getStoryIDs($this->chapterIDs);

		// get libraries
		list($libraries, $libraryIDs) = StoryEditor::getLibraries($storyIDs);

		// check permissions
		foreach ($libraries as $library) {
			$library->checkModeratorPermission('canEditChapter');
		}

		ChapterEditor::removeReportData($this->chapterIDs);
		self::unmarkAll();

		HeaderUtil::redirect($this->url);
		exit;
	}

	/**
	 * Merges chapters.
	 */
	public function merge() {
		if ($this->chapter === null || empty($this->chapterIDs)) {
			throw new IllegalLinkException();
		}

		// remove target chapter from source
		$chapterIDArray = explode(',', $this->chapterIDs);
		if (($key = array_search($this->chapter->chapterID, $chapterIDArray)) !== false) {
			unset($chapterIDArray[$key]);
			$this->chapterIDs = implode(',', $chapterIDArray);
		}

		// get story ids
		$storyIDs = ChapterEditor::getStoryIDs($this->chapterIDs);

		// get libraries
		list($libraries, $libraryIDs) = StoryEditor::getLibraries($storyIDs);

		// check permissions
		$this->library->checkModeratorPermission('canMergeChapter');
		foreach ($libraries as $library) {
			$library->checkModeratorPermission('canMergeChapter');
		}

		// remove user stats
		StoryEditor::updateUserStats($storyIDs, 'delete');
		ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($storyIDs), 'delete');

		// merge chapters
		ChapterEditor::mergeAll($this->chapterIDs, $this->chapter->chapterID);
		ChapterEditor::unmarkAll();

		// handle stories (check for empty, deleted and hidden stories)
		StoryEditor::checkVisibilityAll($storyIDs);

		// refresh last chapter, replies, attachments, polls in stories
		StoryEditor::refreshAll($storyIDs);

		// re-add user stats
		StoryEditor::updateUserStats($storyIDs, 'enable');
		ChapterEditor::updateUserStats(StoryEditor::getAllChapterIDs($storyIDs), 'enable');

		// refresh counts
		LibraryEditor::refreshAll($libraryIDs);

		// refresh last chapter in libraries
		$this->library->setLastChapters();
		foreach ($libraries as $library) {
			$library->setLastChapters();
		}

		HeaderUtil::redirect($this->url);
		exit;
	}
}
?>