<?php

//wcf importe
require_once(WCF_DIR . 'lib/data/DatabaseObject.class.php');

/**
 * Represents a library in the archiv
 *
 * A library is a container for stories or other libraries
 * @author Jana Pape
 * @copyright 2010
 * @package de.hpffa.sls
 * @subpackage data.library
 * @category Story Library System
 * */
class Library extends DatabaseObject {

	protected $parentLibraries = null;
	protected $clicks = null;
	protected $stories = null;
	protected $chapters = null;
	protected $chaptersPerDay = null;
	protected static $libraries = null;
	protected static $librarySelect;
	protected static $libraryStructure = null;
	protected $characters = array();
	protected $genres = array();

	/**
	 * Defines that a library acts as a container for stories.
	 */
	const TYPE_LIBRARY = 0;

	/**
	 * Defines that a library acts as a category.
	 */
	const TYPE_CATEGORY = 1;

	/**
	 * Defines that a library acts as an external link.
	 */
	const TYPE_LINK = 2;


	/**
	 * Genre modes.
	 */
	const GENRE_MODE_OFF = 0;
	const GENRE_MODE_GLOBAL = 1;
	const GENRE_MODE_LIBRARY = 2;
	const GENRE_MODE_COMBINATION = 3;

	/**
	 * Character modes.
	 */
	const CHARACTER_MODE_OFF = 0;
	const CHARACTER_MODE_GLOBAL = 1;
	const CHARACTER_MODE_LIBRARY = 2;

	/**
	 * Warning modes.
	 */
	const WARNING_MODE_OFF = 0;
	const WARNING_MODE_GLOBAL = 1;
	const WARNING_MODE_LIBRARY = 2;

	/**
	 * Classification modes.
	 */
	const CLASSIFICATION_MODE_OFF = 0;
	const CLASSIFICATION_MODE_GLOBAL = 1;
	const CLASSIFICATION_MODE_LIBRARY = 2;

	/**
	 * Creates a new Library object.
	 *
	 * If id is set, the function reads the library data from database.
	 * Otherwise it uses the given resultset.
	 * 
	 * @param 	integer		$libraryID		id of a library 
	 * @param 	array		$row			resultset with library data form database
	 * @param 	Library 		$cacheObject		object with library data form database
	 */
	public function __construct($libraryID, $row = null, $cacheObject = null) {
		if ($libraryID !== null)
			$cacheObject = self::getLibrary($libraryID);
		if ($row != null)
			parent::__construct($row);
		if ($cacheObject != null)
			parent::__construct($cacheObject->data);
	}

	/**
	 * Returns true if this library is no category and no external link.
	 *
	 * @return	boolean
	 */
	public function isLibrary() {
		return $this->libraryType == self::TYPE_LIBRARY;
	}

	/**
	 * Returns true if this library is a category.
	 *
	 * @return	boolean
	 */
	public function isCategory() {
		return $this->libraryType == self::TYPE_CATEGORY;
	}

	/**
	 * Returns true if this library is an external link.
	 *
	 * @return	boolean
	 */
	public function isExternalLink() {
		return $this->libraryType == self::TYPE_LINK;
	}

	/**
	 * Returns a list of the parent libraries of this library.
	 *
	 * @return	array
	 */
	public function getParentLibraries() {
		if ($this->parentLibraries === null) {
			$this->parentLibraries = array();
			$libraries = WCF::getCache()->get('library', 'libraries');

			$parentLibrary = $this;
			while ($parentLibrary->parentID != 0) {
				$parentLibrary = $libraries[$parentLibrary->parentID];
				array_unshift($this->parentLibraries, $parentLibrary);
			}
		}

		return $this->parentLibraries;
	}

