<?php
// sls imports
require_once(SLS_DIR.'lib/data/library/LibraryEditor.class.php');
require_once(SLS_DIR.'lib/data/chapter/ChapterEditor.class.php');
require_once(SLS_DIR.'lib/data/story/Story.class.php');

/**
 * StoryEditor provides functions to create and edit the data of a story.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.story
 * @category 	Story Library System
 */
class StoryEditor extends Story {
	/**
	 * Assigns an announcement to the given libraries.
	 *
	 * @param	array		$libraryIDs
	 */
	public function assignLibraries($libraryIDs) {
		if (!in_array($this->libraryID, $libraryIDs)) {
			$libraryIDs[] = $this->libraryID;
		}

		$libraryIDs = array_unique($libraryIDs);

		$inserts = '';
		foreach ($libraryIDs as $libraryID) {
			if (!empty($inserts)) $inserts .= ',';
			$inserts .= '('.$libraryID.', '.$this->storyID.')';
		}

		// insert new libraries
		$sql = "INSERT IGNORE INTO 	sls".SLS_N."_story_announcement
						(libraryID, storyID)
			VALUES			".$inserts;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Removes assigned libraries.
	 */
	public function removeAssignedLibraries() {
		$sql = "DELETE FROM 	sls".SLS_N."_story_announcement
			WHERE		storyID = ".$this->storyID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Returns the list of assigned libraries.
	 *
	 * @return	array		list of library ids
	 */
	public function getAssignedLibraries() {
		$libraryIDs = array();
		$sql = "SELECT	libraryID
			FROM	sls".SLS_N."_story_announcement
			WHERE	storyID = ".$this->storyID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$libraryIDs[] = $row['libraryID'];
		}

		return $libraryIDs;
	}

	/**
	 * Adds a new chapter to this story.
	 *
	 * @param	Chapter		$chapter		the new chapter
	 * @param	integer		$closedStory	true (1), if story ought to be closed
	 */
	public function addChapter($chapter, $closeStory = 0) {
		$this->data['lastChapterer'] = $chapter->username;
		$this->data['lastChapterTime'] = $chapter->time;
		$this->data['lastChaptererID'] = $chapter->userID;
		$this->data['chapters']++;
		
		$sql = "UPDATE 	sls".SLS_N."_story
			SET	".(($closeStory) ? ("isClosed = 1,") : (""))."
				chapters = chapters + 1,
				lastChapterTime = ".$this->lastChapterTime.",
				lastChaptererID = ".$this->lastChaptererID.",
				lastChapterer = '".escapeString($this->lastChapterer)."'
			WHERE 	storyID = ".$this->storyID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Updates the type of subscription on this story for the active user.
	 *
	 * @param	integer		$subscription		new type of subscription on this story for the active user
	 */
	public function setSubscription($subscription) {
		if (WCF::getUser()->userID && $this->subscribed != $subscription) {
			if (!$subscription) {
				// delete notification
				$sql = "DELETE FROM 	sls".SLS_N."_story_subscription
					WHERE		userID = ".WCF::getUser()->userID."
							AND storyID = ".$this->storyID;
				WCF::getDB()->sendQuery($sql);
			}
			else {
				// add new notification
				$sql = "INSERT INTO 	sls".SLS_N."_story_subscription
							(userID, storyID, enableNotification)
					VALUES		(".WCF::getUser()->userID.", ".$this->storyID.", ".WCF::getUser()->enableEmailNotification.")";
				WCF::getDB()->sendQuery($sql);
			}
		}
	}

	/**
	 * Sets the topic of this story.
	 *
	 * @param	string		$topic		new topic for this story
	 */
	public function setTopic($topic, $updateFirstChapter = true) {
		if ($topic == $this->topic) return;

		$this->topic = $topic;
		$sql = "UPDATE 	sls".SLS_N."_story
			SET	topic = '".escapeString($topic)."'
			WHERE 	storyID = ".$this->storyID;
		WCF::getDB()->registerShutdownUpdate($sql);

		// update the subject of the first chapter in this story
		if ($updateFirstChapter && $this->firstChapterID) {
			$sql = "UPDATE 	sls".SLS_N."_chapter
				SET	subject = '".escapeString($topic)."'
				WHERE 	chapterID = ".$this->firstChapterID;
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}

	/**
	 * Sets the prefix of this story.
	 *
	 * @param	string		$prefix
	 */
	public function setPrefix($prefix) {
		if ($prefix == $this->prefix) return;

		$this->prefix = $prefix;
		$sql = "UPDATE 	sls".SLS_N."_story
			SET	prefix = '".escapeString($prefix)."'
			WHERE 	storyID = ".$this->storyID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}

	/**
	 * Returns true, if this story contains chapters.
	 *
	 * @return	boolean		true, if this story contains chapters
	 */
	public function hasChapters() {
		$sql = "SELECT 	COUNT(*) AS count
			FROM 	sls".SLS_N."_chapter
			WHERE 	storyID = ".$this->storyID;
		$result = WCF::getDB()->getFirstRow($sql);
		return $result['count'];
	}

	/**
	 * Sets the last chapter of this story.
	 *
	 * @param	Chapter	$chapter
	 */
	public function setLastChapter($chapter = null) {
		self::__setLastChapter($this->storyID, $chapter);
	}

	/**
	 * Marks this story.
	 */
	public function mark() {
		$markedStories = self::getMarkedStories();
		if ($markedStories == null || !is_array($markedStories)) {
			$markedStories = array($this->storyID);
			WCF::getSession()->register('markedStories', $markedStories);
		}
		else {
			if (!in_array($this->storyID, $markedStories)) {
				array_push($markedStories, $this->storyID);
				WCF::getSession()->register('markedStories', $markedStories);
			}
		}
	}

	/**
	 * Unmarks this story.
	 */
	public function unmark() {
		$markedStories = self::getMarkedStories();
		if (is_array($markedStories) && in_array($this->storyID, $markedStories)) {
			$key = array_search($this->storyID, $markedStories);

			unset($markedStories[$key]);
			if (count($markedStories) == 0) {
				self::unmarkAll();
			}
			else {
				WCF::getSession()->register('markedStories', $markedStories);
			}
		}
	}

	/**
	 * Moves this story into the recycle bin.
	 */
	public function trash($trashChapters = true, $reason = '') {
		self::trashAll($this->storyID, $trashChapters, $reason);
	}

	/**
	 * Deletes this story completely.
	 */
	public function delete($deleteChapters = true, $updateUserStats = true) {
		self::deleteAllCompletely($this->storyID, $deleteChapters, $updateUserStats);
	}

	/**
	 * Restores this deleted story.
	 */
	public function restore($restoreChapters = true) {
		self::restoreAll($this->storyID, $restoreChapters);
	}

	/**
	 * Disables this story.
	 */
	public function disable($disableChapters = true) {
		self::disableAll($this->storyID, $disableChapters);
	}

	/**
	 * Enables this story.
	 */
	public function enable($enableChapters = true) {
		self::enableAll($this->storyID, $enableChapters);
	}

	/**
	 * Closes this story.
	 */
	public function close() {
		self::closeAll($this->storyID);
	}

	/**
	 * Closes the stories with given ids.
	 *
	 * @param	string		$storyIDs
	 */
	public static function closeAll($storyIDs) {
		if (empty($storyIDs)) return;

		$sql = "UPDATE 	sls".SLS_N."_story
			SET	isClosed = 1
			WHERE 	storyID IN (".$storyIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}

	/**
	 * Opens this story.
	 */
	public function open() {
		$sql = "UPDATE 	sls".SLS_N."_story
			SET	isClosed = 0
			WHERE 	storyID = ".$this->storyID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}

	/**
	 * Returns the chapter ids of this story.
	 */
	public function getChapterIDs() {
		return self::getAllChapterIDs($this->storyID);
	}

	/**
	 * Copies the stories with the given story ids and merges the copies with this story.
	 */
	public function copyAndMerge($storyIDs) {
		if (empty($storyIDs)) return;

		// remove user stats
		self::updateUserStats($this->storyID, 'delete');
		ChapterEditor::updateUserStats(self::getAllChapterIDs($this->storyID), 'delete');

		// copy chapters
		ChapterEditor::copyAll(self::getAllChapterIDs($storyIDs), $this->storyID, null, $this->libraryID, false);

		// re-add user stats
		$this->refresh();
		self::updateUserStats($this->storyID, 'enable');
		ChapterEditor::updateUserStats(self::getAllChapterIDs($this->storyID), 'enable');
	}

	/**
	 * Merges the stories with the given story ids with this story.
	 */
	public function merge($storyIDs) {
		if (empty($storyIDs)) return;

		$storyIDArray = explode(',', $storyIDs);
		if (in_array($this->storyID, $storyIDArray)) {
			unset($storyIDArray[array_search($this->storyID, $storyIDArray)]);
			$storyIDs = implode(',', $storyIDArray);
			if (empty($storyIDs)) return;
		}

		// add views
		$sql = "SELECT	SUM(views) AS views
			FROM	sls".SLS_N."_story
			WHERE	storyID IN (".$storyIDs.")";
		$row = WCF::getDB()->getFirstRow($sql);
		if ($row['views']) {
			$sql = "UPDATE	sls".SLS_N."_story
				SET	views = views + ".$row['views']."
				WHERE	storyID = ".$this->storyID;
			WCF::getDB()->sendQuery($sql);
		}

		// update tags
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$taggable = TagEngine::getInstance()->getTaggable('com.woltlab.sls.story');
			$sql = "UPDATE IGNORE	wcf".WCF_N."_tag_to_object
				SET		objectID = ".$this->storyID."
				WHERE		taggableID = ".$taggable->getTaggableID()."
						AND languageID = ".$this->languageID."
						AND objectID IN (".$storyIDs.")";
			WCF::getDB()->sendQuery($sql);
			$sql = "DELETE FROM	wcf".WCF_N."_tag_to_object
				WHERE		taggableID = ".$taggable->getTaggableID()."
						AND objectID IN (".$storyIDs.")";
			WCF::getDB()->sendQuery($sql);
		}

		// remove user stats
		$chapterIDs = self::getAllChapterIDs($storyIDs);
		self::updateUserStats($storyIDs.','.$this->storyID, 'delete');
		ChapterEditor::updateUserStats($chapterIDs.','.self::getAllChapterIDs($this->storyID), 'delete');

		// move chapters
		ChapterEditor::moveAll($chapterIDs, $this->storyID, $this->libraryID, false);

		// re-add user stats
		$this->refresh();
		self::updateUserStats($this->storyID, 'enable');
		ChapterEditor::updateUserStats(self::getAllChapterIDs($this->storyID), 'enable');

		// delete stories
		self::deleteAllCompletely($storyIDs, false, false);
	}

	/**
	 * Refreshes the last chapter, replies, amount of attachments and amount of polls of this story.
	 */
	public function refresh($refreshLastChapter = true) {
		self::refreshAll($this->storyID, $refreshLastChapter);
	}

	/**
	 * Creates a link of this story.
	 */
	public function createLink() {
		$sql = "INSERT INTO	sls".SLS_N."_story
					(libraryID, languageID, prefix, topic, time, userID, username, lastChapterTime,
					lastChaptererID, lastChapterer, replies, views, ratings, rating, attachments,
					polls, isAnnouncement, isSticky, isDisabled, everEnabled, isClosed, isDeleted,
					movedStoryID, movedTime)
			VALUES		(".$this->libraryID.",
					".$this->languageID.",
					'".escapeString($this->prefix)."',
					'".escapeString($this->topic)."',
					".$this->time.",
					".$this->userID.",
					'".escapeString($this->username)."',
					".$this->lastChapterTime.",
					".$this->lastChaptererID.",
					'".escapeString($this->lastChapterer)."',
					".$this->replies.",
					".$this->views.",
					".$this->ratings.",
					".$this->rating.",
					".$this->attachments.",
					".$this->polls.",
					".$this->isAnnouncement.",
					".$this->isSticky.",
					".$this->isDisabled.",
					".$this->everEnabled.",
					".$this->isClosed.",
					".$this->isDeleted.",
					".$this->storyID.",
					".TIME_NOW.")";
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Copies the sql data of this story.
	 */
	public function copy($libraryID) {
		return self::insert($this->topic, $libraryID, array(
			'languageID' => $this->languageID,
			'userID' => $this->userID,
			'username' => $this->username,
			'prefix' => $this->prefix,
			'time' => $this->time,
			'lastChapterTime' => $this->lastChapterTime,
			'lastChaptererID' => $this->lastChaptererID,
			'lastChapterer' => $this->lastChapterer,
			'replies' => $this->replies,
			'views' => $this->views,
			'ratings' => $this->ratings,
			'rating' => $this->rating,
			'attachments' => $this->attachments,
			'polls' => $this->polls,
			'isSticky' => $this->isSticky,
			'isAnnouncement' => $this->isAnnouncement,
			'isClosed' => $this->isClosed,
			'isDisabled' => $this->isDisabled,
			'everEnabled' => $this->everEnabled,
			'isDeleted' => $this->isDeleted
		));
	}

	/**
	 * Checks whether this story is empty, thrashed or hidden.
	 */
	public function checkVisibility($reason = '') {
		self::checkVisibilityAll($this->storyID, $reason);
	}

	/**
	 * Creates a new story with the given data in the database.
	 * Returns a StoryEditor object of the new story.
	 *
	 * @param	integer				$libraryID
	 * @param	string				$subject		subject of the new story
	 * @param	string				$text			text of the first chapter in the new story
	 * @param	integer				$authorID		user id of the author of the new story
	 * @param	string				$author			username of the author of the new story
	 * @param	integer				$sticky			true (1), if it is a sticky story
	 * @param	integer				$isClosed		true (1), if it is a closed story
	 * @param	array				$options		options of the new story
	 * @param	integer				$subscription		type of notifation on the new story for the active user
	 * @param	AttachmentsEditor		$attachmentsEditor
	 * @param	PollEditor			$pollEditor
	 *
	 * @return	StoryEditor						the new story
	 */
	public static function create($libraryID, $languageID, $prefix, $subject, $text, $userID, $username, $sticky = 0, $announcement = 0, $closed = 0, $options = array(), $subscription = 0, $attachments = null, $poll = null, $disabled = 0) {
		$attachmentsAmount = $attachments != null ? count($attachments->getAttachments()) : 0;
		$polls = ($poll != null && $poll->pollID) ? 1 : 0;

		// insert story
		$storyID = self::insert($subject, $libraryID, array(
			'languageID' => $languageID,
			'userID' => $userID,
			'username' => $username,
			'prefix' => $prefix,
			'time' => TIME_NOW,
			'lastChapterTime' => TIME_NOW,
			'lastChaptererID' => $userID,
			'lastChapterer' => $username,
			'attachments' => $attachmentsAmount,
			'polls' => $polls,
			'isSticky' => $sticky,
			'isAnnouncement' => $announcement,
			'isClosed' => $closed,
			'isDisabled' => $disabled,
			'everEnabled' => ($disabled ? 0 : 1)
		));

		// create chapter
		$chapter = ChapterEditor::create($storyID, $subject, $text, $userID, $username, $options, $attachments, $poll, null, $disabled, true);

		// update first chapter id
		$sql = "UPDATE	sls".SLS_N."_story
			SET	firstChapterID = ".$chapter->chapterID."
			WHERE	storyID = ".$storyID;
		WCF::getDB()->sendQuery($sql);

		// update first chapter preview
		ChapterEditor::updateFirstChapterPreview($storyID, $chapter->chapterID, $text, $options);

		// get story object
		$story = new StoryEditor($storyID);

		// update subscription
		$story->setSubscription($subscription);

		// get similar stories
		self::updateSimilarStories($storyID, $subject, $libraryID);

		return $story;
	}

	/**
	 * Creates the story row in database table.
	 *
	 * @param 	string 		$topic
	 * @param	integer		$libraryID
	 * @param 	array		$additionalFields
	 * @return	integer		new story id
	 */
	public static function insert($topic, $libraryID, $additionalFields = array()) {
		$keys = $values = '';
		foreach ($additionalFields as $key => $value) {
			$keys .= ','.$key;
			$values .= ",'".escapeString($value)."'";
		}

		$sql = "INSERT INTO	sls".SLS_N."_story
					(libraryID, topic
					".$keys.")
			VALUES		(".$libraryID.", '".escapeString($topic)."'
					".$values.")";
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getInsertID();
	}

	/**
	 * Updates similar stories.
	 *
	 * @param	integer		$storyID
	 * @param	string		$subject
	 * @param	integer		$libraryID
	 */
	public static function updateSimilarStories($storyID, $subject, $libraryID = 0) {
		if (THREAD_ENABLE_SIMILAR_THREADS) {
			// get library ids
			$notSearchableLibraryIDArray = array();
			$libraries = WCF::getCache()->get('library', 'libraries');
			foreach ($libraries as $library) if (!$library->searchableForSimilarStories) $notSearchableLibraryIDArray[] = $library->libraryID;

			// get similar chapters
			$matches = array();
			$sql = "SELECT		chapter.chapterID,
						MATCH (chapter.subject, chapter.message) AGAINST ('".escapeString($subject)."')
						+ (5 / (1 + POW(LN(1 + (".TIME_NOW." - chapter.time) / 2592000), 2)))
						".($libraryID != 0 ? "+ IF(story.libraryID=".$libraryID.",2,0)" : "")." AS relevance
				FROM		sls".SLS_N."_chapter chapter
				LEFT JOIN	sls".SLS_N."_story story USING (storyID)
				WHERE		MATCH (chapter.subject, chapter.message) AGAINST ('".escapeString($subject)."' IN BOOLEAN MODE)
						AND (chapter.storyID <> ".$storyID.")
						".((count($notSearchableLibraryIDArray) > 0) ? ' AND story.libraryID NOT IN ('.implode(',', $notSearchableLibraryIDArray).')' : '')."
				GROUP BY	chapter.chapterID
				ORDER BY	relevance DESC";
			$result = WCF::getDB()->sendQuery($sql, 5);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$matches[] = $row['chapterID'];
			}

			// save matches
			if (count($matches)) {
				$sql = "INSERT IGNORE INTO	sls".SLS_N."_story_similar
								(storyID, similarStoryID)
					SELECT			".$storyID.", storyID
					FROM			sls".SLS_N."_chapter
					WHERE			chapterID IN (".implode(',', $matches).")";
				WCF::getDB()->registerShutdownUpdate($sql);
			}
		}
	}

	/**
	 * Unmarks all marked stories.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedStories');
	}

	/**
	 * Returns the currently marked stories.
	 */
	public static function getMarkedStories() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedStories'])) {
			return $sessionVars['markedStories'];
		}
		return null;
	}

	/**
	 * Deletes the stories with the given story ids.
	 */
	public static function deleteAll($storyIDs, $deleteChapters = true, $reason = '') {
		if (empty($storyIDs)) return;

		$trashIDs = '';
		$deleteIDs = '';
		if (THREAD_ENABLE_RECYCLE_BIN) {
			// recylce bin enabled
			// first of all we check which stories are already in recylce bin
			$sql = "SELECT 	storyID, isDeleted
				FROM 	sls".SLS_N."_story
				WHERE 	storyID IN (".$storyIDs.")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if ($row['isDeleted']) {
					// story in recylce bin
					// delete completely
					if (!empty($deleteIDs)) $deleteIDs .= ',';
					$deleteIDs .= $row['storyID'];
				}
				else {
					// move story to recylce bin
					if (!empty($trashIDs)) $trashIDs .= ',';
					$trashIDs .= $row['storyID'];
				}
			}
		}
		else {
			// no recylce bin
			// delete all stories completely
			$deleteIDs = $storyIDs;
		}

		self::trashAll($trashIDs, $deleteChapters, $reason);
		self::deleteAllCompletely($deleteIDs, $deleteChapters);
	}

	/**
	 * Moves the stories with the given story ids into the recycle bin.
	 */
	public static function trashAll($storyIDs, $trashChapters = true, $reason = '') {
		if (empty($storyIDs)) return;

		// trash story
		$sql = "UPDATE 	sls".SLS_N."_story
			SET	isDeleted = 1,
				deleteTime = ".TIME_NOW.",
				deletedBy = '".escapeString(WCF::getUser()->username)."',
				deletedByID = ".WCF::getUser()->userID.",
				deleteReason = '".escapeString($reason)."',
				isDisabled = 0
			WHERE 	storyID IN (".$storyIDs.")";
		WCF::getDB()->sendQuery($sql);

		// trash chapter
		if ($trashChapters) {
			ChapterEditor::trashAll(self::getAllChapterIDs($storyIDs), $reason);
		}
	}

	/**
	 * Deletes the stories with the given story ids completely.
	 */
	public static function deleteAllCompletely($storyIDs, $deleteChapters = true, $updateUserStats = true) {
		if (empty($storyIDs)) return;

		// update user chapters & activity points
		if ($updateUserStats) {
			self::updateUserStats($storyIDs, 'delete');
		}

		// delete chapters
		if ($deleteChapters) {
			ChapterEditor::deleteAllCompletely(self::getAllChapterIDs($storyIDs), true, true, $updateUserStats);
		}

		// delete stories
		self::deleteData($storyIDs);
	}

	/**
	 * Deletes the sql data of the stories with the given story ids.
	 */
	protected static function deleteData($storyIDs) {
		// delete story
		$sql = "DELETE FROM	sls".SLS_N."_story
			WHERE 		storyID IN (".$storyIDs.")
					OR movedStoryID IN (".$storyIDs.")";
		WCF::getDB()->sendQuery($sql);

		// delete ratingd
		$sql = "DELETE FROM	sls".SLS_N."_story_rating
			WHERE 		storyID IN (".$storyIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);

		// delete subscriptions
		$sql = "DELETE FROM 	sls".SLS_N."_story_subscription
			WHERE 		storyID IN (".$storyIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);

		// delete story visits
		$sql = "DELETE FROM	sls".SLS_N."_story_visit
			WHERE 		storyID IN (".$storyIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);

		// delete announcements
		$sql = "DELETE FROM	sls".SLS_N."_story_announcement
			WHERE 		storyID IN (".$storyIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);

		// delete similar stories
		$sql = "DELETE FROM	sls".SLS_N."_story_similar
			WHERE 		storyID IN (".$storyIDs.")
					OR similarStoryID IN (".$storyIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);

		// delete tags
		if (MODULE_TAGGING) {
			require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
			$taggable = TagEngine::getInstance()->getTaggable('com.woltlab.sls.story');

			$sql = "DELETE FROM	wcf".WCF_N."_tag_to_object
				WHERE 		taggableID = ".$taggable->getTaggableID()."
						AND objectID IN (".$storyIDs.")";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}

	/**
	 * Restores the stories with the given story ids.
	 */
	public static function restoreAll($storyIDs, $restoreChapters = true) {
		if (empty($storyIDs)) return;

		// restore story
		$sql = "UPDATE 	sls".SLS_N."_story
			SET	isDeleted = 0
			WHERE 	storyID IN (".$storyIDs.")";
		WCF::getDB()->sendQuery($sql);

		// restore chapter
		if ($restoreChapters) {
			ChapterEditor::restoreAll(self::getAllChapterIDs($storyIDs));
		}
	}

	/**
	 * Returns the ids of the chapters with the given story ids.
	 */
	public static function getAllChapterIDs($storyIDs) {
		if (empty($storyIDs)) return;

		$chapterIDs = '';
		$sql = "SELECT	chapterID
			FROM 	sls".SLS_N."_chapter
			WHERE 	storyID IN (".$storyIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($chapterIDs)) $chapterIDs .= ',';
			$chapterIDs .= $row['chapterID'];
		}

		return $chapterIDs;
	}

	/**
	 * Returns the libraries of the stories with the given story ids.
	 *
	 * @param	string		$storyIDs
	 * @return	array
	 */
	public static function getLibraries($storyIDs) {
		if (empty($storyIDs)) return array(array(), '', 'libraries' => array(), 'libraryIDs' => '');

		$libraries = array();
		$libraryIDs = '';
		$sql = "SELECT 	DISTINCT libraryID
			FROM 	sls".SLS_N."_story
			WHERE 	storyID IN (".$storyIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($libraryIDs)) $libraryIDs .= ',';
			$libraryIDs .= $row['libraryID'];
			$libraries[$row['libraryID']] = new LibraryEditor($row['libraryID']);
		}

		return array($libraries, $libraryIDs, 'libraries' => $libraries, 'libraryIDs' => $libraryIDs);
	}

	/**
	 * Moves all stories with the given ids into the library with the given library id.
	 */
	public static function moveAll($storyIDs, $newLibraryID) {
		if (empty($storyIDs)) return;

		// remove story links
		$sql = "DELETE FROM	sls".SLS_N."_story
			WHERE		libraryID = ".$newLibraryID."
					AND movedStoryID IN (".$storyIDs.")";
		WCF::getDB()->sendQuery($sql);

		// update user chapters & activity points (stories)
		self::updateUserStats($storyIDs, 'move', $newLibraryID);

		// get chapter ids
		$chapterIDs = '';
		$sql = "SELECT	chapterID
			FROM	sls".SLS_N."_chapter
			WHERE	storyID IN (".$storyIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($chapterIDs)) $chapterIDs .= ',';
			$chapterIDs .= $row['chapterID'];
		}

		// update user chapters & activity points (chapters)
		ChapterEditor::updateUserStats($chapterIDs, 'move', $newLibraryID);

		// move stories
		$sql = "UPDATE 	sls".SLS_N."_story
			SET	libraryID = ".$newLibraryID."
			WHERE 	storyID IN (".$storyIDs.")
				AND libraryID <> ".$newLibraryID;
		WCF::getDB()->sendQuery($sql);

		// check prefixes
		self::checkPrefixes($storyIDs, $newLibraryID);
	}

	/**
	 * Creates a link for all stories with the given ids.
	 */
	public static function createLinks($storyIDs, $libraryID) {
		if (empty($storyIDs)) return;

		$sql = "SELECT	*
			FROM 	sls".SLS_N."_story
			WHERE 	storyID IN (".$storyIDs.")
				AND libraryID <> ".$libraryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$story = new StoryEditor(null, $row);
			$story->createLink();
		}
	}

	/**
	 * Copies all SQL data of the stories with the given story ids.
	 */
	public static function copyAll($storyIDs, $libraryID) {
		if (empty($storyIDs)) return;

		// copy 'story' data
		$mapping = array();
		$sql = "SELECT	*
			FROM 	sls".SLS_N."_story
			WHERE 	storyID IN (".$storyIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$story = new StoryEditor(null, $row);
			$mapping[$story->storyID] = $story->copy($libraryID);
		}

		// copy 'story_announcement' data
		$sql = "SELECT	*
			FROM 	sls".SLS_N."_story_announcement
			WHERE 	storyID IN (".$storyIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "INSERT INTO	sls".SLS_N."_story_announcement
						(libraryID, storyID)
				VALUES 		(".$row['libraryID'].", ".$mapping[$row['storyID']].")";
			WCF::getDB()->registerShutdownUpdate($sql);
		}

		// copy 'story_rating' data
		$sql = "SELECT	*
			FROM 	sls".SLS_N."_story_rating
			WHERE 	storyID IN (".$storyIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "INSERT INTO	sls".SLS_N."_story_rating
						(storyID, rating, userID, ipAddress)
				VALUES		(".$mapping[$row['storyID']].", ".$row['rating'].",
						".$row['userID'].", '".escapeString($row['ipAddress'])."')";
			WCF::getDB()->registerShutdownUpdate($sql);
		}

		// copy 'story_subscription' data
		$sql = "SELECT	*
			FROM 	sls".SLS_N."_story_subscription
			WHERE 	storyID IN (".$storyIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "INSERT INTO 	sls".SLS_N."_story_subscription
						(userID, storyID, enableNotification, emails)
				VALUES		(".$row['userID'].", ".$mapping[$row['storyID']].",
						".$row['enableNotification'].", ".$row['emails'].")";
			WCF::getDB()->registerShutdownUpdate($sql);
		}

		// copy 'story_visit' data
		$sql = "SELECT 	*
			FROM 	sls".SLS_N."_story_visit
			WHERE 	storyID IN (".$storyIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "INSERT INTO 	sls".SLS_N."_story_visit
						(storyID, userID, lastVisitTime)
				VALUES		(".$mapping[$row['storyID']].", ".$row['userID'].", ".$row['lastVisitTime'].")";
			WCF::getDB()->registerShutdownUpdate($sql);
		}

		// update user chapters & activity points
		self::updateUserStats($storyIDs, 'copy', $libraryID);

		// copy chapters (and polls, attachments)
		ChapterEditor::copyAll(self::getAllChapterIds($storyIDs), null, $mapping, $libraryID);

		// check prefixes
		self::checkPrefixes(implode(',', $mapping), $libraryID);
	}

	/**
	 * Checks whether the stories with the given story ids are empty, thrashed or hidden.
	 */
	public static function checkVisibilityAll($storyIDs, $reason = '') {
		if (empty($storyIDs)) return;

		$emptyStories = '';
		$trashedStories = '';
		$hiddenStories = '';
		$enabledStories = '';
		$restoresStories = '';
		$sql = "SELECT		COUNT(chapter.chapterID) AS chapters,
					SUM(chapter.isDeleted) AS deletedChapters,
					SUM(chapter.isDisabled) AS hiddenChapters,
					story.storyID, story.isDeleted, story.isDisabled
			FROM 		sls".SLS_N."_story story
			LEFT JOIN 	sls".SLS_N."_chapter chapter
			ON 		(chapter.storyID = story.storyID)
			WHERE 		story.storyID IN (".$storyIDs.")
			GROUP BY 	story.storyID";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['deletedChapters'] = intval($row['deletedChapters']);
			$row['hiddenChapters'] = intval($row['hiddenChapters']);

			// story has no chapters
			// delete story
			if ($row['chapters'] == 0) {
				if (!empty($emptyStories)) $emptyStories .= ',';
				$emptyStories .= $row['storyID'];
			}

			// all chapters of this story are into the recylce bin
			// move story also into the recylce bin
			else if ($row['chapters'] == $row['deletedChapters']) {
				if (!empty($trashedStories)) $trashedStories .= ',';
				$trashedStories .= $row['storyID'];
			}

			// all chapters of this story are hidden
			// hide story also
			else if ($row['chapters'] == $row['hiddenChapters'] || $row['chapters'] == $row['hiddenChapters'] + $row['deletedChapters']) {
				if (!empty($hiddenStories)) $hiddenStories .= ',';
				$hiddenStories .= $row['storyID'];
			}

			// story is deleted, but no chapters are deleted
			// restore story
			else if (intval($row['deletedChapters']) == 0 && $row['isDeleted'] == 1) {
				if (!empty($restoresStories)) $restoresStories .= ',';
				$restoresStories .= $row['storyID'];
			}

			// story is hidden, but no chapters are hidden
			// enable story
			else if (intval($row['hiddenChapters']) == 0 && $row['isDisabled'] == 1) {
				if (!empty($enabledStories)) $enabledStories .= ',';
				$enabledStories .= $row['storyID'];
			}
		}

		self::deleteAllCompletely($emptyStories, false, false);
		self::trashAll($trashedStories, false, $reason);
		self::disableAll($hiddenStories, false);
		self::restoreAll($restoresStories, false);
		self::enableAll($enabledStories, false);
	}

	/**
	 * Disables the stories with the given story ids.
	 */
	public static function disableAll($storyIDs, $disableChapters = true) {
		if (empty($storyIDs)) return;

		// disable story
		$sql = "UPDATE 	sls".SLS_N."_story
			SET	isDeleted = 0,
				isDisabled = 1
			WHERE 	storyID IN (".$storyIDs.")";
		WCF::getDB()->sendQuery($sql);

		// disable chapter
		if ($disableChapters) {
			ChapterEditor::disableAll(self::getAllChapterIDs($storyIDs));
		}
	}

	/**
	 * Enables the stories with the given story ids.
	 */
	public static function enableAll($storyIDs, $enableChapters = true) {
		if (empty($storyIDs)) return;

		// send notifications
		$statStoryIDs = '';
		$sql = "SELECT	*
			FROM	sls".SLS_N."_story
			WHERE	storyID IN (".$storyIDs.")
				AND isDisabled = 1
				AND everEnabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($statStoryIDs)) $statStoryIDs .= ',';
			$statStoryIDs .= $row['storyID'];

			// send notifications
			$story = new StoryEditor(null, $row);
			$story->sendNotification();
		}

		// update user chapters & activity points
		self::updateUserStats($statStoryIDs, 'enable');

		// enable story
		$sql = "UPDATE 	sls".SLS_N."_story
			SET	isDisabled = 0,
				everEnabled = 1
			WHERE 	storyID IN (".$storyIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);

		// enable chapter
		if ($enableChapters) {
			ChapterEditor::enableAll(self::getAllChapterIDs($storyIDs));
		}
	}

	/**
	 * Refreshes the last chapter, replies, amount of attachments and amount of polls of this story.
	 */
	public static function refreshAll($storyIDs, $refreshLastChapter = true, $refreshFirstChapterID = true) {
		if (empty($storyIDs)) return;

		$sql = "UPDATE 	sls".SLS_N."_story story
			SET	replies = IF(story.isDeleted = 0 AND story.isDisabled = 0,
					(
						SELECT 	COUNT(*)
						FROM 	sls".SLS_N."_chapter
						WHERE 	storyID = story.storyID
							AND isDeleted = 0
							AND isDisabled = 0
					) - 1, replies),
				attachments = IFNULL((
					SELECT 	SUM(attachments)
					FROM 	sls".SLS_N."_chapter
					WHERE 	storyID = story.storyID
						AND isDeleted = 0
						AND isDisabled = 0
				), 0),
				polls = (
					SELECT 	COUNT(*)
					FROM 	sls".SLS_N."_chapter
					WHERE 	storyID = story.storyID
						AND isDeleted = 0
						AND isDisabled = 0
						AND pollID <> 0
				)
			WHERE 	storyID IN (".$storyIDs.")";
		WCF::getDB()->sendQuery($sql);

		if ($refreshLastChapter) {
			self::setLastChapterAll($storyIDs);
		}

		if ($refreshFirstChapterID) {
			self::refreshFirstChapterIDAll($storyIDs);
		}
	}

	/**
	 * Sets the last chapter of the stories with the given story ids.
	 */
	public static function setLastChapterAll($storyIDs) {
		if (empty($storyIDs)) return;

		$stories = explode(',', $storyIDs);
		foreach ($stories as $storyID) {
			self::__setLastChapter($storyID);
		}
	}

	/**
	 * Sets the last chapter of the story with the given story id.
	 */
	protected static function __setLastChapter($storyID, $chapter = null) {
		if ($chapter != null) {
			$result = array('time' => $chapter->time, 'userID' => $chapter->userID, 'username' => $chapter->username);
		}
		else {
			$sql = "SELECT		time, userID, username
				FROM 		sls".SLS_N."_chapter
				WHERE 		storyID = ".$storyID."
						AND isDeleted = 0
						AND isDisabled = 0
				ORDER BY 	time DESC";
			$result = WCF::getDB()->getFirstRow($sql);
		}

		if ($result['time']) {
			$sql = "UPDATE 	sls".SLS_N."_story
				SET	lastChapterTime = ".intval($result['time']).",
					lastChaptererID = ".intval($result['userID']).",
					lastChapterer = '".escapeString($result['username'])."'
				WHERE 	storyID = ".$storyID;
		}
		else {
			$sql = "UPDATE 	sls".SLS_N."_story
				SET	lastChapterTime = time,
					lastChaptererID = userID,
					lastChapterer = username
				WHERE 	storyID = ".$storyID;
		}
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Creates a new story.
	 */
	public static function createFromChapters($chapterIDs, $libraryID) {
		// get chapter
		$sql = "SELECT 		chapter.*, story.languageID
			FROM 		sls".SLS_N."_chapter chapter
			LEFT JOIN	sls".SLS_N."_story story
			ON		(story.storyID = chapter.storyID)
			WHERE 		chapter.chapterID IN (".$chapterIDs.")
			ORDER BY 	chapter.time ASC";
		$row = WCF::getDB()->getFirstRow($sql);
		$chapter = new Chapter(null, $row);

		$sql = "INSERT INTO 	sls".SLS_N."_story
					(libraryID, topic, firstChapterID, time, userID, username, languageID)
			VALUES		(".$libraryID.",
					'".escapeString($chapter->subject ? $chapter->subject : substr($chapter->message, 0, 255))."',
					".$chapter->chapterID.",
					".$chapter->time.",
					".$chapter->userID.",
					'".escapeString($chapter->username)."',
					".intval($row['languageID']).")";
		WCF::getDB()->sendQuery($sql);
		$storyID = WCF::getDB()->getInsertID();

		// update user chapters & activity points
		self::updateUserStats($storyID, 'copy', $libraryID);

		// update first chapter preview
		ChapterEditor::updateFirstChapterPreview($storyID, $chapter->chapterID, $chapter->message, array('enableSmilies' => $chapter->enableSmilies, 'enableHtml' => $chapter->enableHtml, 'enableBBCodes' => $chapter->enableBBCodes));

		return new StoryEditor($storyID);
	}

	/**
	 * Sticks this story.
	 */
	public function stick() {
		$sql = "UPDATE	sls".SLS_N."_story
			SET 	isSticky = 1,
				isAnnouncement = 0
			WHERE	storyID = ".$this->storyID."
				AND isSticky = 0";
		WCF::getDB()->registerShutdownUpdate($sql);
	}

	/**
	 * Unsticks this story.
	 */
	public function unstick() {
		$sql = "UPDATE	sls".SLS_N."_story
			SET 	isSticky = 0
			WHERE	storyID = ".$this->storyID."
				AND isSticky = 1";
		WCF::getDB()->registerShutdownUpdate($sql);
	}

	/**
	 * Sets the status of this story.
	 */
	public function setStatus($sticky, $announcement) {
		$sql = "UPDATE	sls".SLS_N."_story
			SET 	isSticky = ".$sticky.",
				isAnnouncement = ".$announcement."
			WHERE	storyID = ".$this->storyID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}

	/**
	 * Updates story data.
	 */
	public function update($storyData) {
		$updateSql = '';
		foreach ($storyData as $key => $value) {
			if (!empty($updateSql)) $updateSql .= ',';
			$updateSql .= $key."='".escapeString($value)."'";
		}

		if (!empty($updateSql)) {
			$sql = "UPDATE 	sls".SLS_N."_story
				SET	".$updateSql."
				WHERE	storyID = ".$this->storyID;
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * Sends the email notification.
	 */
	public function sendNotification($chapter = null, $attachmentList = null) {
		$sql = "SELECT		user.*
			FROM		sls".SLS_N."_library_subscription subscription
			LEFT JOIN	wcf".WCF_N."_user user
			ON		(user.userID = subscription.userID)
			WHERE		subscription.libraryID = ".$this->libraryID."
					AND subscription.enableNotification = 1
					AND subscription.emails = 0
					AND subscription.userID <> ".$this->userID."
					AND user.userID IS NOT NULL";
		$result = WCF::getDB()->sendQuery($sql);
		if (WCF::getDB()->countRows($result)) {
			// get first chapter
			if ($chapter === null) {
				require_once(SLS_DIR.'lib/data/chapter/Chapter.class.php');
				$chapter = new Chapter($this->firstChapterID);
			}

			// get attachments
			if ($attachmentList === null) {
				require_once(WCF_DIR.'lib/data/attachment/MessageAttachmentList.class.php');
				$attachmentList = new MessageAttachmentList($this->firstChapterID);
				$attachmentList->readObjects();
			}

			// set attachments
			require_once(WCF_DIR.'lib/data/message/bbcode/AttachmentBBCode.class.php');
			AttachmentBBCode::setAttachments($attachmentList->getSortedAttachments());

			// parse text
			require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
			$parser = MessageParser::getInstance();
			$parser->setOutputType('text/plain');
			$parsedText = $parser->parse($chapter->message, $chapter->enableSmilies, $chapter->enableHtml, $chapter->enableBBCodes, false);
			// truncate message
			if (!POST_NOTIFICATION_SEND_FULL_MESSAGE && StringUtil::length($parsedText) > 500) $parsedText = StringUtil::substring($parsedText, 0, 500) . '...';

			// send notifications
			$languages = array();
			$languages[WCF::getLanguage()->getLanguageID()] = WCF::getLanguage();
			$languages[0] = WCF::getLanguage();
			require_once(WCF_DIR.'lib/data/mail/Mail.class.php');
			require_once(WCF_DIR.'lib/data/user/User.class.php');
			require_once(SLS_DIR.'lib/data/library/Library.class.php');
			$library = Library::getLibrary($this->libraryID);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$recipient = new User(null, $row);

				// get language
				if (!isset($languages[$recipient->languageID])) {
					$languages[$recipient->languageID] = new Language($recipient->languageID);
				}

				// enable language
				$languages[$recipient->languageID]->setLocale();

				// send mail
				$data = array(
					'PAGE_TITLE' => $languages[$recipient->languageID]->get(PAGE_TITLE),
					'PAGE_URL' => PAGE_URL,
					'$recipient' => $recipient->username,
					'$author' => $this->username,
					'$libraryTitle' => $languages[$recipient->languageID]->get($library->title),
					'$topic' => $this->topic,
					'$storyID' => $this->storyID,
					'$text' => $parsedText);
				$mail = new Mail(	array($recipient->username => $recipient->email),
							$languages[$recipient->languageID]->get('sls.storyAdd.notification.subject', array('$title' => $languages[$recipient->languageID]->get($library->title))),
							$languages[$recipient->languageID]->get('sls.storyAdd.notification.mail', $data));
				$mail->send();
			}

			// enable user language
			WCF::getLanguage()->setLocale();

			// update notification count
			$sql = "UPDATE	sls".SLS_N."_library_subscription
				SET 	emails = emails + 1
				WHERE	libraryID = ".$this->libraryID."
					AND enableNotification = 1
					AND emails = 0";
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}

	/**
	 * Refreshes the first chapter ids of given stories.
	 *
	 * @param	string		$storyIDs
	 */
	public static function refreshFirstChapterIDAll($storyIDs) {
		$storyIDsArray = explode(',', $storyIDs);
		foreach ($storyIDsArray as $storyID) {
			// get chapter
			$sql = "SELECT 		chapterID, storyID, time, userID, username
				FROM 		sls".SLS_N."_chapter chapter
				WHERE 		storyID = ".$storyID."
				ORDER BY 	time ASC";
			$row = WCF::getDB()->getFirstRow($sql);
			if (!empty($row['chapterID'])) {
				$sql = "UPDATE	sls".SLS_N."_story
					SET	firstChapterID = ".$row['chapterID'].",
						time = ".$row['time'].",
						userID = ".$row['userID'].",
						username = '".escapeString($row['username'])."'
					WHERE	storyID = ".$storyID;
				WCF::getDB()->sendQuery($sql);
			}
		}
	}

	/**
	 * Checks if prefixes of given stories match available prefixes.
	 * Removes unavailable prefixes.
	 *
	 * @param	string		$storyIDs
	 * @param 	string		$libraryID
	 * @return 	integer		affected rows
	 */
	public static function checkPrefixes($storyIDs, $libraryID) {
		if (empty($storyIDs)) return;

		// get library
		$library = Library::getLibrary($libraryID);

		// get valid prefixes
		$prefixes = implode("','", array_map('escapeString', $library->getPrefixes()));

		// update stories
		$sql = "UPDATE	sls".SLS_N."_story
			SET	prefix = ''
			WHERE	storyID IN (".$storyIDs.")
				".($prefixes ? "AND prefix NOT IN ('".$prefixes."')" : '');
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getAffectedRows();
	}

	/**
	 * Updates the user stats (user chapters, activity points & user rank).
	 *
	 * @param	string		$storyIDs		changed stories
	 * @param 	string		$mode			(enable|copy|move|delete)
	 * @param 	integer		$destinationLibraryID
	 */
	public static function updateUserStats($storyIDs, $mode, $destinationLibraryID = 0) {
		if (empty($storyIDs)) return;

		// get destination library
		$destinationLibrary = null;
		if ($destinationLibraryID) $destinationLibrary = Library::getLibrary($destinationLibraryID);
		if ($mode == 'copy' && !$destinationLibrary->countUserChapters) return;

		// update user chapters, activity points
		$userChapters = array();
		$userActivityPoints = array();
		$sql = "SELECT	libraryID, userID
			FROM	sls".SLS_N."_story
			WHERE	storyID IN (".$storyIDs.")
				".($mode != 'enable' ? "AND everEnabled = 1" : '')."
				AND userID <> 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$library = Library::getLibrary($row['libraryID']);

			switch ($mode) {
				case 'enable':
					if ($library->countUserChapters) {
						// chapters
						if (!isset($userChapters[$row['userID']])) $userChapters[$row['userID']] = 0;
						$userChapters[$row['userID']]++;
						// activity points
						if (!isset($userActivityPoints[$row['userID']])) $userActivityPoints[$row['userID']] = 0;
						$userActivityPoints[$row['userID']] += ACTIVITY_POINTS_PER_THREAD;
					}
					break;
				case 'copy':
					if ($destinationLibrary->countUserChapters) {
						// chapters
						if (!isset($userChapters[$row['userID']])) $userChapters[$row['userID']] = 0;
						$userChapters[$row['userID']]++;
						// activity points
						if (!isset($userActivityPoints[$row['userID']])) $userActivityPoints[$row['userID']] = 0;
						$userActivityPoints[$row['userID']] += ACTIVITY_POINTS_PER_THREAD;
					}
					break;
				case 'move':
					if ($library->countUserChapters != $destinationLibrary->countUserChapters) {
						// chapters
						if (!isset($userChapters[$row['userID']])) $userChapters[$row['userID']] = 0;
						$userChapters[$row['userID']] += ($library->countUserChapters ? -1 : 1);
						// activity points
						if (!isset($userActivityPoints[$row['userID']])) $userActivityPoints[$row['userID']] = 0;
						$userActivityPoints[$row['userID']] += ($library->countUserChapters ? ACTIVITY_POINTS_PER_THREAD * -1 : ACTIVITY_POINTS_PER_THREAD);
					}
					break;
				case 'delete':
					if ($library->countUserChapters) {
						// chapters
						if (!isset($userChapters[$row['userID']])) $userChapters[$row['userID']] = 0;
						$userChapters[$row['userID']]--;
						// activity points
						if (!isset($userActivityPoints[$row['userID']])) $userActivityPoints[$row['userID']] = 0;
						$userActivityPoints[$row['userID']] -= ACTIVITY_POINTS_PER_THREAD;
					}
					break;
			}
		}

		// save chapters
		if (count($userChapters)) {
			require_once(SLS_DIR.'lib/data/user/SLSUser.class.php');
			foreach ($userChapters as $userID => $chapters) {
				SLSUser::updateUserChapters($userID, $chapters);
			}
		}

		// save activity points
		if (count($userActivityPoints)) {
			require_once(WCF_DIR.'lib/data/user/rank/UserRank.class.php');
			foreach ($userActivityPoints as $userID => $points) {
				UserRank::updateActivityPoints($points, $userID);
			}
		}
	}

	/**
	 * Updates the tags of this story.
	 *
	 * @param	array		$tags
	 */
	public function updateTags($tagArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(SLS_DIR.'lib/data/story/TaggedStory.class.php');

		// save tags
		$tagged = new TaggedStory(null, array(
			'storyID' => $this->storyID,
			'taggable' => TagEngine::getInstance()->getTaggable('com.woltlab.sls.story')
		));

		// delete old tags
		TagEngine::getInstance()->deleteObjectTags($tagged, array($this->languageID));

		// save new tags
		if (count($tagArray) > 0) TagEngine::getInstance()->addTags($tagArray, $tagged, $this->languageID);
	}

	/**
	 * Marks this story as done.
	 */
	public function markAsDone() {
		$sql = "UPDATE	sls".SLS_N."_story
			SET	isDone = 1
			WHERE	storyID = ".$this->storyID;
		WCF::getDB()->sendQuery($sql);
	}

	/**
	 * Marks this story as undone.
	 */
	public function markAsUndone() {
		$sql = "UPDATE	sls".SLS_N."_story
			SET	isDone = 0
			WHERE	storyID = ".$this->storyID;
		WCF::getDB()->sendQuery($sql);
	}

		/**
	 * Creates the preview of a post with the given data.
	 *
	 * @param	string		$subject
	 * @param	string		$text
	 *
	 * @return	string		the preview of a post
	 */
	public static function createPreview($storyTitle, $summary, $genreID=0, $character=0, $warningIDs=0, $classificationID=0, $chapter, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
		$row = array(
			'storyID' => 0,
			'title' => $storyTitle,
			'summary' => $summary,
			'enableSmilies' => $enableSmilies,
			'enableHtml' => $enableHtml,
			'enableBBCodes' => $enableBBCodes,
			'messagePreview' => true
		);

		require_once(SLS_DIR.'lib/data/post/ViewableStory.class.php');
		$post = new ViewableStory(null, $row);
		return $post->getFormattedMessage();
	}
}
?>