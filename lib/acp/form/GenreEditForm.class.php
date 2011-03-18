<?php
// sls imports
require_once(SLS_DIR.'lib/acp/form/GenreAddForm.class.php');

/**
 * Shows the genre edit form.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.form
 * @category 	Story Library System
 */
class GenreEditForm extends LibraryAddForm {
	// system
	public $activeMenuItem = 'sls.acp.menu.link.content.genre';
	public $neededPermissions = array('admin.library.canEditGenre');

	/**
	 * genre id
	 *
	 * @var	integer
	 */
	public $genreID = 0;


	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get library id
		if (isset($_REQUEST['genreID'])) $this->genreID = intval($_REQUEST['genreID']);

		// get genre
		$this->genre = new GenreEditor($this->genreID);
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

	// save library
		if (WCF::getUser()->getPermission('admin.library.canEditGenre')) {
			// fix closed categories
			// update data
			$this->genre->update($this->title, $this->description,
				$this->additionalFields);
		}

		// reset cache
		$this->resetCache();
		$this->saved();

		// show success message
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @see Page::readData()
	 */
	public function readData() {
		parent::readData();

		if (!count($_POST)) {
			// get values
			$this->title = $this->library->title;
			$this->description = $this->library->description;
			$this->image = $this->library->image;
			$this->allowDescriptionHtml = $this->library->allowDescriptionHtml;
			$this->imageNew = $this->library->imageNew;
			$this->imageShowAsBackground = $this->library->imageShowAsBackground;
			$this->imageBackgroundRepeat = $this->library->imageBackgroundRepeat;

	}
	}

	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'genreID' => $this->genreID,
			'genre' => $this->genre,
			'action' => 'edit',
			'genreQuickJumpOptions' => Genre::getGenreSelect(array(), false, true),
		));
	}

}
?>
