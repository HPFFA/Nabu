<?php

// sls imports
require_once(SLS_DIR.'lib/data/library/Library.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all libraries.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.page
 * @category 	Story Library System
 */
class LibraryListPage extends AbstractPage {
	// system
	public $templateName = 'libraryList';
	
	/**
	 * library structure
	 * 
	 * @var	array
	 */
	public $libraryStructure = null;
	
	/**
	 * list of libraries
	 * 
	 * @var	array
	 */
	public $libraries = null;
	
	/**
	 * structured list of libraries
	 * 
	 * @var	array
	 */
	public $libraryList = array();
	
	/**
	 * library id
	 * 
	 * @var	integer
	 */
	public $deletedLibraryID = 0;
	
	/**
	 * closed categories
	 * 
	 * @var	array
	 */
	public $closedCategories = array();
		
	/**
	 * If the list was sorted successfully
	 * @var boolean
	 */
	public $successfulSorting = false;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['successfulSorting'])) $this->successfulSorting = true;
		if (isset($_REQUEST['deletedLibraryID'])) $this->deletedLibraryID = intval($_REQUEST['deletedLibraryID']);
	}
	
	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->readClosedCategories();
		$this->renderLibraries();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'libraries' => $this->libraryList,
			'deletedLibraryID' => $this->deletedLibraryID,
			'successfulSorting' => $this->successfulSorting
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wbb.acp.menu.link.content.library.view');
		
		// check permission
		WCF::getUser()->checkPermission(array('admin.library.canEditLibrary', 'admin.library.canDeleteLibrary', 'admin.library.canEditPermissions', 'admin.library.canEditModerators'));
		
		parent::show();
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
}
?>
