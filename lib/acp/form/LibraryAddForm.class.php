<?php

// sls imports
require_once(SLS_DIR . 'lib/data/library/LibraryEditor.class.php');

// wcf imports
require_once(WCF_DIR . 'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR . 'lib/data/user/User.class.php');
require_once(WCF_DIR . 'lib/data/user/group/Group.class.php');
require_once(WCF_DIR . 'lib/system/session/Session.class.php');
require_once(WCF_DIR . 'lib/system/style/StyleManager.class.php');

/**
 * Shows the library add form.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.form
 * @category 	Story Library System
 */
class LibraryAddForm extends ACPForm {

	// system
	public $templateName = 'libraryAdd';
	public $activeMenuItem = 'sls.acp.menu.link.content.library.add';
	public $neededPermissions = 'admin.library.canAddLibrary';
	public $activeTabMenuItem = 'data';
	/**
	 * library editor object
	 * 
	 * @var	LibraryEditor
	 */
	public $library;
	/**
	 * list of available permisions
	 * 
	 * @var	array
	 */
	public $permissionSettings = array();
	/**
	 * list of available moderator permisions
	 * 
	 * @var	array
	 */
	public $moderatorSettings = array();
	/**
	 * list of available parent libraries
	 * 
	 * @var	array
	 */
	public $libraryOptions = array();
	/**
	 * list of available styles
	 * 
	 * @var	array
	 */
	public $availableStyles = array();
	/**
	 * list of additional fields
	 * 
	 * @var	array
	 */
	public $additionalFields = array();
	// parameters
	public $libraryType = 0;
	public $parentID = 0;
	public $position = '';
	public $title = '';
	public $description = '';
	public $allowDescriptionHtml = 0;
	public $image = '';
	public $imageNew = '';
	public $imageShowAsBackground = 1;
	public $imageBackgroundRepeat = 'no';
	public $externalURL = '';
	public $styleID = 0;
	public $enforceStyle = 0;
	public $daysPrune = 0;
	public $sortField = '';
	public $sortOrder = '';
	public $chapterSortOrder = '';
	public $closed = 0;
	public $countUserChapters = 1;
	public $invisible = 0;
	public $showSubLibraries = 1;
	public $permissions = array();
	public $moderators = array();
	public $enableRating = -1;
	public $storiesPerPage = 0;
	public $searchable = 1;
	public $searchableForSimilarStories = 1;
	public $ignorable = 1;
	public $enableMarkingAsDone = 0;
	public $warningMode = 0;
	public $warnings = array();
	public $characterMode = 0;
	public $characters = array();
	public $classificationMode = 0;
	public $classifications = array();
	public $genreMode = 0;
	public $genres = array();
	public $staticParameters = array();

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// characters
		$this->staticParameters['characterIDs'] = array();
		require_once(SLS_DIR . "lib/data/character/CharacterList.class.php");
		$objCharacterList = new CharacterList();
		$objCharacterList->readCharacters();
		$this->characters = $objCharacterList->character;
		CharacterEditor::unmarkAll();


		// warnings
		$this->staticParameters['warningIDs'] = array();
		require_once(SLS_DIR . "lib/data/warning/WarningList.class.php");
		$objWarningList = new WarningList();
		$objWarningList->readWarnings();
		$this->warnings = $objWarningList->warning;
		WarningEditor::unmarkAll();

		// gernes
		$this->staticParameters['genreIDs'] = array();
		require_once(SLS_DIR . "lib/data/genre/GenreList.class.php");
		$objGenreList = new GenreList();
		$objGenreList->readGenres();
		$this->genres = $objGenreList->genre;
		GenreEditor::unmarkAll();

		// classifications
		$this->staticParameters['classificationIDs'] = array();
		require_once(SLS_DIR . "lib/data/classification/ClassificationList.class.php");
		$objClassificationList = new ClassificationList();
		$objClassificationList->readClassifications();
		$this->classifications = $objClassificationList->classification;
		ClassificationEditor::unmarkAll();


		$this->readModeratorSettings();
		$this->readPermissionSettings();
		if (isset($_REQUEST['parentID']))
			$this->parentID = intval($_REQUEST['parentID']);
	}

	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['staticParameters']) && is_array($_POST['staticParameters'])) {
			$this->staticParameters = $_POST['staticParameters'];
		}
		foreach ($this->characters as $character) {
			if (isset($this->staticParameters['characterIDs']) && in_array($character->characterID, $this->staticParameters['characterIDs'])) {
				$character->mark();
			}
		}
		foreach ($this->classifications as $classification) {
			if (isset($this->staticParameters['classificationIDs']) && in_array($classification->classificationID, $this->staticParameters['classificationIDs'])) {
				$classification->mark();
			}
		}
		foreach ($this->warnings as $warning) {
			if (isset($this->staticParameters['warningIDs']) && in_array($warning->warningID, $this->staticParameters['warningIDs'])) {
				$warning->mark();
			}
		}
		foreach ($this->genres as $genre) {
			if (isset($this->staticParameters['genreIDs']) && in_array($genre->genreID, $this->staticParameters['genreIDs'])) {
				$genre->mark();
			}
		}

		$this->enforceStyle = $this->closed = $this->imageShowAsBackground = 0;
		$this->countUserChapters = $this->invisible = $this->showSubLibraries = $this->allowDescriptionHtml = 0;
		$this->searchable = $this->searchableForSimilarStories = $this->ignorable = 0;
		if (isset($_POST['libraryType']))
			$this->libraryType = intval($_POST['libraryType']);
		if (!empty($_POST['position']))
			$this->position = intval($_POST['position']);
		if (isset($_POST['title']))
			$this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['description']))
			$this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['allowDescriptionHtml']))
			$this->allowDescriptionHtml = intval($_POST['allowDescriptionHtml']);
		if (isset($_POST['image']))
			$this->image = StringUtil::trim($_POST['image']);
		if (isset($_POST['externalURL']))
			$this->externalURL = StringUtil::trim($_POST['externalURL']);
		if (isset($_POST['styleID']))
			$this->styleID = intval($_POST['styleID']);
		if (isset($_POST['enforceStyle']))
			$this->enforceStyle = intval($_POST['enforceStyle']);
		if (isset($_POST['daysPrune']))
			$this->daysPrune = intval($_POST['daysPrune']);
		if (isset($_POST['sortField']))
			$this->sortField = $_POST['sortField'];
		if (isset($_POST['sortOrder']))
			$this->sortOrder = $_POST['sortOrder'];
		if (isset($_POST['chapterSortOrder']))
			$this->chapterSortOrder = $_POST['chapterSortOrder'];
		if (isset($_POST['closed']))
			$this->closed = intval($_POST['closed']);
		if (isset($_POST['countUserChapters']))
			$this->countUserChapters = intval($_POST['countUserChapters']);
		if (isset($_POST['invisible']))
			$this->invisible = intval($_POST['invisible']);
		if (isset($_POST['showSubLibraries']))
			$this->showSubLibraries = intval($_POST['showSubLibraries']);
		if (isset($_POST['activeTabMenuItem']))
			$this->activeTabMenuItem = $_POST['activeTabMenuItem'];
		if (isset($_POST['enableRating']))
			$this->enableRating = intval($_POST['enableRating']);
		if (isset($_POST['storiesPerPage']))
			$this->storiesPerPage = intval($_POST['storiesPerPage']);
		if (isset($_POST['imageNew']))
			$this->imageNew = StringUtil::trim($_POST['imageNew']);
		if (isset($_POST['imageShowAsBackground']))
			$this->imageShowAsBackground = intval($_POST['imageShowAsBackground']);
		if (isset($_POST['imageBackgroundRepeat']))
			$this->imageBackgroundRepeat = $_POST['imageBackgroundRepeat'];
		if (isset($_POST['searchable']))
			$this->searchable = intval($_POST['searchable']);
		if (isset($_POST['searchableForSimilarStories']))
			$this->searchableForSimilarStories = intval($_POST['searchableForSimilarStories']);
		if (isset($_POST['ignorable']))
			$this->ignorable = intval($_POST['ignorable']);
		if (isset($_POST['enableMarkingAsDone']))
			$this->enableMarkingAsDone = intval($_POST['enableMarkingAsDone']);

		// permissions
		if (isset($_POST['permission']) && is_array($_POST['permission']))
			$this->permissions = $_POST['permission'];
		if (isset($_POST['moderator']) && is_array($_POST['moderator']))
			$this->moderators = $_POST['moderator'];

		// warnings
		if (isset($_POST['warningMode']))
			$this->warningMode = $_POST['warningMode'];

		// characters
		if (isset($_POST['characterMode']))
			$this->characterMode = $_POST['characterMode'];

		// gernes
		if (isset($_POST['genreMode']))
			$this->genreMode = $_POST['genreMode'];

		// classifications
		if (isset($_POST['classificationMode']))
			$this->classificationMode = $_POST['classificationMode'];
	}

	/**
	 * Validates the given permissions.
	 */
	public function validatePermissions($permissions, $validSettings) {
		foreach ($permissions as $permission) {
			// type
			if (!isset($permission['type']) || ($permission['type'] != 'user' && $permission['type'] != 'group')) {
				throw new UserInputException();
			}

			// id
			if (!isset($permission['id'])) {
				throw new UserInputException();
			}
			if ($permission['type'] == 'user') {
				$user = new User(intval($permission['id']));
				if (!$user->userID)
					throw new UserInputException();
			}
			else {
				$group = new Group(intval($permission['id']));
				if (!$group->groupID)
					throw new UserInputException();
			}

			// settings
			if (!isset($permission['settings']) || !is_array($permission['settings'])) {
				throw new UserInputException();
			}
			// find invalid settings
			foreach ($permission['settings'] as $key => $value) {
				if (!isset($validSettings[$key]) || ($value != -1 && $value != 0 && $value = !1)) {
					throw new UserInputException();
				}
			}
			// find missing settings
			foreach ($validSettings as $key => $value) {
				if (!isset($permission['settings'][$key])) {
					throw new UserInputException();
				}
			}
		}
	}

	/**
	 * @see Form::validate()
	 */
	public function validate() {
		// validate permissions
		$this->validatePermissions($this->permissions, array_flip($this->permissionSettings));
		$this->validatePermissions($this->moderators, array_flip($this->moderatorSettings));

		parent::validate();

		// library type
		if ($this->libraryType < 0 || $this->libraryType > 2) {
			throw new UserInputException('libraryType', 'invalid');
		}

		// parent id
		$this->validateParentID();

		// position
		/* if (!$this->position) {
		  throw new UserInputException('position');
		  } */

		// title
		if (empty($this->title)) {
			throw new UserInputException('title');
		}

		// external url
		if ($this->libraryType == 2 && empty($this->externalURL)) {
			throw new UserInputException('externalURL');
		}

		// sortField
		switch ($this->sortField) {
			case '': case 'topic': case 'attachments': case 'username':
			case 'time': case 'ratingResult': case 'replies': case 'views': case 'lastChapterTime': break;
			default: throw new UserInputException('sortField', 'invalid');
		}

		// sortOrder
		switch ($this->sortOrder) {
			case '': case 'ASC': case 'DESC': break;
			default: throw new UserInputException('sortOrder', 'invalid');
		}

		// chapterSortOrder
		switch ($this->chapterSortOrder) {
			case '': case 'ASC': case 'DESC': break;
			default: throw new UserInputException('chapterSortOrder', 'invalid');
		}
	}

	/**
	 * Validates the given parent id.
	 */
	protected function validateParentID() {
		if ($this->parentID) {
			try {
				Library::getLibrary($this->parentID);
			} catch (IllegalLinkException $e) {
				throw new UserInputException('parentID', 'invalid');
			}
		}
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		// save library

		if (WCF::getUser()->getPermission('admin.library.canAddLibrary')) {
			$this->library = LibraryEditor::create($this->parentID, ($this->position ? $this->position : null), $this->title, $this->description,
							$this->libraryType, $this->image, $this->imageNew, $this->imageShowAsBackground, $this->imageBackgroundRepeat, $this->externalURL, TIME_NOW,
							$this->styleID, $this->enforceStyle, $this->daysPrune,
							$this->sortField, $this->sortOrder, $this->closed, $this->countUserChapters,
							$this->invisible, $this->showSubLibraries, $this->allowDescriptionHtml, $this->enableRating,
							$this->storiesPerPage, $this->searchable, $this->searchableForSimilarStories, $this->ignorable,
							$this->enableMarkingAsDone, $this->characterMode, $this->classificationMode, $this->genreMode, $this->warningMode,
							$this->additionalFields);
		}

		// save characters
		if (WCF::getUser()->getPermission('admin.library.canEditCharacter')) {
			$this->saveCharacters();
		}

		// save warnings
		if (WCF::getUser()->getPermission('admin.library.canEditWarning')) {
			$this->saveWarnings();
		}

		// save classifications
		if (WCF::getUser()->getPermission('admin.library.canEditClassification')) {
			$this->saveClassifications();
		}

		// save genres
		if (WCF::getUser()->getPermission('admin.library.canEditGenre')) {
			$this->saveGenres();
		}



		// save permissions
		if (WCF::getUser()->getPermission('admin.library.canEditPermissions')) {
			$this->savePermissions();
		}

		// save moderators
		if (WCF::getUser()->getPermission('admin.library.canEditModerators')) {
			$this->saveModerators();
		}

		// reset cache
		$this->resetCache();
		$this->saved();

		// reset values
		$this->libraryType = $this->parentID = $this->styleID = $this->storiesPerPage = 0;
		$this->enforceStyle = $this->daysPrune = $this->closed = $this->invisible = $this->allowDescriptionHtml = 0;
		$this->enableMarkingAsDone = 0;
		$this->countUserChapters = $this->showSubLibraries = $this->imageShowAsBackground = $this->searchable = $this->searchableForSimilarStories = $this->ignorable = 1;
		$this->position = $this->title = $this->description = $this->image = $this->imageNew = $this->externalURL = $this->sortField = $this->sortOrder = $this->chapterSortOrder = '';
		$this->permissions = $this->moderators = array();
		$this->enableRating = -1;
		$this->imageBackgroundRepeat = 'no-repeat';

		// show success message
		WCF::getTPL()->assign(array(
			'library' => $this->library,
			'success' => true
		));
	}

	/**
	 * Resets the library cache after changes.
	 */
	protected function resetCache() {
		Library::resetCache();

		// reset sessions
		Session::resetSessions(array(), true, false);
	}

	/**
	 * Saves user and group permissions.
	 */
	public function savePermissions() {
		// create inserts
		$userInserts = $groupInserts = '';
		foreach ($this->permissions as $key => $permission) {
			// skip default values
			$noDefaultValue = false;
			foreach ($permission['settings'] as $value) {
				if ($value != -1)
					$noDefaultValue = true;
			}
			if (!$noDefaultValue) {
				unset($this->permissions[$key]);
				continue;
			}

			if ($permission['type'] == 'user') {
				if (!empty($userInserts))
					$userInserts .= ',';
				$userInserts .= '(' . $this->library->libraryID . ',
						 ' . intval($permission['id']) . ',
						 ' . (implode(', ', ArrayUtil::toIntegerArray($permission['settings']))) . ')';
			}
			else {
				if (!empty($groupInserts))
					$groupInserts .= ',';
				$groupInserts .= '(' . $this->library->libraryID . ',
						 ' . intval($permission['id']) . ',
						 ' . (implode(', ', ArrayUtil::toIntegerArray($permission['settings']))) . ')';
			}
		}

		if (!empty($userInserts)) {
			$sql = "INSERT INTO	sls" . SLS_N . "_library_to_user
						(libraryID, userID, " . implode(', ', $this->permissionSettings) . ")
				VALUES		" . $userInserts;
			WCF::getDB()->sendQuery($sql);
		}

		if (!empty($groupInserts)) {
			$sql = "INSERT INTO	sls" . SLS_N . "_library_to_group
						(libraryID, groupID, " . implode(', ', $this->permissionSettings) . ")
				VALUES		" . $groupInserts;
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * Saves moderators.
	 */
	public function saveModerators() {
		// create inserts
		$inserts = '';
		foreach ($this->moderators as $moderator) {
			if (!empty($inserts))
				$inserts .= ',';
			$inserts .= '	(' . $this->library->libraryID . ',
					' . ($moderator['type'] == 'user' ? intval($moderator['id']) : 0) . ',
					' . ($moderator['type'] == 'group' ? intval($moderator['id']) : 0) . ',
					' . (implode(', ', ArrayUtil::toIntegerArray($moderator['settings']))) . ')';
		}

		if (!empty($inserts)) {
			$sql = "INSERT INTO	sls" . SLS_N . "_library_moderator
						(libraryID, userID, groupID, " . implode(', ', $this->moderatorSettings) . ")
				VALUES		" . $inserts;
			WCF::getDB()->sendQuery($sql);
		}
	}

	public function saveCharacters() {
		// create inserts

		$inserts = '';
		foreach ($this->characters as $character) {
			if ($character->isMarked()) {
				if (!empty($inserts))
					$inserts .= ',';
				$inserts .= '   (' . $this->library->libraryID . ',
					' . $character->characterID . ')';
			}
		}

		if (!empty($inserts)) {
			$sql = "INSERT INTO	sls" . SLS_N . "_library_character
						(libraryID, characterID )
				VALUES		" . $inserts;
			WCF::getDB()->sendQuery($sql);
		}
	}

	public function saveWarnings() {
		// create inserts

		$inserts = '';
		foreach ($this->warnings as $warning) {
			if ($warning->isMarked()) {
				if (!empty($inserts)) $inserts .= ',';
				$inserts .= '   (' . $this->library->libraryID . ',
					' . $warning->warningID . ')';
			}
		}

		if (!empty($inserts)) {
			$sql = "INSERT INTO	sls" . SLS_N . "_library_warning
						(libraryID, warningID )
				VALUES		" . $inserts;
			WCF::getDB()->sendQuery($sql);
		}
	}

	public function saveGenres() {
		// create inserts

		$inserts = '';
		foreach ($this->genres as $genre) {
			if ($genre->isMarked()) {
				if (!empty($inserts))
					$inserts .= ',';
				$inserts .= '   (' . $this->library->libraryID . ',
					' . $genre->genreID . ')';
			}
		}

		if (!empty($inserts)) {
			$sql = "INSERT INTO	sls" . SLS_N . "_library_genre
						(libraryID, genreID )
				VALUES		" . $inserts;
			WCF::getDB()->sendQuery($sql);
		}
	}

	public function saveClassifications() {
		// create inserts

		$inserts = '';
		foreach ($this->classifications as $classification) {
			if ($classification->isMarked()) {
				if (!empty($inserts))
					$inserts .= ',';
				$inserts .= '   (' . $this->library->libraryID . ',
					' . $classification->classificationID . ')';
			}
		}

		if (!empty($inserts)) {
			$sql = "INSERT INTO	sls" . SLS_N . "_library_classification
						(libraryID, classificationID )
				VALUES		" . $inserts;
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();


		$this->readLibraryOptions();
		$this->availableStyles = StyleManager::getAvailableStyles();
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'libraryType' => $this->libraryType,
			'parentID' => $this->parentID,
			'position' => $this->position,
			'title' => $this->title,
			'description' => $this->description,
			'allowDescriptionHtml' => $this->allowDescriptionHtml,
			'image' => $this->image,
			'externalURL' => $this->externalURL,
			'styleID' => $this->styleID,
			'enforceStyle' => $this->enforceStyle,
			'daysPrune' => $this->daysPrune,
			'sortField' => $this->sortField,
			'sortOrder' => $this->sortOrder,
			'chapterSortOrder' => $this->chapterSortOrder,
			'closed' => $this->closed,
			'countUserChapters' => $this->countUserChapters,
			'invisible' => $this->invisible,
			'showSubLibraries' => $this->showSubLibraries,
			'libraryOptions' => $this->libraryOptions,
			'permissions' => $this->permissions,
			'moderators' => $this->moderators,
			'moderatorSettings' => $this->moderatorSettings,
			'permissionSettings' => $this->permissionSettings,
			'action' => 'add',
			'availableStyles' => $this->availableStyles,
			'activeTabMenuItem' => $this->activeTabMenuItem,
			'enableRating' => $this->enableRating,
			'storiesPerPage' => $this->storiesPerPage,
			'imageNew' => $this->imageNew,
			'imageShowAsBackground' => $this->imageShowAsBackground,
			'imageBackgroundRepeat' => $this->imageBackgroundRepeat,
			'searchable' => $this->searchable,
			'searchableForSimilarStories' => $this->searchableForSimilarStories,
			'ignorable' => $this->ignorable,
			'enableMarkingAsDone' => $this->enableMarkingAsDone,
			'warningMode' => $this->warningMode,
			'warnings' => $this->warnings,
			'characterMode' => $this->characterMode,
			'characters' => $this->characters,
			'classificationMode' => $this->classificationMode,
			'classifications' => $this->classifications,
			'genreMode' => $this->genreMode,
			'genres' => $this->genres,
			'staticParameters' => $this->staticParameters,
		));
	}

	/**
	 * Gets available moderator settings.
	 */
	protected function readModeratorSettings() {
		$sql = "SHOW COLUMNS FROM sls" . SLS_N . "_library_moderator";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['Field'] != 'libraryID' && $row['Field'] != 'userID' && $row['Field'] != 'groupID') {
				// check modules
				switch ($row['Field']) {
					case 'canMarkAsDoneStory':
						if (!MODULE_STORY_MARKING_AS_DONE)
							continue 2;
						break;
				}

				$this->moderatorSettings[] = $row['Field'];
			}
		}
	}

	/**
	 * Gets available permission settings.
	 */
	protected function readPermissionSettings() {
		$sql = "SHOW COLUMNS FROM sls" . SLS_N . "_library_to_group";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['Field'] != 'libraryID' && $row['Field'] != 'groupID') {
				// check modules
				switch ($row['Field']) {
					case 'canMarkAsDoneOwnStory':
						if (!MODULE_STORY_MARKING_AS_DONE)
							continue 2;
						break;

					case 'canSetTags':
						if (!MODULE_TAGGING)
							continue 2;
						break;
				}

				$this->permissionSettings[] = $row['Field'];
			}
		}
	}

	/**
	 * Gets a list of available parent libraries.
	 */
	protected function readLibraryOptions() {
		$this->libraryOptions = Library::getLibrarySelect(array(), true, true);
	}

}

?>