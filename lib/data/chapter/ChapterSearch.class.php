<?php

// wcf imports

require_once(WCF_DIR.'lib/data/message/search/AbstractSearchableMessageType.class.php');



/**
 * An implementation of SearchableMessageType for searching in forum chapters.
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.chapter
 * @category 	Story Library System
 */

class ChapterSearch extends AbstractSearchableMessageType {

	public $messageCache = array();

	public $libraryIDs = array();

	public $storyID = 0;

	public $findStories = SEARCH_FIND_STORIES;

	public $findUserStories = 0;

	public $storyTableJoin = false;

	

	public $libraries = array();

	public $libraryStructure = array();

	public $selectedLibraries = array();

	

	/**

	 * Caches the data of the messages with the given ids.

	 */

	public function cacheMessageData($messageIDs, $additionalData = null) {

		if ($additionalData !== null && isset($additionalData['findStories']) && $additionalData['findStories'] == 1) {

			WCF::getTPL()->assign('findStories', 1);

			

			$sqlStoryVisitSelect = $sqlStoryVisitJoin = $sqlSubscriptionSelect = $sqlSubscriptionJoin = $sqlOwnChaptersSelect = $sqlOwnChaptersJoin = '';

			if (WCF::getUser()->userID != 0) {

				$sqlStoryVisitSelect = ', story_visit.lastVisitTime';

				$sqlStoryVisitJoin = " LEFT JOIN 	sls".SLS_N."_story_visit story_visit 

							ON 		(story_visit.storyID = story.storyID

									AND story_visit.userID = ".WCF::getUser()->userID.")";

				$sqlSubscriptionSelect = ', IF(story_subscription.userID IS NOT NULL, 1, 0) AS subscribed';

				$sqlSubscriptionJoin = " LEFT JOIN 	sls".SLS_N."_story_subscription story_subscription 

							ON 		(story_subscription.userID = ".WCF::getUser()->userID."

									AND story_subscription.storyID = story.storyID)";

				

				if (BOARD_THREADS_ENABLE_OWN_POSTS) {

					$sqlOwnChaptersSelect = "DISTINCT chapter.userID AS ownChapters,";

					$sqlOwnChaptersJoin = "	LEFT JOIN	sls".SLS_N."_chapter chapter

								ON 		(chapter.storyID = story.storyID

										AND chapter.userID = ".WCF::getUser()->userID.")";

				}

			}

			

			$sql = "SELECT		".$sqlOwnChaptersSelect."

						story.*,

						library.libraryID, library.title, library.enableMarkingAsDone

						".$sqlStoryVisitSelect."

						".$sqlSubscriptionSelect."

				FROM		sls".SLS_N."_story story

				LEFT JOIN 	sls".SLS_N."_library library

				ON 		(library.libraryID = story.libraryID)

				".$sqlOwnChaptersJoin."

				".$sqlStoryVisitJoin."

				".$sqlSubscriptionJoin."

				WHERE		story.storyID IN (".$messageIDs.")";

			$result = WCF::getDB()->sendQuery($sql);

			require_once(SLS_DIR.'lib/data/story/StorySearchResult.class.php');

			while ($row = WCF::getDB()->fetchArray($result)) {

				$this->messageCache[$row['storyID']] = array('type' => 'chapter', 'message' => new StorySearchResult(null, $row));

			}

		}

		else {

			$sql = "SELECT		chapter.*,

						story.topic, story.prefix,

						library.libraryID, library.title

				FROM		sls".SLS_N."_chapter chapter

				LEFT JOIN 	sls".SLS_N."_story story

				ON 		(story.storyID = chapter.storyID)

				LEFT JOIN 	sls".SLS_N."_library library

				ON 		(library.libraryID = story.libraryID)

				WHERE		chapter.chapterID IN (".$messageIDs.")";

			$result = WCF::getDB()->sendQuery($sql);

			require_once(SLS_DIR.'lib/data/chapter/ChapterSearchResult.class.php');

			while ($row = WCF::getDB()->fetchArray($result)) {

				$this->messageCache[$row['chapterID']] = array('type' => 'chapter', 'message' => new ChapterSearchResult(null, $row));

			}

		}

	}

	

