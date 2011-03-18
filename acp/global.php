<?php
/**
 * @author	Jana Pape
 * @copyright	2010
 */
// define paths
define('RELATIVE_SLS_DIR', '../');

// include config
$packageDirs = array();
require_once(dirname(dirname(__FILE__)).'/config.inc.php');

// include WCF
require_once(RELATIVE_WCF_DIR.'global.php');
if (!count($packageDirs)) $packageDirs[] = SLS_DIR;
$packageDirs[] = WCF_DIR;

// starting sls acp
require_once(SLS_DIR.'lib/system/SLSACP.class.php');
new SLSACP();
?>
