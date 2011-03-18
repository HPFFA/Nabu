<?php
// sls imports
require_once(SLS_DIR.'lib/data/story/Story.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/style/StyleManager.class.php');

/**
 * Represents a viewable story in the forum.
 *
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	data.story
 * @category 	Story Library System
 */
class ViewableStory extends Story {
	/**
	 * Handles the given resultset.
	 *
	 * @param 	array 		$row		resultset with story data form database
	 */
	protected function handleData($data) {
		parent::handleData($data);

		// handle moved stories
		$this->data['realStoryID'] = $this->storyID;
		if ($this->movedStoryID != 0) $this->data['storyID'] = $this->movedStoryID;

		// get last visit time
		if (!$this->lastVisitTime && WCF::getUser()->userID == 0) {
			// user is guest; get story visit from session
			$this->data['lastVisitTime'] = WCF::getUser()->getStoryVisitTime($this->storyID);
		}

		if ($this->lastVisitTime < WCF::getUser()->getLibraryVisitTime($this->libraryID)) {
			$this->data['lastVisitTime'] = WCF::getUser()->getLibraryVisitTime($this->libraryID);
		}
	}

	/**
	 * Gets the story rating result for template output.
	 *
	 * @return	string		story rating result for template output
	 */
	public function getRatingOutput() {
		$rating = $this->getRating();
		if ($rating !== false) $roundedRating = round($rating, 0);
		else $roundedRating = 0;
		$description = '';
		if ($this->ratings > 0) {
			$description = WCF::getLanguage()->get('sls.library.vote.description', array('$votes' => StringUtil::formatNumeric($this->ratings), '$vote' => StringUtil::formatNumeric($rating)));
		}

		return '<img src="'.StyleManager::getStyle()->getIconPath('rating'.$roundedRating.'.png').'" alt="" title="'.$description.'" />';
	}

	/**
	 * Gets the number of pages in this story.
	 *
	 * @return	integer		number of pages in this story
	 */
	public function getPages($library = null) {
		// get library
		if ($library == null || $library->libraryID != $this->libraryID) {
			if ($this->library !== null) $library = $this->library;
			else $library = Library::getLibrary($this->libraryID);
		}

		// get chapters per page
		if (WCF::getUser()->chaptersPerPage) $chaptersPerPage = WCF::getUser()->chaptersPerPage;
		else if ($library->chaptersPerPage) $chaptersPerPage = $library->chaptersPerPage;
		else $chaptersPerPage = STORY_CHPATERS_PER_PAGE;

		return intval(ceil(($this->chapters + 1) / $chaptersPerPage));
	}

	/**
	 * Returns the filename of the story icon.
	 *
	 * @return	string		filename of the story icon
	 */
	public function getIconName() {
		// deleted
		if ($this->isDeleted) return 'storyTrash';

		$icon = 'story';

		// new
		if ($this->isNew()) $icon .= 'New';

		// moved
		if ($this->movedStoryID) {
			$icon .= 'Moved';

			// closed
			if ($this->isClosed) $icon .= 'Closed';

			return $icon;
		}

		// closed
		if ($this->isClosed) $icon .= 'Closed';

		return $icon;
	}

	/**
	 * Returns the flag icon for the story language.
	 *
	 * @return	string
	 */
	public function getLanguageIcon() {
		$languageData = Language::getLanguage($this->languageID);
		if ($languageData !== null) {
			return '<img src="'.StyleManager::getStyle()->getIconPath('language'.ucfirst($languageData['languageCode']).'S.png').'" alt="" title="'.WCF::getLanguage()->get('wcf.global.language.'.$languageData['languageCode']).'" />';
		}
		return '';
	}
}
?>