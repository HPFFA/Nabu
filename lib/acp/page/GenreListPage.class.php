<?php

// sls imports
require_once(SLS_DIR.'lib/data/genre/Genre.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all genres.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.page
 * @category 	Story Library System
 */
class GenreListPage extends AbstractPage {
	// system
	public $templateName = 'genreList';
	
	/**
	 * list of genres
	 * 
	 * @var	array
	 */
	public $genres = array();
	
	/**
	 * genre id
	 * 
	 * @var	integer
	 */
	public $deletedGenreID = 0;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['deletedGenreID'])) $this->deletedGenreID = intval($_REQUEST['deletedGenreID']);
		$this->renderGenres();
	}
	
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'genres' => $this->genres,
			'deletedGenreID' => $this->deletedGenreID
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('sls.acp.menu.link.content.genre.view');
		
		// check permission
		WCF::getUser()->checkPermission(array('admin.library.canEditGenre', 'admin.library.canDeleteGenre'));
		parent::show();
	}
	
	/**
	 * Renders the ordered list of all genres.
	 */
	protected function renderGenres() {
		require_once(SLS_DIR."lib/data/genre/GenreList.class.php");
		$objGenreList = new GenreList();
		$objGenreList->readGenres();
		$this->genres = $objGenreList->gerne;
	}
	
}
?>
