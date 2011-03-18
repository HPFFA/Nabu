<?php
// sls imports
require_once(SLS_DIR.'lib/data/classification/Classification.class.php');

// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows a list of all classifications.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.page
 * @category 	Story Library System
 */
class ClassificationListPage extends AbstractPage {
	// system
	public $templateName = 'classificationList';
	
	/**
	 * list of genres
	 * 
	 * @var	array
	 */
	public $classifications = array();
	
	/**
	 * waring id
	 * 
	 * @var	integer
	 */
	public $deletedClassificationID = 0;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['deletedClassificationID'])) $this->deletedGenreID = intval($_REQUEST['deletedClassificationID']);
		$this->renderClassifications();
	}
	
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'classifications' => $this->classifications,
			'deletedClassificationID' => $this->deletedClassificationID
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		// enable menu item
		WCFACP::getMenu()->setActiveMenuItem('sls.acp.menu.link.content.classification.view');
		
		// check permission
		WCF::getUser()->checkPermission(array('admin.library.canEditClassification', 'admin.library.canDeleteClassification'));
		parent::show();
	}
	
	/**
	 * Renders the ordered list of all classifications.
	 */
	protected function renderClassifications() {
		require_once(SLS_DIR."lib/data/classification/ClassificationList.class.php");
		$objClassificationList = new ClassificationList();
		$objClassificationList->readClassifications();
		$this->classifications = $objClassificationList->classification;
	}
	
}
?>
