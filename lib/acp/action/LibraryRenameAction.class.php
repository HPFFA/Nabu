<?php
// sls imports
require_once(SLS_DIR.'lib/acp/action/AbstractLibraryAction.class.php');

/**
 * Renames a library.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	acp.action
 * @category 	Story Library System
 */
class LibraryRenameAction extends AbstractLibraryAction {
	/**
	 * new library title
	 *
	 * @var string
	 */
	public $title = '';
	
	/**
	 * @see Action::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_POST['title'])) {
			$this->title = $_POST['title'];
			if (CHARSET != 'UTF-8') $this->title = StringUtil::convertEncoding('UTF-8', CHARSET, $this->title);
		}
	}
	
	/**
	 * @see Action::execute();
	 */
	public function execute() {
		parent::execute();
		
		// check permission
		WCF::getUser()->checkPermission('admin.library.canEditLibrary');
				
		// check library title
		if (StringUtil::encodeHTML($this->library->title) != WCF::getLanguage()->get(StringUtil::encodeHTML($this->library->title))) {
			// change language variable
			require_once(WCF_DIR.'lib/system/language/LanguageEditor.class.php');
			$language = new LanguageEditor(WCF::getLanguage()->getLanguageID());
			$language->updateItems(array($this->library->title => $this->title), 0, PACKAGE_ID, array($this->library->title => 1));
		}
		else {
			// change title
			$this->library->updateData(array('title' => $this->title));
		}
		
		// reset cache
		WCF::getCache()->clearResource('library');
		$this->executed();
	}
}
?>