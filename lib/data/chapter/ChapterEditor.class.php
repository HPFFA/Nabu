<?php

// sls imports
require_once(SLS_DIR . 'lib/data/chapter/Chapter.class.php');

/**
 * ChapterEditor provides functions to create and edit the data of a chapter.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.chapter
 * @category 	Story Library System
 */
class ChapterEditor extends Chapter {

    /**
     * Updates the data of this chapter.
     *
     * @param	string				$subject		new subject of this chapter
     * @param	string				$message		new text of this chapter
     * @param	array				$options		new options of this chapter
     * @param	AttachmentsEditor		$attachments
     * @param	PollEditor			$poll
     */
    public function update($title, $text, $options, $additionalData = array()) {
        $updateText = '';

        // save subject
        if ($title != $this->title) {
            $updatetitle = "title = '" . escapeString($title) . "',";
        }

        // save text
        if (CHAPTER_IN_FILE) {
            $file = CHAPTER_FILE_PATH . "/" . $this->authorID . "/" . $this->chapterID . "_cache.txt";
                file_put_contents($file, escapeString($text));
        } else {
            $updateText = "text_cache = '" . escapeString($text) . "',";
        }


        // update chapter cache
        //ToDo überprüfen, ob das gebraucht wird.
        /*   require_once(WCF_DIR . 'lib/data/message/bbcode/MessageParser.class.php');
          $parser = MessageParser::getInstance();
          $parser->setOutputType('text/html');
          $sql = "UPDATE	sls" . SLS_N . "_chapter_cache
          SET	textCache = '" . escapeString($parser->parse($text, $options['enableSmilies'], $options['enableHtml'], $options['enableBBCodes'], false)) . "'
          WHERE	chapterID = " . $this->chapterID;
          WCF::getDB()->registerShutdownUpdate($sql);
         */
        $additionalSql = '';
        foreach ($additionalData as $key => $value) {
            $additionalSql .= ',' . $key . "='" . escapeString($value) . "'";
        }

        // save chapter in database
        $sql = "UPDATE 	sls" . SLS_N . "_chapter
			SET	$updateTitle
				$updateText
                                updateTime = date(),
				enableSmilies = " . $options['enableSmilies'] . ",
				enableHtml = " . $options['enableHtml'] . ",
				enableBBCodes = " . $options['enableBBCodes'] . ",
				" . $additionalSql . "
			WHERE 	chapterID = " . $this->chapterID;
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Updates the text of this chapter.
     *
     * @param	string				$text		new text of this chapter
     */
    public function updateText($text, $additionalData = array()) {
        $additionalSql = '';
        foreach ($additionalData as $key => $value) {
            $additionalSql .= ',' . $key . "='" . escapeString($value) . "'";
        }

        if (CHAPTER_IN_FILE) {
            $this->file = CHAPTER_FILE_PATH . "/" . $this->authorID . "/" . $this->chapterID . "_cache.txt";
            if (file_exists($this->file)) {
                file_put_contents($this->file, $text);
            }
        } else {
            $updateText = "text_cache = '" . escapeString($text) . "',";
        }
        // save chapter in database
        $sql = "UPDATE 	sls" . SLS_N . "_chapter
			SET	" . $updateText . "
                                updateTime = date(),
				" . $additionalSql . "
			WHERE 	chapterID = " . $this->chapterID;
        WCF::getDB()->sendQuery($sql);




        // update chapter cache
        require_once(WCF_DIR . 'lib/data/message/bbcode/MessageParser.class.php');
        MessageParser::getInstance()->setOutputType('text/html');
        $sql = "UPDATE	wbb" . SLS_N . "_chapter_cache
			SET	messageCache = '" . escapeString(MessageParser::getInstance()->parse($message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, false)) . "'
			WHERE	chapterID = " . $this->chapterID;
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Updates the first chapter preview.
     *
     * @param	integer		$storyID
     * @param	integer		$chapterID
     * @param	string		$text
     * @param	array		$options
     */
    public static function updateFirstChapterPreview($storyID, $chapterID, $text, $options) {
        if (!LIBRARY_STORIES_ENABLE_TEXT_PREVIEW) {
            return;
        }

        require_once(WCF_DIR . 'lib/data/message/bbcode/MessageParser.class.php');
        $parser = MessageParser::getInstance();
        $parser->setOutputType('text/plain');
        $text = StringUtil::stripHTML($text);
        $parsedText = $parser->parse($text, $options['enableSmilies'], $options['enableHtml'], $options['enableBBCodes'], false);

        if (StringUtil::length($parsedText) > 500) {
            $parsedMessage = StringUtil::substring($parsedText, 0, 497) . '...';
        }

        $sql = "UPDATE	sls" . SLS_N . "_story
			SET	firstChapterPreview = '" . escapeString($parsedText) . "'
			WHERE	storyID = " . $storyID . "
				AND firstChapterID = " . $chapterID;
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Sets the subject of this text.
     *
     * @param	string		$title	new subject for this text
     */
    public function setTitle($title) {
        $sql = "UPDATE 	sls" . SLS_N . "_chapter SET
				title = '" . escapeString($title) . "'
			WHERE 	chapterID = " . $this->chapterID;
        WCF::getDB()->registerShutdownUpdate($sql);
    }

    /**
     * Marks this chapter.
     */
    public function mark() {
        $markedChapters = self::getMarkedChapters();
        if ($markedChapters == null || !is_array($markedChapters)) {
            $markedChapters = array($this->chapterID);
            WCF::getSession()->register('markedChapters', $markedChapters);
        } else {
            if (!in_array($this->chapterID, $markedChapters)) {
                array_push($markedChapters, $this->chapterID);
                WCF::getSession()->register('markedChapters', $markedChapters);
            }
        }
    }

    /**
     * Unmarks this chapter.
     */
    public function unmark() {
        $markedChapters = self::getMarkedChapters();
        if (is_array($markedChapters) && in_array($this->chapterID, $markedChapters)) {
            $key = array_search($this->chapterID, $markedChapters);

            unset($markedChapters[$key]);
            if (count($markedChapters) == 0) {
                self::unmarkAll();
            } else {
                WCF::getSession()->register('markedChapters', $markedChapters);
            }
        }
    }

    /**
     * Moves this chapter in the recycle bin.
     */
    public function trash($reason = '') {
        self::trashAll($this->chapterID, $reason);
    }

    /**
     * Deletes this chapter completely.
     *
     * Deletes the sql data in tables chapter, chapter_cache and chapter_report.
     */
    public function delete($updateUserStats = true) {
        self::deleteAllCompletely($this->chapterID, $updateUserStats);
    }

    /**
     * Restores this deleted chapter.
     */
    public function restore() {
        self::restoreAll($this->chapterID);
    }

    /**
     * Disables this chapter.
     */
    public function disable() {
        self::disableAll($this->chapterID);
    }

    /**
     * Enables this chapter.
     */
    public function enable() {
        self::enableAll($this->chapterID);
    }

    /**
     * Closes this chapter.
     */
    public function close() {
        $sql = "UPDATE 	sls" . SLS_N . "_chapter
			SET	isClosed = 1
			WHERE 	chapterID = " . $this->chapterID;
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Opens this chapter.
     */
    public function open() {
        $sql = "UPDATE 	sls" . SLS_N . "_chapter
			SET	isClosed = 0
			WHERE 	chapterID = " . $this->chapterID;
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Copies the sql data of this chapter.
     */
    public function copy($storyID) {
        return self::insert($this->title, $this->message, $storyID, array(
            'authorID' => $this->authorID,
            'authorname' => $this->authorname,
            'time' => $this->time,
            'isDeleted' => $this->isDeleted,
            'isDisabled' => $this->isDisabled,
            'everEnabled' => $this->everEnabled,
            'isClosed' => $this->isClosed,
            'editor' => $this->editor,
            'editorID' => $this->editorID,
            'lastEditTime' => $this->lastEditTime,
            'editCount' => $this->editCount,
            'enableSmilies' => $this->enableSmilies,
            'enableHtml' => $this->enableHtml,
            'enableBBCodes' => $this->enableBBCodes,
            'ipAddress' => $this->ipAddress
        ));
    }

    /**
     * Creates a new chapter with the given data in the database.
     * Returns a ChapterEditor object of the new chapter.
     *
     * @param	integer				$storyID
     * @param	string				$title  		title of the new chapter
     * @param	string				$text			text of the new chapter
     * @param	integer				$auhorID		user id of the author of the new chapter
     * @param	string				$authorname		username of the author of the new chapter
     * @param	array				$options		options of the new chapter
     *
     * @return	ChapterEditor						the new chapter
     */
    public static function create($storyID, $title, $text, $authorID, $authorname, $options, $ipAddress = null, $disabled = 0, $firstChapter = false) {
        if ($ipAddress == null)
            $ipAddress = WCF::getSession()->ipAddress;
        $hash = StringUtil::getHash(($firstChapter ? '' : $storyID) . $title . $text . $authorID . $authorname);

        // insert chapter
        $chapterID = self::insert($title, $text, $storyID, array(
                    'authorID' => $authorID,
                    'authorname' => $authorname,
                    'time' => TIME_NOW,
                    'enableSmilies' => $options['enableSmilies'],
                    'enableHtml' => $options['enableHtml'],
                    'enableBBCodes' => $options['enableBBCodes'],
                    'ipAddress' => $ipAddress,
                    'isDisabled' => $disabled,
                    'everEnabled' => ($disabled ? 0 : 1)
                ));

        // save hash
        $sql = "INSERT INTO	sls" . SLS_N . "_chapter_hash
					(chapterID, textHash, time)
			VALUES		(" . $chapterID . ", '" . $hash . "', " . TIME_NOW . ")";
        WCF::getDB()->sendQuery($sql);

        // get chapter
        $chapter = new ChapterEditor($chapterID);


        // create chapter cache
        require_once(WCF_DIR . 'lib/data/message/bbcode/MessageParser.class.php');
        $parser = MessageParser::getInstance();
        $parser->setOutputType('text/html');
        $sql = "INSERT INTO 	sls" . SLS_N . "_chapter_cache
					(chapterID, storyID, textCache)
			VALUES		(" . $chapterID . ",
					" . $storyID . ",
					'" . escapeString($parser->parse($chapter->text, $chapter->enableSmilies, $chapter->enableHtml, $chapter->enableBBCodes, false)) . "')";
        WCF::getDB()->sendQuery($sql);



        // save last chapter
        if (PROFILE_SHOW_LAST_CHAPTERS && $authorID != 0) {
            $sql = "INSERT INTO	sls" . SLS_N . "_author_last_chapter
						(auhtorID, chapterID, time)
				VALUES		(" . $authorID . ", " . $chapterID . ", " . TIME_NOW . ")";
            WCF::getDB()->registerShutdownUpdate($sql);
        }

        return $chapter;
    }

    /**
     * Creates the chapter row in database table.
     *
     * @param 	string 		$title
     * @param 	string		$text
     * @param	integer		$storyID
     * @param 	array		$additionalFields
     * @return	integer		new chapter id
     */
    public static function insert($title, $text, $storyID, $additionalFields = array()) {
        $keys = $values = '';
        foreach ($additionalFields as $key => $value) {
            $keys .= ',' . $key;
            $values .= ",'" . escapeString($value) . "'";
        }
        if (CHAPTER_IN_FILE) {
            $sql = "INSERT INTO	sls" . SLS_N . "_chapter
					(storyID, title
					" . $keys . ")
			VALUES		(" . $storyID . ", '" . escapeString($title) . "'
					" . $values . ")";
        } else {
            $sql = "INSERT INTO	sls" . SLS_N . "_chapter
					(storyID, title, text
					" . $keys . ")
			VALUES		(" . $storyID . ", '" . escapeString($title) . "', '" . escapeString($text) . "'
					" . $values . ")";
        }
        WCF::getDB()->sendQuery($sql);
        $chapterID = WCF::getDB()->getInsertID();
        if (CHAPTER_IN_FILE) {
            $file = CHAPTER_FILE_PATH . "/" . $authorID . "/" . $chapterID . "_cache.txt";
            file_put_contents($file, escapeString($text));
        }
        return $chapterID;
    }

    /**
     * Checks whether a chapter with the given data already exists in the database.
     *
     * @param	string		$subject
     * @param	string		$message
     * @param	integer		$authorID
     * @param	string		$author
     * @param	integer		$threadID
     *
     * @return	boolean		true, if a chapter with the given data already exists in the database
     */
    //TODO später schaun, ob wichtig
    /* public static function test($subject, $message, $authorID, $author, $threadID = 0) {
      $hash = StringUtil::getHash(($threadID ? $threadID : '') . $subject . $message . $authorID . $author);
      $sql = "SELECT		chapterID
      FROM 		wbb" . SLS_N . "_chapter_hash
      WHERE 		messageHash = '" . $hash . "'";
      $row = WCF::getDB()->getFirstRow($sql);
      if (!empty($row['chapterID']))
      return $row['chapterID'];
      return false;
      }
     */

    /**
     * Creates the preview of a chapter with the given data.
     *
     * @param	string		$title
     * @param	string		$text
     *
     * @return	string		the preview of a chapter
     */
    public static function createPreview($title, $notes, $text, $enableSmilies = 1, $enableHtml = 0, $enableBBCodes = 1) {
        $row = array(
            'chapterID' => 0,
            'title' => $title,
            'text' => $text,
            'enableSmilies' => $enableSmilies,
            'enableHtml' => $enableHtml,
            'enableBBCodes' => $enableBBCodes,
            'messagePreview' => true
        );

        require_once(SLS_DIR . 'lib/data/chapter/ViewableChapter.class.php');
        $chapter = new ViewableChapter(null, $row);
        return $chapter->getFormattedText();
    }

    /**
     * Returns the marked chapters.
     *
     * @return	array		marked chapters
     */
    public static function getMarkedChapters() {
        $sessionVars = WCF::getSession()->getVars();
        if (isset($sessionVars['markedChapters'])) {
            return $sessionVars['markedChapters'];
        }
        return null;
    }

    /**
     * Unmarks all marked chapters.
     */
    public static function unmarkAll() {
        WCF::getSession()->unregister('markedChapters');
    }

    /**
     * Restores all chapters with the given ids.
     */
    public static function restoreAll($chapterIDs) {
        $sql = "UPDATE 	sls" . SLS_N . "_chapter
			SET	isDeleted = 0
			WHERE 	chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Enables all chapters with the given ids.
     */
    public static function enableAll($chapterIDs) {
        // send notifications
        require_once(SLS_DIR . 'lib/data/library/Library.class.php');
        $statChapterIDs = '';
        $sql = "SELECT		chapter.*, story.LibraryID
			FROM		sls" . SLS_N . "_chapter chapter
			LEFT JOIN	sls" . SLS_N . "_story story
			ON		(story.storyID = chapter.storyID)
			WHERE		chapter.chapterID IN (" . $chapterIDs . ")
					AND chapter.isDisabled = 1
					AND chapter.everEnabled = 0
					AND chapter.chapterID <> story.firstChapterID";
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            if (!empty($statChapterIDs))
                $statChapterIDs .= ',';
            $statChapterIDs .= $row['chapterID'];

            // send notifications
            $chapter = new ChapterEditor(null, $row);
            $chapter->sendNotification();
        }

        // update user chapters & activity points
        self::updateUserStats($statChapterIDs, 'enable');

        // enable chapters
        $sql = "UPDATE 	sls" . SLS_N . "_chapter
			SET	isDisabled = 0,
				everEnabled = 1
			WHERE 	chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Disables all chapters with the given ids.
     */
    public static function disableAll($chapterIDs) {
        $sql = "UPDATE 	sls" . SLS_N . "_chapter
			SET	isDisabled = 1,
				isDeleted = 0
			WHERE 	chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Deletes the chapters with the given chapter ids.
     */
    public static function deleteAll($chapterIDs, $updateUserStats = true, $reason = '') {
        if (empty($chapterIDs))
            return;

        $trashIDs = '';
        $deleteIDs = '';
        if (STORY_ENABLE_RECYCLE_BIN) {
            // recylce bin enabled
            // first of all we check which chapters are already in recylce bin
            $sql = "SELECT 	chapterID, isDeleted
				FROM 	sls" . SLS_N . "_chapter
				WHERE 	chapterID IN (" . $chapterIDs . ")";
            $result = WCF::getDB()->sendQuery($sql);
            while ($row = WCF::getDB()->fetchArray($result)) {
                if ($row['isDeleted']) {
                    // chapter in recylce bin
                    // delete completely
                    if (!empty($deleteIDs))
                        $deleteIDs .= ',';
                    $deleteIDs .= $row['chapterID'];
                }
                else {
                    // move chapter to recylce bin
                    if (!empty($trashIDs))
                        $trashIDs .= ',';
                    $trashIDs .= $row['chapterID'];
                }
            }
        }
        else {
            // no recylce bin
            // delete all stories completely
            $deleteIDs = $chapterIDs;
        }

        self::trashAll($trashIDs, $reason);
        self::deleteAllCompletely($deleteIDs, true, true, $updateUserStats);

        // reset first chapter id
        self::resetFirstChapterID($chapterIDs);
    }

    /**
     * Moves the chapters with the given chapter ids into the recycle bin.
     */
    public static function trashAll($chapterIDs, $reason = '') {
        if (empty($chapterIDs))
            return;

        $sql = "UPDATE 	sls" . SLS_N . "_chapter
			SET	isDeleted = 1,
				deleteTime = " . TIME_NOW . ",
				deletedBy = '" . escapeString(WCF::getUser()->username) . "',
				deletedByID = " . WCF::getUser()->userID . ",
				deleteReason = '" . escapeString($reason) . "',
				isDisabled = 0
			WHERE 	chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Deletes all chapters with the given chapter ids.
     */
    public static function deleteAllCompletely($chapterIDs, $updateUserStats = true) {
        if (empty($chapterIDs))
            return;


        // update user chapters & activity points
        if ($updateUserStats) {
            self::updateUserStats($chapterIDs, 'delete');
        }

        // delete sql data
        self::deleteData($chapterIDs);
    }

    /**
     * Deletes the sql data of the chapters with the given chapter ids.
     */
    protected static function deleteData($chapterIDs) {
        // delete chapter, chapter_cache, chapter_hash and chapter_report
        $sql = "DELETE FROM 	sls" . SLS_N . "_chapter
			WHERE 		chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->sendQuery($sql);

        $sql = "DELETE FROM	sls" . SLS_N . "_chapter_cache
			WHERE 		chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->registerShutdownUpdate($sql);

        $sql = "DELETE FROM	sls" . SLS_N . "_chapter_hash
			WHERE 		chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->registerShutdownUpdate($sql);

        $sql = "DELETE FROM 	sls" . SLS_N . "_chapter_report
			WHERE 		chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->registerShutdownUpdate($sql);

        // delete last chapters
        $sql = "DELETE FROM 	sls" . SLS_N . "_user_last_chapter
			WHERE 		chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->registerShutdownUpdate($sql);
        //TODO chapter file löschen
    }

    /**
     * Copies all SQL data of the chapters with the given chapters ids.
     */
    public static function copyAll($chapterIDs, $storyID, $storyMapping = null, $libraryID = 0, $updateUserStats = true) {
        if (empty($chapterIDs))
            return;

        // copy 'chapter' data
        $chapterMapping = array();
        $sql = "SELECT	*
			FROM 	sls" . SLS_N . "_chapter
			WHERE 	chapterID IN (" . $chapterIDs . ")";
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            $chapter = new ChapterEditor(null, $row);
            $chapterMapping[$chapter->chapterID] = $chapter->copy($storyID ? $storyID : $storyMapping[$row['storyID']]);
        }

        // refresh first chapter ids
        require_once(SLS_DIR . 'lib/data/story/StoryEditor.class.php');
        StoryEditor::refreshFirstChapterIDAll(($storyID ? $storyID : implode(',', $storyMapping)));

        // update user chapters and activity points
        if ($updateUserStats) {
            self::updateUserStats(implode(',', $chapterMapping), 'copy', $libraryID);
        }

        // copy 'chapter_cache' data
        $sql = "SELECT	*
			FROM 	sls" . SLS_N . "_chapter_cache
			WHERE 	chapterID IN (" . $chapterIDs . ")";
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            $sql = "INSERT INTO 	sls" . SLS_N . "_chapter_cache
						(chapterID, storyID, textCache)
				VALUES		(" . $chapterMapping[$row['chapterID']] . ",
						" . ($storyID ? $storyID : $storyMapping[$row['storyID']]) . ",
						'" . escapeString($row['textCache']) . "')";
            WCF::getDB()->sendQuery($sql);
        }

        // copy 'chapter_report' data
        $sql = "SELECT 	*
			FROM 	sls" . SLS_N . "_chapter_report
			WHERE 	chapterID IN (" . $chapterIDs . ")";
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            $sql = "INSERT INTO 	sls" . SLS_N . "_chapter_report
						(chapterID, userID, report, reportTime)
				VALUES		(" . $chapterMapping[$row['chapterID']] . ",
						" . $row['userID'] . ",
						'" . escapeString($row['report']) . "',
						" . $row['reportTime'] . ")";
            WCF::getDB()->sendQuery($sql);
        }
    }

    /**
     * Moves all chapters with the given ids into the story with the given story id.
     */
    public static function moveAll($chapterIDs, $storyID, $libraryID, $updateUserStats = true) {
        if (empty($chapterIDs))
            return;

        // update user chapters & activity points
        if ($updateUserStats) {
            self::updateUserStats($chapterIDs, 'move', $libraryID);
        }

        $sql = "UPDATE 	sls" . SLS_N . "_chapter
			SET	storyID = " . $storyID . "
			WHERE 	chapterID IN (" . $chapterIDs . ")
				AND storyID <> " . $storyID;
        WCF::getDB()->sendQuery($sql);

        // update chapter cache
        $sql = "UPDATE 	sls" . SLS_N . "_chapter_cache
			SET	storyID = " . $storyID . "
			WHERE 	chapterID IN (" . $chapterIDs . ")
				AND storyID <> " . $storyID;
        WCF::getDB()->sendQuery($sql);

        // reset first chapter id
        self::resetFirstChapterID($chapterIDs);
    }

    /**
     * Resets first chapter id.
     *
     * @param	string		$chapterIDs
     */
    public static function resetFirstChapterID($chapterIDs) {
        $sql = "UPDATE 	sls" . SLS_N . "_story
			SET	firstChapterID = 0
			WHERE 	firstChapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->sendQuery($sql);
    }

    /**
     * Returns the story ids of the chapters with the given chapter ids.
     */
    public static function getStoryIDs($chapterIDs) {
        if (empty($chapterIDs))
            return '';

        $storyIDs = '';
        $sql = "SELECT 	DISTINCT storyID
			FROM 	sls" . SLS_N . "_chapter
			WHERE 	chapterID IN (" . $chapterIDs . ")";
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            if (!empty($storyIDs))
                $storyIDs .= ',';
            $storyIDs .= $row['storyID'];
        }

        return $storyIDs;
    }

    /**
     * Returns a list of ip addresses used by a author.
     *
     * @param	integer		$auhtorID
     * @param	string		$authorname
     * @param	string		$notIpAddress
     * @return	array
     */
    public static function getIpAddressByAuthor($authorID, $authorname = '', $notIpAddress = '', $limit = 10) {
        $sql = "SELECT		DISTINCT ipAddress
			FROM 		sls" . SLS_N . "_chapter
			WHERE 		authorID = " . $authorID . "
					AND ipAddress <> ''" .
                (!empty($authorname) ? " AND authorname = '" . escapeString($authorname) . "'" : '') .
                (!empty($notIpAddress) ? " AND ipAddress <> '" . escapeString($notIpAddress) . "'" : '') . "
			ORDER BY	time DESC";
        $result = WCF::getDB()->sendQuery($sql, $limit);
        $ipAddresses = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $ipAddresses[] = $row["ipAddress"];
        }

        return $ipAddresses;
    }

    /**
     * Returns a list of auhtors which have used the given ip address.
     *
     * @param	string		$ipAddress
     * @param	integer		$notAuthorID
     * @param	string		$notAuhtorname
     * @return	array
     */
    public static function getAuthorByIpAddress($ipAddress, $notAuthorID = 0, $notAuhtorname = '', $limit = 10) {
        $sql = "SELECT		DISTINCT username
			FROM 		sls" . SLS_N . "_chapter
			WHERE 		ipAddress = '" . escapeString($ipAddress) . "'" .
                ($notAuhtorID ? " AND authorID <> " . $notAuthorID : '') .
                (!empty($notAuthorname) ? " AND authorname <> '" . escapeString($notAuhtorname) . "'" : '') . "
			ORDER BY	time DESC";
        $result = WCF::getDB()->sendQuery($sql, $limit);
        $authors = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $authors[] = $row["authorname"];
        }

        return $authors;
    }

    /**
     * Deletes the data of a chapter report.
     */
    public static function removeReportData($chapterIDs) {
        if (empty($chapterIDs))
            return;

        $sql = "DELETE FROM	sls" . SLS_N . "_chapter_report
			WHERE		chapterID IN (" . $chapterIDs . ")";
        WCF::getDB()->registerShutdownUpdate($sql);
    }

    /**
     * Sends the email notification.
     */
    public function sendNotification($story = null, $library = null) {
        // get story
        if ($story === null) {
            require_once(SLS_DIR . 'lib/data/story/Story.class.php');
            $story = new Story($this->storyID);
        }

        // get library
        if ($library === null) {
            require_once(SLS_DIR . 'lib/data/library/Library.class.php');
            $library = Library::getLibrary($story->libraryID);
        }

        $sql = "	(SELECT		user.*
				FROM		sls" . SLS_N . "_story_subscription subscription
				LEFT JOIN	wcf" . WCF_N . "_user user
				ON		(user.userID = subscription.userID)
				WHERE		subscription.storyID = " . $this->storyID . "
						AND subscription.enableNotification = 1
						AND subscription.emails = 0
						AND subscription.userID <> " . $this->userID . "
						AND user.userID IS NOT NULL)
			UNION
				(SELECT		user.*
				FROM		sls" . SLS_N . "_library_subscription subscription
				LEFT JOIN	wcf" . WCF_N . "_user user
				ON		(user.userID = subscription.userID)
				WHERE		subscription.libraryID = " . $library->libraryID . "
						AND subscription.enableNotification = 1
						AND subscription.emails = 0
						AND subscription.userID <> " . $this->userID . "
						AND user.userID IS NOT NULL)";
        $result = WCF::getDB()->sendQuery($sql);
        if (WCF::getDB()->countRows($result)) {

            // get parsed text
            require_once(WCF_DIR . 'lib/data/message/bbcode/MessageParser.class.php');
            $parser = MessageParser::getInstance();
            $parser->setOutputType('text/plain');
            $parsedText = $parser->parse($this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, false);
            // truncate message
            if (!POST_NOTIFICATION_SEND_FULL_MESSAGE && StringUtil::length($parsedText) > 500)
                $parsedText = StringUtil::substring($parsedText, 0, 500) . '...';

            // send notifications
            $languages = array(0 => WCF::getLanguage(), WCF::getLanguage()->getLanguageID() => WCF::getLanguage());
            require_once(WCF_DIR . 'lib/data/mail/Mail.class.php');
            require_once(WCF_DIR . 'lib/data/user/User.class.php');
            while ($row = WCF::getDB()->fetchArray($result)) {
                $recipient = new User(null, $row);

                // get language
                if (!isset($languages[$recipient->languageID])) {
                    $languages[$recipient->languageID] = new Language($recipient->languageID);
                }

                // enable language
                $languages[$recipient->languageID]->setLocale();

                // send mail
                $data = array(
                    'PAGE_TITLE' => $languages[$recipient->languageID]->get(PAGE_TITLE),
                    'PAGE_URL' => PAGE_URL,
                    '$recipient' => $recipient->username,
                    '$author' => $this->username,
                    '$libraryTitle' => $languages[$recipient->languageID]->get($library->title),
                    '$title' => $story->title,
                    '$chapterID' => $this->chapterID,
                    '$text' => $parsedText);
                $mail = new Mail(array($recipient->username => $recipient->email),
                                $languages[$recipient->languageID]->get('sls.chapterAdd.notification.subject', array('$title' => $story->title)),
                                $languages[$recipient->languageID]->get('sls.chapterAdd.notification.mail', $data));
                $mail->send();
            }

            // enable user language
            WCF::getLanguage()->setLocale();

            // update notification count
            $sql = "UPDATE	sls" . SLS_N . "_story_subscription
				SET 	emails = emails + 1
				WHERE	storyID = " . $this->storyID . "
					AND enableNotification = 1
					AND emails = 0";
            WCF::getDB()->registerShutdownUpdate($sql);

            $sql = "UPDATE	sls" . SLS_N . "_library_subscription
				SET 	emails = emails + 1
				WHERE	libraryID = " . $library->libraryID . "
					AND enableNotification = 1
					AND emails = 0";
            WCF::getDB()->registerShutdownUpdate($sql);
        }
    }

    /**
     * Updates the user stats (user chapters, activity points & user rank).
     *
     * @param	string		$chapterIDs		changed stories
     * @param 	string		$mode			(enable|copy|move|delete)
     * @param 	integer		$destinationLibraryID
     */
    public static function updateUserStats($chapterIDs, $mode, $destinationLibraryID = 0) {
        if (empty($chapterIDs))
            return;
        require_once(SLS_DIR . 'lib/data/library/Library.class.php');

        // get destination library
        $destinationLibrary = null;
        if ($destinationLibraryID)
            $destinationLibrary = Library::getLibrary($destinationLibraryID);
        if ($mode == 'copy' && !$destinationLibrary->countUserChapters)
            return;

        // update user chapters, activity points
        $userChapters = array();
        $userActivityPoints = array();
        $sql = "SELECT		chapter.userID, story.libraryID
			FROM		sls" . SLS_N . "_chapter chapter
			LEFT JOIN	sls" . SLS_N . "_story story
			ON		(story.storyID = chapter.storyID)
			WHERE		chapter.chapterID IN (" . $chapterIDs . ")
					" . ($mode != 'enable' ? "AND chapter.everEnabled = 1" : '') . "
					AND chapter.userID <> 0
					AND chapter.chapterID <> story.firstChapterID";
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            $library = Library::getLibrary($row['libraryID']);

            switch ($mode) {
                case 'enable':
                    if ($library->countUserChapters) {
                        // chapters
                        if (!isset($userChapters[$row['userID']]))
                            $userChapters[$row['userID']] = 0;
                        $userChapters[$row['userID']]++;
                        // activity points
                        if (!isset($userActivityPoints[$row['userID']]))
                            $userActivityPoints[$row['userID']] = 0;
                        $userActivityPoints[$row['userID']] += ACTIVITY_POINTS_PER_CHAPTER;
                    }
                    break;
                case 'copy':
                    if ($destinationLibrary->countUserChapters) {
                        // chapters
                        if (!isset($userChapters[$row['userID']]))
                            $userChapters[$row['userID']] = 0;
                        $userChapters[$row['userID']]++;
                        // activity points
                        if (!isset($userActivityPoints[$row['userID']]))
                            $userActivityPoints[$row['userID']] = 0;
                        $userActivityPoints[$row['userID']] += ACTIVITY_POINTS_PER_CHPATER;
                    }
                    break;
                case 'move':
                    if ($library->countUserChapters != $destinationLibrary->countUserChapters) {
                        // chapters
                        if (!isset($userChapters[$row['userID']]))
                            $userChapters[$row['userID']] = 0;
                        $userChapters[$row['userID']] += ( $library->countUserChapters ? -1 : 1);
                        // activity points
                        if (!isset($userActivityPoints[$row['userID']]))
                            $userActivityPoints[$row['userID']] = 0;
                        $userActivityPoints[$row['userID']] += ( $library->countUserChapters ? ACTIVITY_POINTS_PER_CHAPTER * -1 : ACTIVITY_POINTS_PER_CHAPTER);
                    }
                    break;
                case 'delete':
                    if ($library->countUserChapters) {
                        // chapters
                        if (!isset($userChapters[$row['userID']]))
                            $userChapters[$row['userID']] = 0;
                        $userChapters[$row['userID']]--;
                        // activity points
                        if (!isset($userActivityPoints[$row['userID']]))
                            $userActivityPoints[$row['userID']] = 0;
                        $userActivityPoints[$row['userID']] -= ACTIVITY_POINTS_PER_CHAPTER;
                    }
                    break;
            }
        }

        // save chapters
        if (count($userChapters)) {
            require_once(SLS_DIR . 'lib/data/user/SLSUser.class.php');
            foreach ($userChapters as $userID => $chapters) {
                SLSUser::updateUserChapters($userID, $chapters);
            }
        }

        // save activity points
        if (count($userActivityPoints)) {
            require_once(WCF_DIR . 'lib/data/user/rank/UserRank.class.php');
            foreach ($userActivityPoints as $userID => $points) {
                UserRank::updateActivityPoints($points, $userID);
            }
        }
    }

}
?>
