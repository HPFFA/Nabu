<?php

//sls importe
require_once(SLS_DIR . 'lib/data/genre/GenreEditor.class.php');

/**
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.genre
 * @category 	Story Library System
 */
class GenreList {

    protected $sqlSelectRating = '';
    protected $sqlOrderBy = 'genre.name';
    protected $limit = '';
    protected $offset = '';
    protected $sqlSelects = '';
    protected $sqlJoins = '';
    public $genre = array();

    /**
     * Creates a new GenreList object.
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
     * Gets genre ids.
     */
    protected function readGenreIDs() {
	$sql = "SELECT		" . $this->sqlSelectRating . "
					genre.genreID
			FROM		sls" . SLS_N . "_genre genre
			" . $this->sqlJoins . "
                        " . (!empty($this->sqlConditions) ? "WHERE " . $this->sqlConditions : "") . "
			ORDER BY	" . $this->sqlOrderBy;
	$result = WCF::getDB()->sendQuery($sql, $this->limit, $this->offset);
	while ($row = WCF::getDB()->fetchArray($result)) {
	    if (!empty($this->genreIDs))
		$this->genreIDs .= ',';
	    $this->genreIDs .= $row['genreID'];
	}
    }

    /**
     * Reads a list of genres.
     */
    public function readGenres() {
	// get chapter ids
	$this->readGenreIDs();
	if (empty($this->genreIDs))
	    return false;

	// get stories
	$sql = $this->buildQuery();
	$result = WCF::getDB()->sendQuery($sql);
	while ($row = WCF::getDB()->fetchArray($result)) {
	    $this->genre[] = new GenreEditor(null, $row);
	}
    }

    /**
     * Builds the main sql query for selecting genres.
     *
     * @return	string
     */
    protected function buildQuery() {
	return "SELECT		genre.*
			FROM 		sls" . SLS_N . "_genre genre
			WHERE		genre.genreID IN (" . $this->genreIDs . ")
			ORDER BY	" . $this->sqlOrderBy;
    }

}

?>
