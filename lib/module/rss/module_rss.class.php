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

   private $links = array();

   public function runController() {
      switch (AppCore::getUrlRequest()->getOutputType()) {
         case 'xml':
            $model = new Model_Category();
            $cats = $model->getCategoryList();
            $link = new Url_Link(true);
            $link->clear(true);
            // to samé jak v core pro rss
            foreach ($cats as $cat) {
               if ($cat[Model_Category::COLUMN_FEEDS] != true) continue;
               
               
            }


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
