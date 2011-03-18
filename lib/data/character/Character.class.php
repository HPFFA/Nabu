<?php
//wcf importe
require_once(WCF_DIR . 'lib/data/DatabaseObject.class.php');

/**
 * Represents a genre in the library.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.character
 * @category 	Story Library System
 */
class Character extends DatabaseObject {

     /**
     * Creates a new character object.
     *
     * If id is set, the function reads character data from database.
     * Otherwise it uses the given resultset.
     *
     * @param 	integer 	$genreID		id of a character
     * @param 	array 		$row                    resultset with character data form database
     */
    public function __construct($characterID, $row = null) {
        if ($characterID !== null) {
            $sql = "SELECT	*
				FROM 	sls" . SLS_N . "_character
				WHERE 	characterID = " . $characterID;
            $row = WCF::getDB()->getFirstRow($sql);
        }
        parent::__construct($row);
    }

       /**
     * Returns true, if this character is marked in the active session.
     */
    public function isMarked() {
        $sessionVars = WCF::getSession()->getVars();
        if (isset($sessionVars['markedCharacters'])) {
            if (in_array($this->characterID, $sessionVars['markedCharacters']))
                return 1;
        }
        return 0;
    }

}
?>
