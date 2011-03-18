<?php
/**
 * @author	Jana Pape
 * @copyright	2010
 */
// include config
$packageDirs = array();
require_once(dirname(__FILE__).'/config.inc.php');

// include WCF
require_once(RELATIVE_WCF_DIR.'global.php');
if (!count($packageDirs)) $packageDirs[] = SLS_DIR;
$packageDirs[] = WCF_DIR;

// starting archiv core
require_once(SLS_DIR.'lib/system/SLSCore.class.php');
new SLSCore();
?>