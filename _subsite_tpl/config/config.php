<?php
/*
 * Main configuration FILE
 */

if(!defined('VVE_APP_IS_RUN')){
   die ('Aplication not running');
}

// SQL server connection
define('VVE_DB_PREFIX', '{PREFIX}');
define('VVE_PARENT_CONFIG', '../');