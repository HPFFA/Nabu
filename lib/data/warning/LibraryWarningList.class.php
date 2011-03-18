<?php
require_once(SLS_DIR.'lib/data/warning/WarningList.class.php');

/**
 * LibraryWarningList provides extended functions for displaying a list of warnings.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.warning
 * @category 	Story Library System
 */
class LibraryWarningList extends WarningList {
	// parameters
	public $library;

	// data
	public $warnings = 0;

	/**
	 * Creates a new GenreList object.
	 */
	public function __construct(Library $library) {
		$this->library = $library;
		parent::__construct();
	}

	/**
	 * @see StoryList::initDefaultSQL()
	 */
	protected function initDefaultSQL() {
		parent::initDefaultSQL();
                $this->sqlJoins = "LEFT JOIN   sls".SLS_N."_library_to_warning library
                                    ON          (library.warningID = warning.warningID)";
                $this->sqlConditions = "library.libraryID = ".$this->library->libraryID;

	}



}

?>
