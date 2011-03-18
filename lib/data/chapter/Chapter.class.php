<?php

// wcf imports
require_once(WCF_DIR . 'lib/data/message/Message.class.php');

/**
 * Represents a chapter in the library
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage  data.chapter
 * @category 	Story Library System
 */
class Chapter extends Message {

    /**
     * Creates a new chapter object.
     *
     * If id is set, the function reads the chapter data from database.
     * Otherwise it uses the given resultset.
     *
     * @param 	integer 	$chapterID		id of a chapter
     * @param 	array 		$row                    resultset with chapter data form database
     */
    public function __construct($chapterID, $row = null) {
        if ($chapterID !== null) {
            $sql = "SELECT	*
				FROM 	sls" . SLS_N . "_chapter
				WHERE 	chapterID = " . $chapterID;
            $row = WCF::getDB()->getFirstRow($sql);
        }
        parent::__construct($row);
        //$this->chapterID = $row['chapterID'];
        if (CHAPTER_IN_FILE) {
            $this->file = CHAPTER_FILE_PATH . "/" . $this->authorID . "/" . $this->chapterID . ".txt";
            if (file_exists($this->file)) {
                $this->text = file_get_contents($this->file);
            }
        }
    }

    /**
     * Returns true, if this chapter is marked in the active session.
     */
    public function isMarked() {
        $sessionVars = WCF::getSession()->getVars();
        if (isset($sessionVars['markedChapters'])) {
            if (in_array($this->chapterID, $sessionVars['markedChapters']))
                return 1;
        }

        return 0;
    }

    /**
     * Returns the number of quotes of this chapter.
     *
     * @return	integer
     */
    public function isQuoted() {
        require_once(WCF_DIR . 'lib/data/message/multiQuote/MultiQuoteManager.class.php');
        return MultiQuoteManager::getQuoteCount($this->chapterID, 'chapter');
    }

    /**
     * Returns true, if the active user can edit or delete this chapter.
     *
     * @param	Library		$library
     * @param	Story		$story
     * @return	boolean
     */
    public function canEditChapter($library, $story) {
        $isModerator = $library->getModeratorPermission('canEditChapter') || $library->getModeratorPermission('canDeleteChapter');
        $isAuthor = $this->userID && $this->userID == WCF::getUser()->userID;

        $canEditChapter = $library->getModeratorPermission('canEditChapter') || $isAuthor && $library->getPermission('canEditOwnChapter');
        $canDeleteChapter = $library->getModeratorPermission('canDeleteChapter') || $isAuthor && $library->getPermission('canDeleteOwnChapter');

        if ((!$canEditChapter && !$canDeleteChapter) || (!$isModerator && ($library->isClosed || $story->isClosed || $this->isClosed))) {
            return false;
        }

        // check chapter edit timeout
        if (!$isModerator && WCF::getUser()->getPermission('user.library.chapterEditTimeout') != -1 && TIME_NOW - $this->time > WCF::getUser()->getPermission('user.library.chapterEditTimeout') * 60) {
            return false;
        }

        return true;
    }

}
?>