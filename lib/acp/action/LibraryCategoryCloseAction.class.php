<?php
// sls imports

require_once(SLS_DIR.'lib/acp/action/AbstractLibraryAction.class.php');



/**
 * Closes a category.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.action
 * @category 	Story Library System
 */
class LibraryCategoryCloseAction extends AbstractLibraryAction {
	/**
	 * closing status
	 *
	 * @var integer
	 */
	public $close = 0;

	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['close'])) $this->close = intval($_REQUEST['close']);
		if (!$this->library->isCategory()) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission(array('admin.library.canEditLibrary', 'admin.library.canDeleteLibrary', 'admin.library.canEditPermissions', 'admin.library.canEditModerators'));
		
		if ($this->close == 1) {
			$sql = "INSERT IGNORE INTO	sls".SLS_N."_library_closed_category_to_admin
							(userID, libraryID)
				VALUES			(".WCF::getUser()->userID.", ".$this->libraryID.")";
			WCF::getDB()->sendQuery($sql);
		}
		else {
			$sql = "DELETE FROM	sls".SLS_N."_library_closed_category_to_admin
				WHERE		userID = ".WCF::getUser()->userID."
						AND libraryID = ".$this->libraryID;
			WCF::getDB()->sendQuery($sql);
		}
		
		$this->executed();
	}
}
?>