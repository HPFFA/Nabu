<?php
//sls importe
require_once(SLS_DIR . 'lib/data/warning/WarningEditor.class.php');

/**
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.warning
 * @category 	Story Library System
 */
class WarningList {
protected $sqlSelectRating = '';
    protected $sqlOrderBy = 'warning.name';
    protected $limit = '';
    protected $offset = '';
    protected $sqlSelects = '';
    protected $sqlJoins = '';
    public $warning = array();

    /**
	 * Creates a new WarningList object.
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
	 * Gets warning ids.
	 */
	protected function readWarningIDs() {
		$sql = "SELECT		".$this->sqlSelectRating."
					warning.warningID
			FROM		sls".SLS_N."_warning warning
			".(!empty($this->sqlJoins) ? "WHERE ".$this->sqlJoins : "")."
                        ".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : "")."
			ORDER BY	".$this->sqlOrderBy;
		$result = WCF::getDB()->sendQuery($sql, $this->limit, $this->offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			if (!empty($this->warningIDs)) $this->warningIDs .= ',';
			$this->warningIDs .= $row['warningID'];
		}
	}

	/**
	 * Reads a list of warnings.
	 */
	public function readWarnings() {
		// get chapter ids
		$this->readWarningIDs();
		if (empty($this->warningIDs)) return false;

		// get stories
		$sql = $this->buildQuery();
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->warning[] = new WarningEditor(null, $row);
		}
	}

	/**
	 * Builds the main sql query for selecting warnings.
	 *
	 * @return	string
	 */
	protected function buildQuery() {
		return "SELECT		".$this->sqlSelects."
					warning.*
			FROM 		sls".SLS_N."_warning warning
			".$this->sqlJoins."
			WHERE		warning.warningID IN (".$this->warningIDs.")
			ORDER BY	".$this->sqlOrderBy;
	}

}
?>
