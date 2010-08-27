<?php
/**
 * Třida pro export rss kanálů
 * Třída pro tvorbu ecportu rss kanálů z modulů
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.2 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu požadavku na rss kanál z modulu
 */
class Rss {
   private $rssComponent = null;

   private $category = null;

   private $link = null;

   function __construct(Category_Core $category, Routes $routes) {
      $this->category = $category;
      $this->link = new Url_Link_Module(true);
      $this->link->setModuleRoutes($routes);
      $this->setRssComp(new Component_Feed());
   }

   public function runController() {

   }

   public function runView() {
      $this->rssComponent->flush();
   }

   final public function link(){
      return $this->link;
   }

   /**
    * Metoda vrací objekt kategorie
    * @return Category_Core
    */
   final public function category(){
      return $this->category;
   }

   /**
    * Metoda vrací Komponentu pro tvorbu rss kanálů
    * @return Component_Feed
    */
   final public function getRssComp() {
      return $this->rssComponent;
   }

   /**
    * Metoda nastavuje Komponentu pro rss
    * @param Component_Feed $comp
    */
   final public function setRssComp(Component_Feed $comp) {
      $this->rssComponent = $comp;
      $this->getRssComp()->setConfig('title', $this->category()->getName());
      $this->getRssComp()->setConfig('desc', $this->category()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
      $this->getRssComp()->setConfig('link', $this->link());
      $this->getRssComp() ->setConfig('type', $this->type);
      $this->getRssComp() ->setConfig('css', 'rss.css');
   }

}
?>
