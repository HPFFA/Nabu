<?php
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Shows the permissions edit button in group list.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.event.listener
 * @category 	Story Library System
 */
class GroupListPagePermissionsButtonListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		foreach ($eventObj->groups as $key => $group) {
			if (!isset($eventObj->groups[$key]['additionalButtons'])) $eventObj->groups[$key]['additionalButtons'] = '';
			
			if ($eventObj->groups[$key]['editable'] && WCF::getUser()->getPermission('admin.board.canEditPermissions')) {
				$eventObj->groups[$key]['additionalButtons'] .= ' <a href="index.php?form=GroupPermissionsEdit&amp;groupID='.$group['groupID'].'&amp;packageID='.PACKAGE_ID.SID_ARG_2ND.'"><img src="'.RELATIVE_SLS_DIR.'icon/permissionsS.png" alt="" title="'.WCF::getLanguage()->get('sls.acp.library.group.permissions.edit').'" /></a>';
			}
			else {
				$eventObj->groups[$key]['additionalButtons'] .= ' <img src="'.RELATIVE_SLS_DIR.'icon/permissionsDisabledS.png" alt="" title="'.WCF::getLanguage()->get('sls.acp.library.group.permissions.edit').'" />';
			}
			
			if ($eventObj->groups[$key]['editable'] && WCF::getUser()->getPermission('admin.library.canEditModerators')) {
				$eventObj->groups[$key]['additionalButtons'] .= ' <a href="index.php?form=ModeratorPermissionsEdit&amp;groupID='.$group['groupID'].'&amp;packageID='.PACKAGE_ID.SID_ARG_2ND.'"><img src="'.RELATIVE_SLS_DIR.'icon/moderatorPermissionsS.png" alt="" title="'.WCF::getLanguage()->get('sls.acp.library.moderator.permissions.edit').'" /></a>';
			}
			else {
				$eventObj->groups[$key]['additionalButtons'] .= ' <img src="'.RELATIVE_SLS_DIR.'icon/moderatorPermissionsDisabledS.png" alt="" title="'.WCF::getLanguage()->get('sls.acp.library.moderator.permissions.edit').'" />';
			}
		}
	}
}
?>
