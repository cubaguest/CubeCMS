<?php
/**
 * Třída pro generování csv exportů (např tabulek)
 * Třída slouží pro generování csv dat
 *
 * @copyright  	Copyright (c) 2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.1 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro generování csv exportu
 */

class Component_Api_VVEArticle extends Component {
   /**
    * Pole s ostatními prvky
    * @var array
    */
   private $data = array();

   /**
    * Pole s popisem webu
    * @var array
    */
   private $web = array('name' => null, 'link' => null);

   /**
    * Pole s popisem kategorie
    * @var array
    */
   private $category = array('name' => null, 'link' => null);

   /**
    * Pole s konfiguračními hodnotami
    * @var array
    */
   private $article = array('name' => null, 'link' => null, 'text' => null, 'image' => null);

   /**
    * Objekt s XMl
    * @var XMLWriter
    */
   private $xml = null;

   public function  __construct() {
      $this->setWebName(VVE_WEB_NAME, Url_Link::getMainWebDir());
      $this->xml = new XMLWriter();
      $this->xml->openURI('php://output');
   }

   /**
    * Metoda nastaví název webu
    * @param string $name -- název
    * @param string $link -- odkaz
    */
   public function setWebName($name, $link) {
      $this->web['name'] = $name;
      $this->web['link'] = $link;
   }

   /**
    * Metoda nastaví název kategorie
    * @param string $name -- název
    * @param string $link -- odkaz
    */
   public function setCategory($name, $link) {
      $this->category['name'] = $name;
      $this->category['link'] = $link;
   }

   /**
    * Metoda nastaví článek
    * @param string $name -- název
    * @param string $link -- odkaz
    */
   public function setArticle($name, $link, $text, $image = null) {
      $this->article['name'] = $name;
      $this->article['link'] = $link;
      $this->article['text'] = $text;
      $this->article['image'] = $image;
   }

   /**
    * Metoda nastaví další data
    * @param string $name -- název
    * @param string $value -- hodnota
    */
   public function setData($name, $value) {
      $this->data[$name] = $value;
   }

   /**
    * Metoda nastaví api podle předaného kontextu
    * @param string $cnt
    */
   public function setContent($cnt) {

   }

   /**
    * Metoda vrací popis článku v poli
    * @return array -- pole s detailem článku
    */
   public function getArray() {
      $ret = array();
      $ret['web'] = $this->web;
      $ret['category'] = $this->category;
      $ret['article'] = $this->article;
      $ret['data'] = $this->data;
      return $ret;
   }

   /**
    * Vygeneruje a odešle výstup a ukončí script
    */
   public function flush() {
      $out = new Template_Output();
      $out->sendHeaders();
      $this->createXml();
      $this->xml->flush();
      exit();
   }

   /**
    * Metoda vytvoří cvs řetězec
    * @return XMLWriter
    */
   private function createXml() {
      $return = null;

      // hlavička
      $this->xml->startDocument('1.0', 'UTF-8');
      $this->xml->setIndent(4);

      // rss hlavička
      $this->xml->startElement('article'); // SOF article
      $this->xml->writeAttribute('xmlns','http://www.vveframework.eu/v6');
      $this->xml->writeAttribute('xml:lang', Locale::getLang());
      // informace o webu
      $this->xml->startElement('web');
      $this->xml->writeAttribute('link', $this->web['link']);
      $this->xml->writeRaw($this->web['name']);
      $this->xml->endElement();
      // kategorie
      $this->xml->startElement('category'); // sof article
      $this->xml->writeAttribute('link', $this->category['link']);
      $this->xml->writeRaw($this->category['name']);
      $this->xml->endElement();

      // informace o článku/akci
      foreach ($this->article as $key => $value) {
         if($value != null){
            $this->xml->writeElement($key, $value);
         }
      }

      if(!empty ($this->data)){
         $this->xml->startElement('data');
         foreach ($this->data as $key => $data){
            $this->xml->writeElement($key, $data);
         }
         $this->xml->endElement();
      }
      $this->xml->endElement(); // eof article
      $this->xml->endDocument(); //EOF document
   }

   /**
    * Metoda pro výpis komponenty
    */
   public function mainView() {}
}
?>
