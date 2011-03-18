<?php
// sls imports
require_once(SLS_DIR.'lib/data/warning/Warning.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all warnings.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.page
 * @category 	Story Library System
 */
class WarningListPage extends AbstractPage {
	// system
	public $templateName = 'warningList';
	
	/**
	 * list of genres
	 * 
	 * @var	array
	 */
	public $warnings = array();
	
	/**
	 * waring id
	 * 
	 * @var	integer
	 */
	public $deletedWarningID = 0;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['deletedWarningID'])) $this->deletedGenreID = intval($_REQUEST['deletedWarningID']);
		$this->renderWarnings();
	}
	
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'warnings' => $this->warnings,
			'deletedWarningID' => $this->deletedWarningID
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('sls.acp.menu.link.content.warning.view');
		
		// check permission
		WCF::getUser()->checkPermission(array('admin.library.canEditWarning', 'admin.library.canDeleteWarning'));
		parent::show();
	}
	
	/**
	 * Renders the ordered list of all warnings.
	 */
	protected function renderWarnings() {
		require_once(SLS_DIR."lib/data/warning/WarningList.class.php");
		$objWarningList = new WarningList();
		$objWarningList->readWarnings();
		$this->warnings = $objWarningList->warning;
		
	}
	
}
?>
