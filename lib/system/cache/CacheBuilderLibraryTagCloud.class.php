<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilderTagCloud.class.php');

/**
 * Caches the tag cloud of a library.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.cache
 * @category 	Story Library System
 */
class CacheBuilderLibraryTagCloud extends CacheBuilderTagCloud {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		list($cache, $libraryID, $languageIDs) = explode('-', $cacheResource['cache']);
		$data = array();

		// get taggable
		require_once(WCF_DIR.'lib/data/tag/TagEngine.class.php');
		$taggable = TagEngine::getInstance()->getTaggable('de.hpffa.sls.story');
		
		// get tag ids
		$tagIDArray = array();
		$sql = "SELECT		COUNT(*) AS counter, object.tagID
			FROM 		sls".SLS_N."_story story,
					wcf".WCF_N."_tag_to_object object
			WHERE 		story.libraryID = ".$libraryID."
					AND object.taggableID = ".$taggable->getTaggableID()."
					AND object.languageID IN (".$languageIDs.")
					AND object.objectID = story.libraryID
			GROUP BY 	object.tagID
			ORDER BY 	counter DESC";
		$result = WCF::getDB()->sendQuery($sql, 500);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$tagIDArray[$row['tagID']] = $row['counter'];
		}
			
		// get tags
		if (count($tagIDArray)) {
			$sql = "SELECT		name, tagID
				FROM		wcf".WCF_N."_tag
				WHERE		tagID IN (".implode(',', array_keys($tagIDArray)).")";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				$row['counter'] = $tagIDArray[$row['tagID']];
				$this->tags[StringUtil::toLowerCase($row['name'])] = new Tag(null, $row);
			}

			// sort by counter
			uasort($this->tags, array('self', 'compareTags'));
						
			$data = $this->tags;
		}
		
		return $data;
	}
}
?>