	/**

	 * @see SearchableMessageType::getMessageData()

	 */

	public function getMessageData($messageID, $additionalData = null) {

		if (isset($this->messageCache[$messageID])) return $this->messageCache[$messageID];

		return null;

	}

	

	/**

	 * Shows chapter specific form elements in the global search form.

	 */

	public function show($form = null) {

		// get unsearchable libraries

		require_once(SLS_DIR.'lib/data/library/Library.class.php');

		$libraries = WCF::getCache()->get('library', 'libraries');

		$unsearchableLibraryIDArray = array();

		foreach ($libraries as $library) {

			if (!$library->searchable) $unsearchableLibraryIDArray[] = $library->libraryID;

		}

		

		// get existing values

		if ($form !== null && isset($form->searchData['additionalData']['chapter'])) {

			$this->libraryIDs = $form->searchData['additionalData']['chapter']['libraryIDs'];

		}

		

		WCF::getTPL()->assign(array(

			'libraryOptions' => Library::getLibrarySelect(array('canViewLibrary', 'canEnterLibrary', 'canReadStory'), true, false, $unsearchableLibraryIDArray),

			'libraryIDs' => $this->libraryIDs,

			'storyID' => $this->storyID,

			'selectAllLibraries' => count($this->libraryIDs) == 0 || $this->libraryIDs[0] == '*',

			'findStories' => $this->findStories,

			'findUserStories' => $this->findUserStories

		));

	}

	

	/**

	 * Reads the given form parameters.

	 */

	protected function readFormParameters($form = null) {

		// get existing values

		if ($form !== null && isset($form->searchData['additionalData']['chapter'])) {

			$this->libraryIDs = $form->searchData['additionalData']['chapter']['libraryIDs'];

			$this->findStories = $form->searchData['additionalData']['chapter']['findStories'];

			$this->findUserStories = $form->searchData['additionalData']['chapter']['findUserStories'];

			$this->storyID = $form->searchData['additionalData']['chapter']['storyID'];

		}

		

		// get new values

		if (isset($_POST['libraryIDs']) && is_array($_POST['libraryIDs'])) {

			$this->libraryIDs = ArrayUtil::toIntegerArray($_POST['libraryIDs']);

		}

		

		if (isset($_POST['findStories'])) {

			$this->findStories = intval($_POST['findStories']);

		}

		

		if (isset($_REQUEST['findUserStories'])) {

			$this->findUserStories = intval($_REQUEST['findUserStories']);

			if ($this->findUserStories) $this->findStories = 1;

		}

		

		if (isset($_POST['storyID'])) {

			$this->storyID = intval($_POST['storyID']);

		}

	}

	

	private function includeSubLibraries($libraryID) {

		if (isset($this->libraryStructure[$libraryID])) {

			foreach ($this->libraryStructure[$libraryID] as $childLibraryID) {

				if (!isset($this->selectedLibraries[$childLibraryID])) {

					$this->selectedLibraries[$childLibraryID] = $this->libraries[$childLibraryID];

					

					// include children

					$this->includeSubLibraries($childLibraryID);

				}

			}

		}

	}

	

	/**

	 * Returns the conditions for a search in the table of this search type.

	 */

