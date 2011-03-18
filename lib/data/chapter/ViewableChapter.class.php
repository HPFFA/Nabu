<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');
require_once(WCF_DIR.'lib/data/message/util/KeywordHighlighter.class.php');
require_once(WCF_DIR.'lib/data/message/sidebar/MessageSidebarObject.class.php');

// sls imports
require_once(SLS_DIR.'lib/data/chapter/Chapter.class.php');
require_once(SLS_DIR.'lib/data/user/SLSUser.class.php');

/**
 * Represents a viewable chapter in the library
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage  data.chapter
 * @category 	Story Library System
 */
class ViewableChapter extends Chapter implements MessageSidebarObject {
	protected $user;
	protected $story;

	/**
	 * Creates a new ViewableChapter object.
	 *
	 * @param 	integer 	$chapterID
	 * @param 	array 		$row		resultset with chapter data form database
	 * @param 	Story		$story		story of this chapter
	 */
	public function __construct($chapterID, $row = null, $story = null) {
		parent::__construct($chapterID, $row);
		$this->story = $story;
	}

	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		parent::handleData($data);
		$this->user = new SLSUser(null, $data);
	}

	/**
	 * Returns the text of this chapter.
	 *
	 * @return 	string		the text of this chapter
	 */
	public function getFormattedText() {
		// return text cache
		if ($this->textCache) {
			return KeywordHighlighter::doHighlight($this->textCache);
		}

		// parse message
		$parser = MessageParser::getInstance();
		$parser->setOutputType('text/html');
		return $parser->parse($this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, !$this->textPreview);
	}

	/**
	 * Returns true, if the active user doesn't have read this chapter.
	 *
	 * @return	boolean		true, if the active user doesn't have read this chapter
	 */
	public function isNew() {
		if ($this->story == null) return false;
		return ($this->time > $this->story->lastVisitTime);
	}

	/**
	 * @see Chapter::canEditChapter()
	 */
	public function canEditChapter($library, $story) {
		if ($this->story != null) $story = $this->story;
		return parent::canEditPost($library, $story);
	}

	/**
	 * Returns the filename of the chapter icon.
	 *
	 * @return	string		filename of the chapter icon
	 */
	public function getIconName() {
		// deleted
		if ($this->isDeleted) return 'chapterTrash';

		$icon = 'chapter';

		// new
		if ($this->isNew()) $icon .= 'New';

		// closed
		if ($this->isClosed) $icon .= 'Closed';

		return $icon;
	}

	

	// MessageSidebarObject implementation
	/**
	 * @see MessageSidebarObject::getUser()
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @see MessageSidebarObject::getMessageID()
	 */
	public function getMessageID() {
		return $this->chapterID;
	}

	/**
	 * @see MessageSidebarObject::getMessageType()
	 */
	public function getMessageType() {
		return 'chapter';
	}
}
?>