<?php
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches the acp statistics.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.cache
 * @category 	Story Library System
 */
class CacheBuilderACPStat implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();

		// get installation age
		$installationAge = (TIME_NOW - INSTALL_DATE) / 86400;
		if ($installationAge < 1) $installationAge = 1;

		// members
		$sql = "SELECT	COUNT(*) AS members
			FROM	wcf".WCF_N."_user";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['members'] = $row['members'];

		// stories
		$sql = "SELECT	COUNT(*) AS stories
			FROM	sls".SLS_N."_story";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['stories'] = $row['stories'];
		$data['storiesPerDay'] = $row['stories'] / $installationAge;

		// chapters
		$sql = "SELECT	COUNT(*) AS chapters
			FROM	sls".SLS_N."_chapter";
		$row = WCF::getDB()->getFirstRow($sql);
		$data['chapters'] = $row['chapters'];
		$data['chaptersPerDay'] = $row['chapters'] / $installationAge;

		
		// database entries and size
		$data['databaseSize'] = 0;
		$data['databaseEntries'] = 0;
		$sql = "SHOW TABLE STATUS";
		$result = WCF::getDB()->sendQuery($sql);
		while($row = WCF::getDB()->fetchArray($result)) {
			$data['databaseSize'] += $row['Data_length'] + $row['Index_length'];
			$data['databaseEntries'] += $row['Rows'];
		}

		return $data;
	}
}
?>