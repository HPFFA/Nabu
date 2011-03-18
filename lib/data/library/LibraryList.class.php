<?php
// sls imports
require_once(SLS_DIR.'lib/data/library/Library.class.php');
require_once(SLS_DIR.'lib/data/user/SLSUser.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/user/group/Group.class.php');

/**
 * Shows the list of libraries on the start page.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.library
 * @category 	Story Library System
 */
class LibraryList {
	public $maxDepth = 2;
	protected $libraryID = 0;
	protected $libraryStructure;
	protected $libraries;
	protected $libraryList = array();
	protected $lastChapters = array();
	protected $subLibraries = array();
	protected $newChapters = array();
	protected $unreadStoriesCount = array();
	protected $lastChapterTimes = array();
	protected $inheritHiddenLibraries = array();
	protected $libraryStats = array();
	protected $visibleSQL = '';

	/**
	 * Creates a new LibraryListPage object.
	 *
	 * The libraryID determines, which sublibraries are rendered.
	 * 0 means, that all libraries are rendered.
	 *
	 * @param 	integer		$libraryID		id of the parent library.
	 */
	public function __construct($libraryID = 0) {
		$this->libraryID = $libraryID;
	}

	/**
	 * Handles the request for hiding a library.
	 */
	public function readParameters() {
		if (isset($_REQUEST['closeCategory'])) {
			WCF::getUser()->closeCategory(intval($_REQUEST['closeCategory']), 1);
		}
		if (isset($_REQUEST['openCategory'])) {
			WCF::getUser()->closeCategory(intval($_REQUEST['openCategory']), -1);
		}
	}

