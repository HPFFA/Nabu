<?php
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');
/**
 * Caches the normal stats of a library.
 * 
 * @author 	Jana Pape
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.cache
 * @category 	Story Library System
 */
class CacheBuilderNormalStats implements CacheBuilder {
	/**
	 * @see CacheBuilder::getData()
	 */
	public function getData($cacheResource) {
		$data = array();
		/* ############## VIEWS FROM ALL THREADS ############## */
        $sql = "SELECT SUM(views) AS storyviews FROM sls".SLS_N."_story";
        $storyviews = SLSCore::getDB()->getFirstRow($sql);
        $data['storyviews'] = StringUtil::formatInteger($storyviews['storyviews']);

        /* ############## USERS / POSTS / THREADS ############## */
        $librariestats = SLSCore::getCache()->get('stat');
        $data['members'] = StringUtil::formatInteger($librariestats['members']);
        $data['chapters'] = StringUtil::formatInteger($librariestats['chapters']);
       	$data['stories'] = StringUtil::formatInteger($librariestats['stories']);
       	$data['newestmember'] = $librariestats['newestMember'];

       	$sql = "SELECT COUNT(storyID) as storyclosed FROM sls".SLS_N."_story WHERE isClosed = '1'";
       	$result = SLSCore::getDB()->getFirstRow($sql);
       	$data['storyclosed'] = StringUtil::formatInteger($result['storyclosed']);

       	/* ############## USER / POSTS / THREADS (maxID) ###### */
       	$sql = "SELECT MAX(userID) as users FROM wcf".WCF_N."_user";
       	$result = SLSCore::getDB()->getFirstRow($sql);
       	$data['membersmax'] = StringUtil::formatInteger($result['users']);

       	$sql = "SELECT MAX(chapterID) as chapters FROM sls".SLS_N."_chapter";
       	$result = SLSCore::getDB()->getFirstRow($sql);
       	$data['chaptersmax'] = StringUtil::formatInteger($result['chapters']);

       	$sql = "SELECT MAX(storyID) as stories FROM sls".SLS_N."_story";
       	$result = SLSCore::getDB()->getFirstRow($sql);
       	$data['storiesmax'] = StringUtil::formatInteger($result['stories']);

        /* ############## GROUPS ############## */
        $sql = "SELECT COUNT(groupID) AS groups FROM wcf".WCF_N."_group";
        $groups = SLSCore::getDB()->getFirstRow($sql);
        $data['groups'] = StringUtil::formatInteger($groups['groups']);


        /* ############## PMS ############## */
        $sql = "SELECT COUNT(pmID) AS privatemessage FROM wcf".WCF_N."_pm";
        $privatemessage = SLSCore::getDB()->getFirstRow($sql);
        $data['privatemessage'] = StringUtil::formatInteger($privatemessage['privatemessage']);

        $sql = "SELECT MAX(pmID) as pmmax FROM wcf".WCF_N."_pm";
        $result = SLSCore::getDB()->getFirstRow($sql);
        if($result['pmmax'] == "") $result['pmmax'] = 0;
        $data['privatemessagemax'] = StringUtil::formatInteger($result['pmmax']);

 
        /* ############## STYLES ############## */
        $sql = "SELECT COUNT(styleID) as styles FROM wcf".WCF_N."_style";
        $styles = SLSCore::getDB()->getFirstRow($sql);
        $data['styles'] = StringUtil::formatInteger($styles['styles']);

        /* ############## SMLIES ############## */
        $sql = "SELECT COUNT(smileyID) as smileys FROM wcf".WCF_N."_smiley";
        $smilies = SLSCore::getDB()->getFirstRow($sql);
        $data['smileys'] = StringUtil::formatInteger($smilies['smileys']);

        /* ############## AVATAR ############## */
        $sql = "SELECT COUNT(avatarID) as avatars FROM wcf".WCF_N."_avatar";
        $avatars = SLSCore::getDB()->getFirstRow($sql);
        $data['avatars'] = StringUtil::formatInteger($avatars['avatars']);

        /* ##############  BLOCKED ############## */
        $sql = "SELECT SUM(banned) as blockuser, SUM(disableSignature) as blocksignatur FROM wcf".WCF_N."_user";
        $itsblock = SLSCore::getDB()->getFirstRow($sql);
        $data['blockuser'] = StringUtil::formatInteger($itsblock['blockuser']);
        $data['blocksignatur'] = StringUtil::formatInteger($itsblock['blocksignatur']);

        $data['users_online_record'] = USERS_ONLINE_RECORD;
        $data['users_online_record_time'] = USERS_ONLINE_RECORD_TIME;

        return $data;
	}
}
?>