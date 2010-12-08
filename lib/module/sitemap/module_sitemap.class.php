<?php
/**
 * Třída Core Modulu pro obsluhu mapy stránek
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.2 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu mapy stránek
 */
class Module_Sitemap extends Module_Core {
   public function runController() {
      Menu_Main::factory();
      // nastavení
      if(AppCore::getUrlRequest()->getOutputType() == 'html' AND defined('VVE_CM_SITEMAP_MAX_ITEMS_PAGE')){//tohle se může oddělat s verzí 6.2
         SiteMap::setMaxItems((int)VVE_CM_SITEMAP_MAX_ITEMS_PAGE);
      } else if(defined('VVE_CM_SITEMAP_MAX_ITEMS')) {
         SiteMap::setMaxItems((int)VVE_CM_SITEMAP_MAX_ITEMS);
      }

      // načtení kategorií a podle nich vytahání a vytvoření pododkazů
      $cats = new Model_Category();
      $categories = $cats->getCategoryList();

      SiteMap::addPage(Url_Link::getMainWebDir(), _('Hlavní stránka'));
      foreach ($categories as $category) {
         $catObj = new Category(null, false, $category);
         $routesClassName = ucfirst($catObj->getModule()->getName()).'_Routes';
         if(!class_exists($routesClassName)) {
            $routes = new Routes(null);
         } else {
            $routes = new $routesClassName(null);
         }
         if(!file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$catObj->getModule()->getName().DIRECTORY_SEPARATOR.'sitemap.class.php')) {
               $sitemap = new SiteMap($catObj, $routes);
               $sitemap->setCategoryLink(new DateTime($category->{Model_Category::COLUMN_CHANGED}));
         } else {
            $sClassName = ucfirst($catObj->getModule()->getName()).'_Sitemap';
            $sitemap = new $sClassName($catObj, $routes);
         }
         $sitemap->run();
         unset ($sitemap);
      }
   }

   public function runView() {
      $this->template()->setPVar('CURRENT_CATEGORY_PATH', array(_('mapa stránek')));
      $this->template()->categories = Menu_Main::getMenuObj();
      $this->template()->catArr = SiteMap::getItems();
      $this->template()->addTplFile('sitemap.phtml');
   }

   public function runXmlView() {
      SiteMap::generateMap('xml');
   }

   public function runTxtView() {
      SiteMap::generateMap('txt');
   }
}

?>
