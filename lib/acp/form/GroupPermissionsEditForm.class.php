<?php
// sls imports
require_once(SLS_DIR.'lib/data/library/Library.class.php');

// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/data/user/group/Group.class.php');

/**
 * Shows the group to libraries permissions list.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.form
 * @category 	Story Library System
 */
class GroupPermissionsEditForm extends ACPForm {
	// system
	public $templateName = 'permissionsEdit';
	public $activeMenuItem = 'wcf.acp.menu.link.group';
	public $neededPermissions = 'admin.library.canEditPermissions';
	
	/**
	 * user group id
	 * 
	 * @var	integer
	 */
	public $groupID = 0;
	
	/**
	 * user group editor object
	 * 
	 * @var	GroupEditor
	 */
	public $group = null;
	
	/**
	 * list of available user groups
	 * 
	 * @var	array
	 */
	public $groups = array();
	
	/**
	 * existing library structure
	 * 
	 * @var	array
	 */
	public $libraryStructure = null;
	
	/**
	 * list of existing libraries
	 * 
	 * @var	array
	 */
	public $libraries = null;
	
	/**
	 * structured library list
	 * 
	 * @var	array
	 */
	public $libraryList = array();
	
	/**
	 * list of library permissions
	 * 
	 * @var	array
	 */
	public $libraryPermissions = array();
	
	/**
	 * list of available permissions
	 * 
	 * @var	array
	 */
	public $permissionSettings = null;
	
	/**
	 * name of selected permission
	 * 
	 * @var	string
	 */
	public $permissionName = '';
	
	/**
	 * list of global permissions
	 * 
	 * @var	array
	 */
	public $globalPermissions = array();
	
	/**
	 * list of active library permissions
	 * 
	 * @var	array
	 */
	public $activeLibraryPermissions = array();
	
	/**
	 * list of closed categories
	 * 
	 * @var	array
	 */
	public $closedCategories = array();
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get group
		if (isset($_REQUEST['groupID'])) {
			$this->groupID = intval($_REQUEST['groupID']);
			require_once(WCF_DIR.'lib/data/user/group/GroupEditor.class.php');
			$this->group = new GroupEditor($this->groupID);
			if (!$this->group->groupID) {
				throw new IllegalLinkException();
			}
			if (!$this->group->isAccessible()) {
				throw new PermissionDeniedException();
			}
		}
		
		// active permission
		if (isset($_REQUEST['permissionName'])) $this->permissionName = $_REQUEST['ermissionName'];
		
