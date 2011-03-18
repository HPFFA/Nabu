<?php
// sls imports
require_once(SLS_DIR.'lib/acp/form/CharacterAddForm.class.php');

/**
 * Shows the character edit form.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.form
 * @category 	Story Library System
 */
class CharacterEditForm extends LibraryAddForm {
	// system
	public $activeMenuItem = 'sls.acp.menu.link.content.character';
	public $neededPermissions = array('admin.library.canEditCharacter');

	/**
	 * character id
	 *
	 * @var	integer
	 */
	public $characterID = 0;


	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get library id
		if (isset($_REQUEST['characterID'])) $this->characterID = intval($_REQUEST['characterID']);

		// get character
		$this->character = new CharacterEditor($this->characterID);
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

	// save library
		if (WCF::getUser()->getPermission('admin.library.canEditCharacter')) {
			// fix closed categories
			// update data
			$this->character->update($this->title, $this->description,
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
			'characterID' => $this->characterID,
			'character' => $this->character,
			'action' => 'edit',
			'characterQuickJumpOptions' => Character::getCharacterSelect(array(), false, true),
		));
	}

}
?>