	/**
	 * Checks the given library permissions.
	 * Throws a PermissionDeniedException if the active user doesn't have one of the given permissions.
	 * @see		Library::getPermission()
	 *
	 * @param	mixed		$permissions
	 */
	public function checkPermission($permissions = 'canViewLibrary') {
		if (!is_array($permissions))
			$permissions = array($permissions);

		foreach ($permissions as $permission) {
			if (!$this->getPermission($permission)) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Checks whether the active user has the permission with the given name on this library.
	 * 
	 * @param	string		$permission	name of the requested permission
	 * @return	boolean
	 */
	public function getPermission($permission = 'canViewLibrary') {
		return (boolean) WCF::getUser()->getLibraryPermission($permission, $this->libraryID);
	}

	/**
	 * Checks whether the active user has the moderator permission with the given name on this library.
	 *
	 * @param	string		$permission	name of the requested permission
	 * @return	boolean
	 */
	public function getModeratorPermission($permission) {
		return (boolean) WCF::getUser()->getLibraryModeratorPermission($permission, $this->libraryID);
	}

	/**
	 * Checks the requested moderator permissions.
	 * Throws a PermissionDeniedException if the active user doesn't have one of the given permissions.
	 * @see 	Library::getModeratorPermission()
	 * 
	 * @param	mixed		$permissions
	 */
	public function checkModeratorPermission($permissions) {
		if (!is_array($permissions))
			$permissions = array($permissions);

		$result = false;
		foreach ($permissions as $permission) {
			$result = $result || $this->getModeratorPermission($permission);
		}

		if (!$result) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * Enters the active user to this library.
	 */
	public function enter() {
		// check permissions
		$this->checkPermission(array('canViewLibrary', 'canEnterLibrary'));

		// refresh session
		WCF::getSession()->setLibraryID($this->libraryID);

		// change style if necessary
		require_once(WCF_DIR . 'lib/system/style/StyleManager.class.php');
		if ($this->styleID && (!WCF::getSession()->getStyleID() || $this->enforceStyle) && StyleManager::getStyle()->styleID != $this->styleID) {
			StyleManager::changeStyle($this->styleID, true);
		}
	}

	/**
	 * Returns true, if the active user can start new stories in this library.
	 * 
	 * @return	boolean
	 */
	public function canStartStory() {
		return ($this->isLibrary() && $this->getPermission('canStartStory') && !$this->isClosed);
	}

	/**
	 * Returns an array with the genres of this library.
	 *
	 * @return	array
	 */
	public function getGenres() {

		if (self::GENRE_MODE_OFF == $this->genreMode)
			return $this->genres;
		if ($this->genreMode == self::GENRE_MODE_GLOBAL) {
			require_once (SLS_DIR . 'lib/data/genre/GenreList.class.php');
			$this->genreList = new GenreList();
		}
		if ($this->genreMode == self::GENRE_MODE_LIBRARY) {
			require_once (SLS_DIR . 'lib/data/genre/LibraryGenreList.class.php');
			$this->genreList = new LibraryGenreList($this->libraryID);
		}
		$this->genreList->readGenres();
		return $this->genres = $this->genreList->genre;
	}

	/**
	 * Returns an array with the warning options of this board.
	 *
	 * @return	array
	 */
	public function getWarningOptions() {
		// format warning
		$result = self::getWarnings();
		$warinings = array();
		foreach ($result as $value) {
			$warinings[$value] = WCF::getLanguage()->get($value);
		}

		return $warnings;
	}

	/**
	 * Returns an array with the warnings of this board.
	 *
	 * @return	array
	 */
	public function getWarnings() {
		if (!$this->hasWarings())
			return array();

		// get warinings

		$warnings = '';
		/* if (($this->warningMode == self::WARNING_MODE_GLOBAL || $this->warningMode == self::WARNING_MODE_COMBINATION) && STORY_DEFAULT_WARNINGS) {
		  $warings = STORY_DEFAULT_WARNINGS;
		  }
		  if (($this->warningMode == self::WARNING_MODE_LIBRARY || $this->warningMode == self::WARNING_MODE_COMBINATION) && $this->warnings) {
		  if (!empty($warinings))
		  $warings .= "\n";
		  $warnings .= $this->warnings;
		  }
		  ; */
		return explode("\n", StringUtil::unifyNewlines($warnings));
	}

	/**
	 * Returns true, if this board has any warnings.
	 *
	 * @return	boolean
	 */
	public function hasWarnings() {
		/* if ((($this->warningMode == self::WARNING_MODE_LIBRARY || $this->warningMode == self::WARNING_MODE_COMBINATION) && $this->warnings) || (($this->warningMode == self::WARNING_MODE_GLOBAL || $this->warningMode == self::WARNING_MODE_COMBINATION) && STORY_DEFAULT_WARNINGS)) {
		  return 1;
		  } */
		return 0;
	}

	/**
	 * Gets the library with the given library id from cache.
	 *
	 * @param 	integer		$libraryID	id of the requested library
	 * @return	Library
	 */
	public static function getLibrary($libraryID) {
		if (self::$libraries === null) {
			self::$libraries = WCF::getCache()->get('library', 'libraries');
		}

		if (!isset(self::$libraries[$libraryID])) {
			throw new IllegalLinkException();
		}

		return self::$libraries[$libraryID];
	}

	/**
	 * Creates the library select list.
	 * 
	 * @param	array		$permissions		filters libraries by given permissions
	 * @param	boolean		$hideLinks		should be true, to hide external link libraries
	 * @param	boolean		$showInvisibleLibraries	should be true, to display invisible libraries
	 * @param	array		$ignore			list of library ids to ignore in result
	 * @return 	array
	 */
	public static function getLibrarySelect($permissions = array('canViewLibrary'), $hideLinks = false, $showInvisibleLibraries = false, $ignore = array()) {
		self::$librarySelect = array();

		if (self::$libraryStructure === null)
			self::$libraryStructure = WCF::getCache()->get('library', 'libraryStructure');
		if (self::$libraries === null)
			self::$libraries = WCF::getCache()->get('library', 'libraries');

		self::makeLibrarySelect(0, 0, $permissions, $hideLinks, $showInvisibleLibraries, $ignore);

		return self::$librarySelect;
	}

	/**
	 * Generates the library select list.
	 *
	 * @param	integer		$parentID		id of the parent library
	 * @param	integer		$depth 			current list depth
	 * @param	array		$permissions		filters libraries by given permissions
	 * @param	boolean		$hideLinks		should be true, to hide external link libraries
	 * @param	boolean		$showInvisibleLibraries	should be true, to display invisible libraries
	 * @param	array		$ignore			list of library ids to ignore in result
	 */
	protected static function makeLibrarySelect($parentID = 0, $depth = 0, $permissions = array('canViewLibrary'), $hideLinks = false, $showInvisibleLibraries = false, $ignore = array()) {
		if (!isset(self::$libraryStructure[$parentID]))
			return;

		foreach (self::$libraryStructure[$parentID] as $libraryID) {
			if (!empty($ignore) && in_array($libraryID, $ignore))
				continue;

			$library = self::$libraries[$libraryID];
			if (!$showInvisibleLibraries && ($library->isInvisible || WCF::getUser()->isIgnoredLibrary($libraryID)))
				continue;

			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $library->getPermission($permission);
			}

			if (!$result)
				continue;
			if ($hideLinks && $library->isExternalLink())
				continue;

			// we must encode html here because the htmloptions plugin doesn't do it
			$title = WCF::getLanguage()->get(StringUtil::encodeHTML($library->title));
			if ($depth > 0)
				$title = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth) . ' ' . $title;

			self::$librarySelect[$libraryID] = $title;
			self::makeLibrarySelect($libraryID, $depth + 1, $permissions, $hideLinks, $showInvisibleLibraries, $ignore);
		}
	}

	/**
	 * Returns the number of clicks.
	 * 
	 * @return	integer
	 */
	public function getClicks() {
		if (!$this->isExternalLink()) {
			return null;
		}

		if ($this->clicks === null) {
			// get clicks from cache
			$this->clicks = 0;
			$cache = WCF::getCache()->get('libraryData', 'counts');
			if (isset($cache[$this->libraryID]['clicks']))
				$this->clicks = $cache[$this->libraryID]['clicks'];
		}

		return $this->clicks;
	}

	/**
	 * Returns the number of stories in this library.
	 *
	 * @return	integer
	 */
	public function getStories() {
		if (!$this->isLibrary()) {
			return null;
		}

		if ($this->stories === null) {
			// get stories from cache
			$this->stories = 0;
			$cache = WCF::getCache()->get('libraryData', 'counts');
			if (isset($cache[$this->libraryID]['stories']))
				$this->stories = $cache[$this->libraryID]['stories'];
		}

		return $this->stories;
	}

	/**
	 * Returns the number of posts in this library.
	 * 
	 * @return	integer
	 */
	public function getChapters() {
		if (!$this->isLibrary()) {
			return null;
		}

		if ($this->chapters === null) {
			// get posts from cache
			$this->chapters = 0;
			$cache = WCF::getCache()->get('libraryData', 'counts');
			if (isset($cache[$this->libraryID]['chapters']))
				$this->chapters = $cache[$this->libraryID]['chapters'];
		}

		return $this->chapters;
	}

	/**
	 * Returns the number of chapters per day in this library.
	 *
	 * @return	float
	 */
	public function getChaptersPerDay() {
		if ($this->chaptersPerDay === null) {
			$this->chaptersPerDay = 0;
			$days = ceil((TIME_NOW - $this->time) / 86400);
			if ($days <= 0)
				$days = 1;
			$this->chaptersPerDay = $this->getChapters() / $days;
		}

		return $this->chaptersPerDay;
	}

	/**
	 * Returns the editor permissions of the active user.
	 *
	 * @return	array
	 */
	public function getModeratorPermissions() {
		$permissions = array();

		// story permissions
		$permissions['canDeleteStory'] = intval($this->getModeratorPermission('canDeleteStory'));
		$permissions['canReadDeletedStory'] = intval($this->getModeratorPermission('canReadDeletedStory'));
		$permissions['canDeleteStoryCompletely'] = intval($this->getModeratorPermission('canDeleteStoryCompletely'));
		$permissions['canCloseStory'] = intval($this->getModeratorPermission('canCloseStory'));
		$permissions['canEnableStory'] = intval($this->getModeratorPermission('canEnableStory'));
		$permissions['canEditChapter'] = intval($this->getModeratorPermission('canEditChapter'));
		$permissions['canMoveStory'] = intval($this->getModeratorPermission('canMoveStory'));
		$permissions['canCopyStory'] = intval($this->getModeratorPermission('canCopyStory'));
		$permissions['canMarkAsDoneStory'] = intval($this->getModeratorPermission('canMarkAsDoneStory'));
		$permissions['canMarkStory'] = intval($permissions['canDeleteStory'] || $permissions['canMoveStory'] || $permissions['canCopyStory']);
		$permissions['canHandleStory'] = intval($permissions['canCloseStory'] || $permissions['canEnableStory'] || $permissions['canEditChapter'] || $permissions['canMarkAsDoneStory'] || $permissions['canMarkStory']);

		// chapter permissions
		$permissions['canDeleteChapter'] = intval($this->getModeratorPermission('canDeleteChapter'));
		$permissions['canReadDeletedChapter'] = intval($this->getModeratorPermission('canReadDeletedChapter'));
		$permissions['canDeleteChapterCompletely'] = intval($this->getModeratorPermission('canDeleteChapterCompletely'));
		$permissions['canCloseChapter'] = intval($this->getModeratorPermission('canCloseChapter'));
		$permissions['canEnableChapter'] = intval($this->getModeratorPermission('canEnableChapter'));
		$permissions['canMoveChapter'] = intval($this->getModeratorPermission('canMoveChapter'));
		$permissions['canCopyChapter'] = intval($this->getModeratorPermission('canCopyChapter'));
		$permissions['canMergeChapter'] = intval($this->getModeratorPermission('canMergeChapter'));
		$permissions['canMarkChapter'] = intval($permissions['canDeleteChapter'] || $permissions['canMoveChapter'] || $permissions['canCopyChapter']);
		$permissions['canHandleChapter'] = intval($permissions['canCloseChapter'] || $permissions['canEnableChapter'] || $permissions['canEditChapter'] || $permissions['canMarkStory']);

		return $permissions;
	}

	/**
	 * Returns the global moderator permissions.
	 * 
	 * @return	array
	 */
	public static function getGlobalModeratorPermissions() {
		$permissions = array();

		// story permissions
		$permissions['canDeleteStory'] = intval(WCF::getUser()->getPermission('mod.library.canDeleteStory'));
		$permissions['canReadDeletedStory'] = intval(WCF::getUser()->getPermission('mod.library.canReadDeletedStory'));
		$permissions['canDeleteStoryCompletely'] = intval(WCF::getUser()->getPermission('mod.library.canDeleteStoryCompletely'));
		$permissions['canCloseStory'] = intval(WCF::getUser()->getPermission('mod.library.canCloseStory'));
		$permissions['canEnableStory'] = intval(WCF::getUser()->getPermission('mod.library.canEnableStory'));
		$permissions['canEditChapter'] = intval(WCF::getUser()->getPermission('mod.library.canEditChapter'));
		$permissions['canMoveStory'] = intval(WCF::getUser()->getPermission('mod.library.canMoveStory'));
		$permissions['canCopyStory'] = intval(WCF::getUser()->getPermission('mod.library.canCopyStory'));
		$permissions['canMarkAsDoneStory'] = intval(WCF::getUser()->getPermission('mod.library.canMarkAsDoneStory'));
		$permissions['canMarkStory'] = intval($permissions['canDeleteStory'] || $permissions['canMoveStory'] || $permissions['canCopyStory']);
		$permissions['canHandleStory'] = intval($permissions['canCloseStory'] || $permissions['canEnableStory'] || $permissions['canEditChapter'] || $permissions['canMarkStory'] || $permissions['canMarkAsDoneStory']);

		// chapter permissions
		$permissions['canDeleteChapter'] = intval(WCF::getUser()->getPermission('mod.library.canDeleteChapter'));
		$permissions['canReadDeletedChapter'] = intval(WCF::getUser()->getPermission('mod.library.canReadDeletedChapter'));
		$permissions['canDeleteChapterCompletely'] = intval(WCF::getUser()->getPermission('mod.library.canDeleteChapterCompletely'));
		$permissions['canCloseChapter'] = intval(WCF::getUser()->getPermission('mod.library.canCloseChapter'));
		$permissions['canEnableChapter'] = intval(WCF::getUser()->getPermission('mod.library.canEnableChapter'));
		$permissions['canMoveChapter'] = intval(WCF::getUser()->getPermission('mod.library.canMoveChapter'));
		$permissions['canCopyChapter'] = intval(WCF::getUser()->getPermission('mod.library.canCopyChapter'));
		$permissions['canMergeChapter'] = intval(WCF::getUser()->getPermission('mod.library.canMergeChapter'));
		$permissions['canMarkChapter'] = intval($permissions['canDeleteChapter'] || $permissions['canMoveChapter'] || $permissions['canCopyChapter']);
		$permissions['canHandleChapter'] = intval($permissions['canCloseChapter'] || $permissions['canEnableChapter'] || $permissions['canEditChapter'] || $permissions['canMarkStory']);

		return $permissions;
	}

	/**
	 * Returns a list of accessible libraries.
	 *
	 * @param	string		$permission		name of the requested permission
	 * @return	string					comma separated library ids
	 */
	public static function getModeratedLibraries($permission) {
		if (self::$libraries === null)
			self::$libraries = WCF::getCache()->get('library', 'libraries');

		$libraryIDs = '';
		foreach (self::$libraries as $library) {
			if ($library->getModeratorPermission($permission)) {
				if (!empty($libraryIDs))
					$libraryIDs .= ',';
				$libraryIDs .= $library->libraryID;
			}
		}

		return $libraryIDs;
	}

	/**
	 * Returns a list of accessible libraries.
	 * 
	 * @param	array		$permissions		filters libraries by given permissions
	 * @return	array<integer>				comma separated library ids
	 */
	public static function getAccessibleLibraryIDArray($permissions = array('canViewLibrary', 'canEnterLibrary')) {
		if (self::$libraries === null)
			self::$libraries = WCF::getCache()->get('library', 'libraries');

		$libraryIDArray = array();
		foreach (self::$libraries as $library) {
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $library->getPermission($permission);
			}

			if ($result) {
				$libraryIDArray[] = $library->libraryID;
			}
		}

		return $libraryIDArray;
	}

