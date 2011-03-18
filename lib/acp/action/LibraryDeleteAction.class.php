<?php
// sls imports
require_once(SLS_DIR.'lib/acp/action/AbstractLibraryAction.class.php');

/**
 * Deletes a library.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.action
 * @category 	Story Library System
 */
class LibraryDeleteAction extends AbstractLibraryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.library.canDeleteLibrary');
				
		// delete library
		$this->library->delete();
		
		// reset cache
		WCF::getCache()->clearResource('library');
		$this->executed();
		
		// forward to list page
		HeaderUtil::redirect('index.php?page=LibraryList&deletedLibraryID='.$this->libraryID.'&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>