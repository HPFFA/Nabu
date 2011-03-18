<?php
// sls imports
require_once(SLS_DIR.'lib/data/character/Character.class.php');
require_once(SLS_DIR.'lib/data/library/Library.class.php');

/**
 * GenreEditor provides functions to edit the data of a character.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.character
 * @category 	Story Library System
 */
class CharacterEditor extends Character {

	/**
	 * Creates a new CharacterEditor object.
	 * @see Genre::__construct()
	 */
	public function __construct($characterID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($characterID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	sls".SLS_N."_character
				WHERE	characterID = ".$characterID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}


	/**
	 * Deletes the data of characters.
	 */
	public static function deleteData($characterIDs) {
		$sql = "DELETE FROM	sls".SLS_N."_library_to_character
			WHERE		characterID IN (".$characterIDs.")";
		WCF::getDB()->sendQuery($sql);

	}

	/**
	 * Deletes this character.
	 */
	public function delete() {
		$sql = "UPDATE FROM	sls".SLS_N."_story
                        SET             characterID = 0
			WHERE		characterID IN (".$characterIDs.")";
		WCF::getDB()->sendQuery($sql);

		// delete character
		self::deleteData($this->characterID);
	}



	/**
	 * Updates the data of a character.
	 */
	public function update($characterID = null, $character = null, $description = null, $additionalFields = array()) {
		$fields = array();
		if ($characterID !== null) $fields['characterID'] = $characterID;
		if ($character !== null) $fields['name'] = $character;
		if ($description !== null) $fields['description'] = $description;


		$this->updateData(array_merge($fields, $additionalFields));
	}

	/**
	 * Updates the data of a genre.
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
			$sql = "UPDATE	sls".SLS_N."_character
				SET	".$updates."
				WHERE	characterID = ".$this->characterID;
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * Creates a new character.
	 *
	 * @return	CharacterEditor
	 */
	public static function create($character, $description = '', $additionalFields = array()) {
		// save data
		$characterID = self::insert($character, array_merge($additionalFields, array(
			'description' => $description
		)));

		// get character
		$character = new CharacterEditor($characterID, null, null, false);

		// return new character
		return $character;
}

	/**
	 * Creates the character row in database table.
	 *
	 * @param 	string 		$character
	 * @param 	array		$additionalFields
	 * @return	integer		new character id
	 */
	public static function insert($character, $additionalFields = array()) {
		$keys = $values = '';
		foreach ($additionalFields as $key => $value) {
			$keys .= ','.$key;
			if (is_int($value)) $values .= ",".$value;
			else $values .= ",'".escapeString($value)."'";
		}

		$sql = "INSERT INTO	sls".SLS_N."_character
					(name
					".$keys.")
			VALUES		('".escapeString($character)."'
					".$values.")";
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getInsertID();
	}

	/**
	 * Marks this character.
	 */
	public function mark() {
		$markedCharacters = self::getMarkedCharacters();
		if ($markedCharacters == null || !is_array($markedCharacters)) {
			$markedCharacters = array($this->characterID);
			WCF::getSession()->register('markedCharacters', $markedCharacters);
		}
		else {
			if (!in_array($this->characterID, $markedCharacters)) {
				array_push($markedCharacters, $this->characterID);
				WCF::getSession()->register('markedCharacters', $markedCharacters);
			}
		}
	}

	/**
	 * Unmarks this character.
	 */
	public function unmark() {
		$markedCharacters = self::getMarkedCharacters();
		if (is_array($markedCharacters) && in_array($this->characterID, $markedCharacters)) {
			$key = array_search($this->characterID, $markedCharacters);

			unset($markedCharacters[$key]);
			if (count($markedCharacters) == 0) {
				self::unmarkAll();
			}
			else {
				WCF::getSession()->register('markedCharacters', $markedCharacters);
			}
		}
	}

	/**
	 * Returns the marked characters.
	 *
	 * @return	array		marked characters
	 */
	public static function getMarkedCharacters() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedCharacters'])) {
			return $sessionVars['markedCharacters'];
		}
		return null;
	}
	/**
	 * Unmarks all marked characters.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedCharacters');
	}
}
?>
