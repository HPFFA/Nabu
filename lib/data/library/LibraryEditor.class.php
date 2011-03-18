<?php

// sls imports
require_once(SLS_DIR.'lib/data/library/Library.class.php');

/**
 * LibraryEditor provides functions to edit the data of a library.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.library
 * @category 	Story Library System
 */
class LibraryEditor extends Library {
	protected $lastChapterTime = null; 
	
	/**
	 * Creates a new LibraryEditor object.
	 * @see Library::__construct()
	 */
	public function __construct($libraryID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($libraryID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	sls".SLS_N."_library
				WHERE	libraryID = ".$libraryID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}


	/**
	 * Increases the story count of this library.
	 * 
	 * @param	integer		$stories
	 * @param	integer		$chapters
	 */
	public function addStories($stories = 1, $chapters = 1) {
		$sql = "UPDATE	sls".SLS_N."_library
			SET	stories = stories + ".$stories.",
				chapters = chapters + ".$chapters."
			WHERE 	libraryID = ".$this->libraryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Increases the chapter count of this library.
	 * 
	 * @param	integer		$chapters
	 */
	public function addChapters($chapters = 1) {
		$sql = "UPDATE	sls".SLS_N."_library
			SET	chapters = chapters + ".$chapters."
			WHERE 	libraryID = ".$this->libraryID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Sets the last chapter of this library.
	 * 
	 * @param 	 Story		$story		story of the lastest chapter
	 */
	public function setLastChapter($story) {
		$sql = "REPLACE INTO	sls".SLS_N."_library_last_chapter
					(libraryID, languageID, storyID) 
			VALUES 		(".$this->libraryID.", ".$story->languageID.", ".$story->storyID.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Sets the last chapter of this library for the given language ids.
	 * 
	 * @param 	string		$languageIDs
	 */
	public function setLastChapters($languageIDs = '') {
		if ($languageIDs === '') {
			// get all language ids
			$sql = "SELECT	DISTINCT languageID
				FROM	sls".SLS_N."_story
				WHERE	libraryID = ".$this->libraryID."
					AND isDeleted = 0
					AND isDisabled = 0
					AND movedStoryID = 0";
			$result = WCF::getDB()->sendQuery($sql);
			while ($row = WCF::getDB()->fetchArray($result)) {
				if (!empty($languageIDs)) $languageIDs .= ',';
				$languageIDs .= $row['languageID'];
			}
		}
		
		if ($languageIDs !== '') {
			$languages = explode(',', $languageIDs);
			foreach ($languages as $languageID) {
				$sql = "SELECT		storyID
					FROM 		sls".SLS_N."_story
					WHERE 		libraryID = ".$this->libraryID."
							AND isDeleted = 0
							AND isDisabled = 0
							AND movedStoryID = 0
							AND languageID = ".$languageID."
					ORDER BY 	lastChapterTime DESC";
				$row = WCF::getDB()->getFirstRow($sql);
				if (!empty($row['storyID'])) {
					$sql = "REPLACE INTO	sls".SLS_N."_library_last_chapter
								(libraryID, languageID, storyID) 
						VALUES 		(".$this->libraryID.", ".$languageID.", ".$row['storyID'].")";
					WCF::getDB()->registerShutdownUpdate($sql);
				}
			}
		}
		else {
			$sql = "DELETE FROM	sls".SLS_N."_library_last_chapter
				WHERE		libraryID = ".$this->libraryID;
			WCF::getDB()->registerShutdownUpdate($sql);
		}
	}
	
	/**
	 * Returns the last chapter time of this library.
	 * 
	 * @return	integer
	 */
	public function getLastChapterTime($languageID = null) {
		if ($this->lastChapterTime == null) {
			$this->lastChapterTime = 0;
			$sql = "SELECT 		story.lastChapterTime
				FROM 		sls".SLS_N."_library_last_chapter last_chapter
				LEFT JOIN 	sls".SLS_N."_story story
				ON 		(story.storyID = last_chapter.storyID)
				WHERE 		last_chapter.libraryID = ".$this->libraryID.
						($languageID != null ? " AND last_chapter.languageID = ".$languageID : "");
			$row = WCF::getDB()->getFirstRow($sql);
			if (isset($row['lastChapterTime'])) $this->lastChapterTime = $row['lastChapterTime'];
		}
		
		return $this->lastChapterTime;
	}
	
	/**
	 * Updates the story and chapter counter for this library.
	 */
	public function refresh() {
		$this->refreshAll($this->libraryID);
	}
	
	/**
	 * Updates the story and chapter counter for the given libraries.
	 * 
	 * @param	string		$libraryIDs
	 */
	public static function refreshAll($libraryIDs) {
		if (empty($libraryIDs)) return;
		
		$sql = "UPDATE	sls".SLS_N."_library library
			SET	stories = (
					SELECT	COUNT(*)
					FROM	sls".SLS_N."_story
					WHERE	libraryID = library.libraryID
						AND isDeleted = 0
						AND isDisabled = 0
						AND movedStoryID = 0
				),
				chapters = (
					SELECT	COUNT(*) + IFNULL(SUM(chapters), 0)
					FROM	sls".SLS_N."_story
					WHERE	libraryID = library.libraryID
						AND isDeleted = 0
						AND isDisabled = 0
						AND movedStoryID = 0
				)
			WHERE	libraryID IN (".$libraryIDs.")";
		WCF::getDB()->registerShutdownUpdate($sql);
	}
	
	/**
	 * Deletes the data of libraries.
	 */
	public static function deleteData($libraryIDs) {
		$sql = "DELETE FROM	sls".SLS_N."_library_closed_category_to_user
			WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
		$sql = "DELETE FROM	sls".SLS_N."_library_closed_category_to_admin
			WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
		$sql = "DELETE FROM	sls".SLS_N."_library_ignored_by_user
			WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		$sql = "DELETE FROM	sls".SLS_N."_library_last_chapter
			WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		$sql = "DELETE FROM	sls".SLS_N."_library_moderator
			WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		$sql = "DELETE FROM	sls".SLS_N."_library_subscription
        		WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		$sql = "DELETE FROM	sls".SLS_N."_library_to_group
			WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		$sql = "DELETE FROM	sls".SLS_N."_library_to_user
			WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		$sql = "DELETE FROM	sls".SLS_N."_library_visit
			WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
		
		$sql = "DELETE FROM	sls".SLS_N."_library
			WHERE		libraryID IN (".$libraryIDs.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Deletes this library.
	 */
	public function delete() {
		// empty library
		// get alle story ids
		$storyIDs = '';
		$sql = "SELECT	storyID
			FROM	sls".SLS_N."_story
			WHERE	libraryID = ".$this->libraryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($storyIDs)) $storyIDs .= ',';
			$storyIDs .= $row['storyID'];
		}
		if (!empty($storyIDs)) {
			// delete stories
			require_once(SLS_DIR.'lib/data/story/StoryEditor.class.php');
			StoryEditor::deleteAllCompletely($storyIDs);
		}
		
		$this->removePositions();
		
		// update sub libraries
		$sql = "UPDATE	sls".SLS_N."_library
			SET	parentID = ".$this->parentID."
			WHERE	parentID = ".$this->libraryID;
		WCF::getDB()->sendQuery($sql);
	
		$sql = "UPDATE	sls".SLS_N."_library_structure
			SET	parentID = ".$this->parentID."
			WHERE	parentID = ".$this->libraryID;
		WCF::getDB()->sendQuery($sql);
		
		// delete library
		self::deleteData($this->libraryID);
	}
	
	/**
	 * Removes a library from all positions in library tree.
	 */
	public function removePositions() {
		// unshift libraries
		$sql = "SELECT 	parentID, position
			FROM	sls".SLS_N."_library_structure
			WHERE	libraryID = ".$this->libraryID;
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$sql = "UPDATE	sls".SLS_N."_library_structure
				SET	position = position - 1
				WHERE 	parentID = ".$row['parentID']."
					AND position > ".$row['position'];
			WCF::getDB()->sendQuery($sql);
		}
		
		// delete library
		$sql = "DELETE FROM	sls".SLS_N."_library_structure
			WHERE		libraryID = ".$this->libraryID;
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Adds a library to a specific position in the library tree.
	 * 
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public function addPosition($parentID, $position = null) {
		// shift libraries
		if ($position !== null) {
			$sql = "UPDATE	sls".SLS_N."_library_structure
				SET	position = position + 1
				WHERE 	parentID = ".$parentID."
					AND position >= ".$position;
			WCF::getDB()->sendQuery($sql);
		}
		
		// get final position
		$sql = "SELECT 	IFNULL(MAX(position), 0) + 1 AS position
			FROM	sls".SLS_N."_library_structure
			WHERE	parentID = ".$parentID."
				".($position ? "AND position <= ".$position : '');
		$row = WCF::getDB()->getFirstRow($sql);
		$position = $row['position'];
		
		// save position
		$sql = "INSERT INTO	sls".SLS_N."_library_structure
					(parentID, libraryID, position)
			VALUES		(".$parentID.", ".$this->libraryID.", ".$position.")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Updates the data of a library.
	 */
	public function update($parentID = null, $title = null, $description = null, $libraryType = null, $image = null, $imageNew = null, $imageShowAsBackground = null, $imageBackgroundRepeat = null, $externalURL = null, $styleID = null, $enforceStyle = null, $daysPrune = null, $sortField = null, $sortOrder = null, $isClosed = null, $countUserChapters = null, $isInvisible = null, $showSubLibraries = null, $allowDescriptionHtml = null, $enableRating = null, $storiesPerPage = null, $searchable = null, $searchableForSimilarStories = null, $ignorable = null, $enableMarkingAsDone = null, $characterMode = NULL, $classificationMode = NULL, $genreMode = NULL, $warningMode = NULL, $additionalFields = array()) {
		$fields = array();
		if ($parentID !== null) $fields['parentID'] = $parentID;
		if ($title !== null) $fields['title'] = $title;
		if ($description !== null) $fields['description'] = $description;
		if ($libraryType !== null) $fields['libraryType'] = $libraryType;
		if ($image !== null) $fields['image'] = $image;
	if ($externalURL !== null) $fields['externalURL'] = $externalURL;
		if ($styleID !== null) $fields['styleID'] = $styleID;
		if ($enforceStyle !== null) $fields['enforceStyle'] = $enforceStyle;
		if ($daysPrune !== null) $fields['daysPrune'] = $daysPrune;
		if ($sortField !== null) $fields['sortField'] = $sortField;
		if ($sortOrder !== null) $fields['sortOrder'] = $sortOrder;
		if ($isClosed !== null) $fields['isClosed'] = $isClosed;
		if ($countUserChapters !== null) $fields['countUserChapters'] = $countUserChapters;
		if ($isInvisible !== null) $fields['isInvisible'] = $isInvisible;
		if ($showSubLibraries !== null) $fields['showSubLibraries'] = $showSubLibraries;
		if ($allowDescriptionHtml !== null) $fields['allowDescriptionHtml'] = $allowDescriptionHtml;
		if ($enableRating !== null) $fields['enableRating'] = $enableRating;
		if ($storiesPerPage !== null) $fields['storiesPerPage'] = $storiesPerPage;
		if ($imageNew !== null) $fields['imageNew'] = $imageNew;
		if ($imageShowAsBackground !== null) $fields['imageShowAsBackground'] = $imageShowAsBackground;
		if ($imageBackgroundRepeat !== null) $fields['imageBackgroundRepeat'] = $imageBackgroundRepeat;
		if ($searchable !== null) $fields['searchable'] = $searchable;
		if ($searchableForSimilarStories !== null) $fields['searchableForSimilarStories'] = $searchableForSimilarStories;
		if ($ignorable !== null) $fields['ignorable'] = $ignorable;
		if ($enableMarkingAsDone !== null) $fields['enableMarkingAsDone'] = $enableMarkingAsDone;
		if ($characterMode !== null) $fields['characterMode'] = $characterMode;
		if ($classificationMode !== null) $fields['classificationMode'] = $classificationMode;
		if ($genreMode !== null) $fields['genreMode'] = $genreMode;
		if ($warningMode !== null) $fields['warningMode'] = $warningMode;
		
		$this->updateData(array_merge($fields, $additionalFields));
	}
	
	/**
	 * Updates the data of a library.
	 *
	 * @param 	array		$fields
	 */
	public function updateData($fields = array()) { 
		$updates = '';
		foreach ($fields as $key => $value) {
			if (!empty($updates)) $updates .= ',';
			$updates .= $key.'=';
			if (is_int($value)) $updates .= $value;
			else $updates .= "'".escapeString($value)."'";
		}
		
		if (!empty($updates)) {
			$sql = "UPDATE	sls".SLS_N."_library
				SET	".$updates."
				WHERE	libraryID = ".$this->libraryID;
			WCF::getDB()->sendQuery($sql);
		}
	}
	
	/**
	 * Creates a new library.
	 * 
	 * @return	LibraryEditor
	 */
	public static function create($parentID, $position, $title, $description = '', $libraryType = 0, $image = '', $imageNew = '', $imageShowAsBackground = 1, $imageBackgroundRepeat = 'no', $externalURL = '', $time = TIME_NOW,  $styleID = 0, $enforceStyle = 0, $daysPrune = 0, $sortField = '', $sortOrder = '', $isClosed = 0, $countUserChapters = 1, $isInvisible = 0, $showSubLibraries = 1, $allowDescriptionHtml = 0, $enableRating = -1, $storiesPerPage = 0, $searchable = 1, $searchableForSimilarStories = 1, $ignorable = 1, $enableMarkingAsDone = 0, $characterMode = 0, $classificationMode = 0, $genreMode = 0, $warningMode = 0, $additionalFields = array()) {
		// save data
		$libraryID = self::insert($title, array_merge($additionalFields, array(
			'parentID' => $parentID,
			'description' => $description,
			'libraryType' => $libraryType,
			'image' => $image,
			'externalURL' => $externalURL,
			'time' => $time,
			'styleID' => $styleID,
			'enforceStyle' => $enforceStyle,
			'daysPrune' => $daysPrune,
			'sortField' => $sortField,
			'sortOrder' => $sortOrder,
			'countUserChapters' => $countUserChapters,
			'isInvisible' => $isInvisible,
			'showSubLibraries' => $showSubLibraries,
			'allowDescriptionHtml' => $allowDescriptionHtml,
			'enableRating' => $enableRating,
			'storiesPerPage' => $storiesPerPage,
			'imageNew' => $imageNew,
			'imageShowAsBackground' => $imageShowAsBackground,
			'imageBackgroundRepeat' => $imageBackgroundRepeat,
			'searchable' => $searchable,
			'searchableForSimilarStories' => $searchableForSimilarStories,
			'ignorable' => $ignorable,
			'enableMarkingAsDone' => $enableMarkingAsDone,
			'characterMode' => $characterMode,
			'classificationMode' => $classificationMode,
			'warningMode' => $warningMode,
			'genreMode' => $genreMode,
		)));
		
		// get library
		$library = new LibraryEditor($libraryID, null, null, false);
		
		// save position
		$library->addPosition($parentID, $position);
		
		// return new library
		return $library;
}

	/**
	 * Creates the library row in database table.
	 *
	 * @param 	string 		$title
	 * @param 	array		$additionalFields
	 * @return	integer		new library id
	 */
	public static function insert($title, $additionalFields = array()) {
		$keys = $values = '';
		foreach ($additionalFields as $key => $value) {
			$keys .= ','.$key;
			if (is_int($value)) $values .= ",".$value;
			else $values .= ",'".escapeString($value)."'";
		}
	
		$sql = "INSERT INTO	sls".SLS_N."_library
					(title
					".$keys.")
			VALUES		('".escapeString($title)."'
					".$values.")";
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getInsertID();
	}
	
	/**
	 * Updates the position of a library directly.
	 *
	 * @param	integer		$libraryID
	 * @param	integer		$parentID
	 * @param	integer		$position
	 */
	public static function updatePosition($libraryID, $parentID, $position) {
		$sql = "UPDATE	sls".SLS_N."_library
			SET	parentID = ".$parentID."
			WHERE 	libraryID = ".$libraryID;
		WCF::getDB()->sendQuery($sql);
		
		$sql = "REPLACE INTO	sls".SLS_N."_library_structure
					(libraryID, parentID, position)
			VALUES		(".$libraryID.", ".$parentID.", ".$position.")";
		WCF::getDB()->sendQuery($sql);
	}
}
?>