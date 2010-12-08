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
class Module_Rss extends Module_Core {
   private $links = arraY();
   public function runController() {
      $type = 'xml';
      switch ($type) {
         case 'xml':
            $this->links;
            $model = new Model_Category();
            $cats = $model->getCategoryList();
            $rssComp = new Component_Feed();
            foreach ($cats as $cat) {
               if ($cat->{Model_Category::COLUMN_FEEDS} != true) continue;
               // načtení a kontrola cest u modulu
               $routesClassName = ucfirst($cat->{Model_Category::COLUMN_MODULE}).'_Routes';
               if(!class_exists($routesClassName)) {
                  throw new BadClassException(sprintf(_("Nepodařilo se načíst třídu cest (routes) modulu \"%s\"."),
                  $cat->{Model_Category::COLUMN_MODULE}), 10);
               }
               //	Vytvoření objektu s cestama modulu
               $routes = new $routesClassName(null);
               $rssClassName = ucfirst($cat->{Model_Category::COLUMN_MODULE}).'_Rss';
               $rssCore = new $rssClassName(new Category_Core($cat->{Model_Category::COLUMN_URLKEY},
                  false, $cat), $routes);
               $rssCore->setRssComp($rssComp);
               $rssCore->runController();
            }
            $rssComp->setConfig('link', $this->link());
            $rssComp->setConfig('title', null);
            $rssComp->flush();
            break;
         default:
            $model = new Model_Category();
            $cats = $model->getCategoryList();
            $link = new Url_Link(true);
            $link->clear(true);
            foreach ($cats as $cat) {
               if ($cat[Model_Category::COLUMN_FEEDS] != true) continue;
               array_push($this->links, array(
                  'name' => (string) $cat->{Model_Category::COLUMN_CAT_LABEL},
                  'link' => (string) $link->category((string) $cat->{Model_Category::COLUMN_URLKEY})
                     ->file(Url_Request::URL_FILE_RSS)
               ));
            }
            asort($this->links);
            break;
      }
   }

   public function runView() {
      $this->template()->setPVar('CURRENT_CATEGORY_PATH', array(_('Přehled rss zdrojů')));
      $this->template()->addTplFile('rss.phtml');
      $this->template()->sources = $this->links;

      $link = new Url_Link(true);
      $this->template()->sourcesAll = $link->clear(true)->file(Url_Request::URL_FILE_RSS);
   }

   public function runXmlView() {
   }

   public function runTxtView() {
      AppCore::setErrorPage(true);
   }

}
?>
