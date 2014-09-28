<?php

/**
 * Třída pro usnadnění práce s adresáři
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_CMS {
   
   public static function getLangs($text, $path = null, $returnCode = true, $delimiter = ',')
   {
      if(!Locales::isMultilang()) return null;
      $langs = array();
      if(($text instanceof Model_ORM_LangCell) OR ($text instanceof Model_LangContainer_LangColumn)){
         foreach (Locales::getAppLangsNames() as $lang => $name) {
            if($text[$lang] != null){
               $langs[] = $returnCode ? $lang : $name;
            }
         }
      }
      return implode($delimiter, $langs);
   }
   
   public static function getLangsImages($text, $path = null)
   {
      if(!Locales::isMultilang()) return null;
      $string = null;
      if(($text instanceof Model_ORM_LangCell) OR ($text instanceof Model_LangContainer_LangColumn)){
         foreach (Locales::getAppLangs() as $lang) {
            if($text[$lang] != null){
               $string .= self::getLangImage($lang, $path);
            }
         }
      }
      return $string;
   }
   
   public static function getLangImage($lang, $path = null)
   {
      if($lang == null) {
         $lang = Locales::getDefaultLang();
      }
      if($path == null) {
         $path = Url_Request::getBaseWebDir(true).'images/langs/small/';
      }
      return '<img src="'.$path.$lang.'.png" alt="'.$lang.' flag" class="lang-image" />';
   }
   
   /**
    * Varcí odkaz titulní obrázek položky (článek, akce)
    * @param string|Model_ORM_Record $file
    * @param string $prop -- název proménné objektu
    * @return string
    */
   public static function getArticleTitleImage($file, $prop = Articles_Model::COLUMN_TITLE_IMAGE) 
   {
      if($file instanceof Model_ORM_Record){
         $file = $file->$prop;
      }
      return Url_Request::getBaseWebDir().VVE_DATA_DIR.'/'.VVE_ARTICLE_TITLE_IMG_DIR.'/'.$file;
   }
   
   /**
    * Varcí odkaz titulní obrázek položky (článek, akce)
    * @param string|Model_ORM_Record $file
    * @param string $prop -- název proménné objektu
    * @return string
    */
   public static function getTitleImage($file, $prop = Articles_Model::COLUMN_TITLE_IMAGE) 
   {
      if($file instanceof Model_ORM_Record){
         $file = $file->$prop;
      }
      return Url_Request::getBaseWebDir().VVE_DATA_DIR.'/'.VVE_ARTICLE_TITLE_IMG_DIR.'/'.$file;
   }
   
   /**
    * Varcí adresář na titulní obrázek položky (článek, akce)
    * @param bool $returnUrl -- jestli vrátit absolutní cetu nebo url
    * @return string
    */
   public static function getTitleImagePath($returnUrl = true) 
   {
      if($returnUrl){
         return Url_Request::getBaseWebDir().VVE_DATA_DIR.'/'.VVE_ARTICLE_TITLE_IMG_DIR.'/';
      }
      return AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.VVE_ARTICLE_TITLE_IMG_DIR.DIRECTORY_SEPARATOR;
   }
}
