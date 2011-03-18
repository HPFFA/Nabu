<?php
// sls imports
require_once(SLS_DIR.'lib/action/AbstractLibraryAction.class.php');

/**
 * Marks a library as read.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	action
 * @category 	Story Library System
 */
class LibraryMarkAsReadAction extends AbstractLibraryAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		$this->library->markAsRead();
		WCF::getSession()->unregister('lastSubscriptionsStatusUpdateTime');
		$this->executed();
	}
}
?>
