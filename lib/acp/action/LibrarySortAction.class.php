<?php

// sls imports
require_once(SLS_DIR . 'lib/data/library/LibraryEditor.class.php');

// wcf imports
require_once(WCF_DIR . 'lib/action/AbstractAction.class.php');

/**
 * Sorts the structure of libraries.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.action
 * @category 	Story Library System
 */
class LibrarySortAction extends AbstractAction {

    /**
     * new positions
     *
     * @var array
     */
    public $positions = array();

    /**
     * @see Action::readParameters()
     */
    public function readParameters() {
	parent::readParameters();

	if (isset($_POST['libraryListPositions']) && is_array($_POST['libraryListPositions']))
	    $this->positions = ArrayUtil::toIntegerArray($_POST['libraryListPositions']);
    }

    /**
     * @see Action::execute()
     */
    public function execute() {
	parent::execute();

	// check permission
	WCF::getUser()->checkPermission('admin.library.canEditLibrary');

	// delete old positions
	$sql = "TRUNCATE sls" . SLS_N . "_library_structure";
	WCF::getDB()->sendQuery($sql);

	// update postions
	foreach ($this->positions as $libraryID => $data) {
	    foreach ($data as $parentID => $position) {
		LibraryEditor::updatePosition(intval($libraryID), intval($parentID), $position);
	    }
	}

	// insert default values
	$sql = "INSERT IGNORE INTO	sls" . SLS_N . "_library_structure
						(parentID, libraryID)
			SELECT			parentID, libraryID
			FROM			sls" . SLS_N . "_library";
	WCF::getDB()->sendQuery($sql);

	// reset cache
	WCF::getCache()->clearResource('library');
	$this->executed();

	// forward to list page
	HeaderUtil::redirect('index.php?page=LibraryList&successfulSorting=1&packageID=' . PACKAGE_ID . SID_ARG_2ND_NOT_ENCODED);
	exit;
    }

}
?>
