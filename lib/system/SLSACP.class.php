<?php
require_once(WCF_DIR.'lib/system/WCFACP.class.php');

/**
 * This class extends the main WCFACP class by library specific functions.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system
 * @category 	Story Library System
 */
class SLSACP extends WCFACP {
	/**
	 * @see WCF::getOptionsFilename()
	 */
	protected function getOptionsFilename() {
		return SLS_DIR.'options.inc.php';
	}

	/**
	 * Initialises the template engine.
	 */
	protected function initTPL() {
		global $packageDirs;

		self::$tplObj = new ACPTemplate(self::getLanguage()->getLanguageID(), ArrayUtil::appendSuffix($packageDirs, 'acp/templates/'));
		$this->assignDefaultTemplateVariables();
	}

	/**
	 * Does the user authentication.
	 */
	protected function initAuth() {
		parent::initAuth();

		// user ban
		if (self::getUser()->banned) {
			throw new PermissionDeniedException();
		}
	}

	/**
	 * @see WCF::assignDefaultTemplateVariables()
	 */
	protected function assignDefaultTemplateVariables() {
		parent::assignDefaultTemplateVariables();

		self::getTPL()->assign(array(
			// add jump to library link
			'additionalHeaderButtons' => '<li><a href="'.RELATIVE_SLS_DIR.'index.php?page=Index"><img src="'.RELATIVE_SLS_DIR.'icon/libraryS.png" alt="" /> <span>'.WCF::getLanguage()->get('sls.acp.jumpToLibrary').'</span></a></li>',
			// individual page title
			'pageTitle' => WCF::getLanguage()->get(StringUtil::encodeHTML(PAGE_TITLE)) . ' - ' . StringUtil::encodeHTML(PACKAGE_NAME . ' ' . PACKAGE_VERSION)
		));
	}

	/**
	 * @see WCF::loadDefaultCacheResources()
	 */
	protected function loadDefaultCacheResources() {
		parent::loadDefaultCacheResources();
		$this->loadDefaultSLSCacheResources();
	}

	/**
	 * Loads default cache resources of burning library acp.
	 * Can be called statically from other applications or plugins.
	 */
	public static function loadDefaultSLSCacheResources() {
		WCF::getCache()->addResource('library', SLS_DIR.'cache/cache.library.php', SLS_DIR.'lib/system/cache/CacheBuilderLibrary.class.php');
		WCF::getCache()->addResource('bbcodes', WCF_DIR.'cache/cache.bbcodes.php', WCF_DIR.'lib/system/cache/CacheBuilderBBCodes.class.php');
	}
}
?>