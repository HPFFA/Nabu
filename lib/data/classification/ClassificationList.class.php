<?php

//sls importe
require_once(SLS_DIR . 'lib/data/classification/ClassificationEditor.class.php');

/**
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.classification
 * @category 	Story Library System
 */
class ClassificationList {

    protected $sqlSelectRating = '';
    protected $sqlOrderBy = 'classification.name';
    protected $limit = '';
    protected $offset = '';
    protected $sqlSelects = '';
    protected $sqlJoins = '';
    public $classification = array();

    /**
     * Creates a new ClassificationList object.
     */
    public function __construct() {
	// default sql conditions
	$this->initDefaultSQL();
    }

    /**
     * Fills the sql parameters with default values.
     */
    protected function initDefaultSQL() {
	
    }

    /**
     * Gets classification ids.
     */
    protected function readClassificationIDs() {
	$sql = "SELECT		" . $this->sqlSelectRating . "
					classification.classificationID
			FROM		sls" . SLS_N . "_classification classification
			" . (!empty($this->sqlJoins) ? "WHERE " . $this->sqlJoins : "") . "
                        " . (!empty($this->sqlConditions) ? "WHERE " . $this->sqlConditions : "") . "
			ORDER BY	" . $this->sqlOrderBy;
	$result = WCF::getDB()->sendQuery($sql, $this->limit, $this->offset);
	while ($row = WCF::getDB()->fetchArray($result)) {
	    if (!empty($this->classificationIDs))
		$this->classificationIDs .= ',';
	    $this->classificationIDs .= $row['classificationID'];
	}
    }

    /**
     * Reads a list of classifications.
     */
    public function readClassifications() {
	// get classification ids
		
	$this->readClassificationIDs();
	if (empty($this->classificationIDs))
	    return false;

	// get classifications
	 $sql = $this->buildQuery();
	$result = WCF::getDB()->sendQuery($sql);
	while ($row = WCF::getDB()->fetchArray($result)) {
	    $this->classification[] = new ClassificationEditor(null, $row);
	}
    }

    /**
     * Builds the main sql query for selecting classifications.
     *
     * @return	string
     */
    protected function buildQuery() {
	 return "SELECT		classification.*
			FROM 		sls" . SLS_N . "_classification classification
			WHERE		classification.classificationID IN (" . $this->classificationIDs . ")
			ORDER BY	" . $this->sqlOrderBy;
    }

}

?>
