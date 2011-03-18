<?php
// de.hpffa.sls vars
// sls
if (!defined('SLS_DIR')) define('SLS_DIR', dirname(__FILE__).'/');
if (!defined('RELATIVE_SLS_DIR')) define('RELATIVE_SLS_DIR', '');
if (!defined('SLS_N')) define('SLS_N', '1_1');
$packageDirs[] = SLS_DIR;

// general info
if (!defined('RELATIVE_WCF_DIR'))	define('RELATIVE_WCF_DIR', RELATIVE_SLS_DIR.'../wcf/');
if (!defined('PACKAGE_ID')) define('PACKAGE_ID', 155);
if (!defined('PACKAGE_NAME')) define('PACKAGE_NAME', 'Story Archiv System');
if (!defined('PACKAGE_VERSION')) define('PACKAGE_VERSION', '1.0.0');