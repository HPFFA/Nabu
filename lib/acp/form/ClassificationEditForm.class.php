<?php
// sls imports
require_once(SLS_DIR.'lib/acp/form/ClassificationAddForm.class.php');

/**
 * Shows the classification edit form.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.form
 * @category 	Story Library System
 */
class ClassificationEditForm extends LibraryAddForm {
	// system
	public $activeMenuItem = 'sls.acp.menu.link.content.classification';
	public $neededPermissions = array('admin.library.canEditClassification');

	/**
	 * classification id
	 *
	 * @var	integer
	 */
	public $classificationID = 0;


	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get library id
		if (isset($_REQUEST['classificationID'])) $this->classificationID = intval($_REQUEST['classificationID']);

		// get classification
		$this->classification = new ClassificationEditor($this->classificationID);
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

	// save library
		if (WCF::getUser()->getPermission('admin.library.canEditClassification')) {
			// fix closed categories
			// update data
			$this->classification->update($this->title, $this->description,
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
			'classificationID' => $this->classificationID,
			'classification' => $this->classification,
			'action' => 'edit',
			'classificationQuickJumpOptions' => Classification::getClassificationSelect(array(), false, true),
		));
	}

}
?>
