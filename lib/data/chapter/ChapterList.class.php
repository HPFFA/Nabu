<?php
require_once(SLS_DIR.'lib/data/chapter/ViewableChapter.class.php');

/**
 * PostList is a default implementation for displaying a list of chapters.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.chapter
 * @category 	Story Library System
 */
class PostList {
	// parameters
	public $limit = 20, $offset = 0;

	// data
	public $chapters = array();
	public $chapterIDs = '';
	public $story = null;

	// sql plugin options
	public $sqlConditions = '';
	public $sqlConditionJoins = '';
	public $sqlOrderBy = 'chapter.orderby';
	public $sqlSelects = '';
	public $sqlJoins = '';

	/**
	 * Creates a new PostList object.
	 */
	public function __construct() {
		// default sql conditions
		$this->initDefaultSQL();
	}

	/**
	 * Fills the sql parameters with default values.
	 */
	protected function initDefaultSQL() {}

	/**
	 * Counts posts.
	 *
	 * @return	integer
	 */
	public function countChapters() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	sls".SLS_N."_chapter chapter
			".$this->sqlConditionJoins."
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : "");
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * Gets chapter ids.
	 */
	protected function readChapterIDs() {
		$sql = "SELECT		chpater.chapterID
			FROM		sls".SLS_N."_chapter chapter
			".$this->sqlConditionJoins."
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : "")."
			ORDER BY	".$this->sqlOrderBy;
		$result = WCF::getDB()->sendQuery($sql, $this->limit, $this->offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($this->chapterIDs)) $this->chapterIDs .= ',';
			$this->chapterIDs .= $row['chapterID'];


		}

	}


	/**
	 * Reads a list of chapters.
	 */
	public function readChapters() {
		// get chapter ids
		$this->readChapterIDs();
		if (empty($this->chapterIDs)) return false;

		// get chapters
		$sql = $this->buildQuery();
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->chapters[] = new ViewableChapter(null, $row, $this->story);
		}
	}

	/**
	 * Builds the main sql query for selecting posts.
	 *
	 * @return	string
	 */
	protected function buildQuery() {
		return "SELECT		chapter.*,
					".$this->sqlSelects."
					chapter.username
			FROM		sls".SLS_N."_chapter chapter
			".$this->sqlJoins."
			WHERE		chapter.chapterID IN (".$this->chapterIDs.")
			ORDER BY	".$this->sqlOrderBy;
	}
}
?>