	/**
	 * Returns a list of accessible libraries.
	 *
	 * @param	array		$permissions		filters libraries by given permissions
	 * @return	string					comma separated library ids
	 */
	public static function getAccessibleLibraries($permissions = array('canViewLibrary', 'canEnterLibrary')) {
		return implode(',', self::getAccessibleLibraryIDArray($permissions));
	}

	/**
	 * inherits forum permissions.
	 *
	 * @param 	integer 	$parentID
	 * @param 	array 		$permissions
	 */
	public static function inheritPermissions($parentID = 0, &$permissions) {
		if (self::$libraryStructure === null)
			self::$libraryStructure = WCF::getCache()->get('library', 'libraryStructure');
		if (self::$libraries === null)
			self::$libraries = WCF::getCache()->get('library', 'libraries');

		if (isset(self::$libraryStructure[$parentID]) && is_array(self::$libraryStructure[$parentID])) {
			foreach (self::$libraryStructure[$parentID] as $libraryID) {
				$library = self::$libraries[$libraryID];

				// inherit permissions from parent library
				if ($library->parentID) {
					if (isset($permissions[$library->parentID]) && !isset($permissions[$libraryID])) {
						$permissions[$libraryID] = $permissions[$library->parentID];
					}
				}

				self::inheritPermissions($libraryID, $permissions);
			}
		}
	}

