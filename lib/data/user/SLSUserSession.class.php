<?php

// wcf imports
require_once(WCF_DIR . 'lib/data/user/avatar/Gravatar.class.php');
require_once(WCF_DIR . 'lib/data/user/avatar/Avatar.class.php');

// sls imports
require_once(SLS_DIR . 'lib/data/user/AbstractSLSUserSession.class.php');

/**
 * Represents a user session in the library.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.user
 * @category 	Story Library System
 */
class SLSUserSession extends AbstractSLSUserSession {

    protected $closedCategories;
    protected $ignoredLibraries;
    protected $libraryVisits;
    protected $librarySubscriptions;
    protected $ignores = null;
    protected $outstandingNotifications = null;
    protected $subscriptionsUnreadCount = null;
    protected $hasSubscriptions = null;
    protected $outstandingGroupApplications = null;
    protected $outstandingApprovals = null;
    protected $invitations = null;
    /**
     * displayable avatar object.
     *
     * @var DisplayableAvatar
     */
    protected $avatar = null;

    /**
     * @see UserSession::__construct()
     */
    public function __construct($userID = null, $row = null, $username = null) {
        $this->sqlSelects .= "	sls_user.*, avatar.*, sls_user.userID AS slsUserID,
					GROUP_CONCAT(DISTINCT whitelist.whiteUserID ORDER BY whitelist.whiteUserID ASC SEPARATOR ',') AS buddies,
					GROUP_CONCAT(DISTINCT blacklist.blackUserID ORDER BY blacklist.blackUserID ASC SEPARATOR ',') AS ignoredUser,
					(SELECT COUNT(*) FROM wcf" . WCF_N . "_user_whitelist WHERE whiteUserID = user.userID AND confirmed = 0 AND notified = 0) AS numberOfInvitations,";
        $this->sqlJoins .= " 	LEFT JOIN sls" . SLS_N . "_user sls_user ON (sls_user.userID = user.userID)
					LEFT JOIN wcf" . WCF_N . "_user_whitelist whitelist ON (whitelist.userID = user.userID AND whitelist.confirmed = 1)
					LEFT JOIN wcf" . WCF_N . "_user_blacklist blacklist ON (blacklist.userID = user.userID)
					LEFT JOIN wcf" . WCF_N . "_avatar avatar ON (avatar.avatarID = user.avatarID) ";
        parent::__construct($userID, $row, $username);
    }

    /**
     * @see User::handleData()
     */
    protected function handleData($data) {
        parent::handleData($data);

        if (MODULE_AVATAR == 1 && !$this->disableAvatar && $this->showAvatar) {
            if (MODULE_GRAVATAR == 1 && $this->gravatar) {
                $this->avatar = new Gravatar($this->gravatar);
            } else if ($this->avatarID) {
                $this->avatar = new Avatar(null, $data);
            }
        }
    }

    /**
     * Updates the user session.
     */
    public function update() {
        // update global last activity timestamp
        SLSUserSession::updateLastActivityTime($this->userID);

        if (!$this->slsUserID) {
            // define default values
            $this->data['libraryLastVisitTime'] = TIME_NOW;
            $this->data['libraryLastActivityTime'] = TIME_NOW;
            $this->data['libraryLastMarkAllAsReadTime'] = TIME_NOW - VISIT_TIME_FRAME;

            // create sls user record
            $sql = "INSERT IGNORE INTO	sls" . SLS_N . "_user
							(userID, libraryLastVisitTime, libraryLastActivityTime, libraryLastMarkAllAsReadTime)
				VALUES			(" . $this->userID . ", " . $this->libraryLastVisitTime . ", " . $this->libraryLastActivityTime . ", " . $this->libraryLastMarkAllAsReadTime . ")";
            WCF::getDB()->registerShutdownUpdate($sql);
        } else {
            SLSUserSession::updateLibraryLastActivityTime($this->userID);
        }

        $this->getClosedCategories();
        $this->getIgnoredLibraries();
        $this->getLibrarySubscriptions();
        $this->getLibraryVisits();
    }

