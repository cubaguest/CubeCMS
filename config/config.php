<?php
/*
 * Main configuration FILE
 */

if(!defined('VVE_APP_IS_RUN')){
   die ('Aplication not running');
}

// SQL server connection
define('VVE_DB_TYPE', 'mysqli');
define('VVE_DB_SERVER', 'localhost');
define('VVE_DB_NAME', 'cube_cms');
define('VVE_DB_USER', 'cube_cms');
define('VVE_DB_PASSWD', 'cube_cms');
define('VVE_DB_PREFIX', 'vypecky_');

?>
