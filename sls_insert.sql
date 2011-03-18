INSERT INTO `wcf1_group_option` (`packageID`, `optionName`, `categoryName`, `optionType`, `defaultValue`, `validationPattern`, `enableOptions`, `showOrder`, `permissions`, `options`, `additionalData`) VALUES
( 50, 'user.library.canViewLibrary', 'user.library', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.story.canDeleteStory', 'mod.library.story', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.story.canDeleteStoryCompletely', 'mod.library.story', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.story.canCloseStory', 'mod.library.story', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.story.canEnableStory', 'mod.library.story', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.story.canMoveStory', 'mod.library.story', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.story.canCopyStory', 'mod.library.story', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.story.canMergeStory', 'mod.library.story', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.chapter.canReadDeletedChapter', 'mod.library.chapter', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.chapter.canDeleteChapter', 'mod.library.chapter', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.chapter.canDeleteChapterCompletely', 'mod.library.chapter', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.chapter.canCloseChapter', 'mod.library.chapter', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.chapter.canEnableChapter', 'mod.library.chapter', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.chapter.canMoveChapter', 'mod.library.chapter', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.chapter.canCopyChapter', 'mod.library.chapter', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.chapter.canMergeChapter', 'mod.library.chapter', 'boolean', '1', '', '', 2, '', '', 'a:0:{}'),
( 50, 'mod.library.chapter.canReadDeletedChapter', 'mod.library.chapter', 'boolean', '1', '', '', 2, '', '', 'a:0:{}');


INSERT INTO `wcf1_group_option_category` ( `packageID`, `categoryName`, `parentCategoryName`, `showOrder`, `permissions`, `options`) VALUES
( 50, 'user.library', 'user', 1, '', ''),
( 50, 'admin.library', 'admin', 1, '', ''),
( 50, 'mod.library.story', 'mod.library', 1, '', ''),
( 50, 'mod.library.chapter', 'mod.library', 1, '', ''),
( 50, 'mod.library.comment', 'mod.library', 1, '', ''),
( 50, 'mod.library', 'mod', 1, '', '');

INSERT INTO `wcf1_group_option_value` (`groupID`, `optionID`, `optionValue`) VALUES
(1, 1, '0'),
(2, 1, '0'),
(3, 1, '0'),
(4, 1, '1'),
(5, 1, '0'),
(6, 1, '0');