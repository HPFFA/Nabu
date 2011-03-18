<?php

// sls imports
require_once(SLS_DIR . 'lib/acp/form/LibraryAddForm.class.php');

/**
 * Shows the library edit form.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.form
 * @category 	Story Library System
 */
class LibraryEditForm extends LibraryAddForm {

	// system
	public $activeMenuItem = 'sls.acp.menu.link.content.library';
	public $neededPermissions = array('admin.library.canEditLibrary', 'admin.library.canEditPermissions', 'admin.library.canEditModerators');
	/**
	 * library id
	 *
	 * @var	integer
	 */
	public $libraryID = 0;
	/**
	 * existing library structure
	 *
	 * @var	array
	 */
	public static $libraryStructure;

	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get library id
		if (isset($_REQUEST['libraryID']))
			$this->libraryID = intval($_REQUEST['libraryID']);

		// get library
		$this->library = new LibraryEditor($this->libraryID);
	}

	/**
	 * @see LibraryAddForm::validateParentID()
	 */
	protected function validateParentID() {
		parent::validateParentID();

		if ($this->parentID) {
			if (self::$libraryStructure === null)
				self::$libraryStructure = WCF::getCache()->get('library', 'libraryStructure');
			if ($this->libraryID == $this->parentID || $this->searchChildren($this->libraryID, $this->parentID)) {
				throw new UserInputException('parentID', 'invalid');
			}
		}
	}

	/**
	 * Searches for a library in the child tree of another library.
	 */
	protected function searchChildren($parentID, $searchedLibraryID) {
		if (isset(self::$libraryStructure[$parentID])) {
			foreach (self::$libraryStructure[$parentID] as $libraryID) {
				if ($libraryID == $searchedLibraryID)
					return true;
				if ($this->searchChildren($libraryID, $searchedLibraryID))
					return true;
			}
		}

		return false;
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

		// save library
		if (WCF::getUser()->getPermission('admin.library.canEditLibrary')) {
			// fix closed categories
			if ($this->library->isCategory() && $this->libraryType != Library::TYPE_CATEGORY) {
				$sql = "DELETE FROM	sls" . SLS_N . "_library_closed_category_to_user
					WHERE		libraryID = " . $this->library->libraryID;
				WCF::getDB()->sendQuery($sql);
				$sql = "DELETE FROM	sls" . SLS_N . "_library_closed_category_to_admin
					WHERE		libraryID = " . $this->library->libraryID;
				WCF::getDB()->sendQuery($sql);
			}

			// update data
			$this->library->update($this->parentID, $this->title, $this->description,
					$this->libraryType, $this->image, $this->imageNew, $this->imageShowAsBackground,
					$this->imageBackgroundRepeat, $this->externalURL,
					$this->styleID, $this->enforceStyle, $this->daysPrune,
					$this->sortField, $this->sortOrder, $this->closed, $this->countUserChapters,
					$this->invisible, $this->showSubLibraries, $this->allowDescriptionHtml, $this->enableRating,
					$this->storiesPerPage, $this->searchable, $this->searchableForSimilarStories, $this->ignorable,
					$this->enableMarkingAsDone, $this->characterMode, $this->classificationMode, $this->genreMode, $this->warningMode,
					$this->additionalFields);
			$this->library->removePositions();
			$this->library->addPosition($this->parentID, ($this->position ? $this->position : null));

			// fix ignored libraries
			if (!$this->ignorable && $this->library->ignorable) {
				$unignorableLibraryIDArray = array($this->libraryID);
				$parentLibraries = $this->library->getParentLibraries();
				foreach ($parentLibraries as $parentLibrary)
					$unignorableLibraryIDArray[] = $parentLibrary->libraryID;
				$sql = "DELETE FROM	sls" . SLS_N . "_library_ignored_by_user
					WHERE		libraryID IN (" . implode(',', $unignorableLibraryIDArray) . ")";
				WCF::getDB()->sendQuery($sql);
			}
		}

		// save characters
		if (WCF::getUser()->getPermission('admin.library.canEditCharacter')) {
			$this->saveCharacters();
		}

		// save classification
		if (WCF::getUser()->getPermission('admin.library.canEditClassification')) {
			$this->saveClassifications();
		}

		// save warnings
		if (WCF::getUser()->getPermission('admin.library.canEditWarning')) {
			$this->saveWarnings();
		}

		// save gernes
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

		// show success message
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @see LibraryAddForm::savePermissions()
	 */
	public function savePermissions() {
		// delete old entries
		$sql = "DELETE FROM	sls" . SLS_N . "_library_to_user
			WHERE		libraryID = " . $this->libraryID;
		WCF::getDB()->sendQuery($sql);
		$sql = "DELETE FROM	sls" . SLS_N . "_library_to_group
			WHERE		libraryID = " . $this->libraryID;
		WCF::getDB()->sendQuery($sql);

		// save new entries
		parent::savePermissions();
	}

	/**
	 * @see LibraryAddForm::saveModerators()
	 */
	public function saveModerators() {
		// delete old entries
		$sql = "DELETE FROM	sls" . SLS_N . "_library_moderator
			WHERE		libraryID = " . $this->libraryID;
		WCF::getDB()->sendQuery($sql);

		// save new entries
		parent::saveModerators();
	}

	/**
	 * @see LibraryAddForm::saveCharacters()
	 */
	public function saveCharacters() {
		// delete old entries
		$sql = "DELETE FROM	sls" . SLS_N . "_library_character
			WHERE		libraryID = " . $this->libraryID;
		WCF::getDB()->sendQuery($sql);

		// save new entries
		parent::saveCharacters();
	}

	/**
	 * @see LibraryAddForm::saveGenres()
	 */
	public function saveGenres() {
		// delete old entries
		$sql = "DELETE FROM	sls" . SLS_N . "_library_genre
			WHERE		libraryID = " . $this->libraryID;
		WCF::getDB()->sendQuery($sql);

		// save new entries
		parent::saveGenres();
	}

	/**
	 * @see LibraryAddForm::saveClassifications()
	 */
	public function saveClassifications() {
		// delete old entries
		$sql = "DELETE FROM	sls" . SLS_N . "_library_classification
			WHERE		libraryID = " . $this->libraryID;
		WCF::getDB()->sendQuery($sql);

		// save new entries
		parent::saveClassifications();
	}

	/**
	 * @see LibraryAddForm::saveWarnings()
	 */
	public function saveWarnings() {
		// delete old entries
		$sql = "DELETE FROM	sls" . SLS_N . "_library_warning
			WHERE		libraryID = " . $this->libraryID;
		WCF::getDB()->sendQuery($sql);

		// save new entries
		parent::saveWarnings();
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		if (!count($_POST)) {
			// default active tab item
			if (!WCF::getUser()->getPermission('admin.library.canEditLibrary')) {
				if (WCF::getUser()->getPermission('admin.library.canEditPermissions'))
					$this->activeTabMenuItem = 'permissions';
				else
					$this->activeTabMenuItem = 'moderators';
			}

			// get values
			$this->libraryType = $this->library->libraryType;
			$this->parentID = $this->library->parentID;
			$this->title = $this->library->title;
			$this->description = $this->library->description;
			$this->image = $this->library->image;
			$this->externalURL = $this->library->externalURL;
			$this->styleID = $this->library->styleID;
			$this->enforceStyle = $this->library->enforceStyle;
			$this->daysPrune = $this->library->daysPrune;
			$this->sortField = $this->library->sortField;
			$this->sortOrder = $this->library->sortOrder;
			$this->chapterSortOrder = $this->library->chapterSortOrder;
			$this->closed = $this->library->isClosed;
			$this->countUserPosts = $this->library->countUserPosts;
			$this->invisible = $this->library->isInvisible;
			$this->showSubLibraries = $this->library->showSubLibraries;
			$this->allowDescriptionHtml = $this->library->allowDescriptionHtml;
			$this->enableRating = $this->library->enableRating;
			$this->storiesPerPage = $this->library->storiesPerPage;
			$this->imageNew = $this->library->imageNew;
			$this->imageShowAsBackground = $this->library->imageShowAsBackground;
			$this->imageBackgroundRepeat = $this->library->imageBackgroundRepeat;
			$this->searchable = $this->library->searchable;
			$this->searchableForSimilarThreads = $this->library->searchableForSimilarThreads;
			$this->ignorable = $this->library->ignorable;
			$this->enableMarkingAsDone = $this->library->enableMarkingAsDone;
			$this->warningMode = $this->library->warningMode;
			$this->classificationMode = $this->library->classificationMode;
			$this->characterMode = $this->library->characterMode;
			$this->genreMode = $this->library->genreMode;
			
			// get position
			$sql = "SELECT	position
				FROM	sls" . SLS_N . "_library_structure
				WHERE	libraryID = " . $this->libraryID;
			$row = WCF::getDB()->getFirstRow($sql);
			if (isset($row['position']))
				$this->position = $row['position'];

			// get permissions
			$sql = "		(SELECT		user_permission.*, user.userID AS id, 'user' AS type, user.username AS name
						FROM		sls" . SLS_N . "_library_to_user user_permission
						LEFT JOIN	wcf" . WCF_N . "_user user
						ON		(user.userID = user_permission.userID)
						WHERE		libraryID = " . $this->libraryID . ")
				UNION
						(SELECT		group_permission.*, usergroup.groupID AS id, 'group' AS type, usergroup.groupName AS name
						FROM		sls" . SLS_N . "_library_to_group group_permission
						LEFT JOIN	wcf" . WCF_N . "_group usergroup
						ON		(usergroup.groupID = group_permission.groupID)
						WHERE		libraryID = " . $this->libraryID . ")
				ORDER BY	name";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (empty($row['id']))
					continue;
				$permission = array('name' => $row['name'], 'type' => $row['type'], 'id' => $row['id']);
				unset($row['name'], $row['userID'], $row['groupID'], $row['libraryID'], $row['id'], $row['type']);
				foreach ($row as $key => $value) {
					if (!in_array($key, $this->permissionSettings))
						unset($row[$key]);
				}
				$permission['settings'] = $row;
				$this->permissions[] = $permission;
			}

			//get characters
			$sql = " SELECT characterID
				FROM  sls" . SLS_N . "_library_character 
				WHERE libraryID = " . $this->libraryID . ";";
			$result = WCF::getDB()->sendQuery($sql);

			while ($row = WCF::getDB()->fetchArray($result)) {
				foreach ($this->characters as $character) {
					if ($row['characterID'] == $character->characterID)
						$character->mark();
				}
			}

			//get warnings
			$sql = " SELECT warningID
				FROM  sls" . SLS_N . "_library_warning
				WHERE libraryID = " . $this->libraryID . ";";
			$result = WCF::getDB()->sendQuery($sql);

			while ($row = WCF::getDB()->fetchArray($result)) {
				foreach ($this->warnings as $warning) {
					if ($row['warningID'] == $warning->warningID)
						$warning->mark();
				}
			}


			//get genres
			$sql = " SELECT genreID
				FROM  sls" . SLS_N . "_library_genre
				WHERE libraryID = " . $this->libraryID . ";";
			$result = WCF::getDB()->sendQuery($sql);

			while ($row = WCF::getDB()->fetchArray($result)) {
				foreach ($this->genres as $genre) {
					if ($row['genreID'] == $genre->genreID)
						$genre->mark();
				}
			}

			//get classifications
			$sql = " SELECT classificationID
				FROM  sls" . SLS_N . "_library_classification
				WHERE libraryID = " . $this->libraryID . ";";
			$result = WCF::getDB()->sendQuery($sql);

			while ($row = WCF::getDB()->fetchArray($result)) {
				foreach ($this->classifications as $classification) {
					if ($row['classificationID'] == $classification->classificationID)
						$classification->mark();
				}
			}

			// get moderators
			$sql = "SELECT		moderator.*, IFNULL(user.username, usergroup.groupName) AS name, user.userID, usergroup.groupID
				FROM		sls" . SLS_N . "_library_moderator moderator
				LEFT JOIN	wcf" . WCF_N . "_user user
				ON		(user.userID = moderator.userID)
				LEFT JOIN	wcf" . WCF_N . "_group usergroup
				ON		(usergroup.groupID = moderator.groupID)
				WHERE		libraryID = " . $this->libraryID . "
				ORDER BY	name";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (empty($row['userID']) && empty($row['groupID']))
					continue;
				$moderator = array('name' => $row['name'], 'type' => ($row['userID'] ? 'user' : 'group'), 'id' => ($row['userID'] ? $row['userID'] : $row['groupID']));
				unset($row['name'], $row['userID'], $row['groupID'], $row['libraryID']);
				foreach ($row as $key => $value) {
					if (!in_array($key, $this->moderatorSettings))
						unset($row[$key]);
				}
				$moderator['settings'] = $row;
				$this->moderators[] = $moderator;
			}
		}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'libraryID' => $this->libraryID,
			'library' => $this->library,
			'action' => 'edit',
			'libraryQuickJumpOptions' => Library::getLibrarySelect(array(), false, true),
		));
	}

	/**
	 * @see LibraryAddForm::readLibraryOptions()
	 */
	protected function readLibraryOptions() {
		$this->libraryOptions = Library::getLibrarySelect(array(), true, true, array($this->libraryID));
	}

}

?>