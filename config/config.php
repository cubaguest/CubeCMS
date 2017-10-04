<?php
/*
 * Main configuration FILE
 */

if(!defined('CUBE_CMS_APP_IS_RUN')){
   die ('Aplication not running');
}

// SQL server connection
define('CUBE_CMS_DB_TYPE', 'mysqli');
define('CUBE_CMS_DB_SERVER', 'localhost');
define('CUBE_CMS_DB_NAME', 'cube_cms');
define('CUBE_CMS_DB_USER', 'cubecms');
define('CUBE_CMS_DB_PASSWD', 'cubecms');
if(!defined('CUBE_CMS_DB_PREFIX')){
   define('CUBE_CMS_DB_PREFIX', 'cube_cms_');
}
// define('VVE_MEMCACHE_SERVER', '127.0.0.1');
// define('VVE_MEMCACHE_PORT', 11211);

define('MAINTENANCE_EMAIL', 'pepa2@cube-studio.cz');
define('MAINTENANCE_KEY', 'key2014');

define('CUBE_CMS_DEBUG_SQL', true);