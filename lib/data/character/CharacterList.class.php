<?php
//sls importe
require_once(SLS_DIR . 'lib/data/character/CharacterEditor.class.php');

/**
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.character
 * @category 	Story Library System
 */
class CharacterList {
    protected $sqlSelectRating = '';
    protected $sqlOrderBy ='characters.name';
    protected $limit = '';
    protected $offset ='';
    protected $sqlSelects = '';
    protected $sqlJoins = '';
    public $character =array();

    /**
	 * Creates a new CharacterList object.
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
	 * Gets character ids.
	 */
	protected function readCharacterIDs() {
		$sql = "SELECT		".$this->sqlSelectRating."
					characters.characterID
			FROM		sls".SLS_N."_character characters
			".$this->sqlJoins."
                        ".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : "")."
			ORDER BY	".$this->sqlOrderBy;
		$result = WCF::getDB()->sendQuery($sql, $this->limit, $this->offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($this->characterIDs)) $this->characterIDs .= ',';
			$this->characterIDs .= $row['characterID'];
		}
	}

	/**
	 * Reads a list of characters.
	 */
	public function readCharacters() {
		// get chapter ids
		$this->readCharacterIDs();
		if (empty($this->characterIDs)) return false;

		// get characters
		$sql = $this->buildQuery();
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->character[] = new CharacterEditor(null, $row);
		}
	}

	/**
	 * Builds the main sql query for selecting characters.
	 *
	 * @return	string
	 */
	protected function buildQuery() {
		return "SELECT		characters.*
			FROM 		sls".SLS_N."_character characters
			WHERE		characters.characterID IN (".$this->characterIDs.")
			ORDER BY	".$this->sqlOrderBy;
	}

}
?>
