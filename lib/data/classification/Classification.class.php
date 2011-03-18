<?php

//wcf importe
require_once(WCF_DIR . 'lib/data/DatabaseObject.class.php');

/**
 * Represents a classification in the library.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.classification
 * @category 	Story Library System
 */
class Classification extends DatabaseObject {

     /**
     * Creates a new classification object.
     *
     * If id is set, the function reads classification data from database.
     * Otherwise it uses the given resultset.
     *
     * @param 	integer 	$classificationID		id of a classification
     * @param 	array 		$row                    resultset with classification data form database
     */
    public function __construct($classificationID, $row = null) {
        if ($classificationID !== null) {
            $sql = "SELECT	*
				FROM 	sls" . SLS_N . "_classification
				WHERE 	classificationID = " . $classificationID;
            $row = WCF::getDB()->getFirstRow($sql);
        }
        parent::__construct($row);
    }

       /**
     * Returns true, if this classification is marked in the active session.
     */
    public function isMarked() {
        $sessionVars = WCF::getSession()->getVars();
        if (isset($sessionVars['markedClassifications'])) {
            if (in_array($this->classificationID, $sessionVars['markedClassifications']))
                return 1;
        }
        return 0;
    }

}
?>