    /**
     * Initialises the user session.
     */
    public function init() {
        parent::init();

        $this->invitations = $this->ignores = $this->outstandingNotifications = $this->subscriptionsUnreadCount = $this->hasSubscriptions = $this->outstandingModerations = null;
    }

    /**
     * @see UserSession::getGroupData()
     */
    protected function getGroupData() {
        parent::getGroupData();

        // get user permissions (library_to_user)
        $userPermissions = array();
        $sql = "SELECT		*
			FROM		sls" . SLS_N . "_library_to_user
			WHERE		userID = " . $this->userID;
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            $libraryID = $row['libraryID'];
            unset($row['libraryID'], $row['userID']);
            $userPermissions[$libraryID] = $row;
        }

        if (count($userPermissions)) {
            require_once(SLS_DIR . 'lib/data/library/Library.class.php');
            Library::inheritPermissions(0, $userPermissions);

            foreach ($userPermissions as $libraryID => $row) {
                foreach ($row as $key => $val) {
                    if ($val != -1) {
                        $this->libraryPermissions[$libraryID][$key] = $val;
                    }
                }
            }
        }

        // get group leader status
        if (MODULE_MODERATED_USER_GROUP == 1) {
            $sql = "SELECT	COUNT(*) AS count
				FROM	wcf" . WCF_N . "_group_leader leader, wcf" . WCF_N . "_group usergroup
				WHERE	(leader.leaderUserID = " . $this->userID . "
					OR leader.leaderGroupID IN (" . implode(',', $this->getGroupIDs()) . "))
					AND leader.groupID = usergroup.groupID";
            $row = WCF::getDB()->getFirstRow($sql);
            $this->groupData['wcf.group.isGroupLeader'] = ($row['count'] ? 1 : 0);
        }
    }

    /**
     * Returns true, if the active user ignores the given user.
     *
     * @return	boolean
     */
    public function ignores($userID) {
        if ($this->ignores === null) {
            if ($this->ignoredUser) {
                $this->ignores = explode(',', $this->ignoredUser);
            } else {
                $this->ignores = array();
            }
        }

        return in_array($userID, $this->ignores);
    }

    /**
     * Sets the global library last visit timestamp.
     */
    public function setLastVisitTime($timestamp) {
        $this->data['libraryLastVisitTime'] = $timestamp;
        if (($timestamp - VISIT_TIME_FRAME) > $this->libraryLastMarkAllAsReadTime) {
            $this->data['libraryLastMarkAllAsReadTime'] = ($timestamp - VISIT_TIME_FRAME);
        }

        $sql = "UPDATE	sls" . SLS_N . "_user
			SET	libraryLastVisitTime = " . $timestamp . ",
				libraryLastActivityTime = " . TIME_NOW . ",
				libraryLastMarkAllAsReadTime = " . $this->libraryLastMarkAllAsReadTime . "
			WHERE	userID = " . $this->userID;
        WCF::getDB()->registerShutdownUpdate($sql);
    }

    /**
     * Sets the last mark all as read timestamp.
     */
    public function setLastMarkAllAsReadTime($timestamp) {
        $this->data['libraryLastMarkAllAsReadTime'] = $timestamp;

        $sql = "UPDATE	sls" . SLS_N . "_user
			SET	libraryLastMarkAllAsReadTime = " . $timestamp . "
			WHERE	userID = " . $this->userID;
        WCF::getDB()->registerShutdownUpdate($sql);
    }

    /**
     * Loads the library visits of this user from database.
     */
    protected function getLibraryVisits() {
        $this->libraryVisits = array();

        $sql = "SELECT	libraryID, lastVisitTime
			FROM 	sls" . SLS_N . "_library_visit
			WHERE 	userID = " . $this->userID;
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            $this->libraryVisits[$row['libraryID']] = $row['lastVisitTime'];
        }
    }

    /**
     * Returns the library visit of this user for the library with the given library id.
     *
     * @param	integer		$libraryID
     * @return	integer		library visit of this user for the library with the given library id
     */
    public function getLibraryVisitTime($libraryID) {
        $libraryVisitTime = 0;
        if (isset($this->libraryVisits[$libraryID]))
            $libraryVisitTime = $this->libraryVisits[$libraryID];

        if ($libraryVisitTime < $this->getLastMarkAllAsReadTime()) {
            $libraryVisitTime = $this->getLastMarkAllAsReadTime();
        }

        return $libraryVisitTime;
    }

    /**
     * Sets the library visit of this user for the library with the given library id.
     *
     * @param	integer		$libraryID
     */
    public function setLibraryVisitTime($libraryID) {
        $sql = "REPLACE INTO	sls" . SLS_N . "_library_visit
					(userID, libraryID, lastVisitTime)
			VALUES		(" . $this->userID . ",
					" . $libraryID . ",
					" . TIME_NOW . ")";
        WCF::getDB()->registerShutdownUpdate($sql);
        WCF::getSession()->resetUserData();

        $this->libraryVisits[$libraryID] = TIME_NOW;
    }

    /**
     * Loads the closed categories of this user from database.
     */
    protected function getClosedCategories() {
        $this->closedCategories = array();

        $sql = "SELECT 	*
			FROM 	sls" . SLS_N . "_library_closed_category_to_user
			WHERE 	userID = " . $this->userID;
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            $this->closedCategories[$row['libraryID']] = $row['isClosed'];
        }
    }

    /**
     * Returns true, if the category with the given library id is closed by this user.
     *
     * @param	integer		$libraryID
     * @return	boolean
     */
    public function isClosedCategory($libraryID) {
        if (!isset($this->closedCategories[$libraryID]))
            return 0;
        return $this->closedCategories[$libraryID];
    }

    /**
     * Loads the ignored libraries of this user from database.
     */
    protected function getIgnoredLibraries() {
        $this->ignoredLibraries = array();

        $sql = "SELECT 	*
			FROM 	sls" . SLS_N . "_library_ignored_by_user
			WHERE 	userID = " . $this->userID;
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            $this->ignoredLibraries[$row['libraryID']] = $row['libraryID'];
        }
    }

    /**
     * Returns true, if the library with the given library id is ignored by this user.
     *
     * @param	integer		$libraryID
     * @return	boolean
     */
    public function isIgnoredLibrary($libraryID) {
        if (isset($this->ignoredLibraries[$libraryID]))
            return 1;
        return 0;
    }

    /**
     * Closes the category with the given library id for this user.
     *
     * @param	integer		$libraryID
     * @param	integer		$close		1 closes the category
     * 						-1 opens the category
     */
    public function closeCategory($libraryID, $close = 1) {
        require_once(SLS_DIR . 'lib/data/library/Library.class.php');
        $library = Library::getLibrary($libraryID);
        if (!$library->isCategory()) {
            throw new IllegalLinkException();
        }

        $sql = "REPLACE INTO	sls" . SLS_N . "_library_closed_category_to_user
					(userID, libraryID, isClosed)
			VALUES		(" . $this->userID . ",
					" . $libraryID . ",
					" . $close . ")";
        WCF::getDB()->registerShutdownUpdate($sql);
        WCF::getSession()->resetUserData();

        $this->closedCategories[$libraryID] = $close;
    }

    /**
     * Loads the subscribed libraries of this user from database.
     */
    protected function getLibrarySubscriptions() {
        $this->librarySubscriptions = array();

        $sql = "SELECT	*
			FROM 	sls" . SLS_N . "_library_subscription
			WHERE 	userID = " . $this->userID;
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            $this->librarySubscriptions[$row['libraryID']] = $row;
        }
    }

    /**
     * Returns true, if the library with the given library id is a subscribed library of this user.
     *
     * @param	integer		$libraryID
     * @return	boolean		true, if the library with the given library id is a subscribed library of this user
     */
    public function isLibrarySubscription($libraryID) {
        if (isset($this->librarySubscriptions[$libraryID]))
            return true;
        return false;
    }

    /**
     * Subscribes the library with the given library id for this user.
     *
     * @param	integer		$libraryID
     */
    public function subscribeLibrary($libraryID) {
        if (!$this->isLibrarySubscription($libraryID)) {
            $sql = "REPLACE INTO	sls" . SLS_N . "_library_subscription
						(userID, libraryID, enableNotification)
				VALUES 		(" . $this->userID . ",
						" . $libraryID . ",
						" . WCF::getUser()->enableEmailNotification . ")";
            WCF::getDB()->registerShutdownUpdate($sql);
            WCF::getSession()->resetUserData();
            WCF::getSession()->unregister('hasSubscriptions');

            $this->librarySubscriptions[$libraryID] = array('userID' => $this->userID, 'libraryID' => $libraryID, 'enableNotification' => WCF::getUser()->enableEmailNotification, 'emails' => 0);
        }
    }

    /**
     * Unsubscribes the library with the given library id for this user.
     *
     * @param	integer		$libraryID
     */
    public function unsubscribeLibrary($libraryID) {
        if ($this->isLibrarySubscription($libraryID)) {
            $sql = "DELETE FROM	sls" . SLS_N . "_library_subscription
				WHERE 		userID = " . $this->userID . " AND libraryID = " . $libraryID;
            WCF::getDB()->registerShutdownUpdate($sql);
            WCF::getSession()->resetUserData();
            WCF::getSession()->unregister('hasSubscriptions');

            unset($this->librarySubscriptions[$libraryID]);
        }
    }

    /**
     * Updates the subscription of the library with the given library for this user.
     *
     * @param	integer		$libraryID
     */
    public function updateLibrarySubscription($libraryID) {
        if (isset($this->librarySubscriptions[$libraryID]) && $this->librarySubscriptions[$libraryID]['emails'] > 0) {
            $sql = "UPDATE	sls" . SLS_N . "_library_subscription
				SET 	emails = 0
				WHERE 	userID = " . $this->userID . "
					AND libraryID = " . $libraryID;
            WCF::getDB()->registerShutdownUpdate($sql);
        }
    }

    /**
     * Sets the story visit of this user for the story with the given story id.
     *
     * @param	integer		$storyID
     */
    public function setStoryVisitTime($storyID, $timestamp = TIME_NOW) {
        $sql = "REPLACE INTO	sls" . SLS_N . "_story_visit
					(userID, storyID, lastVisitTime)
			VALUES 		(" . $this->userID . ",
					" . $storyID . ",
					" . $timestamp . ")";
        WCF::getDB()->registerShutdownUpdate($sql);
    }

    /**
     * Updates the global last activity timestamp in user database.
     *
     * @param	integer		$userID
     * @param	integer		$timestamp
     */
    public static function updateLastActivityTime($userID, $timestamp = TIME_NOW) {
        // update lastActivity in wcf user table
        $sql = "UPDATE	wcf" . WCF_N . "_user
			SET	lastActivityTime = " . $timestamp . "
			WHERE	userID = " . $userID;
        WCF::getDB()->registerShutdownUpdate($sql);
    }

    /**
     * Updates the library last activity timestamp in user database.
     * 
     * @param	integer		$userID
     * @param	integer		$timestamp
     */
    public static function updateLibraryLastActivityTime($userID, $timestamp = TIME_NOW) {
        // update libraryLastActivity in sls user table
        $sql = "UPDATE	sls" . SLS_N . "_user
			SET	libraryLastActivityTime = " . $timestamp . "
			WHERE	userID = " . $userID;
        WCF::getDB()->registerShutdownUpdate($sql);
    }

    /**
     * @see	PM::getOutstandingNotifications()
     */
    public function getOutstandingNotifications() {
        if ($this->outstandingNotifications === null) {
            require_once(WCF_DIR . 'lib/data/message/pm/PM.class.php');
            $this->outstandingNotifications = PM::getOutstandingNotifications(WCF::getUser()->userID);
        }

        return $this->outstandingNotifications;
    }

    /**
     * Returns the number of unread subscribed stories.
     *
     * @return	integer
     */
    public function getSubscriptionsUnreadCount() {
        if ($this->subscriptionsUnreadCount === null) {
            $this->subscriptionsUnreadCount = 0;

            // update subscriptions status
            $lastSubscriptionsStatusUpdateTime = intval(WCF::getSession()->getVar('lastSubscriptionsStatusUpdateTime'));
            if ($lastSubscriptionsStatusUpdateTime < TIME_NOW - 180) {
                require_once(SLS_DIR . 'lib/data/story/SubscribedStory.class.php');
                $this->subscriptionsUnreadCount = SubscribedStory::getUnreadCount();

                // save status
                WCF::getSession()->register('subscriptionsUnreadCount', $this->subscriptionsUnreadCount);
                WCF::getSession()->register('lastSubscriptionsStatusUpdateTime', TIME_NOW);
            } else {
                $this->subscriptionsUnreadCount = intval(WCF::getSession()->getVar('subscriptionsUnreadCount'));
            }
        }

        return $this->subscriptionsUnreadCount;
    }

    /**
     * Returns true, if the user has subscriptions.
     */
    public function hasSubscriptions() {
        if ($this->hasSubscriptions === null) {
            $this->hasSubscriptions = WCF::getSession()->getVar('hasSubscriptions');
            if ($this->hasSubscriptions === null) {
                $this->hasSubscriptions = false;
                $sql = "SELECT	COUNT(*) AS count
					FROM	sls" . SLS_N . "_story_subscription
					WHERE	userID = " . $this->userID;
                $row = WCF::getDB()->getFirstRow($sql);
                if ($row['count'])
                    $this->hasSubscriptions = true;
                else {
                    $sql = "SELECT	COUNT(*) AS count
						FROM	sls" . SLS_N . "_library_subscription
						WHERE	userID = " . $this->userID;
                    $row = WCF::getDB()->getFirstRow($sql);
                    if ($row['count'])
                        $this->hasSubscriptions = true;
                }

                WCF::getSession()->register('hasSubscriptions', $this->hasSubscriptions);
            }
        }

        return $this->hasSubscriptions;
    }

    /**
     * Returns true, if the user is a group leader.
     *
     * @return	boolean
     */
    public function isGroupLeader() {
        return $this->getPermission('wcf.group.isGroupLeader');
    }

    /**
     * Returns the number of outstanding group applications.
     * 
     * @return	integer
     */
    public function getOutstandingGroupApplications() {
        if (MODULE_MODERATED_USER_GROUP == 1) {
            if ($this->outstandingGroupApplications === null) {
                $this->outstandingGroupApplications = WCF::getSession()->getVar('outstandingGroupApplications');
                if ($this->outstandingGroupApplications === null) {
                    $this->outstandingGroupApplications = 0;
                    $sql = "SELECT	COUNT(*) AS count
						FROM 	wcf" . WCF_N . "_group_application
						WHERE 	groupID IN (
								SELECT	groupID
								FROM	wcf" . WCF_N . "_group_leader leader
								WHERE	leader.leaderUserID = " . $this->userID . "
									OR leader.leaderGroupID IN (" . implode(',', $this->getGroupIDs()) . ")
							)
							AND applicationStatus IN (0,1)";
                    $row = WCF::getDB()->getFirstRow($sql);
                    $this->outstandingGroupApplications = $row['count'];

                    WCF::getSession()->register('outstandingGroupApplications', $this->outstandingGroupApplications);
                }
            }

            return $this->outstandingGroupApplications;
        }

        return 0;
    }

    /**
     * Returns true, if the user is a Moderator.
     *
     * @return	integer
     */
    public function isModerator() {
        return ($this->getPermission('mod.library.canEnableStory') || $this->getPermission('mod.library.canEnableChapter') || $this->getPermission('mod.library.canModeratChapter'));
    }

    /**
     * Returns the number of outstanding story / chapter approvals.
     * 
     * @return	integer
     */
    public function getOutstandingApprovals() {
        if ($this->outstandingApprovals === null) {
            $this->outstandingApprovals = WCF::getSession()->getVar('outstandingApprovals');
            if ($this->outstandingApprovals === null) {
                $this->outstandingApprovals = 0;
                require_once(SLS_DIR . 'lib/data/library/Library.class.php');

                // disabled stories
                $libraryIDs = Library::getModeratedLibraries('canEnableStory');
                if (!empty($libraryIDs)) {
                    $sql = "SELECT	COUNT(*) AS count
						FROM	sls" . SLS_N . "_story
						WHERE	isDisabled = 1
							AND libraryID IN (" . $libraryIDs . ")";
                    $row = WCF::getDB()->getFirstRow($sql);
                    $this->outstandingApprovals += $row['count'];
                }

                // disabled chapters
                $libraryIDs = Library::getModeratedLibraries('canEnableChapter');
                if (!empty($libraryIDs)) {
                    $sql = "SELECT		COUNT(*) AS count
						FROM		sls" . SLS_N . "_chapter chapter
						LEFT JOIN	sls" . SLS_N . "_story story
						ON		(story.storyID = chapter.storyID)
						WHERE		chapter.isDisabled = 1
								AND story.libraryID IN (" . $libraryIDs . ")";
                    $row = WCF::getDB()->getFirstRow($sql);
                    $this->outstandingApprovals += $row['count'];
                }

                // reported chapters
                $libraryIDs = Library::getModeratedLibraries('canModeratChapter');
                $libraryIDs2 = Library::getModeratedLibraries('canReadDeletedChapter');
                if (!empty($libraryIDs)) {
                    $sql = "SELECT		COUNT(*) AS count
						FROM		sls" . SLS_N . "_chapter_report report
						LEFT JOIN	sls" . SLS_N . "_chapter chapter
						ON		(chapter.chapterID = report.chapterID)
						LEFT JOIN	sls" . SLS_N . "_story story
						ON		(story.storyID = chapter.storyID)
						WHERE		story.libraryID IN (" . $libraryIDs . ")
								AND (chapter.isDeleted = 0" . (!empty($libraryIDs2) ? " OR story.libraryID IN (" . $libraryIDs2 . ")" : '') . ")";
                    $row = WCF::getDB()->getFirstRow($sql);
                    $this->outstandingApprovals += $row['count'];
                }

                WCF::getSession()->register('outstandingApprovals', $this->outstandingApprovals);
            }
        }

        return $this->outstandingApprovals;
    }

    /**
     * Returns the last mark all as read timestamp.
     * 
     * @return	integer
     */
    public function getLastMarkAllAsReadTime() {
        return $this->libraryLastMarkAllAsReadTime;
    }

    /**
     * @see	PM::getOutstandingNotifications()
     */
    public function getInvitations() {
        if ($this->invitations === null) {
            $this->invitations = array();
            $sql = "SELECT		user_table.userID, user_table.username
				FROM		wcf" . WCF_N . "_user_whitelist whitelist
				LEFT JOIN	wcf" . WCF_N . "_user user_table
				ON		(user_table.userID = whitelist.userID)
				WHERE		whitelist.whiteUserID = " . $this->userID . "
						AND whitelist.confirmed = 0
						AND whitelist.notified = 0
				ORDER BY	whitelist.time";
            $result = WCF::getDB()->sendQuery($sql);
            while ($row = WCF::getDB()->fetchArray($result)) {
                $this->invitations[] = new User(null, $row);
            }
        }

        return $this->invitations;
    }

    /**
     * Returns the avatar of this user.
     *
     * @return	DisplayableAvatar
     */
    public function getAvatar() {
        return $this->avatar;
    }

}
?>