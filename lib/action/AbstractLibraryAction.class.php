<?php
// sls imports
require_once(SLS_DIR.'lib/data/library/Library.class.php');

// wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Abstract library action.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	action
 * @category 	Story Library System
 */
class AbstractLibraryAction extends AbstractSecureAction {
	public $libraryID = 0;
	public $library = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get board
		if (isset($_REQUEST['libraryID'])) $this->libraryID = intval($_REQUEST['libraryID']);
		$this->library = new Library($this->libraryID);
		$this->library->enter();
	}
	
	/**
	 * @see AbstractAction::executed()
	 */
	protected function executed() {
		parent::executed();
		
		if (empty($_REQUEST['ajax'])) HeaderUtil::redirect('index.php?page=Library&libraryID='.$this->libraryID.SID_ARG_2ND_NOT_ENCODED);
		exit;
	}
}
?>
