<?php
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * Caches the amount of members, chapters and stories, the newest member and the chapters per day.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.cache
 * @category 	Story Library System
 */
class CacheBuilderStat implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();

		// amount of members
		$sql = "SELECT 	COUNT(*) AS amount
			FROM 	wcf".WCF_N."_user";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['members'] = $result['amount'];

		// amount of chapters
		$sql = "SELECT 	COUNT(*) AS amount
			FROM 	sls".SLS_N."_chapter";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['chapters'] = $result['amount'];

		// amount of stories
		$sql = "SELECT 	COUNT(*) AS amount
			FROM 	sls".SLS_N."_story";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['stories'] = $result['amount'];

		// newest member
		$sql = "SELECT 		*
			FROM 		wcf".WCF_N."_user
			ORDER BY 	userID DESC";
		$result = WCF::getDB()->getFirstRow($sql);
		$data['newestMember'] = new User(null, $result);

		// chapters per day
		$days = ceil((TIME_NOW - INSTALL_DATE) / 86400);
		if ($days <= 0) $days = 1;
		$data['chaptersPerDay'] = $data['chapters'] / $days;

		return $data;
	}
}
?>