	/**
	 * Gets the chapter time of the last unread chapter for each library.
	 */
	protected function getLastChapterTimes() {
		$sql = "SELECT 		libraryID, story.storyID, story.lastChapterTime
					".((WCF::getUser()->userID) ? (", story_visit.lastVisitTime") : (", 0 AS lastVisitTime"))."
			FROM 		sls".SLS_N."_story story
			".((WCF::getUser()->userID) ? ("
			LEFT JOIN 	sls".SLS_N."_story_visit story_visit
			ON 		(story_visit.storyID = story.storyID AND story_visit.userID = ".WCF::getUser()->userID.")
			") : (""))."
			WHERE 		story.lastChapterTime > ". WCF::getUser()->getLastMarkAllAsReadTime()."
					AND isDeleted = 0
					AND isDisabled = 0
					AND movedStoryID = 0"
					.(count(WCF::getSession()->getVisibleLanguageIDArray()) ? " AND story.languageID IN (".implode(',', WCF::getSession()->getVisibleLanguageIDArray()).")" : "");
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (WCF::getUser()->userID) $lastVisitTime = $row['lastVisitTime'];
			else $lastVisitTime = WCF::getUser()->getStoryVisitTime($row['storyID']);

			if ($row['lastChapterTime'] > $lastVisitTime) {
				// count unread stories
				if ($row['lastChapterTime'] > WCF::getUser()->getLibraryVisitTime($row['libraryID'])) {
					if (!isset($this->unreadStoriesCount[$row['libraryID']])) $this->unreadStoriesCount[$row['libraryID']] = 0;
					$this->unreadStoriesCount[$row['libraryID']]++;
				}

				// save last chapter time
				if (!isset($this->lastChapterTimes[$row['libraryID']]) || $row['lastChapterTime'] > $this->lastChapterTimes[$row['libraryID']]) {
					$this->lastChapterTimes[$row['libraryID']] = $row['lastChapterTime'];
				}
			}
		}
	}

	/**
	 * Renders the list of libraries on the index page or the list of sublibraries on a library page.
	 */
	public function renderLibraries() {
		// get library structure from cache
		$this->libraryStructure = WCF::getCache()->get('library', 'libraryStructure');

		if (!isset($this->libraryStructure[$this->libraryID])) {
			// the library with the given library id has no children
			WCF::getTPL()->assign('libraries', array());
			return;
		}

		$this->readParameters();
		$this->getLastChapterTimes();

		// get libraries from cache
		$this->libraries = WCF::getCache()->get('library', 'libraries');

		// show newest chapters on index
		if (LIBRARY_LIST_ENABLE_LAST_CHAPTER) {
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
		}
		// stats
		if (LIBRARY_LIST_ENABLE_STATS) {
			$this->libraryStats = WCF::getCache()->get('libraryData', 'counts');
		}

		$this->clearLibraryList($this->libraryID);
		$this->makeLibraryList($this->libraryID, $this->libraryID);
		WCF::getTPL()->assign('libraries', $this->libraryList);
		WCF::getTPL()->assign('newChapters', $this->newChapters);
		WCF::getTPL()->assign('unreadStoriesCount', $this->unreadStoriesCount);

		// show newest chapters on index
		if (LIBRARY_LIST_ENABLE_LAST_CHAPTER) {
			WCF::getTPL()->assign('lastChapters', $this->lastChapters);
		}
		// show sublibraries on index
		if (LIBRARY_LIST_ENABLE_SUB_LIBRARIES) {
			WCF::getTPL()->assign('subLibraries', $this->subLibraries);
		}
		// moderators
		if (LIBRARY_LIST_ENABLE_MODERATORS) {
			WCF::getTPL()->assign('moderators', WCF::getCache()->get('library', 'moderators'));
		}
		// stats
		if (LIBRARY_LIST_ENABLE_STATS) {
			WCF::getTPL()->assign('libraryStats', $this->libraryStats);
		}
	}

	/**
	 * Compares sublibraries for sublibrary sorting.
	 *
	 * @param	Library		$libraryA
	 * @param	Library		$libraryB
	 * @return	integer
	 */
	protected static function compareSubLibraries($libraryA, $libraryB) {
		return strcoll($libraryA->title, $libraryB->title);
	}

	/**
	 * Removes invisible libraries from library list.
	 *
	 * @param	integer		parentID		render the sublibraries of the library with the given id
	 */
	protected function clearLibraryList($parentID = 0) {
		if (!isset($this->libraryStructure[$parentID])) return;

		// remove invisible libraries
		foreach ($this->libraryStructure[$parentID] as $key => $libraryID) {
			$library = $this->libraries[$libraryID];
			if (WCF::getUser()->isIgnoredLibrary($libraryID) || !$library->getPermission() || $library->isInvisible) {
				unset($this->libraryStructure[$parentID][$key]);
				continue;
			}

			$this->clearLibraryList($libraryID);
		}

		if (!count($this->libraryStructure[$parentID])) {
			unset($this->libraryStructure[$parentID]);
		}
	}

	/**
	 * Renders one level of the library structure.
	 *
	 * @param	integer		$parentID		render the sublibraries of the library with the given id
	 * @param	integer		$subLibraryFrom		helping variable for displaying the invisible sublibraries as a link under the parent library
	 * @param	integer		$depth			the depth of the current level
	 * @param	integer		$openParents		helping variable for rendering the html list in the librarylist template
	 * @param	integer		$parentClosed		determines whether a parent category is collapsed
	 * @param	boolean		$showSubLibraries
	 */
	protected function makeLibraryList($parentID = 0, $subLibrariesFrom = 0, $depth = 1, $openParents = 0, $parentClosed = 0, $showSubLibraries = true) {
	    if (!isset($this->libraryStructure[$parentID])) return;

		$i = 0;
		$count = count($this->libraryStructure[$parentID]);
		foreach ($this->libraryStructure[$parentID] as $libraryID) {
			$library = $this->libraries[$libraryID];
			if (!isset($this->lastChapterTimes[$libraryID])) {
				$this->lastChapterTimes[$libraryID] = 0;
			}

			// librarylist depth on index
			$updateNewChapters = 0;
			$updateLibraryInfo = 1;
			$childrenOpenParents = $openParents + 1;
			$newSubLibrariesFrom = $subLibrariesFrom;
			if ($parentClosed == 0 && (WCF::getUser()->isClosedCategory($parentID) == -1 || $depth <= $this->maxDepth) && $subLibrariesFrom == $parentID) {
				$updateLibraryInfo = 0;
				$open = WCF::getUser()->isClosedCategory($libraryID) == -1 || ($depth + 1 <= $this->maxDepth && WCF::getUser()->isClosedCategory($libraryID) != 1);
				$hasChildren = isset($this->libraryStructure[$libraryID]) && $open;
				$last = ($i == ($count - 1));
				if ($hasChildren && !$last) $childrenOpenParents = 1;
				$this->libraryList[] = array('open' => $open, 'depth' => $depth, 'hasChildren' => $hasChildren, 'openParents' => ((!$hasChildren && $last) ? ($openParents) : (0)), 'library' => $library);
				$newSubLibrariesFrom = $libraryID;
			}
			// show sublibraries on index
			else if ($parentClosed == 0 && LIBRARY_LIST_ENABLE_SUB_LIBRARIES && $showSubLibraries) {
				$this->subLibraries[$subLibrariesFrom][$libraryID] = $library;
			}
			// library is invisible; show new chapters in parent library
			else {
				$updateNewChapters = 1;
			}

			// make next level of the library list
			$this->makeLibraryList($libraryID, $newSubLibrariesFrom, $depth + 1, $childrenOpenParents, $parentClosed || WCF::getUser()->isClosedCategory($libraryID) == 1, $showSubLibraries && $library->showSubLibraries);

			// user can not enter library; unset last chapter
			if (!$library->getPermission('canEnterLibrary') && isset($this->lastChapters[$libraryID])) {
				unset($this->lastChapters[$libraryID]);
			}

			// show newest chapters on index
			if ($updateLibraryInfo && $parentID != 0 && LIBRARY_LIST_ENABLE_LAST_CHAPTER) {
				if (isset($this->lastChapters[$libraryID])) {
					if (!isset($this->lastChapters[$parentID]) || $this->lastChapters[$libraryID]->lastChapterTime > $this->lastChapters[$parentID]->lastChapterTime) {
						$this->lastChapters[$parentID] = $this->lastChapters[$libraryID];
					}
				}
			}

			// update parent stats
			if ($updateLibraryInfo && $parentID != 0 && LIBRARY_LIST_ENABLE_STATS) {
				if (isset($this->libraryStats[$parentID]) && isset($this->libraryStats[$libraryID])) {
					$this->libraryStats[$parentID]['stories'] += $this->libraryStats[$libraryID]['stories'];
					$this->libraryStats[$parentID]['chapters'] += $this->libraryStats[$libraryID]['chapters'];
				}
			}

			
			// library has unread chapters
			if ($this->lastChapterTimes[$libraryID] > WCF::getUser()->getLibraryVisitTime($libraryID)) {
				$this->newChapters[$libraryID] = true;
				if ($updateNewChapters) {
					// update unread story count
					if (isset($this->unreadStoriesCount[$libraryID])) {
						if (!isset($this->unreadStoriesCount[$parentID])) $this->unreadStoriesCount[$parentID] = 0;
						$this->unreadStoriesCount[$parentID] += $this->unreadStoriesCount[$libraryID];
					}

					// update last chapter time
					if (!isset($this->lastChapterTimes[$parentID]) || $this->lastChapterTimes[$parentID] < $this->lastChapterTimes[$libraryID]) {
						$this->lastChapterTimes[$parentID] = $this->lastChapterTimes[$libraryID];
					}
				}
			}
			else {
				$this->newChapters[$libraryID] = false;
			}

			$i++;
		}
	}
}
?>