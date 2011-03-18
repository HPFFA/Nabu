<?php
// wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Caches last chapters and libraries clicks.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.cache
 * @category 	Story Library System
 */
class CacheBuilderLibraryData implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array('lastChapters' => array(), 'counts' => array(), 'classification' => array(), 'genre' => array(), 'character' => array(), 'warning' => array());
		
		// counts
		$sql = "SELECT	libraryID, clicks, stories, chapters
			FROM 	sls".SLS_N."_library";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['counts'][$row['libraryID']] = $row;
		}

                // classification
		$sql = "SELECT      library.libraryID, classification.classificationID, classification.name
			FROM        sls".SLS_N."_library_classification library
                        LEFT JOIN   sls".SLS_N."_classification classification
                        ON          (library.classificationID = classification.classificationID)
                        ORDER BY    classification.name DESC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['classification'][$row['libraryID']] = $row;
		}

                // genre
		$sql ="SELECT      library.libraryID, genre.genreID, genre.name
			FROM        sls".SLS_N."_library_genre library
                        LEFT JOIN   sls".SLS_N."_genre genre
                        ON          (library.genreID = genre.genreID)
                        GROUP BY    library.libraryID ";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['genre'][$row['libraryID']] = $row;
		}
                
                // characters
		$sql = "SELECT      library.libraryID, characters.characterID, characters.name
			FROM        sls".SLS_N."_library_character library
                        LEFT JOIN   sls".SLS_N."_character characters
                        ON          (library.characterID = characters.characterID)
                        ORDER BY    characters.name DESC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['characters'][$row['libraryID']] = $row;
		}

                // warnings
		$sql = "SELECT      library.libraryID, warnings.warningID, warnings.name
			FROM        sls".SLS_N."_library_warning library
                        LEFT JOIN   sls".SLS_N."_warning warnings
                        ON          (library.warningID = warnings.warningID)
                        ORDER BY    warnings.name DESC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['warning'][$row['libraryID']] = $row;
		}
		
		// last chapters
		$sql = "SELECT		story.title, story.lastChapterTime,
					story.lastAuthorID, story.lastAuthor,
					last_chapter.*
			FROM 		sls".SLS_N."_library_last_chapter last_chapter
			LEFT JOIN	sls".SLS_N."_story story
			ON		(story.storyID = last_chapter.storyID)
			ORDER BY	last_chapter.libraryID,
					story.lastChapterTime DESC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$data['lastChapters'][$row['libraryID']][$row['languageID']] = $row;
		}
		
		return $data;
	}
}
?>