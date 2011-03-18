<?php
// wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Extends the search form by forum specific search options.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.event.listener
 * @category 	Story Library System
 */
class ExtendedSearchFormListener implements EventListener {
	public $findStories = 0;
	public $findUserStories = 0;

	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($eventName == 'readParameters') {
			// handle special search options here
			$action = '';
			if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
			if (empty($action)) return;
			
			// get accessible library ids
			require_once(SLS_DIR.'lib/data/library/Library.class.php');
			$libraryIDArray = Library::getAccessibleLibraryIDArray(array('canViewLibrary', 'canEnterLibrary', 'canReadStory'));
			foreach ($libraryIDArray as $key => $libraryID) {
				if (WCF::getUser()->isIgnoredLibrary($libraryID)) {
					unset($libraryIDArray[$key]);
				}
			}
			if (!count($libraryIDArray)) return;
			
			switch ($action) {
				case 'unread':
					$sql = "SELECT		story.storyID
						FROM		sls".SLS_N."_story story
						WHERE		story.libraryID IN (".implode(',', $libraryIDArray).")
								AND story.lastChapterTime > ".WCF::getUser()->getLastMarkAllAsReadTime()."
								".(WCF::getUser()->userID ? "
								AND story.lastChapterTime > IFNULL((
									SELECT	lastVisitTime
									FROM 	sls".SLS_N."_story_visit
									WHERE 	storyID = story.storyID
										AND userID = ".WCF::getUser()->userID."
								), 0)
								AND story.lastChapterTime > IFNULL((
									SELECT	lastVisitTime
									FROM 	sls".SLS_N."_library_visit
									WHERE 	libraryID = story.libraryID
										AND userID = ".WCF::getUser()->userID."
								), 0)
								" : '')."
								AND story.isDeleted = 0
								AND story.isDisabled = 0
								".(count(WCF::getSession()->getVisibleLanguageIDArray()) ? " AND languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")" : "")."
								AND story.movedStoryID = 0
						ORDER BY	story.lastChapterTime DESC";
					break;
					
				case 'newChaptersSince':
					$since = TIME_NOW;
					if (isset($_REQUEST['since'])) $since = intval($_REQUEST['since']);
					
					$sql = "SELECT		story.storyID
						FROM		sls".SLS_N."_story story
						WHERE		story.libraryID IN (".implode(',', $libraryIDArray).")
								AND story.lastChapterTime > ".$since."
								AND story.isDeleted = 0
								AND story.isDisabled = 0
								".(count(WCF::getSession()->getVisibleLanguageIDArray()) ? " AND languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")" : "")."
								AND story.movedStoryID = 0
						ORDER BY	story.lastChapterTime DESC";
				
					break;
					
				case 'unreplied':
					$sql = "SELECT		storyID
						FROM		sls".SLS_N."_story
						WHERE		libraryID IN (".implode(',', $libraryIDArray).")
								AND isDeleted = 0
								AND isDisabled = 0
								AND movedStoryID = 0
								".(count(WCF::getSession()->getVisibleLanguageIDArray()) ? " AND languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")" : "")."
								AND replies = 0
						ORDER BY	lastChapterTime DESC";
					break;
						
				case '24h':
					$sql = "SELECT		storyID
						FROM		sls".SLS_N."_story
						WHERE		libraryID IN (".implode(',', $libraryIDArray).")
								AND lastChapterTime > ".(TIME_NOW - 86400)."
								AND isDeleted = 0
								AND isDisabled = 0
								".(count(WCF::getSession()->getVisibleLanguageIDArray()) ? " AND languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")" : "")."
								AND movedStoryID = 0
						ORDER BY	lastChapterTime DESC";
					break;
					
				default: return;
			}
			
			// build search hash
			$searchHash = StringUtil::getHash($sql);
			
			// execute query
			$matches = array();
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$matches[] = array('messageID' => $row['storyID'], 'messageType' => 'chapter');
			}
			
			// result is empty
			if (count($matches) == 0) {
				throw new NamedUserException(WCF::getLanguage()->get('sls.search.error.noMatches'));
			}
			
			// save result in database
			$searchData = array('packageID' => PACKAGE_ID, 'query' => '', 'result' => $matches, 'additionalData' => array('chapter' => array('findStories' => 1)), 'sortOrder' => 'DESC', 'sortField' => 'time');
			$searchData = serialize($searchData);
			
			$sql = "INSERT INTO	wcf".WCF_N."_search
						(userID, searchData, searchDate, searchType, searchHash)
				VALUES		(".WCF::getUser()->userID.",
						'".escapeString($searchData)."',
						".TIME_NOW.",
						'messages',
						'".$searchHash."')";
			WCF::getDB()->sendQuery($sql);
			$searchID = WCF::getDB()->getInsertID();
			
			// forward to result page
			HeaderUtil::redirect('index.php?form=Search&searchID='.$searchID.SID_ARG_2ND_NOT_ENCODED);
			exit;
		}
		else if ($eventName == 'readFormParameters') {
			if (isset($_POST['findStories'])) $this->findStories = intval($_POST['findStories']);
			if (isset($_REQUEST['findUserStories'])) $this->findUserStories = intval($_REQUEST['findUserStories']);
			if ($this->findUserStories == 1) $this->findStories = 1;
			
			// handle findStories option
			if ($this->findStories == 1 && (!count($eventObj->types) || in_array('chapter', $eventObj->types))) {
				// remove all other searchable message types
				// findStories only supports chapter search
				$eventObj->types = array('chapter');
			}
			else {
				$this->findStories = /*$_POST['findStories'] =*/ 0;
			}
		}
		else if ($eventName == 'assignVariables') {
			if ($eventObj instanceof SearchResultPage) {
				$html = '<div class="floatedElement">
						<label for="findStories">' . WCF::getLanguage()->get('sls.search.results.display') . '</label>
						<select name="findStories" id="findStories">
							<option value="0">' . WCF::getLanguage()->get('sls.search.results.display.chapter') . '</option>
							<option value="1"' . ($eventObj->additionalData['chapter']['findStories'] == 1 ? ' selected="selected"' : '') . '>' . WCF::getLanguage()->get('sls.search.results.display.story') . '</option>
						</select>
					</div>';
				WCF::getTPL()->append('additionalDisplayOptions', $html);
			}
			else {
				$html = '<div class="floatedElement">
						<label for="findStories">' . WCF::getLanguage()->get('sls.search.results.display') . '</label>
						<select name="findStories" id="findStories">
							<option value="0"' . ($this->findStories == 0 ? ' selected="selected"' : '') . '>' . WCF::getLanguage()->get('sls.search.results.display.chapter') . '</option>
							<option value="1"' . ($this->findStories == 1 ? ' selected="selected"' : '') . '>' . WCF::getLanguage()->get('sls.search.results.display.story') . '</option>
						</select>
					</div>';
				WCF::getTPL()->append('additionalDisplayOptions', $html);
				WCF::getTPL()->append('additionalAuthorOptions', '<label><input type="checkbox" name="findUserStories" value="1"'.($this->findUserStories == 1 ? ' checked="checked"' : '').'/> '.WCF::getLanguage()->get('sls.search.findUserStories').'</label>');
			}
		}
	}
}
?>
