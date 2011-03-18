<?php
// sls imports
require_once(SLS_DIR.'lib/data/library/Library.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a story in the archiv.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.story
 * @category 	Story Library System
 */
class Story extends DatabaseObject {
	protected $chapter;
	protected $library = null;

	/**
	 * Creates a new story object.
	 *
	 * If id is set, the function reads the story data from database.
	 * Otherwise it uses the given resultset.
	 *
	 * @param 	integer 	$storyID	id of a story
	 * @param 	array 		$row		resultset with story data form database
	 * @param	integer		$chapterID		id of a chapter in the requested story
	 */
	public function __construct($storyID, $row = null, $chapterID = null) {
		if ($chapterID !== null && $chapterID !== 0) {
			require_once(SLS_DIR.'lib/data/chapter/Chapter.class.php');
			$this->chapter = new Chapter($chapterID);
			if ($this->chapter->storyID) {
				$storyID = $this->chapter->storyID;
			}
		}

		if ($storyID !== null) {
			// select story and story subscription, visit and rating
			$sql = "SELECT		story.*,
						story_rating.rating AS userRating
						".(WCF::getUser()->userID ? ', IF(subscription.userID IS NOT NULL, 1, 0) AS subscribed, enableNotification, emails, story_visit.lastVisitTime' : '')."
				FROM 		sls".SLS_N."_story story
				".((WCF::getUser()->userID) ? ("
				LEFT JOIN 	sls".SLS_N."_story_subscription subscription
				ON 		(subscription.userID = ".WCF::getUser()->userID."
						AND subscription.storyID = ".$storyID.")
				LEFT JOIN 	sls".SLS_N."_story_visit story_visit
				ON 		(story_visit.storyID = story.storyID
						AND story_visit.userID = ".WCF::getUser()->userID.")") : (""))."
				LEFT JOIN 	sls".SLS_N."_story_rating story_rating
				ON 		(story_rating.storyID = story.storyID
						AND ".(WCF::getUser()->userID ? "story_rating.userID = ".WCF::getUser()->userID : "story_rating.ipAddress = '".escapeString(WCF::getSession()->ipAddress)."'").")
				WHERE 		story.storyID = ".$storyID;
			$row = WCF::getDB()->getFirstRow($sql);
		}

		parent::__construct($row);
	}

	/**
	 * Returns the result of the rating of this story.
	 *
	 * @return	mixed		result of the rating of this story
	 */
	public function getRating() {
		if ($this->ratings > 0 && $this->ratings >= STORY_MIN_RATINGS) {
			return $this->rating / $this->ratings;
		}
		return false;
	}

	/**
	 * Returns true, if this story is marked.
	 */
	public function isMarked() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedStories'])) {
			if (in_array($this->storyID, $sessionVars['markedStories'])) return 1;
		}

		return 0;
	}

	/**
	 * Returns the requested chapter, if chapter id was given at creation of the Story object.
	 *
	 * @return	boolean		requested chapter, if chapter id was given at creation of the Story object
	 */
	public function getChapter() {
		return $this->chapter;
	}

	/**
	 * Enters the active user to this story.
	 */
	public function enter($library = null, $refreshSession = true) {
		if (!$this->storyID || $this->movedStoryID) {
			throw new IllegalLinkException();
		}

		if ($library == null || $library->libraryID != $this->libraryID) {
			$library = Library::getLibrary($this->libraryID);
		}

		$library->enter();

		// check permissions
		if ((!$library->getPermission('canReadStory') && (!$library->getPermission('canReadOwnStory') || !$this->userID || $this->userID != WCF::getUser()->userID)) || ($this->isDeleted && !$library->getModeratorPermission('canReadDeletedStory')) || ($this->isDisabled && !$library->getModeratorPermission('canEnableStory'))) {
			throw new PermissionDeniedException();
		}

		// refresh session
		if ($refreshSession) {
			WCF::getSession()->setStoryID($this->storyID);
		}

		// save library
		$this->library = $library;
	}

	/**
	 * Returns true, if the active user can reply this story.
	 */
	public function canReplyStory($library = null) {
		if ($library == null || $library->libraryID != $this->libraryID) {
			if ($this->library !== null) $library = $this->library;
			else $library = Library::getLibrary($this->libraryID);
		}
		return (!$library->isClosed && (($this->isClosed && $library->getModeratorPermission('canReplyClosedStory'))
			|| (!$this->isClosed && ($library->getPermission('canReplyStory') || ($this->userID && $this->userID == WCF::getUser()->userID && $library->getPermission('canReplyOwnStory'))))));
	}

	/**
	 * Subscribes the active user to this story.
	 */
	public function subscribe() {
		if (!$this->subscribed) {
			$sql = "INSERT INTO	sls".SLS_N."_story_subscription
						(userID, storyID, enableNotification)
				VALUES 		(".WCF::getUser()->userID.", ".$this->storyID.", ".WCF::getUser()->enableEmailNotification.")";
			WCF::getDB()->registerShutdownUpdate($sql);
			$this->data['subscribed'] = 1;
			WCF::getSession()->unregister('hasSubscriptions');
		}
	}

	/**
	 * Unsubscribes the active user to this story.
	 */
	public function unsubscribe() {
		if ($this->subscribed) {
			$sql = "DELETE FROM 	sls".SLS_N."_story_subscription
				WHERE 		userID = ".WCF::getUser()->userID."
						AND storyID = ".$this->storyID;
			WCF::getDB()->registerShutdownUpdate($sql);
			$this->data['subscribed'] = 0;
			WCF::getSession()->unregister('hasSubscriptions');
		}
	}

	/**
	 * Updates the subscription of this story for the active user.
	 */
	public function updateSubscription() {
		if ($this->emails > 0) {
			$sql = "UPDATE 	sls".SLS_N."_story_subscription
				SET 	emails = 0
				WHERE 	userID = " . WCF::getUser()->userID . "
					AND storyID = ". $this->storyID;
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}

	/**
	 * Returns the tags of this story.
	 *
	 * @return	array
	 */
	public function getTags($languageIDArray) {
		// include files
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		require_once(SLS_DIR.'lib/data/story/TaggedStory.class.php');

		// get tags
		return TagEngine::getInstance()->getTagsByTaggedObject(new TaggedStory(null, array(
			'storyID' => $this->storyID,
			'taggable' => TagEngine::getInstance()->getTaggable('com.woltlab.sls.story')
		)), $languageIDArray);
	}

	/**
	 * Returns true, if this story is new for the active user.
	 *
	 * @return	boolean		true, if this story is new for the active user
	 */
	public function isNew() {
		if (!$this->movedStoryID && $this->lastChapterTime > $this->lastVisitTime) {
			return true;
		}

		return false;
	}
}
?>