<?php
// sls imports
require_once(SLS_DIR.'lib/data/library/Library.class.php');

/**
 * Shows the list of subscribed libraries.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.library
 * @category 	Story Library System
 */
class SubscribedLibraryList {
	public $libraries = array();
	public $unreadStoriesCount = array();
	public $lastChapters = array();
	
	/**
	 * Gets unread stories of subscribed libraries.
	 */
	protected function readUnreadStories() {
		$sql = "SELECT 		libraryID, story.storyID, story.lastChapterTime, story_visit.lastVisitTime
			FROM 		sls".SLS_N."_story story
			LEFT JOIN 	sls".SLS_N."_story_visit story_visit
			ON 		(story_visit.storyID = story.storyID AND story_visit.userID = ".WCF::getUser()->userID.")
			WHERE 		story.lastChapterTime > ". WCF::getUser()->getLastMarkAllAsReadTime()."
					AND isDeleted = 0
					AND isDisabled = 0
					AND movedStoryID = 0"
					.(count(WCF::getSession()->getVisibleLanguageIDArray()) ? " AND story.languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")" : "")."
					AND libraryID IN (
						SELECT	libraryID
						FROM	sls".SLS_N."_library_subscription
						WHERE	userID = ".WCF::getUser()->userID."
					)";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['lastChapterTime'] > $row['lastVisitTime'] && $row['lastChapterTime'] > WCF::getUser()->getLibraryVisitTime($row['libraryID'])) {
				if (!isset($this->unreadStoriesCount[$row['libraryID']])) $this->unreadStoriesCount[$row['libraryID']] = 0;
				$this->unreadStoriesCount[$row['libraryID']]++;
			}
		}
	}
	
	/**
	 * Gets subscribed libraries.
	 */
	protected function readLibraries() {
		$sql = "SELECT		library.*
			FROM		sls".SLS_N."_library_subscription subscription
			LEFT JOIN	sls".SLS_N."_library library
			ON		(library.libraryID = subscription.libraryID)
			WHERE		subscription.userID = ".WCF::getUser()->userID."
			ORDER BY	library.title";
		$this->libraries = WCF::getDB()->getResultList($sql);
	}

	/**
	 * Renders the list of libraries.
	 */
	public function renderLibraries() {
		// get unread stories
		$this->readUnreadStories();
		
		// get libraries
		$this->readLibraries();

		// assign data
		WCF::getTPL()->assign('libraries', $this->libraries);
		WCF::getTPL()->assign('unreadStoriesCount', $this->unreadStoriesCount);
		
		// show newest chapters
		if (BOARD_LIST_ENABLE_LAST_POST) {
			$lastChapters = WCF::getCache()->get('libraryData', 'lastChapters');
			
			if (is_array($lastChapters)) {
				$visibleLanguages = false;
				if (count(WCF::getSession()->getVisibleLanguageIDArray())) {
					$visibleLanguages = WCF::getSession()->getVisibleLanguageIDArray();
				}
				
				foreach ($lastChapters as $libraryID => $languages) {
					foreach ($languages as $languageID => $row) {
						if (!$languageID || !$visibleLanguages || in_array($languageID, $visibleLanguages)) {
							$this->lastChapters[$row['libraryID']] = new DatabaseObject($row);
							continue 2;
						}
					}
				}
			}
			
			WCF::getTPL()->assign('lastChapters', $this->lastChapters);
		}
		
		// stats
		if (BOARD_LIST_ENABLE_STATS) {
			WCF::getTPL()->assign('libraryStats', WCF::getCache()->get('libraryData', 'counts'));
		}
	}
}
?>
