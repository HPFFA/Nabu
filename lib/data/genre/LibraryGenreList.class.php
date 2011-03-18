<?php
require_once(SLS_DIR.'lib/data/genre/GenreList.class.php');

/**
 * LibraryGenreList provides extended functions for displaying a list of genres.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.genre
 * @category 	Story Library System
 */
class LibraryGenreList extends GenreList {
	// parameters
	public $library;

	// data
	public $genres = 0;

	/**
	 * Creates a new GenreList object.
	 */
	public function __construct( $libraryID) {
		$this->libraryID = $libraryID;
		parent::__construct();
	}

	/**
	 * @see StoryList::initDefaultSQL()
	 */
	protected function initDefaultSQL() {
		parent::initDefaultSQL();
                $this->sqlJoins = "LEFT JOIN   sls".SLS_N."_library_genre library
                                    ON          (library.genreID = genre.genreID)";
                $this->sqlConditions = "library.libraryID = ".$this->libraryID;

	}



}

?>
