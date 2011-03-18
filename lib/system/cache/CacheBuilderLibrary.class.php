<?php
// sls imports
require_once(SLS_DIR.'lib/data/library/Library.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');
require_once(WCF_DIR.'lib/data/user/group/Group.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * Caches all libraries, the structure of libraries and all moderators.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.cache
 * @category 	Story Library System
 */
class CacheBuilderLibrary implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('libraries' => array(), 'libraryStructure' => array(), 'moderators' => array());
		
		// libraries
		$sql = "SELECT	*
			FROM 	sls".SLS_N."_library";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['libraries'][$row['libraryID']] = new Library(null, $row);
		}
		
		// library structure
		$sql = "SELECT		*
			FROM 		sls".SLS_N."_library_structure
			ORDER BY 	parentID ASC, position ASC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['libraryStructure'][$row['parentID']][] = $row['libraryID'];
		}
		
		// library moderators
		$sql = "SELECT 		user.username, wcf_group.groupName,
					moderator.*, IFNULL(user.username, wcf_group.groupName) AS name
			FROM 		sls".SLS_N."_library_moderator moderator
			LEFT JOIN 	wcf".WCF_N."_user user
			ON		(user.userID = moderator.userID)
			LEFT JOIN 	wcf".WCF_N."_group wcf_group
			ON 		(wcf_group.groupID = moderator.groupID)
			ORDER BY 	libraryID,
					name";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (empty($row['name'])) continue;
			
			if ($row['userID'] != 0) {
				$object = new User(null, $row); 
				$key = 'u' . $row['userID'];
			}
			else {
				$object = new Group(null, $row);
				$key = 'g' . $row['groupID'];
			}
			$data['moderators'][$row['libraryID']][$key] = $object;
		}
		
		return $data;
	}
}
?>