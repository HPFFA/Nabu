<?php
// sls imports
require_once(SLS_DIR.'lib/data/character/Character.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all characters.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.page
 * @category 	Story Library System
 */
class CharacterListPage extends AbstractPage {
	// system
	public $templateName = 'characterList';
	
	/**
	 * list of genres
	 * 
	 * @var	array
	 */
	public $characters = array();
	
	/**
	 * waring id
	 * 
	 * @var	integer
	 */
	public $deletedCharacterID = 0;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['deletedCharacterID'])) $this->deletedGenreID = intval($_REQUEST['deletedCharacterID']);
		$this->renderCharacters();
	}
	
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'characters' => $this->characters,
			'deletedCharacterID' => $this->deletedCharacterID
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('sls.acp.menu.link.content.character.view');
		
		// check permission
		WCF::getUser()->checkPermission(array('admin.library.canEditCharacter', 'admin.library.canDeleteCharacter'));
		parent::show();
	}
	
	/**
	 * Renders the ordered list of all characters.
	 */
	protected function renderCharacters() {
		require_once(SLS_DIR."lib/data/character/CharacterList.class.php");
		$objCharacterList = new CharacterList();
		$objCharacterList->readCharacters();
		$this->characters = $objCharacterList->character;
	}
	
}
?>
