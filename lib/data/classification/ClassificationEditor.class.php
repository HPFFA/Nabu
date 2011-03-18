<?php
// sls imports
require_once(SLS_DIR.'lib/data/classification/Classification.class.php');
require_once(SLS_DIR.'lib/data/library/Library.class.php');

/**
 * ClassificationEditor provides functions to edit the data of a classification.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.classification
 * @category 	Story Library System
 */
class ClassificationEditor extends Classification {

	/**
	 * Creates a new ClassificationEditor object.
	 * @see Classification::__construct()
	 */
	public function __construct($classificationID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($classificationID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	sls".SLS_N."_classification
				WHERE	classificationID = ".$classificationID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}


	/**
	 * Deletes the data of classifications.
	 */
	public static function deleteData($classificationIDs) {
		$sql = "DELETE FROM	sls".SLS_N."_library_to_classification
			WHERE		classificationID IN (".$classificationIDs.")";
		WCF::getDB()->sendQuery($sql);

	}

	/**
	 * Deletes this classification.
	 */
	public function delete() {
		$sql = "UPDATE FROM	sls".SLS_N."_story
                        SET             classificationID = 0
			WHERE		classificationID IN (".$classificationIDs.")";
		WCF::getDB()->sendQuery($sql);

		// delete classification
		self::deleteData($this->classificationID);
	}



	/**
	 * Updates the data of a classification.
	 */
	public function update($classificationID = null, $classification = null, $description = null, $additionalFields = array()) {
		$fields = array();
		if ($classificationID !== null) $fields['classificationID'] = $classificationID;
		if ($classification !== null) $fields['classification'] = $classification;
		if ($description !== null) $fields['description'] = $description;


		$this->updateData(array_merge($fields, $additionalFields));
	}

	/**
	 * Updates the data of a classification.
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
			$sql = "UPDATE	sls".SLS_N."_classification
				SET	".$updates."
				WHERE	classificationID = ".$this->classificationID;
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * Creates a new classification.
	 *
	 * @return	ClassificationEditor
	 */
	public static function create($classification, $description = '', $additionalFields = array()) {
		// save data
		$classificationID = self::insert($classification, array_merge($additionalFields, array(
			'description' => $description
		)));

		// get classification
		$classification = new ClassificationEditor($classificationID, null, null, false);

		// return new classification
		return $classification;
}

	/**
	 * Creates the classification row in database table.
	 *
	 * @param 	string 		$classification
	 * @param 	array		$additionalFields
	 * @return	integer		new classification id
	 */
	public static function insert($classification, $additionalFields = array()) {
		$keys = $values = '';
		foreach ($additionalFields as $key => $value) {
			$keys .= ','.$key;
			if (is_int($value)) $values .= ",".$value;
			else $values .= ",'".escapeString($value)."'";
		}

		$sql = "INSERT INTO	sls".SLS_N."_classification
					(name
					".$keys.")
			VALUES		('".escapeString($classification)."'
					".$values.")";
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getInsertID();
	}
/**
	 * Marks this classification.
	 */
	public function mark() {
		$markedClassifications = self::getMarkedClassifications();
		if ($markedClassifications == null || !is_array($markedClassifications)) {
			$markedClassifications = array($this->classificationID);
			WCF::getSession()->register('markedClassifications', $markedClassifications);
		}
		else {
			if (!in_array($this->classificationID, $markedClassifications)) {
				array_push($markedClassifications, $this->classificationID);
				WCF::getSession()->register('markedClassifications', $markedClassifications);
			}
		}
	}

	/**
	 * Unmarks this classification.
	 */
	public function unmark() {
		$markedClassifications = self::getMarkedClassifications();
		if (is_array($markedClassifications) && in_array($this->classificationID, $markedClassifications)) {
			$key = array_search($this->classificationID, $markedClassifications);

			unset($markedClassifications[$key]);
			if (count($markedClassifications) == 0) {
				self::unmarkAll();
			}
			else {
				WCF::getSession()->register('markedClassifications', $markedClassifications);
			}
		}
	}

	/**
	 * Returns the marked classifications.
	 *
	 * @return	array		marked classifications
	 */
	public static function getMarkedClassifications() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedClassifications'])) {
			return $sessionVars['markedClassifications'];
		}
		return null;
	}
	/**
	 * Unmarks all marked classifications.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedClassifications');
	}

}
?>
