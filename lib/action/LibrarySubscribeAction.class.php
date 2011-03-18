<?php
// sls imports
require_once(SLS_DIR.'lib/action/AbstractLibraryAction.class.php');

/**
 * Subscribes to a library.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	action
 * @category 	Story Library System
 */
class LibrarySubscribeAction extends AbstractLibraryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		$this->library->subscribe();
		$this->executed();
	}
}
?>
