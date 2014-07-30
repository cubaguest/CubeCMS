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
if(!defined('VVE_DB_PREFIX')){
   define('VVE_DB_PREFIX', 'cube_cms_');
}
// define('VVE_MEMCACHE_SERVER', '127.0.0.1');
// define('VVE_MEMCACHE_PORT', 11211);

define('MAINTENANCE_EMAIL', 'pepa2@cube-studio.cz');
define('MAINTENANCE_KEY', 'key2014');