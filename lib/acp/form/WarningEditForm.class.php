<?php
// sls imports
require_once(SLS_DIR.'lib/acp/form/WarningAddForm.class.php');

/**
 * Shows the warning edit form.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.form
 * @category 	Story Library System
 */
class WarningEditForm extends LibraryAddForm {
	// system
	public $activeMenuItem = 'sls.acp.menu.link.content.warning';
	public $neededPermissions = array('admin.library.canEditWarning');

	/**
	 * warning id
	 *
	 * @var	integer
	 */
	public $warningID = 0;


	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// get library id
		if (isset($_REQUEST['warningID'])) $this->warningID = intval($_REQUEST['warningID']);

		// get warning
		$this->warning = new WarningEditor($this->warningID);
	}

	/**
	 * @see Form::save()
	 */
	public function save() {
		AbstractForm::save();

	// save library
		if (WCF::getUser()->getPermission('admin.library.canEditWarning')) {
			// fix closed categories
			// update data
			$this->warning->update($this->title, $this->description,
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
			'warningID' => $this->warningID,
			'warning' => $this->warning,
			'action' => 'edit',
			'warningQuickJumpOptions' => Warning::getWarningSelect(array(), false, true),
		));
	}

}
?>
