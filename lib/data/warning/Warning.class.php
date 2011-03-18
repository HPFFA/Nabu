<?php

//wcf importe
require_once(WCF_DIR . 'lib/data/DatabaseObject.class.php');

/**
 * Represents a waring in the library.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.warning
 * @category 	Story Library System
 */
class Warning extends DatabaseObject {

     /**
     * Creates a new warning object.
     *
     * If id is set, the function reads the warning data from database.
     * Otherwise it uses the given resultset.
     *
     * @param 	integer 	$genreID		id of a genre
     * @param 	array 		$row                    resultset with warning data form database
     */
    public function __construct($warningID, $row = null) {
        if ($warningID !== null) {
            $sql = "SELECT	*
				FROM 	sls" . SLS_N . "_genre
				WHERE 	warningID = " . $warningID;
            $row = WCF::getDB()->getFirstRow($sql);
        }
        parent::__construct($row);
    }

       /**
     * Returns true, if this genre is marked in the active session.
     */
    public function isMarked() {
        $sessionVars = WCF::getSession()->getVars();
        if (isset($sessionVars['markedWarnings'])) {
            if (in_array($this->warningID, $sessionVars['markedWarnings']))
                return 1;
        }
        return 0;
    }

}
?>
