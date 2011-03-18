<?php
require_once(SLS_DIR.'lib/data/story/ViewableStory.class.php');

/**
 * StoryList is a default implementation for displaying a list of stories.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.story
 * @category 	Story Library System
 */
class StoryList {
	// parameters
	public $limit = 20, $offset = 0;

	// data
	public $stories = array();
	public $storyIDs = '';

	// sql plugin options
	public $sqlConditions = '';
	public $sqlOrderBy = 'story.lastChapterTime DESC';
	public $sqlSelects = '';
	public $sqlJoins = '';
	public $sqlSelectRating = '';
	public $enableRating = STORY_ENABLE_RATING;

	/**
	 * Creates a new StoryList object.
	 */
	public function __construct() {
		// default sql conditions
		$this->initDefaultSQL();
	}

	/**
	 * Fills the sql parameters with default values.
	 */
	protected function initDefaultSQL() {
		if (WCF::getUser()->userID != 0) {
			// own chapters
			if (LIBRARY_STORIES_ENABLE_OWN_CHAPTERS) {
				$this->sqlSelects = "DISTINCT chapter.userID AS ownChapters,";
				$this->sqlJoins = "	LEFT JOIN	sls".SLS_N."_chapter chapter
							ON 		(chapter.storyID = story.storyID
									AND chapter.userID = ".WCF::getUser()->userID.")";
			}

			// last visit time
			$this->sqlSelects .= 'story_visit.lastVisitTime,';
			$this->sqlJoins .= "	LEFT JOIN 	sls".SLS_N."_story_visit story_visit
						ON 		(story_visit.storyID = story.storyID
								AND story_visit.userID = ".WCF::getUser()->userID.")";

			// subscriptions
			$this->sqlSelects .= 'IF(story_subscription.userID IS NOT NULL, 1, 0) AS subscribed,';
			$this->sqlJoins .= "	LEFT JOIN 	sls".SLS_N."_story_subscription story_subscription
						ON 		(story_subscription.userID = ".WCF::getUser()->userID."
								AND story_subscription.storyID = story.storyID)";
		}

		// ratings
		if ($this->enableRating) {
			$this->sqlSelectRating = "if (ratings>0 AND ratings>=".STORY_MIN_RATINGS.",rating/ratings,0) AS ratingResult,";
			$this->sqlSelects .= $this->sqlSelectRating;
		}
	}

	/**
	 * Counts stories.
	 *
	 * @return	integer
	 */
	public function countStories() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	sls".SLS_N."_story story
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : "");
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * Gets story ids.
	 */
	protected function readStoryIDs() {
		$sql = "SELECT		".$this->sqlSelectRating."
					story.storyID
			FROM		sls".SLS_N."_story story
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : "")."
			ORDER BY	".$this->sqlOrderBy;
		$result = WCF::getDB()->sendQuery($sql, $this->limit, $this->offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($this->storyIDs)) $this->storyIDs .= ',';
			$this->storyIDs .= $row['storyID'];
		}
	}

	/**
	 * Reads a list of stories.
	 */
	public function readStories() {
		// get chapter ids
		$this->readStoryIDs();
		if (empty($this->storyIDs)) return false;

		// get stories
		$sql = $this->buildQuery();
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->stories[] = new ViewableStory(null, $row);
		}
	}

	/**
	 * Builds the main sql query for selecting stories.
	 *
	 * @return	string
	 */
	protected function buildQuery() {
		return "SELECT		".$this->sqlSelects."
					story.*
			FROM 		sls".SLS_N."_story story
			".$this->sqlJoins."
			WHERE		story.storyID IN (".$this->storyIDs.")
			ORDER BY	".$this->sqlOrderBy;
	}
}
?>