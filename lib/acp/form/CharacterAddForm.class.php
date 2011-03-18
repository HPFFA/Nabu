<?php
// sls imports
require_once(SLS_DIR.'lib/data/character/CharacterEditor.class.php');

// wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');

/**
 * Shows the character add form.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.form
 * @category 	Story Library System
 */
class CharacterAddForm extends ACPForm {
	// system
	public $templateName = 'characterAdd';
	public $activeMenuItem = 'sls.acp.menu.link.content.character.add';
	public $neededPermissions = 'admin.library.canAddCharacter';
	
	/**
	 * character editor object
	 * 
	 * @var	CharacterEditor
	 */
	public $character;
	/**
	 * list of additional fields
	 * 
	 * @var	array
	 */
	public $additionalFields = array();
	
	// parameters
	public $title = '';
	public $description = '';
	public $allowDescriptionHtml = 0;
	public $image = '';
	public $imageNew = '';
	public $imageShowAsBackground = 1;
	public $imageBackgroundRepeat = 'no';
	
	
	/**
	 * @see Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		$this->imageShowAsBackground = 0;
		$this->allowDescriptionHtml = 0;
		
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['allowDescriptionHtml'])) $this->allowDescriptionHtml = intval($_POST['allowDescriptionHtml']);
		if (isset($_POST['image'])) $this->image = StringUtil::trim($_POST['image']);
		if (isset($_POST['imageNew'])) $this->imageNew = StringUtil::trim($_POST['imageNew']);
		if (isset($_POST['imageShowAsBackground'])) $this->imageShowAsBackground = intval($_POST['imageShowAsBackground']);
		if (isset($_POST['imageBackgroundRepeat'])) $this->imageBackgroundRepeat = $_POST['imageBackgroundRepeat'];
		
	}
	
		
	/**
	 * @see Form::validate()
	 */
	public function validate() {
	
		parent::validate();
	
		
		// title
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
		
	}
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		// save library
		if (WCF::getUser()->getPermission('admin.library.canAddCharacter')) {
			$this->character = CharacterEditor::create($this->title, $this->description,
			$this->additionalFields);
		}
		
	
		// reset cache
		$this->resetCache();
		$this->saved();
		
		// reset values
		$this->title = $this->description = $this->image = $this->imageNew = '';
		
		// show success message
		WCF::getTPL()->assign(array(
			'character' => $this->character,
			'success' => true
		));
	}
	
	/**
	 * Resets the library cache after changes.
	 */
	protected function resetCache() {

		Library::resetCache();
		
		// reset sessions
		Session::resetSessions(array(), true, false);
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'title' => $this->title,
			'description' => $this->description,
			'allowDescriptionHtml' => $this->allowDescriptionHtml,
			'image' => $this->image,
			'action' => 'add',
			'imageNew' => $this->imageNew,
			'imageShowAsBackground' => $this->imageShowAsBackground,
			'imageBackgroundRepeat' => $this->imageBackgroundRepeat,
		));
	}
	
}
?>