		$this->readPermissionSettings();
	}
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['libraryPermissions']) && is_array($_POST['LibraryPermissions'])) $this->libraryPermissions = $_POST['libraryPermissions'];
	}
	
	/**
 	 * Validates the given permissions.
 	 */
 	public function validatePermissions() {
 		$validPermissions = array_flip($this->permissionSettings);
 		
 		foreach ($this->libraryPermissions as $libraryID => $permissions) {
 			foreach ($permissions as $key => $value) {
 				if (!isset($validPermissions[$key])) {
 					unset($this->libraryPermissions[$libraryID][$key]);
 				}
 				
 				if (($value != -1 && $value != 0 && $value =! 1)) {
 					throw new UserInputException();
 				}
 			}
 		}
 	}
 	
 	/**
 	 * @see Form::validate()
 	 */
 	public function validate() {
 		parent::validate();
 		
 		$this->validatePermissions();
 	}
 	
 	/**
 	 * @see Form::save()
 	 */
 	public function save() {
 		parent::save();
 		
 		$inserts = $fields = '';
 		foreach ($this->permissionSettings as $name) {
 			$fields .= ', '.$name;
 		}
 		
 		foreach ($this->libraryPermissions as $libraryID => $permissions) {
 			$noDefaultValue = false;
 			foreach ($permissions as $value) {
 				if ($value != -1) $noDefaultValue = true;
 			}
 			if (!$noDefaultValue) continue;
 			
 			if (!empty($inserts)) $inserts .= ',';
 			$inserts .= '('.intval($libraryID).', '.$this->groupID;
 			foreach ($this->permissionSettings as $name) {
 				$inserts .= ', '.(isset($permissions[$name]) ? $permissions[$name] : -1);
 			}
 			$inserts .= ')';
 		}
 		
 		// delete old entries
 		$sql = "DELETE FROM	sls".SLS_N."_library_to_group
 			WHERE		groupID = ".$this->groupID;
 		WCF::getDB()->sendQuery($sql);
 			
 		if (!empty($inserts)) {
 			$sql = "INSERT IGNORE INTO	sls".SLS_N."_library_to_group
 							(libraryID, groupID".$fields.")
 				VALUES			".$inserts;
			WCF::getDB()->sendQuery($sql);
		}
		
		// reset permissions cache
		WCF::getCache()->clear(SLS_DIR . 'cache/', 'cache.libraryPermissions-*', true);
		// reset sessions
		Session::resetSessions(array(), true, false);
		$this->saved();
		
		// show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

	
		$this->readClosedCategories();
		$this->groups = Group::getAllGroups();
		$this->readLibraryPermissions();
		$this->loadGlobalPermissions();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$this->renderLibraries();
		WCF::getTPL()->assign(array(
			'libraryStructure' => $this->libraryStructure,
			'libraries' => $this->libraryList,
			'groupID' => $this->groupID,
			'group' => $this->group,
			'globalPermissions' => $this->globalPermissions,
			'libraryPermissions' => $this->activeLibraryPermissions,
			'type' => 'group',
			'permissionName' => $this->permissionName,
			'groups' => $this->groups,
			'availablePermissions' => $this->permissionSettings
		));
	}
	
	/**
	 * Gets the list of closed categories.
	 */
	protected function readClosedCategories() {
		$sql = "SELECT	libraryID
			FROM	sls".SLS_N."_library_closed_category_to_admin
			WHERE	userID = ".WCF::getUser()->userID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->closedCategories[$row['libraryID']] = $row['libraryID'];
		}
	}
	
	/**
	 * Gets a list of library permissions.
	 */
	protected function readLibraryPermissions() {
		$sql = "SELECT		*
			FROM		sls".SLS_N."_library_to_group
			WHERE		groupID = ".$this->groupID."
			ORDER BY	libraryID";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$libraryID = $row['libraryID'];
			unset($row['libraryID'], $row['groupID']);
			$this->activeLibraryPermissions[$libraryID] = $row;
		}
	}
	
	/**
	 * Renders the ordered list of all libraries.
	 */
	protected function renderLibraries() {
		// get library structure from cache		
		$this->libraryStructure = WCF::getCache()->get('library', 'libraryStructure');
		// get libraries from cache
		$this->libraries = WCF::getCache()->get('library', 'libraries');
				
		$this->makeLibraryList();
	}
	
	/**
	 * Renders one level of the library structure.
	 *
	 * @param	integer		parentID		render the sublibraries of the library with the given id
	 * @param	integer		depth			the depth of the current level
	 * @param	integer		openParents		helping variable for rendering the html list in the librarylist template
	 */
	protected function makeLibraryList($parentID = 0, $depth = 1, $openParents = 0) {
		if (!isset($this->libraryStructure[$parentID])) return;
		
		$i = 0; $children = count($this->libraryStructure[$parentID]);
		foreach ($this->libraryStructure[$parentID] as $libraryID) {
			$library = $this->libraries[$libraryID];
			
			// librarylist depth on index
			$childrenOpenParents = $openParents + 1;
			$hasChildren = isset($this->libraryStructure[$libraryID]);
			$last = $i == count($this->libraryStructure[$parentID]) - 1;
			if ($hasChildren && !$last) $childrenOpenParents = 1;
			$this->libraryList[] = array('depth' => $depth, 'hasChildren' => $hasChildren, 'openParents' => ((!$hasChildren && $last) ? ($openParents) : (0)), 'library' => $library, 'parentID' => $parentID, 'position' => $i+1, 'maxPosition' => $children, 'open' => (!isset($this->closedCategories[$libraryID]) ? 1 : 0));
			
			// make next level of the library list
			$this->makeLibraryList($libraryID, $depth + 1, $childrenOpenParents);
			
			$i++;
		}
	}
	
	/**
	 * Gets available permission settings.
	 */
	protected function readPermissionSettings() {
		$sql = "SHOW COLUMNS FROM sls".SLS_N."_library_to_group";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if ($row['Field'] != 'libraryID' && $row['Field'] != 'groupID') {
				// check modules
				switch ($row['Field']) {
					case 'canMarkAsDoneOwnStory': 
						if (!MODULE_STORY_MARKING_AS_DONE) continue 2;
						break;
					
					case 'canSetTags':
						if (!MODULE_TAGGING) continue 2;
						break;
				}
				
				$this->permissionSettings[] = $row['Field'];
			}
		}
	}
	
	/**
	 * Gets a list of all global permissions.
	 */
	protected function loadGlobalPermissions() {
		$this->globalPermissions = array(
			'canViewLibrary' => $this->group->getGroupOption('user.library.canViewLibrary'),
			'canEnterLibrary' => $this->group->getGroupOption('user.library.canEnterLibrary'),
			'canReadStory' => $this->group->getGroupOption('user.library.canReadStory'),
			'canReadOwnStory' => $this->group->getGroupOption('user.library.canReadOwnStory'),
			'canStartStory' => $this->group->getGroupOption('user.library.canStartStory'),
			'canReplyStory' => $this->group->getGroupOption('user.library.canReplyStory'),
			'canReplyOwnStory' => $this->group->getGroupOption('user.library.canReplyOwnStory'),
			'canStartStoryWithoutModeration' => $this->group->getGroupOption('user.library.canStartStoryWithoutModeration'),
			'canReplyStoryWithoutModeration' => $this->group->getGroupOption('user.library.canReplyStoryWithoutModeration'),
			'canRateStory' => $this->group->getGroupOption('user.library.canRateStory'),
			'canDeleteOwnChapter' => $this->group->getGroupOption('user.library.canDeleteOwnChapter'),
			'canEditOwnChapter' => $this->group->getGroupOption('user.library.canEditOwnChapter')
		);
		
		if (MODULE_STORY_MARKING_AS_DONE) {
			$this->globalPermissions['canMarkAsDoneOwnStory'] = $this->group->getGroupOption('user.library.canMarkAsDoneOwnStory');
		}
		
		if (MODULE_TAGGING) {
			$this->globalPermissions['canSetTags'] = $this->group->getGroupOption('user.library.canSetTags');
		}
		
	}

}
?>
