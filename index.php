<?php
/**
 * @author	Jana Pape
 * @copyright	2010
 */
require_once('./global.php');
RequestHandler::handle(ArrayUtil::appendSuffix($packageDirs, 'lib/'));
?>
