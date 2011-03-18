<?php
// sls imports
require_once(SLS_DIR.'lib/system/session/SLSSession.class.php');
require_once(SLS_DIR.'lib/data/user/SLSUserSession.class.php');
require_once(SLS_DIR.'lib/data/user/SLSGuestSession.class.php');

// wcf imports
require_once(WCF_DIR.'lib/system/session/CookieSessionFactory.class.php');

/**
 * SLSSessionFactory extends the CookieSessionFactory class with library specific functions.
 * 
 * @author 	Jana �a�e
 * @copyright	2010
 * @package	de.hpffa.sls
 * @subpackage	system.session
 * @category 	Story Library System
 */
class SLSSessionFactory extends CookieSessionFactory {
	protected $guestClassName = 'SLSGuestSession';
	protected $userClassName = 'SLSUserSession';
	protected $sessionClassName = 'SLSSession';
}
?>