<?php
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Marks all libraries as read.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	action
 * @category 	Story Library System
 */
class LibraryMarkAllAsReadAction extends AbstractSecureAction {
	/**
	 * @see Action::execute()
	 */
	public function execute() {
		parent::execute();
		
		// set last mark as read time
		WCF::getUser()->setLastMarkAllAsReadTime(TIME_NOW);
		
		// update subscriptions
		if (WCF::getUser()->userID) {
			require_once(SLS_DIR.'lib/data/story/SubscribedStory.class.php');
			SubscribedStory::clearSubscriptions();
			
			$sql = "UPDATE	sls".SLS_N."_library_subscription
				SET	emails = 0
				WHERE	userID = ".WCF::getUser()->userID;
			WCF::getDB()->registerShutdownUpdate($sql);
			$sql = "UPDATE	sls".SLS_N."_story_subscription
				SET	emails = 0
				WHERE	userID = ".WCF::getUser()->userID;
			WCF::getDB()->registerShutdownUpdate($sql);
		}
		
		// reset session
		WCF::getSession()->resetUserData();
		WCF::getSession()->unregister('lastSubscriptionsStatusUpdateTime');
		$this->executed();
		
		if (empty($_REQUEST['ajax'])) HeaderUtil::redirect('index.php'.SID_ARG_1ST);
		exit;
	}
}
?>
