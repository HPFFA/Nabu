<?php
// sls imports
require_once(SLS_DIR.'lib/data/story/TaggedStory.class.php');
require_once(SLS_DIR.'lib/data/library/Library.class.php');

// wcf imports
require_once(WCF_DIR.'lib/data/tag/AbstractTaggableObject.class.php');

/**
 * An implementation of Taggable to support the tagging of stories.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.story
 * @category 	Story Library System
 */
class TaggableStory extends AbstractTaggableObject {
	/**
	 * @see Taggable::getObjectsByIDs()
	 */
	public function getObjectsByIDs($objectIDs, $taggedObjects) {
		$sql = "SELECT		*
			FROM		sls".SLS_N."_story
			WHERE		storyID IN (" . implode(",", $objectIDs) . ")
					AND isDeleted = 0
					AND isDisabled = 0";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$taggedObjects[] = new TaggedStory(null, $row);
		}
		return $taggedObjects;
	}

	/**
	 * @see Taggable::countObjectsByTagID()
	 */
	public function countObjectsByTagID($tagID) {
		$accessibleLibraryIDArray = Library::getAccessibleLibraryIDArray();
		if (count($accessibleLibraryIDArray) == 0) return 0;

		$sql = "SELECT		COUNT(*) AS count
			FROM		wcf".WCF_N."_tag_to_object tag_to_object
			LEFT JOIN	sls".SLS_N."_story story
			ON		(story.storyID = tag_to_object.objectID)
			WHERE 		tag_to_object.tagID = ".$tagID."
					AND tag_to_object.taggableID = ".$this->getTaggableID()."
					AND story.libraryID IN (".implode(',', $accessibleLibraryIDArray).")
					AND story.isDeleted = 0
					AND story.isDisabled = 0";
		$row = WCF::getDB()->getFirstRow($sql);
		return $row['count'];
	}

	/**
	 * @see Taggable::getObjectsByTagID()
	 */
	public function getObjectsByTagID($tagID, $limit = 0, $offset = 0) {
		$accessibleLibraryIDArray = Library::getAccessibleLibraryIDArray();
		if (count($accessibleLibraryIDArray) == 0) return array();

		$sqlStoryVisitSelect = $sqlStoryVisitJoin = $sqlSubscriptionSelect = $sqlSubscriptionJoin = $sqlOwnPostsSelect = $sqlOwnPostsJoin = '';
		if (WCF::getUser()->userID != 0) {
			$sqlStoryVisitSelect = ', story_visit.lastVisitTime';
			$sqlStoryVisitJoin = " LEFT JOIN 	sls".SLS_N."_story_visit story_visit
						ON 		(story_visit.storyID = story.storyID
								AND story_visit.userID = ".WCF::getUser()->userID.")";
			$sqlSubscriptionSelect = ', IF(story_subscription.userID IS NOT NULL, 1, 0) AS subscribed';
			$sqlSubscriptionJoin = " LEFT JOIN 	sls".SLS_N."_story_subscription story_subscription
						ON 		(story_subscription.userID = ".WCF::getUser()->userID."
								AND story_subscription.storyID = story.storyID)";

			if (BOARD_THREADS_ENABLE_OWN_POSTS) {
				$sqlOwnPostsSelect = "DISTINCT post.userID AS ownPosts,";
				$sqlOwnPostsJoin = "	LEFT JOIN	sls".SLS_N."_post post
							ON 		(post.storyID = story.storyID
									AND post.userID = ".WCF::getUser()->userID.")";
			}
		}

		$stories = array();
		$sql = "SELECT		".$sqlOwnPostsSelect."
					story.*,
					library.libraryID, library.title
					".$sqlStoryVisitSelect."
					".$sqlSubscriptionSelect."
			FROM		wcf".WCF_N."_tag_to_object tag_to_object
			LEFT JOIN	sls".SLS_N."_story story
			ON		(story.storyID = tag_to_object.objectID)
			LEFT JOIN 	sls".SLS_N."_library library
			ON 		(library.libraryID = story.libraryID)
			".$sqlOwnPostsJoin."
			".$sqlStoryVisitJoin."
			".$sqlSubscriptionJoin."
			WHERE		tag_to_object.tagID = ".$tagID."
					AND tag_to_object.taggableID = ".$this->getTaggableID()."
					AND story.libraryID IN (".implode(',', $accessibleLibraryIDArray).")
					AND story.isDeleted = 0
					AND story.isDisabled = 0
			ORDER BY	story.lastPostTime DESC";
		$result = WCF::getDB()->sendQuery($sql, $limit, $offset);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$row['taggable'] = $this;
			$stories[] = new TaggedStory(null, $row);
		}
		return $stories;
	}

	/**
	 * @see Taggable::getIDFieldName()
	 */
	public function getIDFieldName() {
		return 'storyID';
	}

	/**
	 * @see Taggable::getResultTemplateName()
	 */
	public function getResultTemplateName() {
		return 'taggedStories';
	}

	/**
	 * @see Taggable::getSmallSymbol()
	 */
	public function getSmallSymbol() {
		return StyleManager::getStyle()->getIconPath('storyS.png');
	}

	/**
	 * @see Taggable::getMediumSymbol()
	 */
	public function getMediumSymbol() {
		return StyleManager::getStyle()->getIconPath('storyM.png');
	}

	/**
	 * @see Taggable::getLargeSymbol()
	 */
	public function getLargeSymbol() {
		return StyleManager::getStyle()->getIconPath('storyL.png');
	}
}
?>