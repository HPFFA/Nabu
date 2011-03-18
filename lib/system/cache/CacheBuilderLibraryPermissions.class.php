<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the library permissions for a combination of user groups.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.cache
 * @category 	Story Library System
 */
class CacheBuilderLibraryPermissions implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $groupIDs) = explode('-', $cacheResource['cache']);
		$data = array();
		
		$sql = "SELECT		*
			FROM		sls".SLS_N."_library_to_group
			WHERE		groupID IN (".$groupIDs.")";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$libraryID = $row['libraryID'];
			unset($row['libraryID'], $row['groupID']);
			
			foreach ($row as $permission => $value) {
				if ($value == -1) continue;
				
				if (!isset($data[$libraryID][$permission])) $data[$libraryID][$permission] = $value;
				else $data[$libraryID][$permission] = $value || $data[$libraryID][$permission];
			}
		}
		
		if (count($data)) {
			require_once(SLS_DIR.'lib/data/library/Library.class.php');
			Library::inheritPermissions(0, $data);
		}
		
		$data['groupIDs'] = $groupIDs;
		return $data;
	}
}
?>