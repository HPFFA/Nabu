<?php
require_once(SLS_DIR.'lib/data/classification/ClassificationList.class.php');

/**
 * LibraryClassificationList provides extended functions for displaying a list of classifications.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.classification
 * @category 	Story Library System
 */
class LibraryClassificationList extends ClassificationList {
	// parameters
	public $libraryID;

	// data
	public $classifications = 0;

	/**
	 * Creates a new ClassificationList object.
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
                $this->sqlJoins = "LEFT JOIN   sls".SLS_N."_library_to_classification library
                                    ON          (library.classificationID = classification.classificationID)";
                $this->sqlConditions = "library.libraryID = ".$this->libraryID;

	}



}

?>
