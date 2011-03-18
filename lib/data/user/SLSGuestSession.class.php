<?php
// sls imports
require_once(SLS_DIR.'lib/data/user/AbstractSLSUserSession.class.php');

/**
 * Represents a guest session in the library.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.user
 * @category 	Story Library System
 */
class SLSGuestSession extends AbstractSLSUserSession {
	protected $libaryVisits = null;
	protected $storyVisits = null;
	protected $closedCategories = null;
	protected $lastVisitTime = null;

	/**
	 * Initialises the user session.
	 */
	public function init() {
		parent::init();

		$this->libaryVisits = $this->storyVisits = $this->closedCategories = $this->lastVisitTime = null;
	}

	/**
	 * Sets the global libary last visit timestamp.
	 */
	public function setLastVisitTime($timestamp) {
		$this->lastVisitTime = $timestamp;
		// cookie
		HeaderUtil::setCookie('libaryLastVisitTime', $this->lastVisitTime, TIME_NOW + 365 * 24 * 3600);
		// session
		SessionFactory::getActiveSession()->register('libaryLastVisitTime', $this->lastVisitTime);
	}

	/**
	 * Returns the last visit time of this user.
	 *
	 * @return	integer
	 */
	public function getLastVisitTime() {
		if ($this->lastVisitTime === null) {
			$this->lastVisitTime = 0;
			if (isset($_COOKIE[COOKIE_PREFIX.'libaryLastVisitTime'])) {
				$this->lastVisitTime = intval($_COOKIE[COOKIE_PREFIX.'libaryLastVisitTime']);
			}
			else {
				$this->lastVisitTime = intval(SessionFactory::getActiveSession()->getVar('libaryLastVisitTime'));
			}

			if ($this->lastVisitTime < TIME_NOW - 3600 * 24 * 365) {
				$this->lastVisitTime = TIME_NOW - VISIT_TIME_FRAME;
			}
		}

		return $this->lastVisitTime;
	}

	/**
	 * Gets the libary visits of this guest from session variables.
	 */
	protected function getLibraryVisits() {
		if ($this->libaryVisits === null) {
			$this->libaryVisits = WCF::getSession()->getVar('libaryVisits');
			if ($this->libaryVisits === false) $this->libaryVisits = array();
		}
	}

	/**
	 * Returns the libary visit of this guest for the libary with the given libary id.
	 *
	 * @return	integer		libary visit of this guest for the libary with the given libary id
	 */
	public function getLibraryVisitTime($libaryID) {
		$this->getLibraryVisits();
		$libaryVisitTime = 0;

		if (isset($this->libaryVisits[$libaryID])) return $libaryVisitTime = $this->libaryVisits[$libaryID];
		if ($libaryVisitTime < $this->getLastVisitTime()) {
			$libaryVisitTime = $this->getLastVisitTime();
		}

		return $libaryVisitTime;
	}

	/**
	 * Sets the libary visit of this guest for the libary with the given libary id.
	 *
	 * @param	integer		$libaryID
	 */
	public function setLibraryVisitTime($libaryID) {
		$this->getLibraryVisits();

		$this->libaryVisits[$libaryID] = TIME_NOW;
		WCF::getSession()->register('libaryVisits', $this->libaryVisits);
	}

	/**
	 * Gets the story visits of this guest from session variables.
	 */
	protected function getStoryVisits() {
		if ($this->storyVisits === null) {
			$this->storyVisits = WCF::getSession()->getVar('storyVisits');
			if ($this->storyVisits === false) $this->storyVisits = array();
		}
	}

	/**
	 * Returns the story visit of this guest for the story with the given story id.
	 *
	 * @return	integer		story visit of this guest for the story with the given story id
	 */
	public function getStoryVisitTime($storyID) {
		$this->getStoryVisits();
		$storyVisitTime = 0;

		if (isset($this->storyVisits[$storyID])) return $storyVisitTime = $this->storyVisits[$storyID];
		if ($storyVisitTime < $this->getLastVisitTime()) {
			$storyVisitTime = $this->getLastVisitTime();
		}

		return $storyVisitTime;
	}

	/**
	 * Sets the story visit of this guest for the story with the given story id.
	 *
	 * @param	integer		$storyID
	 */
	public function setStoryVisitTime($storyID, $timestamp = TIME_NOW) {
		$this->getStoryVisits();

		$this->storyVisits[$storyID] = $timestamp;
		WCF::getSession()->register('storyVisits', $this->storyVisits);
	}

	/**
	 * Gets the closed categories of this guest from session variables.
	 */
	protected function getClosedCategories() {
		if ($this->closedCategories === null) {
			$this->closedCategories = WCF::getSession()->getVar('closedCategories');
			if ($this->closedCategories === null) $this->closedCategories = array();
		}
	}

	/**
	 * Returns true, if the category with the given libary id is closed by this guest.
	 *
	 * @param	integer		$libaryID
	 * @return	boolean
	 */
	public function isClosedCategory($libaryID) {
		$this->getClosedCategories();

		if (!isset($this->closedCategories[$libaryID])) return 0;
		return $this->closedCategories[$libaryID];
	}

	/**
	 * Closes the category with the given libary id for this guest.
	 *
	 * @param	integer		$libaryID
	 * @param	integer		$close		1 closes the category
	 *						-1 opens the category
	 */
	public function closeCategory($libaryID, $close = 1) {
		$this->getClosedCategories();

		require_once(SLS_DIR.'lib/data/library/Library.class.php');
		$libary = Library::getLibrary($libaryID);
		if (!$libary->isCategory()) {
			throw new IllegalLinkException();
		}

		$this->closedCategories[$libaryID] = $close;
		WCF::getSession()->register('closedCategories', $this->closedCategories);
	}

	/**
	 * Does nothing.
	 */
	public function isIgnoredLibrary($libaryID) {
		return 0;
	}

	/**
	 * Returns the last mark all as read timestamp.
	 *
	 * @return	integer
	 */
	public function getLastMarkAllAsReadTime() {
		return $this->getLastVisitTime();
	}

	/**
	 * Sets the last mark all as read timestamp.
	 */
	public function setLastMarkAllAsReadTime($timestamp) {
		$this->setLastVisitTime($timestamp);
	}
}
?>