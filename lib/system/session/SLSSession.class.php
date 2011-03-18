<?php

// sls imports
require_once(SLS_DIR . 'lib/data/user/SLSUserSession.class.php');
require_once(SLS_DIR . 'lib/data/user/SLSGuestSession.class.php');

// wcf imports
require_once(WCF_DIR . 'lib/system/session/CookieSession.class.php');
require_once(WCF_DIR . 'lib/data/user/User.class.php');

/**
 * SLSSession extends the CookieSession class with library specific functions.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.session
 * @category 	Story Library System
 */
class SLSSession extends CookieSession {

    protected $userSessionClassName = 'SLSUserSession';
    protected $guestSessionClassName = 'SLSGuestSession';
    protected $libraryID = 0;
    protected $storyID = 0;
    protected $styleID = 0;

    /**
     * Initialises the session.
     */
    public function init() {
        parent::init();

        // handle style id
        if ($this->user->userID)
            $this->styleID = $this->user->styleID;
        if (($styleID = $this->getVar('styleID')) !== null)
            $this->styleID = $styleID;

        if ($this->userID) {
            // user
            // update library / story visits
            if ($this->user->libraryLastActivityTime > $this->user->libraryLastVisitTime && $this->user->libraryLastActivityTime < TIME_NOW - SESSION_TIMEOUT) {
                $this->user->setLastVisitTime($this->user->libraryLastActivityTime);

                // remove unnecessary library and story visits
                $sql = "DELETE FROM	sls" . SLS_N . "_story_visit
					WHERE		userID = " . $this->userID . "
							AND lastVisitTime <= " . ($this->user->libraryLastMarkAllAsReadTime);
                WCF::getDB()->registerShutdownUpdate($sql);

                $sql = "DELETE FROM	sls" . SLS_N . "_library_visit
					WHERE		userID = " . $this->userID . "
							AND lastVisitTime <= " . ($this->user->libraryLastMarkAllAsReadTime);
                WCF::getDB()->registerShutdownUpdate($sql);

                // reset user data
                $this->resetUserData();
            }

            // update global last activity time
            if ($this->lastActivityTime < TIME_NOW - USER_ONLINE_TIMEOUT + 299) {
                SLSUserSession::updateLastActivityTime($this->userID);
            }
        } else {
            // guest
            $libraryLastActivityTime = 0;
            $libraryLastVisitTime = $this->user->getLastVisitTime();
            if (isset($_COOKIE[COOKIE_PREFIX . 'libraryLastActivityTime'])) {
                $libraryLastActivityTime = intval($_COOKIE[COOKIE_PREFIX . 'libraryLastActivityTime']);
            }

            if ($libraryLastActivityTime != 0 && $libraryLastActivityTime < $libraryLastVisitTime && $libraryLastActivityTime < TIME_NOW - SESSION_TIMEOUT) {
                $this->user->setLastVisitTime($libraryLastActivityTime);
                $this->resetUserData();
            }

            HeaderUtil::setCookie('libraryLastActivityTime', TIME_NOW, TIME_NOW + 365 * 24 * 3600);
        }
    }

    /**
     * @see CookieSession::update()
     */
    public function update() {
        //ToDo wird das wirklich benögigt?
        //$this->updateSQL .= ", libraryID = " . $this->libraryID . ", storyID = " . $this->storyID;

        parent::update();
    }

    /**
     * Sets the current library id for this session.
     *
     * @param	integer		$libraryID
     */
    public function setLibraryID($libraryID) {
        $this->libraryID = $libraryID;
    }

    /**
     * Sets the current story id for this session.
     *
     * @param	integer		$storyID
     */
    public function setStoryID($storyID) {
        $this->storyID = $storyID;
    }

    /**
     * Sets the active style id.
     * 
     * @param 	integer		$newStyleID
     */
    public function setStyleID($newStyleID) {
        $this->styleID = $newStyleID;
        if ($newStyleID > 0)
            $this->register('styleID', $newStyleID);
        else
            $this->unregister('styleID');
    }

    /**
     * Returns the active style id.
     *
     * @return	integer
     */
    public function getStyleID() {
        return $this->styleID;
    }

}
?>