	/**
	 * Subscribes the active user to this library.
	 */
	public function subscribe() {
		WCF::getUser()->subscribeLibrary($this->libraryID);
	}

	/**
	 * Unsubscribes the active user to this library.
	 */
	public function unsubscribe() {
		WCF::getUser()->unsubscribeLibrary($this->libraryID);
	}

	/**
	 * Marks this library as read for the active user.
	 */
	public function markAsRead() {
		WCF::getUser()->setLibraryVisitTime($this->libraryID);
	}

	/**
	 * Resets the library cache after changes.
	 */
	public static function resetCache() {
		// reset cache
		WCF::getCache()->clearResource('library');
		// reset permissions cache
		WCF::getCache()->clear(SLS_DIR . 'cache/', 'cache.libraryPermissions-*', true);

		self::$libraries = self::$libraryStructure = self::$librarySelect = null;
	}

	/**
	 * Returns a list of sublibraries.
	 * 
	 * @param	mixed		$libraryID
	 * @return	array<integer>
	 */
	public static function getSubLibraryIDArray($libraryID) {
		$libraryIDArray = (is_array($libraryID) ? $libraryID : array($libraryID));
		$subLibraryIDArray = array();

		// load cache
		if (self::$libraryStructure === null)
			self::$libraryStructure = WCF::getCache()->get('library', 'libraryStructure');
		foreach ($libraryIDArray as $libraryID) {
			$subLibraryIDArray = array_merge($subLibraryIDArray, self::makeSubLibraryIDArray($libraryID));
		}

		$subLibraryIDArray = array_unique($subLibraryIDArray);
		return $subLibraryIDArray;
	}

