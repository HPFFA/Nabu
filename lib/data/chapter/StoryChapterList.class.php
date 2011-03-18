<?php
// sls imports
require_once(SLS_DIR.'lib/data/chapter/DependentChapterList.class.php');

/**
 * StoryChapterList provides extended functions for displaying a list of chapters.
 * Including user profile information like avatar, number user chapters, special profile fields etc.
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.chapter
 * @category 	Story Library System
 */
class StoryChapterList extends DependentChapterList {
	// data
	public $maxChapterTime = 0;
	public $userData = array();
	public $userOptions;

	/**
	 * @see ChapterList::initDefaultSQL();
	 */
	protected function initDefaultSQL() {
		parent::initDefaultSQL();

		// default selects / joins
		$this->sqlSelects = "user_option.*, sls_user.*, user.*, rank.*, IFNULL(user.username, chapter.authorname) AS username,";
		$this->sqlJoins = "	LEFT JOIN 	wcf".WCF_N."_user user
					ON 		(user.userID = chapter.userID)
					LEFT JOIN 	sls".SLS_N."_user sls_user
					ON 		(sls_user.userID = chapter.userID)
					LEFT JOIN 	wcf".WCF_N."_user_option_value user_option
					ON		(user_option.userID = chapter.userID)
					LEFT JOIN 	wcf".WCF_N."_user_rank rank
					ON		(rank.rankID = user.rankID)";

		if (CHAPTER_SIDEBAR_ENABLE_AVATAR) {
			$this->sqlSelects .= 'avatar.avatarID, avatar.avatarExtension, avatar.width, avatar.height,';
			$this->sqlJoins .= ' LEFT JOIN wcf'.WCF_N.'_avatar avatar ON (avatar.avatarID = user.avatarID) ';
		}
	}

	/**
	 * @see ChapterList::readChapterIDs()
	 */
	protected function readChapterIDs() {
		$sql = "SELECT		chapterID
			FROM		sls".SLS_N."_chapter chapter
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : "")."
			ORDER BY	".$this->sqlOrderBy;
		$result = WCF::getDB()->sendQuery($sql, $this->limit, $this->offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			// chapter id
			if (!empty($this->chapterIDs)) $this->chapterIDs .= ',';
			$this->chapterIDs .= $row['chapterID'];

		}

	}

	/**
	 * @see ChapterList::readChapters()
	 */
	public function readChapters() {
		parent::readChapters();

		// calculate max chapter time
		foreach ($this->chapters as $chapter) {
			if ($chapter->time > $this->maxChapterTime) $this->maxChapterTime = $chapter->time;
		}
	}

	
}
?>