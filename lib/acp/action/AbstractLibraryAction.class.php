<?php
// sls imports 
require_once(SLS_DIR.'lib/data/library/LibraryEditor.class.php');
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
/**
 * Provides default implementations for library actions.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.action
 * @category 	Story Library System
 */
class AbstractLibraryAction extends AbstractAction {
	/**
	 * library id
	 *
	 * @var integer
	 */
	public $libraryID = 0;
	
	/**
	 * library editor object
	 *
	 * @var LibraryEditor
	 */
	public $library = null;
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['libraryID'])) $this->libraryID = intval($_REQUEST['libraryID']);
		$this->library = new LibraryEditor($this->libraryID);
		if (!$this->library->libraryID) {
			throw new IllegalLinkException();
		}
	}
}
?>