	/**
	 * Returns a list of sublibraries.
	 * 
	 * @param	integer		$parentLibraryID
	 * @return	array<integer>
	 */
	public static function makeSubLibraryIDArray($parentLibraryID) {
		if (!isset(self::$libraryStructure[$parentLibraryID])) {
			return array();
		}

		$subLibraryIDArray = array();
		foreach (self::$libraryStructure[$parentLibraryID] as $libraryID) {
			$subLibraryIDArray = self::makeSubLibraryIDArray($libraryID);
			$subLibraryIDArray[] = $libraryID;
		}

		return $subLibraryIDArray;
	}

	/**
	 * Returns the filename of the library icon.
	 *
	 * @return	string		filename of the library icon
	 */
	public function getIconName() {
		if ($this->isLibrary()) {
			$icon = 'library';
			if ($this->isClosed)
				$icon .= 'Closed';
		}
		else if ($this->isCategory()) {
			$icon = 'category';
		} else {
			$icon = 'libraryRedirect';
		}

		return $icon;
	}

	/**
	 * Returns an array with the characters of this library.
	 *
	 * @return	array
	 */
	public function getCharacters() {

		if (self::CHARACTER_MODE_OFF == $this->characterMode)
			return $this->characters;
		if ($this->characterMode == self::CHARACTER_MODE_GLOBAL) {
			require_once (SLS_DIR . 'lib/data/character/CharacterList.class.php');
			$this->characterList = new CharacterList();
		}
		if ($this->characterMode == self::CHARACTER_MODE_LIBRARY) {
			require_once (SLS_DIR . 'lib/data/character/LibraryCharacterList.class.php');
			$this->characterList = new LibraryCharacterList($this->libraryID);
		}
		$this->characterList->readCharacters();
		$this->characters = $this->characterList->character;
		return $this->characters;
	}

	/**
	 * Returns an array with the characters of this library.
	 *
	 * @return	array
	 */
	public function getClassifications() {

		if (self::CLASSIFICATION_MODE_OFF == $this->classificationMode)
			return $this->classifications;
		if ($this->classificationMode == self::CLASSIFICATION_MODE_GLOBAL) {
			require_once (SLS_DIR . 'lib/data/classification/ClassificationList.class.php');
			$this->classificationList = new ClassificationList();
		}
		if ($this->classificationMode == self::CLASSIFICATION_MODE_LIBRARY) {
			require_once (SLS_DIR . 'lib/data/classification/LibraryClassificationList.class.php');
			$this->classificationList = new LibraryClassificationList($this->libraryID);
		}
		$this->classificationList->readClassifications();
		$this->classifications = $this->classificationList->classification;
		return $this->classifications;
	}

}

?>
