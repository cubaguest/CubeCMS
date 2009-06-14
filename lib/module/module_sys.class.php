<?php
/**
 * Třída sjednocuje systémové parametry jednotlivých modulů. Obsahuje všechy objekty
 * potřebné pro práci s moduly (modul, práva, akce, odkazy, ...)
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 5.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třídy pro sjednocení systémových informací o modulu
 */
class Module_Sys {
   /**
    * Objekt modulu
    * @var Module
    */
   private $module = null;

   /**
    * Objekt práv
    * @var Rights
    */
   private $rights = null;

   /**
    * Objekt odkazu
    * @var Links
    */
   private $links = null;

   /**
    * Objekt cest
    * @var Routes
    */
   private $routes = null;

   /**
    * Objket akcí
    * @var Action
    */
   private $action = null;

   /**
    * Objekt článku
    * @var Articlle
    */
   private $article = null;

   /**
    * Objekt locales a pro překlady
    * @var Locale
    */
   private $locale = null;

   /**
    * Metoda nastaví objekt modulu
    * @param Module $module
    */
   public function setModule(Module $module) {
      $this->module = $module;
   }

   /**
    * Metoda vracíí objekt modulu
    * @return Module
    */
   public function module() {
      return $this->module;
   }

   /**
    * Metoda nastaví objekt odkazů
    * @param Links $link
    */
   public function setLink(Links $link) {
      $this->links = $link;
   }

   /**
    * Metoda vrací objekt odkazů Links
    * @return Links
    */
   public function link() {
      return clone $this->links;
   }

   /**
    * Metoda nastaví objekt článku
    * @param Article $article
    */
   public function setArticle(Article $article) {
      $this->article = $article;
   }

   /**
    * Metoda vrací objekt článku
    * @return Article
    */
   public function article() {
      return $this->article;
   }

   /**
    * Metoda nastaví objekt akcí
    * @param Action $action
    */
   public function setAction(Action $action) {
      $this->action = $action;
   }

   /**
    * Metoda vrací objekt akcí
    * @return Action
    */
   public function action() {
      return $this->action;
   }

   /**
    * Metoda nastaví objekkt práv
    * @param Rights $rights
    */
   public function setRights(Rights $rights) {
      $this->rights = $rights;
   }

   /**
    * Metoda vrací objekt práv
    * @return Rights
    */
   public function rights() {
      return $this->rights;
   }

   /**
    * Metoda nastaví objekt cest
    * @param Routes $routes
    */
   public function setRoute(Routes $routes) {
      $this->routes = $routes;
   }

   /**
    * Metoda vrací objekt cest
    * @return Routes
    */
   public function route() {
      return $this->routes;
   }

   /**
    * Metoda nastaví objekt Locale
    * @param Locale $locale
    */
   public function setLocale(Locale $locale) {
      $this->locale = $locale;
   }

   /**
    * Metoda vrací objekt Locale
    * @return Locale
    */
   public function locale() {
      return $this->locale;
   }

   /**
    * Metoda vrací objekt autorizace
    * @return Auth
    */
   public function auth() {
      return AppCore::getAuth();
   }
}
?>
