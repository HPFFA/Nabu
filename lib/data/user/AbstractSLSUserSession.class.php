<?php
// wcf imports
require_once(WCF_DIR.'lib/system/session/UserSession.class.php');

/**
 * Abstract class for sls user and guest sessions.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.user
 * @category 	Story Library System
 */
class AbstractSLSUserSession extends UserSession {
	protected $libraryPermissions = array();
	protected $libraryModeratorPermissions = array();

	/**
	 * Checks whether this user has the permission with the given name on the library with the given library id.
	 *
	 * @param	string		$permission	name of the requested permission
	 * @param	integer		$libraryID
	 * @return	mixed				value of the permission
	 */
	public function getLibraryPermission($permission, $libraryID) {
		if (isset($this->libraryPermissions[$libraryID][$permission])) {
			return $this->libraryPermissions[$libraryID][$permission];
		}
		return $this->getPermission('user.library.'.$permission);
	}

	/**
	 * Checks whether this user has the moderator permission with the given name on the library with the given library id.
	 *
	 * @param	string		$permission	name of the requested permission
	 * @param	integer		$libraryID
	 * @return	mixed				value of the permission
	 */
	public function getLibraryModeratorPermission($permission, $libraryID) {
		if (isset($this->libraryModeratorPermissions[$libraryID][$permission])) {
			return $this->libraryModeratorPermissions[$libraryID][$permission];
		}

		return (($this->getPermission('mod.library.isSuperMod') || isset($this->libraryModeratorPermissions[$libraryID])) && $this->getPermission('mod.library.'.$permission));
	}

	/**
	 * @see UserSession::getGroupData()
	 */
	protected function getGroupData() {
		parent::getGroupData();

		// get group permissions from cache (library_to_group)
		$groups = implode(",", $this->groupIDs);
		$groupsFileName = StringUtil::getHash(implode("-", $this->groupIDs));

		// register cache resource
		WCF::getCache()->addResource('libraryPermissions-'.$groups, SLS_DIR.'cache/cache.libraryPermissions-'.$groupsFileName.'.php', SLS_DIR.'lib/system/cache/CacheBuilderLibraryPermissions.class.php');

		// get group data from cache
		$this->libraryPermissions = WCF::getCache()->get('libraryPermissions-'.$groups);
		if (isset($this->libraryPermissions['groupIDs']) && $this->libraryPermissions['groupIDs'] != $groups) {
			$this->libraryPermissions = array();
		}

		// get library moderator permissions
		$sql = "SELECT		*
			FROM		sls".SLS_N."_library_moderator
			WHERE		groupID IN (".implode(',', $this->groupIDs).")
					".($this->userID ? " OR userID = ".$this->userID : '')."
			ORDER BY 	userID DESC";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$libraryID = $row['libraryID'];
			unset($row['libraryID'], $row['userID'], $row['groupID']);

			if (!isset($this->libraryModeratorPermissions[$libraryID])) {
				$this->libraryModeratorPermissions[$libraryID] = array();
			}

			foreach ($row as $permission => $value) {
				if ($value == -1) continue;

				if (!isset($this->libraryModeratorPermissions[$libraryID][$permission])) $this->libraryModeratorPermissions[$libraryID][$permission] = $value;
				else $this->libraryModeratorPermissions[$libraryID][$permission] = $value || $this->libraryModeratorPermissions[$libraryID][$permission];
			}
		}

		if (count($this->libraryModeratorPermissions)) {
			require_once(SLS_DIR.'lib/data/library/Library.class.php');
			Library::inheritPermissions(0, $this->libraryModeratorPermissions);
		}
	}
}
?>