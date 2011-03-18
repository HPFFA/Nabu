<?php

//wcf importe
require_once(WCF_DIR . 'lib/data/DatabaseObject.class.php');

/**
 * Represents a library in the forum.
 * A library is a container for stories and other libraries.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.genre
 * @category 	Story Library System
 */
class Genre extends DatabaseObject {

     /**
     * Creates a new genre object.
     *
     * If id is set, the function reads the chapter data from database.
     * Otherwise it uses the given resultset.
     *
     * @param 	integer 	$genreID		id of a genre
     * @param 	array 		$row                    resultset with genre data form database
     */
    public function __construct($genreID, $row = null) {
        if ($genreID !== null) {
            $sql = "SELECT	*
				FROM 	sls" . SLS_N . "_genre
				WHERE 	genreID = " . $genreID;
            $row = WCF::getDB()->getFirstRow($sql);
        }
        parent::__construct($row);
    }

       /**
     * Returns true, if this genre is marked in the active session.
     */
    public function isMarked() {
		$sessionVars = WCF::getSession()->getVars();
        if (isset($sessionVars['markedGenres'])) {
            if (in_array($this->genreID, $sessionVars['markedGenres']))
                return 1;
        }
        return 0;
    }

}
?>
