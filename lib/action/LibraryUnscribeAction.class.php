<?php
// sls imports
require_once(SLS_DIR.'lib/action/AbstractLibraryAction.class.php');

/**
 * Unsubscribes from a library.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	action
 * @category 	Story Library System
 */
class LibraryUnsubscribeAction extends AbstractLibraryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		$this->library->unsubscribe();
		$this->executed();
	}
}
?>
