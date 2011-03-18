
CREATE  TABLE IF NOT EXISTS `sls1_1_library_closed_category_to_admin` (
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `libraryID`) ,
  INDEX `libraryID` (`libraryID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_closed_category_to_user` (
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `isClosed` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `libraryID`) ,
  INDEX `libraryID` (`libraryID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_ignored_by_user` (
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `libraryID`) ,
  INDEX `libraryID` (`libraryID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_last_chapter` (
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `languageID` INT(10) NOT NULL DEFAULT '0' ,
  `storyID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`libraryID`, `languageID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_moderator` (
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `groupID` INT(10) NOT NULL DEFAULT '0' ,
  `canDeleteStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadDeletedStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeleteStoryCompletely` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canCloseStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEnableStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMoveStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canCopyStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMergeStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEditChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeleteChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadDeletedChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeleteChaptertCompletely` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canCloseChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEnableChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMoveChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canCopyChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMergeChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyClosedStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMarkAsDoneStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  INDEX `userID` (`userID` ASC) ,
  INDEX `groupID` (`groupID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_structure` (
  `parentID` INT(10) NOT NULL DEFAULT '0' ,
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `position` SMALLINT(5) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`parentID`, `libraryID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_subscription` (
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `enableNotification` TINYINT(1) NOT NULL DEFAULT '0' ,
  `emails` TINYINT(3) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `libraryID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_to_group` (
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `groupID` INT(10) NOT NULL DEFAULT '0' ,
  `canViewLibrary` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEnterLibrary` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadOwnStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyOwnStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartStoryWithoutModeration` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyStoryWithoutModeration` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canRateStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeleteOwnChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEditOwnChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canSetTags` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMarkAsDoneOwnStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  PRIMARY KEY (`groupID`, `libraryID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_to_user` (
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `canViewLibrary` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEnterLibrary` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadOwnStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyOwnStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartStoryWithoutModeration` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyStoryWithoutModeration` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canRateStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeleteOwnChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEditOwnChapter` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canSetTags` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMarkAsDoneOwnStory` TINYINT(1) NOT NULL DEFAULT '-1' ,
  PRIMARY KEY (`userID`, `libraryID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_visit` (
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `lastVisitTime` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`libraryID`, `userID`) ,
  INDEX `userID` (`userID` ASC, `lastVisitTime` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library` (
  `libraryID` INT(10) NOT NULL AUTO_INCREMENT ,
  `parentID` INT(10) NOT NULL DEFAULT '0' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  `allowDescriptionHtml` TINYINT(1) NOT NULL DEFAULT '0' ,
  `libraryType` TINYINT(1) NOT NULL DEFAULT '0' ,
  `image` VARCHAR(255) NOT NULL DEFAULT '' ,
  `imageNew` VARCHAR(255) NOT NULL DEFAULT '' ,
  `imageShowAsBackground` TINYINT(1) NOT NULL DEFAULT '1' ,
  `imageBackgroundRepeat` ENUM('no-repeat','repeat-y','repeat-x','repeat') NOT NULL DEFAULT 'no-repeat' ,
  `externalURL` VARCHAR(255) NOT NULL DEFAULT '' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `styleID` INT(10) NOT NULL DEFAULT '0' ,
  `enforceStyle` TINYINT(1) NOT NULL DEFAULT '0' ,
  `daysPrune` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `sortField` VARCHAR(20) NOT NULL DEFAULT '' ,
  `sortOrder` VARCHAR(4) NOT NULL DEFAULT '' ,
  `storySortOrder` VARCHAR(4) NOT NULL DEFAULT '' ,
  `isClosed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `countUserChapters` TINYINT(1) NOT NULL DEFAULT '1' ,
  `isInvisible` TINYINT(1) NOT NULL DEFAULT '0' ,
  `showSubLibraries` TINYINT(1) NOT NULL DEFAULT '1' ,
  `clicks` INT(10) NOT NULL DEFAULT '0' ,
  `stories` INT(10) NOT NULL DEFAULT '0' ,
  `chapters` INT(10) NOT NULL DEFAULT '0' ,
  `enableRating` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `storiesPerPage` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `searchable` TINYINT(1) NOT NULL DEFAULT '1' ,
  `searchableForSimilarStories` TINYINT(1) NOT NULL DEFAULT '1' ,
  `ignorable` TINYINT(1) NOT NULL DEFAULT '1' ,
  `enableMarkingAsDone` TINYINT(1) NOT NULL DEFAULT '0' ,
  `genres` MEDIUMTEXT NULL DEFAULT '0' ,
  `genreRequired` TINYINT(4) NOT NULL DEFAULT '0' ,
  `genreMode` TINYINT(4) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`libraryID`) )
ENGINE = MyISAM
AUTO_INCREMENT = 143
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_import_mapping` (
  `idType` VARCHAR(75) NOT NULL DEFAULT '' ,
  `oldID` VARCHAR(255) NOT NULL DEFAULT '' ,
  `newID` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  UNIQUE INDEX `idType` (`idType` ASC, `oldID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_import_source` (
  `sourceName` VARCHAR(255) NOT NULL ,
  `packageID` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `classPath` VARCHAR(255) NOT NULL ,
  `templateName` VARCHAR(255) NOT NULL DEFAULT '' ,
  UNIQUE INDEX `sourceName` (`sourceName` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_chapter_cache` (
  `chapterID` INT(10) NOT NULL DEFAULT '0' ,
  `storyID` INT(10) NOT NULL DEFAULT '0' ,
  `textCache` MEDIUMTEXT NOT NULL ,
  PRIMARY KEY (`chapterID`) ,
  INDEX `storyid` (`storyID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_chapter_hash` (
  `chapterID` INT(10) NOT NULL ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `textHash` VARCHAR(40) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`textHash`) ,
  INDEX `postID` (`chapterID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_chapter_report` (
  `reportID` INT(10) NOT NULL AUTO_INCREMENT ,
  `chapterID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `report` MEDIUMTEXT NOT NULL ,
  `reportTime` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`reportID`) ,
  UNIQUE INDEX `postID` (`chapterID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 26
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_chapter` (
  `chapterID` INT(10) NOT NULL AUTO_INCREMENT ,
  `storyID` INT(10) NOT NULL DEFAULT '0' ,
  `parentChapterID` INT(11) NOT NULL DEFAULT '0' ,
  `authorID` INT(10) NOT NULL DEFAULT '0' ,
  `authorname` VARCHAR(255) NOT NULL DEFAULT '' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `text` MEDIUMTEXT NOT NULL ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `isDeleted` TINYINT(1) NOT NULL DEFAULT '0' ,
  `everEnabled` TINYINT(1) NOT NULL DEFAULT '1' ,
  `isDisabled` TINYINT(1) NOT NULL DEFAULT '0' ,
  `isClosed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `editor` VARCHAR(255) NOT NULL DEFAULT '' ,
  `editorID` INT(10) NOT NULL DEFAULT '0' ,
  `lastEditTime` INT(10) NOT NULL DEFAULT '0' ,
  `editCount` MEDIUMINT(7) NOT NULL DEFAULT '0' ,
  `editReason` TEXT NULL DEFAULT NULL ,
  `deleteTime` INT(10) NOT NULL DEFAULT '0' ,
  `deletedBy` VARCHAR(255) NOT NULL DEFAULT '' ,
  `deletedByID` INT(10) NOT NULL DEFAULT '0' ,
  `deleteReason` TEXT NULL DEFAULT NULL ,
  `enableHtml` TINYINT(1) NOT NULL DEFAULT '0' ,
  `enableBBCodes` TINYINT(1) NOT NULL DEFAULT '1' ,
  `ipAddress` VARCHAR(15) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`chapterID`) ,
  INDEX `storyID` (`storyID` ASC, `authorID` ASC) ,
  INDEX `storyID_2` (`storyID` ASC, `isDeleted` ASC, `isDisabled` ASC, `time` ASC) ,
  INDEX `authorID` (`authorID` ASC) ,
  INDEX `isDeleted` (`isDeleted` ASC) ,
  INDEX `isDisabled` (`isDisabled` ASC) ,
  INDEX `ipAddress` (`ipAddress` ASC) ,
  INDEX `parentChaptertID` (`parentChapterID` ASC) ,
  INDEX `writet_time` (`time` ASC, `chapterID` ASC) ,
  FULLTEXT INDEX `subject` (`title` ASC, `text` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 42305
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_thread_announcement` (
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `threadID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`boardID`, `threadID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_thread_rating` (
  `threadID` INT(10) NOT NULL DEFAULT '0' ,
  `rating` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `ipAddress` VARCHAR(15) NOT NULL DEFAULT '' ,
  INDEX `threadID` (`threadID` ASC, `userID` ASC) ,
  INDEX `threadID_2` (`threadID` ASC, `ipAddress` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_thread_similar` (
  `threadID` INT(10) NOT NULL DEFAULT '0' ,
  `similarThreadID` INT(10) NOT NULL DEFAULT '0' ,
  UNIQUE INDEX `threadID` (`threadID` ASC, `similarThreadID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_thread_subscription` (
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `threadID` INT(10) NOT NULL DEFAULT '0' ,
  `enableNotification` TINYINT(1) NOT NULL DEFAULT '0' ,
  `emails` TINYINT(3) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `threadID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_story_visit` (
  `storyID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `lastVisitTime` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`storyID`, `userID`) ,
  INDEX `userID` (`userID` ASC, `lastVisitTime` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_story` (
  `storyID` INT(10) NOT NULL AUTO_INCREMENT ,
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `languageID` INT(10) NOT NULL DEFAULT '0' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `firstChapterID` INT(10) NOT NULL DEFAULT '0' ,
  `firstChapterPreview` TEXT NULL DEFAULT NULL ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `authorID` INT(10) NOT NULL DEFAULT '0' ,
  `authorrname` VARCHAR(255) NOT NULL DEFAULT '' ,
  `lastChapterTime` INT(10) NOT NULL DEFAULT '0' ,
  `lastAuthorID` INT(10) NOT NULL DEFAULT '0' ,
  `lastAuthor` VARCHAR(255) NOT NULL DEFAULT '' ,
  `chapters` MEDIUMINT(7) NOT NULL DEFAULT '0' ,
  `views` MEDIUMINT(7) NOT NULL DEFAULT '0' ,
  `ratings` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `rating` MEDIUMINT(7) NOT NULL DEFAULT '0' ,
  `isDisabled` TINYINT(1) NOT NULL DEFAULT '0' ,
  `everEnabled` TINYINT(1) NOT NULL DEFAULT '1' ,
  `isClosed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `isDeleted` TINYINT(1) NOT NULL DEFAULT '0' ,
  `movedStoryID` INT(10) NOT NULL DEFAULT '0' ,
  `movedTime` INT(10) NOT NULL DEFAULT '0' ,
  `deleteTime` INT(10) NOT NULL DEFAULT '0' ,
  `deletedBy` VARCHAR(255) NOT NULL DEFAULT '' ,
  `deletedByID` INT(10) NOT NULL DEFAULT '0' ,
  `deleteReason` TEXT NULL DEFAULT NULL ,
  `isDone` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`storyID`) ,
  INDEX `firstChapterID` (`firstChapterID` ASC) ,
  INDEX `movedStoryID` (`movedStoryID` ASC) ,
  INDEX `lastChaptertTime` (`lastChapterTime` ASC) ,
  INDEX `languageID` (`languageID` ASC) ,
  INDEX `libraryID` (`libraryID` ASC, `lastChapterTime` ASC, `isDeleted` ASC, `isDisabled` ASC) ,
  INDEX `isDeleted` (`isDeleted` ASC) ,
  INDEX `isDisabled` (`isDisabled` ASC) ,
  INDEX `userID` (`authorID` ASC) ,
  INDEX `movedTime` (`movedTime` ASC) ,
  INDEX `views` (`views` ASC) ,
  INDEX `views_2` (`views` ASC, `libraryID` ASC) ,
  INDEX `isClosed` (`isClosed` ASC) ,
  INDEX `story_time` (`time` ASC, `storyID` ASC) ,
  INDEX `chapters` (`chapters` ASC, `libraryID` ASC) ,
  INDEX `rating` (`libraryID` ASC, `rating` ASC, `ratings` ASC) ,
  INDEX `userID_2` (`authorID` ASC, `authorrname` ASC, `storyID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 2217
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_user_last_post` (
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `postID` INT(10) NOT NULL DEFAULT '0' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  INDEX `userID` (`userID` ASC) ,
  INDEX `postID` (`postID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_user` (
  `userID` INT(10) NOT NULL AUTO_INCREMENT ,
  `boardLastVisitTime` INT(10) NOT NULL DEFAULT '0' ,
  `boardLastActivityTime` INT(10) NOT NULL DEFAULT '0' ,
  `boardLastMarkAllAsReadTime` INT(10) NOT NULL DEFAULT '0' ,
  `posts` MEDIUMINT(7) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`) ,
  INDEX `posts` (`posts` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 1154
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_closed_category_to_admin` (
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `boardID`) ,
  INDEX `boardID` (`boardID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_closed_category_to_user` (
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `isClosed` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `boardID`) ,
  INDEX `boardID` (`boardID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_ignored_by_user` (
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `boardID`) ,
  INDEX `boardID` (`boardID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_last_post` (
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `languageID` INT(10) NOT NULL DEFAULT '0' ,
  `threadID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`boardID`, `languageID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_moderator` (
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `groupID` INT(10) NOT NULL DEFAULT '0' ,
  `canDeleteThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadDeletedThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeleteThreadCompletely` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canCloseThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEnableThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMoveThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canCopyThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMergeThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEditPost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeletePost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadDeletedPost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeletePostCompletely` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canClosePost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEnablePost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMovePost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canCopyPost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMergePost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyClosedThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canPinThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartAnnouncement` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMarkAsDoneThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  INDEX `userID` (`userID` ASC) ,
  INDEX `groupID` (`groupID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_structure` (
  `parentID` INT(10) NOT NULL DEFAULT '0' ,
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `position` SMALLINT(5) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`parentID`, `boardID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_subscription` (
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `enableNotification` TINYINT(1) NOT NULL DEFAULT '0' ,
  `emails` TINYINT(3) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `boardID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_to_group` (
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `groupID` INT(10) NOT NULL DEFAULT '0' ,
  `canViewBoard` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEnterBoard` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadOwnThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyOwnThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartThreadWithoutModeration` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyThreadWithoutModeration` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartPoll` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canVotePoll` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canRateThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canUsePrefix` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canUploadAttachment` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDownloadAttachment` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canViewAttachmentPreview` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeleteOwnPost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEditOwnPost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canSetTags` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMarkAsDoneOwnThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  PRIMARY KEY (`groupID`, `boardID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_to_user` (
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `canViewBoard` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEnterBoard` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReadOwnThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyOwnThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartThreadWithoutModeration` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canReplyThreadWithoutModeration` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canStartPoll` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canVotePoll` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canRateThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canUsePrefix` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canUploadAttachment` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDownloadAttachment` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canViewAttachmentPreview` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canDeleteOwnPost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canEditOwnPost` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canSetTags` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `canMarkAsDoneOwnThread` TINYINT(1) NOT NULL DEFAULT '-1' ,
  PRIMARY KEY (`userID`, `boardID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board_visit` (
  `boardID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `lastVisitTime` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`boardID`, `userID`) ,
  INDEX `userID` (`userID` ASC, `lastVisitTime` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_board` (
  `boardID` INT(10) NOT NULL AUTO_INCREMENT ,
  `parentID` INT(10) NOT NULL DEFAULT '0' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  `allowDescriptionHtml` TINYINT(1) NOT NULL DEFAULT '0' ,
  `boardType` TINYINT(1) NOT NULL DEFAULT '0' ,
  `image` VARCHAR(255) NOT NULL DEFAULT '' ,
  `imageNew` VARCHAR(255) NOT NULL DEFAULT '' ,
  `imageShowAsBackground` TINYINT(1) NOT NULL DEFAULT '1' ,
  `imageBackgroundRepeat` ENUM('no-repeat','repeat-y','repeat-x','repeat') NOT NULL DEFAULT 'no-repeat' ,
  `externalURL` VARCHAR(255) NOT NULL DEFAULT '' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `prefixes` MEDIUMTEXT NULL DEFAULT NULL ,
  `prefixRequired` TINYINT(1) NOT NULL DEFAULT '0' ,
  `prefixMode` TINYINT(1) NOT NULL DEFAULT '0' ,
  `styleID` INT(10) NOT NULL DEFAULT '0' ,
  `enforceStyle` TINYINT(1) NOT NULL DEFAULT '0' ,
  `daysPrune` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `sortField` VARCHAR(20) NOT NULL DEFAULT '' ,
  `sortOrder` VARCHAR(4) NOT NULL DEFAULT '' ,
  `postSortOrder` VARCHAR(4) NOT NULL DEFAULT '' ,
  `isClosed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `countUserPosts` TINYINT(1) NOT NULL DEFAULT '1' ,
  `isInvisible` TINYINT(1) NOT NULL DEFAULT '0' ,
  `showSubBoards` TINYINT(1) NOT NULL DEFAULT '1' ,
  `clicks` INT(10) NOT NULL DEFAULT '0' ,
  `threads` INT(10) NOT NULL DEFAULT '0' ,
  `posts` INT(10) NOT NULL DEFAULT '0' ,
  `enableRating` TINYINT(1) NOT NULL DEFAULT '-1' ,
  `threadsPerPage` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `postsPerPage` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `searchable` TINYINT(1) NOT NULL DEFAULT '1' ,
  `searchableForSimilarThreads` TINYINT(1) NOT NULL DEFAULT '1' ,
  `ignorable` TINYINT(1) NOT NULL DEFAULT '1' ,
  `enableMarkingAsDone` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`boardID`) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_post_cache` (
  `postID` INT(10) NOT NULL DEFAULT '0' ,
  `threadID` INT(10) NOT NULL DEFAULT '0' ,
  `messageCache` MEDIUMTEXT NOT NULL ,
  PRIMARY KEY (`postID`) ,
  INDEX `threadid` (`threadID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_post_hash` (
  `postID` INT(10) NOT NULL ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `messageHash` VARCHAR(40) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`messageHash`) ,
  INDEX `postID` (`postID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_post_report` (
  `reportID` INT(10) NOT NULL AUTO_INCREMENT ,
  `postID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `report` MEDIUMTEXT NOT NULL ,
  `reportTime` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`reportID`) ,
  UNIQUE INDEX `postID` (`postID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wbb1_1_post` (
  `postID` INT(10) NOT NULL AUTO_INCREMENT ,
  `threadID` INT(10) NOT NULL DEFAULT '0' ,
  `parentPostID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `username` VARCHAR(255) NOT NULL DEFAULT '' ,
  `subject` VARCHAR(255) NOT NULL DEFAULT '' ,
  `message` MEDIUMTEXT NOT NULL ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `isDeleted` TINYINT(1) NOT NULL DEFAULT '0' ,
  `everEnabled` TINYINT(1) NOT NULL DEFAULT '1' ,
  `isDisabled` TINYINT(1) NOT NULL DEFAULT '0' ,
  `isClosed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `editor` VARCHAR(255) NOT NULL DEFAULT '' ,
  `editorID` INT(10) NOT NULL DEFAULT '0' ,
  `lastEditTime` INT(10) NOT NULL DEFAULT '0' ,
  `editCount` MEDIUMINT(7) NOT NULL DEFAULT '0' ,
  `editReason` TEXT NULL DEFAULT NULL ,
  `deleteTime` INT(10) NOT NULL DEFAULT '0' ,
  `deletedBy` VARCHAR(255) NOT NULL DEFAULT '' ,
  `deletedByID` INT(10) NOT NULL DEFAULT '0' ,
  `deleteReason` TEXT NULL DEFAULT NULL ,
  `attachments` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `pollID` INT(10) NOT NULL DEFAULT '0' ,
  `enableSmilies` TINYINT(1) NOT NULL DEFAULT '1' ,
  `enableHtml` TINYINT(1) NOT NULL DEFAULT '0' ,
  `enableBBCodes` TINYINT(1) NOT NULL DEFAULT '1' ,
  `showSignature` TINYINT(1) NOT NULL DEFAULT '1' ,
  `ipAddress` VARCHAR(15) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`postID`) ,
  INDEX `threadID` (`threadID` ASC, `userID` ASC) ,
  INDEX `threadID_2` (`threadID` ASC, `isDeleted` ASC, `isDisabled` ASC, `time` ASC) ,
  INDEX `userID` (`userID` ASC) ,
  INDEX `isDeleted` (`isDeleted` ASC) ,
  INDEX `isDisabled` (`isDisabled` ASC) ,
  INDEX `ipAddress` (`ipAddress` ASC) ,
  INDEX `parentPostID` (`parentPostID` ASC) ,
  FULLTEXT INDEX `subject` (`subject` ASC, `message` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_acp_menu_item` (
  `menuItemID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `menuItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `parentMenuItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuItemLink` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuItemIcon` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`menuItemID`) ,
  UNIQUE INDEX `menuItem` (`menuItem` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 124
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_acp_session_access_log` (
  `sessionAccessLogID` INT(10) NOT NULL AUTO_INCREMENT ,
  `sessionLogID` INT(10) NOT NULL DEFAULT '0' ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `ipAddress` VARCHAR(39) NOT NULL DEFAULT '' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `requestURI` VARCHAR(255) NOT NULL DEFAULT '' ,
  `requestMethod` VARCHAR(4) NOT NULL DEFAULT '' ,
  `className` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`sessionAccessLogID`) ,
  INDEX `sessionLogID` (`sessionLogID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 47
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_acp_session_data` (
  `sessionID` CHAR NOT NULL DEFAULT '' ,
  `userData` MEDIUMTEXT NULL DEFAULT NULL ,
  `sessionVariables` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`sessionID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_acp_session_log` (
  `sessionLogID` INT(10) NOT NULL AUTO_INCREMENT ,
  `sessionID` CHAR NOT NULL DEFAULT '' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `ipAddress` VARCHAR(39) NOT NULL DEFAULT '' ,
  `hostname` VARCHAR(255) NOT NULL DEFAULT '' ,
  `userAgent` VARCHAR(255) NOT NULL DEFAULT '' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `lastActivityTime` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`sessionLogID`) ,
  INDEX `sessionID` (`sessionID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_acp_session` (
  `sessionID` CHAR NOT NULL DEFAULT '' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `ipAddress` VARCHAR(39) NOT NULL DEFAULT '' ,
  `userAgent` VARCHAR(255) NOT NULL DEFAULT '' ,
  `lastActivityTime` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `requestURI` VARCHAR(255) NOT NULL DEFAULT '' ,
  `requestMethod` VARCHAR(4) NOT NULL DEFAULT '' ,
  `username` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`sessionID`, `packageID`) )
ENGINE = MEMORY
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_acp_template_patch` (
  `patchID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `templateID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `success` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `fuzzFactor` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `patch` LONGTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`patchID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_acp_template` (
  `templateID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `templateName` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`templateID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `templateName` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 167
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_attachment_container_type` (
  `containerTypeID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL ,
  `containerType` VARCHAR(255) NOT NULL ,
  `isPrivate` TINYINT(1) NOT NULL DEFAULT '0' ,
  `url` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`containerTypeID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `containerType` ASC) ,
  INDEX `isPrivate` (`isPrivate` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_attachment` (
  `attachmentID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `containerID` INT(10) NOT NULL DEFAULT '0' ,
  `containerType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `attachmentName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `attachmentSize` INT(10) NOT NULL DEFAULT '0' ,
  `fileType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `isBinary` TINYINT(1) NOT NULL DEFAULT '0' ,
  `isImage` TINYINT(1) NOT NULL DEFAULT '0' ,
  `width` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `height` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `thumbnailType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `thumbnailSize` INT(10) NOT NULL DEFAULT '0' ,
  `thumbnailWidth` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `thumbnailHeight` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `downloads` INT(10) NOT NULL DEFAULT '0' ,
  `lastDownloadTime` INT(10) NOT NULL DEFAULT '0' ,
  `sha1Hash` VARCHAR(40) NOT NULL DEFAULT '' ,
  `idHash` VARCHAR(40) NOT NULL DEFAULT '' ,
  `uploadTime` INT(10) NOT NULL DEFAULT '0' ,
  `embedded` TINYINT(1) NOT NULL DEFAULT '0' ,
  `showOrder` SMALLINT(5) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`attachmentID`) ,
  INDEX `packageID` (`packageID` ASC, `containerID` ASC, `containerType` ASC) ,
  INDEX `packageID_2` (`packageID` ASC, `idHash` ASC, `containerType` ASC) ,
  INDEX `userID` (`userID` ASC, `packageID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_avatar_category` (
  `avatarCategoryID` INT(10) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` MEDIUMINT(5) NOT NULL DEFAULT '0' ,
  `groupID` INT(10) NOT NULL DEFAULT '0' ,
  `neededPoints` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`avatarCategoryID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_avatar` (
  `avatarID` INT(10) NOT NULL AUTO_INCREMENT ,
  `avatarCategoryID` INT(10) NOT NULL DEFAULT '0' ,
  `avatarName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `avatarExtension` VARCHAR(7) NOT NULL DEFAULT '' ,
  `width` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `height` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `groupID` INT(10) NOT NULL DEFAULT '0' ,
  `neededPoints` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`avatarID`) ,
  INDEX `userID` (`userID` ASC, `groupID` ASC) ,
  INDEX `avatarCategoryID` (`avatarCategoryID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_bbcode_attribute` (
  `bbcodeID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `attributeNo` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `attributeHtml` VARCHAR(255) NOT NULL DEFAULT '' ,
  `attributeText` VARCHAR(255) NOT NULL DEFAULT '' ,
  `validationPattern` VARCHAR(255) NOT NULL DEFAULT '' ,
  `required` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `useText` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`bbcodeID`, `attributeNo`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_bbcode` (
  `bbcodeID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `bbcodeTag` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `htmlOpen` VARCHAR(255) NOT NULL DEFAULT '' ,
  `htmlClose` VARCHAR(255) NOT NULL DEFAULT '' ,
  `textOpen` VARCHAR(255) NOT NULL DEFAULT '' ,
  `textClose` VARCHAR(255) NOT NULL DEFAULT '' ,
  `allowedChildren` VARCHAR(255) NOT NULL DEFAULT 'all' ,
  `className` VARCHAR(255) NOT NULL DEFAULT '' ,
  `wysiwyg` TINYINT(1) NOT NULL DEFAULT '0' ,
  `wysiwygIcon` VARCHAR(255) NOT NULL DEFAULT '' ,
  `sourceCode` TINYINT(1) NOT NULL DEFAULT '0' ,
  `disabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`bbcodeID`) ,
  UNIQUE INDEX `bbcodeTag` (`bbcodeTag` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 27
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_cache_resource` (
  `cacheResource` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`cacheResource`) )
ENGINE = MEMORY
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_captcha` (
  `captchaID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `captchaString` VARCHAR(255) NOT NULL DEFAULT '' ,
  `captchaDate` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`captchaID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_cronjobs_log` (
  `cronjobsLogID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `cronjobID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `execTime` INT(10) NOT NULL DEFAULT '0' ,
  `success` TINYINT(4) NOT NULL DEFAULT '0' ,
  `error` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`cronjobsLogID`) ,
  INDEX `cronjobID` (`cronjobID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 68
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_cronjobs` (
  `cronjobID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `classPath` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `description` VARCHAR(255) NOT NULL DEFAULT '' ,
  `startMinute` VARCHAR(255) NOT NULL DEFAULT '*' ,
  `startHour` VARCHAR(255) NOT NULL DEFAULT '*' ,
  `startDom` VARCHAR(255) NOT NULL DEFAULT '*' ,
  `startMonth` VARCHAR(255) NOT NULL DEFAULT '*' ,
  `startDow` VARCHAR(255) NOT NULL DEFAULT '*' ,
  `lastExec` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `nextExec` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `execMultiple` TINYINT(4) NOT NULL DEFAULT '0' ,
  `active` TINYINT(4) NOT NULL DEFAULT '1' ,
  `canBeEdited` TINYINT(4) NOT NULL DEFAULT '1' ,
  `canBeDisabled` TINYINT(4) NOT NULL DEFAULT '1' ,
  PRIMARY KEY (`cronjobID`) ,
  INDEX `packageID` (`packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_event_listener` (
  `listenerID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `environment` ENUM('user','admin') NOT NULL DEFAULT 'user' ,
  `eventClassName` VARCHAR(80) NOT NULL DEFAULT '' ,
  `eventName` VARCHAR(50) NOT NULL DEFAULT '' ,
  `listenerClassFile` VARCHAR(200) NOT NULL DEFAULT '' ,
  `inherit` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `niceValue` TINYINT(3) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`listenerID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `environment` ASC, `eventClassName` ASC, `eventName` ASC, `listenerClassFile` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 113
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_feed_entry` (
  `entryID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `sourceID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `author` VARCHAR(255) NOT NULL DEFAULT '' ,
  `link` VARCHAR(255) NOT NULL DEFAULT '' ,
  `guid` VARCHAR(255) NOT NULL DEFAULT '' ,
  `pubDate` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`entryID`) ,
  UNIQUE INDEX `sourceID` (`sourceID` ASC, `guid` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 61
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_feed_source` (
  `sourceID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `sourceName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `sourceURL` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `lastUpdate` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `updateCycle` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`sourceID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `sourceName` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_group_application` (
  `applicationID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `userID` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `groupID` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  `applicationTime` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `reason` TEXT NULL DEFAULT NULL ,
  `reply` TEXT NULL DEFAULT NULL ,
  `applicationStatus` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `enableNotification` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `groupLeaderID` INT(11) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`applicationID`) ,
  UNIQUE INDEX `userID` (`userID` ASC, `groupID` ASC) ,
  INDEX `groupID` (`groupID` ASC, `applicationStatus` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_group_leader` (
  `groupID` INT(11) NOT NULL DEFAULT '0' ,
  `leaderUserID` INT(11) NOT NULL DEFAULT '0' ,
  `leaderGroupID` INT(11) NOT NULL DEFAULT '0' ,
  INDEX `groupID` (`groupID` ASC) ,
  INDEX `leaderUserID` (`leaderUserID` ASC, `groupID` ASC) ,
  INDEX `leaderGroupID` (`leaderGroupID` ASC, `groupID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_group_option_category` (
  `categoryID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `categoryName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `parentCategoryName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`categoryID`) ,
  UNIQUE INDEX `categoryName` (`categoryName` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 42
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_group_option_value` (
  `groupID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `optionID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `optionValue` MEDIUMTEXT NOT NULL ,
  PRIMARY KEY (`groupID`, `optionID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_group_option` (
  `optionID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `optionName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `categoryName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `optionType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `defaultValue` MEDIUMTEXT NULL DEFAULT NULL ,
  `validationPattern` TEXT NULL DEFAULT NULL ,
  `enableOptions` MEDIUMTEXT NULL DEFAULT NULL ,
  `showOrder` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  `additionalData` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`optionID`) ,
  UNIQUE INDEX `optionName` (`optionName` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 190
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_group` (
  `groupID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `groupName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `groupType` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `groupDescription` TEXT NULL DEFAULT NULL ,
  `neededAge` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `neededPoints` INT(10) NOT NULL DEFAULT '0' ,
  `userOnlineMarking` VARCHAR(255) NOT NULL DEFAULT '%s' ,
  `showOnTeamPage` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `teamPagePosition` MEDIUMINT(5) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`groupID`) )
ENGINE = MyISAM
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_help_item` (
  `helpItemID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `helpItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `parentHelpItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `refererPattern` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  `isDisabled` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`helpItemID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `helpItem` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 57
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_language_category` (
  `languageCategoryID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `languageCategory` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`languageCategoryID`) ,
  UNIQUE INDEX `languageCategory` (`languageCategory` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 95
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_language_item` (
  `languageItemID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `languageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `languageItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `languageItemValue` MEDIUMTEXT NOT NULL ,
  `languageHasCustomValue` TINYINT(1) NOT NULL DEFAULT '0' ,
  `languageCustomItemValue` MEDIUMTEXT NULL DEFAULT NULL ,
  `languageUseCustomValue` TINYINT(1) NOT NULL DEFAULT '0' ,
  `languageCategoryID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`languageItemID`) ,
  UNIQUE INDEX `languageItem` (`languageItem` ASC, `packageID` ASC, `languageID` ASC) ,
  INDEX `languageHasCustomValue` (`languageHasCustomValue` ASC) ,
  INDEX `languageID` (`languageID` ASC, `languageCategoryID` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 8476
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_language_to_packages` (
  `languageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`languageID`, `packageID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_language` (
  `languageID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `languageCode` VARCHAR(20) NOT NULL DEFAULT '' ,
  `languageEncoding` VARCHAR(20) NOT NULL DEFAULT '' ,
  `isDefault` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `hasContent` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`languageID`) ,
  UNIQUE INDEX `languageCode` (`languageCode` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_option_category` (
  `categoryID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `categoryName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `parentCategoryName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`categoryID`) ,
  UNIQUE INDEX `categoryName` (`categoryName` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 63
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_option` (
  `optionID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `optionName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `categoryName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `optionType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `optionValue` MEDIUMTEXT NULL DEFAULT NULL ,
  `validationPattern` TEXT NULL DEFAULT NULL ,
  `selectOptions` MEDIUMTEXT NULL DEFAULT NULL ,
  `enableOptions` MEDIUMTEXT NULL DEFAULT NULL ,
  `showOrder` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `hidden` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  `additionalData` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`optionID`) ,
  UNIQUE INDEX `optionName` (`optionName` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 214
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_dependency` (
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `dependency` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `priority` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`packageID`, `dependency`) ,
  INDEX `dependency` (`dependency` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_exclusion` (
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `excludedPackage` VARCHAR(255) NOT NULL DEFAULT '' ,
  `excludedPackageVersion` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`packageID`, `excludedPackage`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_installation_file_log` (
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `filename` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`packageID`, `filename`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_installation_plugin` (
  `pluginName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `priority` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`pluginName`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_installation_queue` (
  `queueID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parentQueueID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `processNo` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `package` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `archive` VARCHAR(255) NOT NULL DEFAULT '' ,
  `action` ENUM('install','update','uninstall','rollback') NOT NULL DEFAULT 'install' ,
  `cancelable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ,
  `done` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `confirmInstallation` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `packageType` ENUM('default','requirement','optional') NOT NULL DEFAULT 'default' ,
  `installationType` ENUM('install','setup','other') NOT NULL DEFAULT 'other' ,
  PRIMARY KEY (`queueID`) )
ENGINE = MyISAM
AUTO_INCREMENT = 54
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_installation_sql_log` (
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `sqlTable` VARCHAR(100) NOT NULL DEFAULT '' ,
  `sqlColumn` VARCHAR(100) NOT NULL DEFAULT '' ,
  `sqlIndex` VARCHAR(100) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`packageID`, `sqlTable`, `sqlColumn`, `sqlIndex`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_requirement_map` (
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `requirement` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `level` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`packageID`, `requirement`) ,
  INDEX `requirement` (`requirement` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_requirement` (
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `requirement` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`packageID`, `requirement`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_update_exclusion` (
  `packageUpdateVersionID` INT(10) NOT NULL DEFAULT '0' ,
  `excludedPackage` VARCHAR(255) NOT NULL DEFAULT '' ,
  `excludedPackageVersion` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`packageUpdateVersionID`, `excludedPackage`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_update_fromversion` (
  `packageUpdateVersionID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `fromversion` VARCHAR(50) NOT NULL DEFAULT '' ,
  UNIQUE INDEX `packageUpdateVersionID` (`packageUpdateVersionID` ASC, `fromversion` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_update_requirement` (
  `packageUpdateVersionID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `package` VARCHAR(255) NOT NULL DEFAULT '' ,
  `minversion` VARCHAR(50) NOT NULL DEFAULT '' ,
  UNIQUE INDEX `packageUpdateVersionID` (`packageUpdateVersionID` ASC, `package` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_update_server` (
  `packageUpdateServerID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `server` VARCHAR(255) NOT NULL DEFAULT '' ,
  `status` VARCHAR(10) NOT NULL DEFAULT '' ,
  `statusUpdate` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ,
  `errorText` TEXT NULL DEFAULT NULL ,
  `updatesFile` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `htUsername` VARCHAR(50) NOT NULL DEFAULT '' ,
  `htPassword` VARCHAR(40) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`packageUpdateServerID`) )
ENGINE = MyISAM
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_update_version` (
  `packageUpdateVersionID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageUpdateID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `packageVersion` VARCHAR(50) NOT NULL DEFAULT '' ,
  `updateType` VARCHAR(10) NOT NULL DEFAULT '' ,
  `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `file` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`packageUpdateVersionID`) ,
  UNIQUE INDEX `packageUpdateID` (`packageUpdateID` ASC, `packageVersion` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 2673
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package_update` (
  `packageUpdateID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageUpdateServerID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `package` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageDescription` VARCHAR(255) NOT NULL DEFAULT '' ,
  `author` VARCHAR(255) NOT NULL DEFAULT '' ,
  `authorURL` VARCHAR(255) NOT NULL DEFAULT '' ,
  `standalone` TINYINT(1) NOT NULL DEFAULT '0' ,
  `plugin` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`packageUpdateID`) ,
  UNIQUE INDEX `packageUpdateServerID` (`packageUpdateServerID` ASC, `package` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 926
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_package` (
  `packageID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `package` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageDir` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `instanceName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `instanceNo` INT(10) UNSIGNED NOT NULL DEFAULT '1' ,
  `packageDescription` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageVersion` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageDate` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `installDate` INT(10) NOT NULL DEFAULT '0' ,
  `updateDate` INT(10) NOT NULL DEFAULT '0' ,
  `packageURL` VARCHAR(255) NOT NULL DEFAULT '' ,
  `parentPackageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `isUnique` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `standalone` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `author` VARCHAR(255) NOT NULL DEFAULT '' ,
  `authorURL` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`packageID`) ,
  INDEX `package` (`package` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 52
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_page_location` (
  `locationID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `locationPattern` VARCHAR(255) NOT NULL DEFAULT '' ,
  `locationName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `classPath` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`locationID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `locationName` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 35
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_page_menu_item` (
  `menuItemID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `menuItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuItemLink` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuItemIconS` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuItemIconM` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuPosition` ENUM('header','footer') NOT NULL DEFAULT 'header' ,
  `showOrder` INT(10) NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  `isDisabled` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`menuItemID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `menuItem` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_pm_folder` (
  `folderID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `folderName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `color` ENUM('yellow','red','blue','green','white') NOT NULL DEFAULT 'yellow' ,
  PRIMARY KEY (`folderID`) ,
  INDEX `userID` (`userID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_pm_hash` (
  `pmID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `time` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `messageHash` VARCHAR(40) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`messageHash`) ,
  INDEX `pmID` (`pmID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_pm_rule_action` (
  `ruleActionID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `ruleAction` VARCHAR(255) NOT NULL ,
  `ruleActionClassFile` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`ruleActionID`) ,
  UNIQUE INDEX `ruleAction` (`ruleAction` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_pm_rule_condition_type` (
  `ruleConditionTypeID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `ruleConditionType` VARCHAR(255) NOT NULL ,
  `ruleConditionTypeClassFile` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`ruleConditionTypeID`) ,
  UNIQUE INDEX `ruleConditionType` (`ruleConditionType` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_pm_rule_condition` (
  `ruleConditionID` INT(10) NOT NULL AUTO_INCREMENT ,
  `ruleID` INT(10) NOT NULL ,
  `ruleConditionType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `ruleCondition` VARCHAR(255) NOT NULL DEFAULT '' ,
  `ruleConditionValue` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`ruleConditionID`) ,
  INDEX `ruleID` (`ruleID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_pm_rule` (
  `ruleID` INT(10) NOT NULL AUTO_INCREMENT ,
  `userID` INT(10) NOT NULL ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `logicalOperator` ENUM('or','and','nor') NOT NULL DEFAULT 'or' ,
  `ruleAction` VARCHAR(255) NOT NULL ,
  `ruleDestination` VARCHAR(255) NOT NULL DEFAULT '' ,
  `disabled` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`ruleID`) ,
  INDEX `userID` (`userID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_pm_to_user` (
  `pmID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `recipientID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `recipient` VARCHAR(255) NOT NULL DEFAULT '' ,
  `folderID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `isDeleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `isViewed` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `isReplied` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `isForwarded` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `isBlindCopy` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userWasNotified` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`pmID`, `recipientID`) ,
  INDEX `recipientID` (`recipientID` ASC, `isDeleted` ASC, `folderID` ASC) ,
  INDEX `pmID` (`pmID` ASC, `isBlindCopy` ASC, `recipient` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_pm` (
  `pmID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parentPmID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `username` VARCHAR(255) NOT NULL DEFAULT '' ,
  `subject` VARCHAR(255) NOT NULL DEFAULT '' ,
  `message` MEDIUMTEXT NOT NULL ,
  `time` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `attachments` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `enableSmilies` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ,
  `enableHtml` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `enableBBCodes` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ,
  `showSignature` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `saveInOutbox` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `isDraft` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `isViewedByAll` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`pmID`) ,
  INDEX `userID` (`userID` ASC, `saveInOutbox` ASC, `pmID` ASC) ,
  INDEX `userID_2` (`userID` ASC, `isDraft` ASC) ,
  INDEX `parentPmID` (`parentPmID` ASC) ,
  FULLTEXT INDEX `subject` (`subject` ASC, `message` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_poll_option_vote` (
  `pollID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `pollOptionID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `ipAddress` VARCHAR(15) NOT NULL DEFAULT '' ,
  INDEX `pollID` (`pollID` ASC, `userID` ASC) ,
  INDEX `pollID_2` (`pollID` ASC, `ipAddress` ASC) ,
  INDEX `pollOptionID` (`pollOptionID` ASC, `userID` ASC) ,
  INDEX `pollOptionID_2` (`pollOptionID` ASC, `ipAddress` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_poll_option` (
  `pollOptionID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `pollID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `pollOption` VARCHAR(255) NOT NULL DEFAULT '' ,
  `votes` MEDIUMINT(7) UNSIGNED NOT NULL DEFAULT '0' ,
  `showOrder` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`pollOptionID`) ,
  INDEX `pollID` (`pollID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_poll_vote` (
  `pollID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `isChangeable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ,
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `ipAddress` VARCHAR(15) NOT NULL DEFAULT '' ,
  INDEX `pollID` (`pollID` ASC, `userID` ASC) ,
  INDEX `pollID_2` (`pollID` ASC, `ipAddress` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_poll` (
  `pollID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `messageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `messageType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `question` VARCHAR(255) NOT NULL DEFAULT '' ,
  `time` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `endTime` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `choiceCount` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' ,
  `votes` MEDIUMINT(7) UNSIGNED NOT NULL DEFAULT '0' ,
  `votesNotChangeable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `sortByResult` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `isPublic` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`pollID`) ,
  INDEX `messageID` (`messageID` ASC, `messageType` ASC, `packageID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_searchable_message_type` (
  `typeID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `typeName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `classPath` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`typeID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `typeName` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_search` (
  `searchID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `searchData` MEDIUMTEXT NOT NULL ,
  `searchDate` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `searchType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `searchHash` CHAR NOT NULL DEFAULT '' ,
  PRIMARY KEY (`searchID`) ,
  INDEX `searchHash` (`searchHash` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_session_data` (
  `sessionID` CHAR NOT NULL DEFAULT '' ,
  `userData` MEDIUMTEXT NULL DEFAULT NULL ,
  `sessionVariables` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`sessionID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_session` (
  `sessionID` CHAR NOT NULL DEFAULT '' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `ipAddress` VARCHAR(39) NOT NULL DEFAULT '' ,
  `userAgent` VARCHAR(255) NOT NULL DEFAULT '' ,
  `lastActivityTime` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `requestURI` VARCHAR(255) NOT NULL DEFAULT '' ,
  `requestMethod` VARCHAR(4) NOT NULL DEFAULT '' ,
  `username` VARCHAR(255) NOT NULL DEFAULT '' ,
  `spiderID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `boardID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `libraryID` INT(10) NOT NULL DEFAULT '0' ,
  `storyID` INT(10) NOT NULL DEFAULT '0' ,
  `threadID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`sessionID`) ,
  INDEX `packageID` (`packageID` ASC, `lastActivityTime` ASC, `spiderID` ASC) ,
  INDEX `userID` (`userID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_smiley_category` (
  `smileyCategoryID` INT(10) NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` MEDIUMINT(5) NOT NULL DEFAULT '0' ,
  `disabled` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`smileyCategoryID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_smiley` (
  `smileyID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `smileyCategoryID` INT(10) NOT NULL DEFAULT '0' ,
  `smileyPath` VARCHAR(255) NOT NULL DEFAULT '' ,
  `smileyTitle` VARCHAR(255) NOT NULL DEFAULT '' ,
  `smileyCode` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` MEDIUMINT(5) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`smileyID`) ,
  INDEX `smileyCategoryID` (`smileyCategoryID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 29
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_spider` (
  `spiderID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `spiderIdentifier` VARCHAR(255) NULL DEFAULT '' ,
  `spiderName` VARCHAR(255) NULL DEFAULT '' ,
  `spiderURL` VARCHAR(255) NULL DEFAULT '' ,
  PRIMARY KEY (`spiderID`) ,
  UNIQUE INDEX `spiderIdentifier` (`spiderIdentifier` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 395
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_stat_type` (
  `statTypeID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL ,
  `typeName` VARCHAR(255) NOT NULL ,
  `tableName` VARCHAR(255) NOT NULL ,
  `dateFieldName` VARCHAR(255) NOT NULL ,
  `userFieldName` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`statTypeID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `typeName` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_style_to_package` (
  `styleID` INT(10) NOT NULL ,
  `packageID` INT(10) NOT NULL ,
  `isDefault` TINYINT(1) NOT NULL DEFAULT '0' ,
  `disabled` TINYINT(1) NOT NULL DEFAULT '0' ,
  UNIQUE INDEX `styleID` (`styleID` ASC, `packageID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_style_variable_to_attribute` (
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `cssSelector` VARCHAR(200) NOT NULL DEFAULT '' ,
  `attributeName` VARCHAR(50) NOT NULL DEFAULT '' ,
  `variableName` VARCHAR(50) NOT NULL DEFAULT '' ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `cssSelector` ASC, `attributeName` ASC, `variableName` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_style_variable` (
  `styleID` INT(10) NOT NULL AUTO_INCREMENT ,
  `variableName` VARCHAR(50) NOT NULL DEFAULT '' ,
  `variableValue` MEDIUMTEXT NULL DEFAULT NULL ,
  UNIQUE INDEX `styleID` (`styleID` ASC, `variableName` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_style` (
  `styleID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `styleName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `templatePackID` INT(10) NOT NULL DEFAULT '0' ,
  `isDefault` TINYINT(1) NOT NULL DEFAULT '0' ,
  `disabled` TINYINT(1) NOT NULL DEFAULT '0' ,
  `styleDescription` TEXT NULL DEFAULT NULL ,
  `styleVersion` VARCHAR(255) NOT NULL DEFAULT '' ,
  `styleDate` CHAR NOT NULL DEFAULT '0000-00-00' ,
  `image` VARCHAR(255) NOT NULL DEFAULT '' ,
  `copyright` VARCHAR(255) NOT NULL DEFAULT '' ,
  `license` VARCHAR(255) NOT NULL DEFAULT '' ,
  `authorName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `authorURL` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`styleID`) )
ENGINE = MyISAM
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_tag_taggable` (
  `taggableID` INT(10) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `classPath` VARCHAR(255) NOT NULL DEFAULT '' ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`taggableID`) ,
  UNIQUE INDEX `name` (`name` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_tag_to_object` (
  `objectID` INT(10) NOT NULL DEFAULT '0' ,
  `tagID` INT(10) NOT NULL DEFAULT '0' ,
  `taggableID` INT(10) NOT NULL DEFAULT '0' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `languageID` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`taggableID`, `languageID`, `objectID`, `tagID`) ,
  INDEX `taggableID` (`taggableID` ASC, `languageID` ASC, `tagID` ASC) ,
  INDEX `tagID` (`tagID` ASC, `taggableID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_tag` (
  `tagID` INT(10) NOT NULL AUTO_INCREMENT ,
  `languageID` INT(10) NOT NULL DEFAULT '0' ,
  `name` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`tagID`) ,
  UNIQUE INDEX `languageID` (`languageID` ASC, `name` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_template_pack` (
  `templatePackID` INT(10) NOT NULL AUTO_INCREMENT ,
  `parentTemplatePackID` INT(10) NOT NULL DEFAULT '0' ,
  `templatePackName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `templatePackFolderName` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`templatePackID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_template_patch` (
  `patchID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `templateID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `success` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `fuzzFactor` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `patch` LONGTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`patchID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_template` (
  `templateID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `templateName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `templatePackID` INT(10) NOT NULL DEFAULT '0' ,
  `obsolete` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`templateID`) ,
  INDEX `packageID` (`packageID` ASC, `templateName` ASC) ,
  INDEX `packageID_2` (`packageID` ASC, `templatePackID` ASC, `templateName` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 135
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_usercp_menu_item` (
  `menuItemID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `menuItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `parentMenuItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuItemLink` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuItemIcon` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` INT(10) NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`menuItemID`) ,
  UNIQUE INDEX `menuItem` (`menuItem` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 28
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_activity_point` (
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `activityPoints` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  UNIQUE INDEX `userID` (`userID` ASC, `packageID` ASC) ,
  INDEX `packageID` (`packageID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_blacklist` (
  `userID` INT(10) UNSIGNED NOT NULL ,
  `blackUserID` INT(10) UNSIGNED NOT NULL ,
  UNIQUE INDEX `userID` (`userID` ASC, `blackUserID` ASC) ,
  INDEX `blackUserID` (`blackUserID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_failed_login` (
  `failedLoginID` INT(10) NOT NULL AUTO_INCREMENT ,
  `environment` ENUM('user','admin') NOT NULL DEFAULT 'user' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `username` VARCHAR(255) NOT NULL DEFAULT '' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `ipAddress` VARCHAR(15) NOT NULL DEFAULT '' ,
  `userAgent` VARCHAR(255) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`failedLoginID`) ,
  INDEX `userID` (`userID` ASC) ,
  INDEX `ipAddress` (`ipAddress` ASC) ,
  INDEX `time` (`time` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_infraction_suspension_to_user` (
  `userSuspensionID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `suspensionID` INT(10) NOT NULL DEFAULT '0' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `expires` INT(10) NOT NULL DEFAULT '0' ,
  `revoked` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userSuspensionID`) ,
  INDEX `packageID` (`packageID` ASC) ,
  INDEX `userID` (`userID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_infraction_suspension_type` (
  `suspensionTypeID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `suspensionType` VARCHAR(255) NOT NULL ,
  `classFile` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`suspensionTypeID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `suspensionType` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_infraction_suspension` (
  `suspensionID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `points` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `expires` INT(10) NOT NULL DEFAULT '0' ,
  `suspensionType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `suspensionData` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`suspensionID`) ,
  INDEX `packageID` (`packageID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_infraction_warning_object_type` (
  `objectTypeID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL ,
  `objectType` VARCHAR(255) NOT NULL ,
  `classFile` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`objectTypeID`) ,
  UNIQUE INDEX `packageID` (`packageID` ASC, `objectType` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_infraction_warning_to_user` (
  `userWarningID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `objectID` INT(10) NOT NULL DEFAULT '0' ,
  `objectType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `judgeID` INT(10) NOT NULL DEFAULT '0' ,
  `warningID` INT(10) NOT NULL DEFAULT '0' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `points` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `expires` INT(10) NOT NULL DEFAULT '0' ,
  `reason` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`userWarningID`) ,
  INDEX `userID` (`userID` ASC) ,
  INDEX `judgeID` (`judgeID` ASC) ,
  INDEX `warningID` (`warningID` ASC) ,
  INDEX `packageID` (`packageID` ASC, `objectID` ASC, `objectType` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_infraction_warning` (
  `warningID` INT(10) NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) NOT NULL DEFAULT '0' ,
  `title` VARCHAR(255) NOT NULL DEFAULT '' ,
  `points` SMALLINT(5) NOT NULL DEFAULT '0' ,
  `expires` INT(10) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`warningID`) ,
  INDEX `packageID` (`packageID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_option_category` (
  `categoryID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `categoryName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `categoryIconS` VARCHAR(255) NOT NULL DEFAULT '' ,
  `categoryIconM` VARCHAR(255) NOT NULL DEFAULT '' ,
  `parentCategoryName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`categoryID`) ,
  UNIQUE INDEX `categoryName` (`categoryName` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 23
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_option_value` (
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption1` TEXT NULL DEFAULT NULL ,
  `userOption2` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption3` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption4` TEXT NULL DEFAULT NULL ,
  `userOption5` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption6` TEXT NULL DEFAULT NULL ,
  `userOption7` CHAR NOT NULL DEFAULT '0000-00-00' ,
  `userOption8` TEXT NULL DEFAULT NULL ,
  `userOption9` TEXT NULL DEFAULT NULL ,
  `userOption10` TEXT NULL DEFAULT NULL ,
  `userOption11` TEXT NULL DEFAULT NULL ,
  `userOption12` MEDIUMTEXT NULL DEFAULT NULL ,
  `userOption13` TEXT NULL DEFAULT NULL ,
  `userOption14` TEXT NULL DEFAULT NULL ,
  `userOption15` TEXT NULL DEFAULT NULL ,
  `userOption16` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption17` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption18` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption19` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption20` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption21` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption22` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption23` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption24` TEXT NULL DEFAULT NULL ,
  `userOption25` TEXT NULL DEFAULT NULL ,
  `userOption26` TEXT NULL DEFAULT NULL ,
  `userOption27` TEXT NULL DEFAULT NULL ,
  `userOption28` TEXT NULL DEFAULT NULL ,
  `userOption29` TEXT NULL DEFAULT NULL ,
  `userOption30` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption31` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption32` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption33` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption34` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption35` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption36` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption37` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption38` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption39` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption40` TEXT NULL DEFAULT NULL ,
  `userOption41` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption42` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption43` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption44` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption45` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOption46` TEXT NULL DEFAULT NULL ,
  `userOption47` TEXT NULL DEFAULT NULL ,
  `userOption48` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`userID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_option` (
  `optionID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `optionName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `categoryName` VARCHAR(255) NOT NULL DEFAULT '' ,
  `optionType` VARCHAR(255) NOT NULL DEFAULT '' ,
  `defaultValue` MEDIUMTEXT NULL DEFAULT NULL ,
  `validationPattern` TEXT NULL DEFAULT NULL ,
  `selectOptions` MEDIUMTEXT NULL DEFAULT NULL ,
  `enableOptions` MEDIUMTEXT NULL DEFAULT NULL ,
  `required` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `askDuringRegistration` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `editable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `visible` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `outputClass` VARCHAR(255) NOT NULL DEFAULT '' ,
  `searchable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `showOrder` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `disabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  `additionalData` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`optionID`) ,
  UNIQUE INDEX `optionName` (`optionName` ASC, `packageID` ASC) ,
  INDEX `categoryName` (`categoryName` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 49
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_profile_menu_item` (
  `menuItemID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `packageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `menuItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `parentMenuItem` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuItemLink` VARCHAR(255) NOT NULL DEFAULT '' ,
  `menuItemIcon` VARCHAR(255) NOT NULL DEFAULT '' ,
  `showOrder` INT(10) NOT NULL DEFAULT '0' ,
  `permissions` TEXT NULL DEFAULT NULL ,
  `options` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`menuItemID`) ,
  UNIQUE INDEX `menuItem` (`menuItem` ASC, `packageID` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_profile_visitor` (
  `ownerID` INT(10) NOT NULL ,
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  UNIQUE INDEX `ownerID` (`ownerID` ASC, `userID` ASC) ,
  INDEX `time` (`time` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_rank` (
  `rankID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `groupID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `neededPoints` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `rankTitle` VARCHAR(255) NOT NULL DEFAULT '' ,
  `rankImage` VARCHAR(255) NOT NULL DEFAULT '' ,
  `repeatImage` TINYINT(3) NOT NULL DEFAULT '1' ,
  `gender` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`rankID`) )
ENGINE = MyISAM
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_to_groups` (
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `groupID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `groupID`) ,
  INDEX `groupID` (`groupID` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_to_languages` (
  `userID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `languageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `languageID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user_whitelist` (
  `userID` INT(10) UNSIGNED NOT NULL ,
  `whiteUserID` INT(10) UNSIGNED NOT NULL ,
  `confirmed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `notified` TINYINT(1) NOT NULL DEFAULT '0' ,
  `time` INT(10) NOT NULL DEFAULT '0' ,
  UNIQUE INDEX `userID` (`userID` ASC, `whiteUserID` ASC) ,
  INDEX `whiteUserID` (`whiteUserID` ASC, `confirmed` ASC) ,
  INDEX `userID_2` (`userID` ASC, `confirmed` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `wcf1_user` (
  `userID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) NOT NULL DEFAULT '' ,
  `email` VARCHAR(255) NOT NULL DEFAULT '' ,
  `password` VARCHAR(40) NOT NULL DEFAULT '' ,
  `salt` VARCHAR(40) NOT NULL DEFAULT '' ,
  `languageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `registrationDate` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `styleID` INT(10) NOT NULL DEFAULT '0' ,
  `activationCode` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `registrationIpAddress` VARCHAR(15) NOT NULL DEFAULT '' ,
  `lastLostPasswordRequest` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `lostPasswordKey` VARCHAR(40) NOT NULL DEFAULT '' ,
  `newEmail` VARCHAR(255) NOT NULL DEFAULT '' ,
  `reactivationCode` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `oldUsername` VARCHAR(255) NOT NULL DEFAULT '' ,
  `lastUsernameChange` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `quitStarted` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `banned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `banReason` MEDIUMTEXT NULL DEFAULT NULL ,
  `rankID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `userTitle` VARCHAR(255) NOT NULL DEFAULT '' ,
  `activityPoints` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `avatarID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `gravatar` VARCHAR(255) NOT NULL DEFAULT '' ,
  `disableAvatar` TINYINT(1) NOT NULL DEFAULT '0' ,
  `disableAvatarReason` TEXT NULL DEFAULT NULL ,
  `lastActivityTime` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `profileHits` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `signature` TEXT NULL DEFAULT NULL ,
  `signatureCache` TEXT NULL DEFAULT NULL ,
  `enableSignatureSmilies` TINYINT(1) NOT NULL DEFAULT '1' ,
  `enableSignatureHtml` TINYINT(1) NOT NULL DEFAULT '0' ,
  `enableSignatureBBCodes` TINYINT(1) NOT NULL DEFAULT '1' ,
  `disableSignature` TINYINT(1) NOT NULL DEFAULT '0' ,
  `disableSignatureReason` TEXT NULL DEFAULT NULL ,
  `pmTotalCount` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `pmUnreadCount` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `pmOutstandingNotifications` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `userOnlineGroupID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`) ,
  INDEX `username` (`username` ASC) ,
  INDEX `registrationDate` (`registrationDate` ASC) ,
  INDEX `styleID` (`styleID` ASC) ,
  INDEX `activationCode` (`activationCode` ASC) ,
  INDEX `registrationIpAddress` (`registrationIpAddress` ASC, `registrationDate` ASC) ,
  INDEX `activityPoints` (`activityPoints` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_user` (
  `userID` INT(10) NOT NULL AUTO_INCREMENT ,
  `libraryLastVisitTime` INT(10) NOT NULL DEFAULT '0' ,
  `libraryLastActivityTime` INT(10) NOT NULL DEFAULT '0' ,
  `libraryLastMarkAllAsReadTime` INT(10) NOT NULL DEFAULT '0' ,
  `chapters` MEDIUMINT(7) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`) ,
  INDEX `posts` (`chapters` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 1154
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_story_subscription` (
  `userID` INT(10) NOT NULL DEFAULT '0' ,
  `storyID` INT(10) NOT NULL DEFAULT '0' ,
  `enableNotification` TINYINT(1) NOT NULL DEFAULT '0' ,
  `emails` TINYINT(3) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`userID`, `storyID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_classification` (
  `libraryID` INT(11) NOT NULL ,
  `classificationID` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`libraryID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_warning` (
  `warningID` INT(11) NOT NULL ,
  `warning` VARCHAR(256) NULL DEFAULT NULL ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`warningID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_warining_to_Library` (
  `libraryID` INT(11) NOT NULL ,
  `waringID` INT(11) NOT NULL ,
  PRIMARY KEY (`libraryID`, `waringID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_classification` (
  `classificationID` INT(11) NOT NULL ,
  `classification` VARCHAR(256) NULL DEFAULT NULL ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`classificationID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_character` (
  `characterID` INT(11) NOT NULL ,
  `character` VARCHAR(256) NULL DEFAULT NULL ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`characterID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_character` (
  `libraryID` INT(11) NOT NULL ,
  `characterID` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`libraryID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_genre` (
  `genreID` INT(11) NOT NULL ,
  `genre` VARCHAR(256) NULL DEFAULT NULL ,
  `description` MEDIUMTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`genreID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sls1_1_library_genre` (
  `libraryID` INT(11) NOT NULL ,
  `genreID` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`libraryID`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

