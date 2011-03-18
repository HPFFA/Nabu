<?php
// sls imports
require_once(SLS_DIR.'lib/data/warning/Warning.class.php');

/**
 * WarningEditor provides functions to edit the data of a warning.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.warning
 * @category 	Story Library System
 */
class WarningEditor extends Warning {

	/**
	 * Creates a new WarningEditor object.
	 * @see Genre::__construct()
	 */
	public function __construct($warningID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($warningID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	sls".SLS_N."_warning
				WHERE	warningID = ".$warningID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}


	/**
	 * Deletes the data of warnings.
	 */
	public static function deleteData($warningIDs) {
		$sql = "DELETE FROM	sls".SLS_N."_library_to_warning
			WHERE		warningID IN (".$warningIDs.")";
		WCF::getDB()->sendQuery($sql);

	}

	/**
	 * Deletes this warning.
	 */
	public function delete() {
		$sql = "UPDATE FROM	sls".SLS_N."_story
                        SET             warningID = 0
			WHERE		warningID IN (".$warningIDs.")";
		WCF::getDB()->sendQuery($sql);

		// delete warning
		self::deleteData($this->warningID);
	}



	/**
	 * Updates the data of a genre.
	 */
	public function update($warningID = null, $warning = null, $description = null, $additionalFields = array()) {
		$fields = array();
		if ($warningID !== null) $fields['warningID'] = $warningID;
		if ($warning !== null) $fields['warning'] = $warning;
		if ($description !== null) $fields['description'] = $description;


		$this->updateData(array_merge($fields, $additionalFields));
	}

	/**
	 * Updates the data of a warning.
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
			$sql = "UPDATE	sls".SLS_N."_warning
				SET	".$updates."
				WHERE	warningID = ".$this->warningID;
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * Creates a new warning.
	 *
	 * @return	WarningEditor
	 */
	public static function create($warning, $description = '', $additionalFields = array()) {
		// save data
		$warningD = self::insert($warning, array_merge($additionalFields, array(
			'description' => $description
		)));

		// get warning
		$warning = new WarningEditor($warningID, null, null, false);

		// return new warning
		return $warning;
}

	/**
	 * Creates the warning row in database table.
	 *
	 * @param 	string 		$warning
	 * @param 	array		$additionalFields
	 * @return	integer		new warning id
	 */
	public static function insert($warning, $additionalFields = array()) {
		$keys = $values = '';
		foreach ($additionalFields as $key => $value) {
			$keys .= ','.$key;
			if (is_int($value)) $values .= ",".$value;
			else $values .= ",'".escapeString($value)."'";
		}

		$sql = "INSERT INTO	sls".SLS_N."_warning
					(name
					".$keys.")
			VALUES		('".escapeString($warning)."'
					".$values.")";
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getInsertID();
	}
/**
	 * Marks this warning.
	 */
	public function mark() {
		$markedWarnings = self::getMarkedWarnings();
		if ($markedWarnings == null || !is_array($markedWarnings)) {
			$markedWarnings = array($this->warningID);
			WCF::getSession()->register('markedWarnings', $markedWarnings);
		}
		else {
			if (!in_array($this->warningID, $markedWarnings)) {
				array_push($markedWarnings, $this->warningID);
				WCF::getSession()->register('markedWarnings', $markedWarnings);
			}
		}
	}

	/**
	 * Unmarks this warning.
	 */
	public function unmark() {
		$markedWarnings = self::getMarkedWarnings();
		if (is_array($markedWarnings) && in_array($this->warningID, $markedWarnings)) {
			$key = array_search($this->warningID, $markedWarnings);

			unset($markedWarnings[$key]);
			if (count($markedWarnings) == 0) {
				self::unmarkAll();
			}
			else {
				WCF::getSession()->register('markedWarnings', $markedWarnings);
			}
		}
	}

	/**
	 * Returns the marked warnings.
	 *
	 * @return	array		marked warnings
	 */
	public static function getMarkedWarnings() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedWarnings'])) {
			return $sessionVars['markedWarnings'];
		}
		return null;
	}
	/**
	 * Unmarks all marked warnings.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedWarnings');
	}
}
?>