	public function getConditions($form = null) {

		$this->readFormParameters($form);

		

		$libraryIDs = $this->libraryIDs;

		if (count($libraryIDs) && $libraryIDs[0] == '*') $libraryIDs = array();

		

		// remove empty elements

		foreach ($libraryIDs as $key => $libraryID) {

			if ($libraryID == '-') unset($libraryIDs[$key]);

		}

		

		// get all libraries from cache

		require_once(SLS_DIR.'lib/data/library/Library.class.php');

		$this->libraries = WCF::getCache()->get('library', 'libraries');

		$this->libraryStructure = WCF::getCache()->get('library', 'libraryStructure');

		$this->selectedLibraries = array();

		

		// check whether the selected library does exist

		foreach ($libraryIDs as $libraryID) {

			if (!isset($this->libraries[$libraryID]) || !$this->libraries[$libraryID]->searchable) {

				throw new UserInputException('libraryIDs', 'notValid');

			}

			

			if (!isset($this->selectedLibraries[$libraryID])) {

				$this->selectedLibraries[$libraryID] = $this->libraries[$libraryID];

				

				// include children

				$this->includeSubLibraries($libraryID);

			}

		}

		if (count($this->selectedLibraries) == 0) $this->selectedLibraries = $this->libraries;

		

		// check permission of the active user

		foreach ($this->selectedLibraries as $library) {

			if (WCF::getUser()->isIgnoredLibrary($library->libraryID) || !$library->getPermission() || !$library->getPermission('canEnterLibrary') || !$library->getPermission('canReadStory') || !$library->searchable) {

				unset($this->selectedLibraries[$library->libraryID]);

			}

		}

		

		if (count($this->selectedLibraries) == 0) {

			throw new PermissionDeniedException();

		}

		

		// build library id list

		$selectedLibraryIDs = '';

		if (count($this->selectedLibraries) != count($this->libraries)) {

			foreach ($this->selectedLibraries as $library) {

				if (!empty($selectedLibraryIDs)) $selectedLibraryIDs .= ',';

				$selectedLibraryIDs .= $library->libraryID;

			}

		}

		

		// build final condition

		require_once(WCF_DIR.'lib/system/database/ConditionBuilder.class.php');

		$condition = new ConditionBuilder(false);

		

		// library ids

		if (!empty($selectedLibraryIDs)) {

			$this->storyTableJoin = true;

			$condition->add('story.storyID = messageTable.storyID');

			$condition->add('story.libraryID IN ('.$selectedLibraryIDs.')');

		}

		else if ($this->findStories || count(WCF::getSession()->getVisibleLanguageIDArray()) || $this->storyTableJoin) {

			$condition->add('story.storyID = messageTable.storyID');

		}

		

		// find user stories

		if ($this->findUserStories && $form !== null && ($userIDs = $form->getUserIDs())) {

			$condition->add('story.userID IN ('.implode(',', $userIDs).')');

		}

		

		// story id

		if ($this->storyID != 0) {

			$condition->add('messageTable.storyID = '.$this->storyID);

		}

		$condition->add('messageTable.isDeleted = 0');

		$condition->add('messageTable.isDisabled = 0');

		// language

		if (count(WCF::getSession()->getVisibleLanguageIDArray())) $condition->add('story.languageID IN ('.implode(',', WCF::getSession()->getVisibleLanguageIDArray()).')');

		

		// return sql condition

		return '('.$condition->get().')'.($this->findStories ? '/* findStories */' : '').($this->findUserStories ? '/* findUserStories */' : '');

	}

	

	/**

	 * @see SearchableMessageType::getJoins()

	 */

	public function getJoins() {

		return (($this->storyTableJoin || $this->findStories || count(WCF::getSession()->getVisibleLanguageIDArray())) ? ", sls".SLS_N."_story story" : '');

	}

	

	/**

	 * Returns the database table name for this search type.

	 */

	public function getTableName() {

		return 'sls'.SLS_N.'_chapter';

	}

	

	/**

	 * Returns the message id field name for this search type.

	 */

	public function getIDFieldName() {

		return ($this->findStories ? 'story.storyID' : 'chapterID');

	}

	

	/**

	 * @see SearchableMessageType::getAdditionalData()

	 */

	public function getAdditionalData() {

		return array(

			'findStories' => $this->findStories,

			'findUserStories' => $this->findUserStories,

			'libraryIDs' => $this->libraryIDs,

			'storyID' => $this->storyID

		);

	}

	

	/**

	 * @see SearchableMessageType::getFormTemplateName()

	 */

	public function getFormTemplateName() {

		return 'searchChapter';

	}

	

	/**

	 * @see SearchableMessageType::getResultTemplateName()

	 */

	public function getResultTemplateName() {

		return 'searchResultChapter';

	}

}

?>

