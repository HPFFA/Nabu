<?php
// sls imports
require_once(SLS_DIR.'lib/data/genre/Genre.class.php');

/**
 * GenreEditor provides functions to edit the data of a genre.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.genre
 * @category 	Story Library System
 */
class GenreEditor extends Genre {

	/**
	 * Creates a new GenreEditor object.
	 * @see Genre::__construct()
	 */
	public function __construct($genreID, $row = null, $cacheObject = null, $useCache = true) {
		if ($useCache) parent::__construct($genreID, $row, $cacheObject);
		else {
			$sql = "SELECT	*
				FROM	sls".SLS_N."_genre
				WHERE	genreID = ".$genreID;
			$row = WCF::getDB()->getFirstRow($sql);
			parent::__construct(null, $row);
		}
	}


	/**
	 * Deletes the data of genres.
	 */
	public static function deleteData($genreIDs) {
		$sql = "DELETE FROM	sls".SLS_N."_library_to_genre
			WHERE		genreID IN (".$genreIDs.")";
		WCF::getDB()->sendQuery($sql);

	}

	/**
	 * Deletes this genre.
	 */
	public function delete() {
		$sql = "UPDATE FROM	sls".SLS_N."_story
                        SET             genreID = 0
			WHERE		genreID IN (".$genreIDs.")";
		WCF::getDB()->sendQuery($sql);

		// delete genre
		self::deleteData($this->genreID);
	}



	/**
	 * Updates the data of a genre.
	 */
	public function update($genreID = null, $genre = null, $description = null, $additionalFields = array()) {
		$fields = array();
		if ($genreID !== null) $fields['genreID'] = $genreID;
		if ($genre !== null) $fields['genre'] = $genre;
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
			$sql = "UPDATE	sls".SLS_N."_genre
				SET	".$updates."
				WHERE	genreID = ".$this->genreID;
			WCF::getDB()->sendQuery($sql);
		}
	}

	/**
	 * Creates a new genre.
	 *
	 * @return	GenreEditor
	 */
	public static function create($genre, $description = '', $additionalFields = array()) {
		// save data
		$genreID = self::insert($genre, array_merge($additionalFields, array(
			'description' => $description
		)));

		// get genre
		$genre = new GenreEditor($genreID, null, null, false);

		// return new genre
		return $genre;
}

	/**
	 * Creates the genre row in database table.
	 *
	 * @param 	string 		$genre
	 * @param 	array		$additionalFields
	 * @return	integer		new genre id
	 */
	public static function insert($genre, $additionalFields = array()) {
		$keys = $values = '';
		foreach ($additionalFields as $key => $value) {
			$keys .= ','.$key;
			if (is_int($value)) $values .= ",".$value;
			else $values .= ",'".escapeString($value)."'";
		}

		$sql = "INSERT INTO	sls".SLS_N."_genre
					(genre
					".$keys.")
			VALUES		('".escapeString($genre)."'
					".$values.")";
		WCF::getDB()->sendQuery($sql);
		return WCF::getDB()->getInsertID();
	}

	/**
	 * Marks this genre.
	 */
	public function mark() {
		$markedGenres = self::getMarkedGenres();
		if ($markedGenres == null || !is_array($markedGenres)) {
			$markedGenres = array($this->genreID);
			WCF::getSession()->register('markedGenres', $markedGenres);
		}
		else {
			if (!in_array($this->genreID, $markedGenres)) {
				array_push($markedGenres, $this->genreID);
				WCF::getSession()->register('markedGenres', $markedGenres);
			}
		}
	}

	/**
	 * Unmarks this genre.
	 */
	public function unmark() {
		$markedGenres = self::getMarkedGenres();
		if (is_array($markedGenres) && in_array($this->genreID, $markedGenres)) {
			$key = array_search($this->genreID, $markedGenres);

			unset($markedGenres[$key]);
			if (count($markedGenres) == 0) {
				self::unmarkAll();
			}
			else {
				WCF::getSession()->register('markedGenres', $markedGenres);
			}
		}
	}

	/**
	 * Returns the marked genres.
	 *
	 * @return	array		marked genres
	 */
	public static function getMarkedGenres() {
		$sessionVars = WCF::getSession()->getVars();
		if (isset($sessionVars['markedGenres'])) {
			return $sessionVars['markedGenres'];
		}
		return null;
	}
	/**
	 * Unmarks all marked genres.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedGenres');
	}
}
?>