<?php
// wcf imports
require_once(WCF_DIR.'lib/data/tag/Tagged.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

// wbb imports
require_once(SLS_DIR.'lib/data/story/ViewableStory.class.php');

/**
 * An implementation of Tagged to support the tagging of stories.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.story
 * @category 	Story Library System
 */
class TaggedStory extends ViewableStory implements Tagged {
	/**
	 * user object
	 *
	 * @var	User
	 */
	protected $user = null;

	/**
	 * @see ViewableStory::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);

		// get user
		$this->user = new User(null, array('userID' => $this->userID, 'username' => $this->username));
	}

	/**
	 * @see Tagged::getTitle()
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @see Tagged::getObjectID()
	 */
	public function getObjectID() {
		return $this->storyID;
	}

	/**
	 * @see Tagged::getTaggable()
	 */
	public function getTaggable() {
		return $this->taggable;
	}

	/**
	 * @see Tagged::getDescription()
	 */
	public function getDescription() {
		require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
		$parser = MessageParser::getInstance();
		$parser->setOutputType('text/html');
		return $parser->parse($this->firstChapterPreview, true, false, true, false);
	}

	/**
	 * @see Tagged::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('storyS.png');
	}

	/**
	 * @see Tagged::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('storyM.png');
	}

	/**
	 * @see Tagged::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('storyL.png');
	}

	/**
	 * @see Tagged::getUser()
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @see Tagged::getDate()
	 */
	public function getDate() {
		return $this->lastChapterTime;
	}

	/**
	 * @see Tagged::getDate()
	 */
	public function getURL() {
		return RELATIVE_SLS_DIR . 'index.php?page=Story&storyID='.$this->storyID;
	}
}
?>