<?php
// parametry u produktů
if(defined('CUBE_CMS_SHOP') && CUBE_CMS_SHOP == true){
   $mProductParams = new Shop_Model_ProductParams();
   $mProductParams->createTable();
}