<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Represents a user in the library.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.user
 * @category 	Story Library System
 */
class SLSUser extends UserProfile {
	protected $avatar = null;

	/**
	 * @see UserProfile::__construct()
	 */
	public function __construct($userID = null, $row = null, $username = null, $email = null) {
		$this->sqlJoins .= ' LEFT JOIN sls'.SLS_N.'_user sls_user ON (sls_user.userID = user.userID) ';
		parent::__construct($userID, $row, $username, $email);
	}

	/**
	 * Updates the amount of chapters of a user.
	 *
	 * @param	integer		$userID
	 * @param	integer		$chapters
	 */
	public static function updateUserChapters($userID, $chapters) {
		$sql = "UPDATE	sls".SLS_N."_user
			SET	chapters = IF(".$chapters." > 0 OR chapters > ABS(".$chapters."), chapters + ".$chapters.", 0)
			WHERE	userID = ".$userID;
		WCF::getDB()->registerShutdownUpdate($sql);
	}
}
?>