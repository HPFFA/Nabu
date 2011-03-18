<?php
require_once(SLS_DIR.'lib/data/character/CharacterList.class.php');

/**
 * LibraryCharacterList provides extended functions for displaying a list of characters.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.character
 * @category 	Story Library System
 */
class LibraryCharacterList extends CharacterList {
	// parameters
	public $library;


	// data
	

	/**
	 * Creates a new CharacterList object.
	 */
	public function __construct($libraryID) {
		$this->libraryID = $libraryID;
		parent::__construct();
	}

	/**
	 * @see StoryList::initDefaultSQL()
	 */
	protected function initDefaultSQL() {
		parent::initDefaultSQL();
                $this->sqlJoins = "LEFT JOIN   sls".SLS_N."_library_character library
                                    ON          (library.characterID = characters.characterID)";
                $this->sqlConditions = "library.libraryID = ".$this->libraryID;

	}



}

?>
