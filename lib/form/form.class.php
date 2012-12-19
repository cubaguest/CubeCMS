<?php
/**
 * Třída pro obsluhu formuláře
 * Třída implementující objekt pro obsluhu formuláře. Umožňuje kontrolu
 * odeslání formuláře, správného vyplnění všech prvků. Všechny prvky jsou přístupny
 * přes asociativní pole a lze u nich nastavovat validace, překódování a načítat
 * z nich data.
 *
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE 5.1.0 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu formulářů
 */
class Form extends TrObject implements ArrayAccess, Iterator {
   const GRP_POS_END = 1;
   const GRP_POS_BEGIN = 2;

   /**
    * Prefix pro všechny prvky ve formuláři
    * @var string
    */
   private $formPrefix = null;

   /**
    * Pole z prvky formuláře
    * @var array
    */
   private $elements = array();

   /**
    * Pole se skupinami elementů
    * @var array
    */
   private $elementsGroups = array();

   /**
    * Proměná obsahuje jestli byl formulář odeslán
    * @var boolean
    */
   private $isSend = null;

   /**
    * Jestli byl formulář naplněn
    * @var boolean
    */
   private $isPopulated = false;

   /**
    * Jestli je formulář validně vyplněn
    * @var boolean
    */
   private $isValid = null;

   /**
    * Objekt elementu, do kterého bude formlář vykreslen
    * @var Html_Element
    */
   private $htmlElement = null;

   /**
    * Jestli je formulář vícejazyčný
    * @var boolean
    */
   private $formIsMultilang = false;

   /**
    * Element pro zjištění odeslání formuláře
    * @var Form_Element_Hidden
    */
   private $elementCheckForm = null;

   private $protectForm = false;
   private $elementToken = null;
   private $tokenIsOk = false;

   private $submitElement = null;


   /**
    * Konstruktor vytváří objekt formuláře
    * @param string $prefix -- (option) prefix pro formulářové prvky
    */
   function __construct($prefix = null, $protectForm = false) {
      $this->formPrefix = $prefix;
      $this->htmlElement = new Html_Element('form');
      $this->setAction(new Url_Link_Module());
      $this->setSendMethod();
      $this->elementCheckForm = new Form_Element_Hidden('_'.$prefix.'_check');
      $this->elementCheckForm->setValues('send');
      $this->protectForm = $protectForm;
      if($protectForm){
         $this->createToken();
      }
   }

   public function  __toString() {
      return $this->creatString();
   }

   /**
    * Metoda vygeneruje unikátní token a přidá jej do formuláře
    */
   private function createToken()
   {
      if($this->elementToken == null){
         $this->elementToken = new Form_Element_Token('_'.$this->formPrefix.'_token');
      }
   }

   /**
    * Metoda vytvoří řetězec s formulářem, pro použití v šabloně
    * @param Form_Decorator $decorator -- objekt dekorátoru
    * @return string -- formulář jako řetězec
    */
   private function creatString(Form_Decorator_Interface $decorator = null) {
      if($decorator == null) {
         $decorator = new Form_Decorator();
      }
      $html = clone $this->html();
      $pHtml = new Html_Element('p', $this->elementCheckForm->controll());
      if($this->protectForm){
         $this->createToken();
         $pHtml->addContent((string)$this->elementToken->controll());
      }
      $html->addContent($pHtml);

      $prevGrp = null;
      $d = clone $decorator;
      $grps = $this->elementsGroups;
      reset($grps);
      while (list($key, $grp) = each($grps)) {
         // pokud element není ve skupině
         if(!is_array($grp)) {
            $next = true;
            // zařazení elementu do skupiny
            $d->addElement($this->elements[$key]);
            // aktuální prvek z pole - curent dává následující, ne na který se ukazuje
            $next = current($grps);
            // pokud je další pole nabo není element, ukončíme skupinu
            if($next === false OR is_array($next)){ // konec nebo další je skupina
               $html->addContent((string)$d);
               $d = clone $decorator;
            }
         }
         // pokud patří do skupiny
         else {
            // pokud je skupina prázdná
            if(!empty ($grp['elements'])) {
            // render elementů do skupiny
               foreach ($grp['elements'] as $key2 => $elemName) {
                  $d->addElement($this->elements[$key2]);
               }
               $d->setGroupName($grp['label']);
               $d->setGroupText($grp['text']);

               $html->addContent((string)$d);
               $d = clone $decorator;
            };
         }
      }
      $html->addContent($this->scripts());
      return (string)$html;
   }

   /**
    * Metoda vykreslí skripty formuláře
    */
   public function scripts() {
      $script = null;
      return $script;
   }

   /**
    * Magická metoda pro vložení neinicializované proměné do objektu
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    */
   public function  __set($name, $value) {
//      $this->elements[$name] = $value;
      $this->addElement($value);
   }

   /**
    * Metoda vraci inicializovanou proměnnou, pokud je
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __get($name) {
      if(!isset($this->elements[$name])) {
         $this->elements[$name] = null;
      }
      return $this->elements[$name];
   }

   /**
    * Metoda kontroluje jestli byla daná proměnná inicializována
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __isset($name) {
      return isset ($this->elements[$name]);
   }

   /**
    * Metoda maže danou proměnnou z objektu
    * @param string $name -- název proměnné
    */
   public function  __unset($name) {
      if(isset ($this->elements[$name])) {
         unset ($this->elements[$name]);
      }
      if(isset ($this->elementsGroups[$name])) {
         unset ($this->elementsGroups[$name]);
      }
      foreach ($this->elementsGroups as $key => $group) {
         if(!is_array($group)) continue;
         unset ($this->elementsGroups[$key]['elements'][$name]);
      }
   }

      /**
    * Metoda vykreslí začáteční tag pro formulář (tag <form>)
    * @return string
    */
   public function renderStart() {
      $cnt = $this->html()->__toStringBegin();
      $this->createToken();
      $p = new Html_Element('p', (string)$this->elementCheckForm->controll());
      if($this->elementToken !== null){
         $p->addContent((string)$this->elementToken->controll());
      }
      return $cnt.$p;
   }

   /**
    * Metoda vykreslí začáteční tag pro formulář (tag <form>)
    * @return string
    */
   public function renderEnd() {
      $cnt = $this->scripts();
      $cnt .= $this->html()->__toStringEnd();
      return $cnt;
   }


   /*
    * Metody pro přístup k prvkům formuláře
   */

   /**
    * Metoda přidává nový prvek do pole elemntů
    * @param string $name -- název prvku
    * @param mixed $value -- hodnota prvku
    * @return Form_Element
    */
   function offsetSet($name, $value) {
      $this->elements[$name] = $value;
   }
   /**
    * Metoda pro přístup k prvkům formuláře pře asociativní pole
    * @param string $name -- název prvku
    * @return Form_Element
    */
   function offsetGet($name) {
      if(!isset($this->elements[$name])) {
         $this->elements[$name] = null;
      }
      return $this->elements[$name];
   }

   /**
    * Metoda pro zjištění existence elementu ve struktuře formuláře.
    * @param string $name -- název formulářového elementu
    */
   function offsetExists($name) {
      return isset ($this->$name);
   }

   /**
    * Metoda je volána při odstranění elementu z formuláře
    * @param string $name -- název elemeentu
    */
   function offsetUnset($name) {
      unset ($this->$name);
   }

   /**
    * Metoda přesune iterátor na začátek a vrátí první element
    * @return Form_Element
    */
   function rewind() {
      return reset($this->elements);
   }

   /**
    * Metoda vrací aktuální element
    * @return Form_Element
    */
   function current() {
      return current($this->elements);
   }

   /**
    * Metoda vrací aktuální klíč elementu (název)
    * @return string
    */
   function key() {
      return key($this->elements);
   }

   /**
    * Metoda vrátí následující element
    * @return Form_Element
    */
   function next() {
      return next($this->elements);
   }

   /**
    * Metoda zjišťuje jestli je element validní, tj. jestli existuje
    * @return <type>
    */
   function valid() {
      return key($this->elements) !== null;
   }

   /*
    * Metody zajišťující kontrolu a odeslání formuláře
   */

   /**
    * Metoda zjišťuje jestli byl formulář odeslán
    * @return boolean -- true pokud byl formulář odeslán
    * @todo - optimalizovat, protože procházet to dvakrát je fakt nesmysl
    */
   public function isSend() {
      if($this->isSend != true) { // pokud nebyl odeslán
         // kontrola check prvku
         if($this->elementCheckForm->isSend() AND $this->elementCheckForm->getValues() != null) {
            $this->elementCheckForm->populate();
            $this->isSend = true;
         }
         if($this->submitElement instanceof Form_Element_SaveCancel AND $this->submitElement->isSend()){
              $this->submitElement->populate();
              if($this->submitElement->getValues() == true){
                 $this->isSend = true;
              } else {
                 $this->isSend = false;
                 $this->isPopulated = false;
                 $this->isValid = false;
                 return true;
              }
         }
         // kontrola ostatní submit elementů
         if($this->isSend != true) {
            foreach ($this->elements as $element) {
               if(($element instanceof Form_Element_Submit
                 OR $element instanceof Form_Element_SubmitImage)
                 AND $element->isSend()) {
                  $element->populate();
                  $this->isSend = true;
                  break;
               }
            }
         }
      }
      if(empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post'){ //catch file overload error...
         if(isset($_SERVER['CONTENT_LENGTH'])){
            $sendSize = $_SERVER['CONTENT_LENGTH'];
            AppCore::getUserErrors()->addMessage(
               sprintf($this->tr("Bylo odesláno %s dat, což je více než je možné přijmout. Maximálně lze odeslat %s."), vve_create_size_str($sendSize), vve_create_size_str(VVE_MAX_UPLOAD_SIZE) ) );
         } else {
            AppCore::getUserErrors()->addMessage(
               sprintf($this->tr("Bylo odesláno více dat než je možné přijmout. Maximálně lze odeslat %s."), vve_create_size_str(VVE_MAX_UPLOAD_SIZE) ) );
         }
      }
      if($this->isSend == true) $this->populate();
      return $this->isSend;
   }

   /**
    * Metoda zjišťuje jestli byl formulář odeslán v pořádku
    * @return booleant -- true pokud je formulář v pořádku
    */
   public function isValid() {
      if($this->isSend === null){
         $this->isSend();
      }
      // kontrola tokenu
      if($this->protectForm && $this->tokenIsOk != true && $this->elementToken->isSend()){
         $this->elementToken->populate();
         $this->elementToken->validate();
         if(!$this->elementToken->isValid()){ // neodpovídající token
            AppCore::getUserErrors()->addMessage($this->tr('Odeslán nesprávný token, pravděpodobně útok CSRF'));
            return false;
         }
         $this->tokenIsOk = true;
      }
      if($this->isSend == true){
         $this->validate();
      }
      return $this->isValid;
   }

   /**
    * Metoda provede naplnění všech prvků ve formuláři a jejich validaci
    */
   public function populate() {
      $this->isPopulated = false;
      foreach ($this->elements as $name => $element) {
         if(!$element->isPopulated()){
            $element->populate();
         }
      }
      $this->isPopulated = true;
   }

   public function validate() {
      $this->isValid = true;
      foreach ($this->elements as $name => $element) {
         if(!$element->isPopulated()){
            $element->populate();
         }
         $element->validate();
         if(!$element->isValid()) {
            $this->isValid = false;
            continue;
         }
         $element->filter();
      }
   }

   /**
    * Metoda vrací jestli byl formůlář již vyplněn
    *
    * @return bool -- true pokud byl formulář již vyplněn
    */
   public function isPopulated() {
      return $this->isPopulated;
   }

   /*
    * Metody pro práci s formulářem
   */

   /**
    * Metoda nastavuje akci pro formulář, tedy adresu kam se má odkazovat
    * @param string $link -- odkaz pro akci
    * @todo
    */
   public function setAction($link) {
      $this->html()->setAttrib('action', (string)$link);
   }

   /**
    * Metoda vrací akci pro odeslání formuláře
    * @return Links
    * @todo
    */
   public function getAction() {
      return $this->html()->getAttrib('action');
   }

   /**
    * Metoda nastavuje metodu odeslání formuláře (post|get)
    * @param string $method -- typ metody (výchozí je 'post')
    * @todo
    */
   public function setSendMethod($method = "post") {
      $this->html()->setAttrib('method', strtolower($method));
   }

   /**
    * Metoda vrací nastavenou metodu odeslání formuláře
    * @return string (post|get)
    * @todo
    */
   public function getSendMethod() {
      return $this->html()->getAttrib('method');
   }

   /**
    * Metoda prací prefix formuláře
    * @return string
    */
   public function getPrefix(){
      return $this->formPrefix;
   }

   /**
    * Metoda nasatví jestli má být formulář chráněn nebo ne
    * @param bool $protect (option) true pro zapnutí
    * @return Form
    */
   public function setProtected($protect = true)
   {
      $this->protectForm = $protect;
      if($protect){
         $this->createToken();
      }
      return $this;
   }

   /**
    * Metoda přidá element do formuláře
    * @param Form_Element $element -- objetk formulářového elementu
    * @param string $group -- řetězec označující skupinu (např. z metody addGroup())
    * @param int $position -- na kterou pozici se má element přidat (pokud je -1 tak se vloží na konec)
    */
   public function addElement(Form_Element $element, $group = null, $position = -1) {
      $name = $element->getName();
      $this->elements[$name] = $element;
      $this->elements[$name]->setPrefix($this->formPrefix);
      if($element->isMultiLang()) {
         $this->formIsMultilang = true;
      }

      if($group == null) {
         if($position == -1){
            $this->elementsGroups[$name] = $this->elements[$name]->getName();
         } else {
         /**
          * Nejde pouze pomocí array_splice
          * @see http://www.php.net/manual/en/function.array-splice.php#41118
          */
            $firstPart = array_slice($this->elementsGroups, 0, $position);
            $secondPart = array_slice($this->elementsGroups, $position);
            $insertPart = array($name => $this->elements[$name]->getName());
            $this->elementsGroups = array_merge($firstPart, $insertPart, $secondPart);
         }
         
      } else {
         if(!isset ($this->elementsGroups[$group])) {
            $this->addGroup($group);
         }

         if($position == -1){
            $this->elementsGroups[$group]['elements'][$name] = $this->elements[$name]->getName();
         } else {
         /**
          * Nejde pouze pomocí array_splice
          * @see http://www.php.net/manual/en/function.array-splice.php#41118
          */
            $firstPart = array_slice($this->elementsGroups[$group]['elements'], 0, $position);
            $secondPart = array_slice($this->elementsGroups[$group]['elements'], $position);
            $insertPart = array($name => $this->elements[$name]->getName());
            $this->elementsGroups[$group]['elements'] = array_merge($firstPart, $insertPart, $secondPart);
//            $this->elementsGroups[$group]['elements'][$name] = $this->elements[$name]->getName();
         }

      }

      // pokud je soubor přidám do formu že se bude přenášet po částech
      if($element instanceof Form_Element_File) {
         $this->html()->setAttrib("enctype", "multipart/form-data");
      }
      // pokud je submit, přidáme ho do elementu pro submiting
      if($element instanceof Form_Element_Submit OR $element instanceof Form_Element_SubmitImage OR $element instanceof Form_Element_SaveCancel) {
         $this->submitElement = &$this->elements[$name];
      }
   }

   /**
    * Metoda odstraní zadaný element z formuláře
    * @param string $name -- název elementu
    */
   public function removeElement($name) {
      unset ($this->$name);
   }

   /**
    * Metoda zjišťuje jestli formulář má zadaný element
    * @return bool -- true pokud formulář obsahuje zadaný element
    */
   public function haveElement($name) {
      return isset ($this->$name);
   }

   /**
    * Metoda přidá skupinu pro elementy
    * @param string $name -- název skupiny - pro zařazování
    * @param string $label -- název skupiny - její název při renderu
    * @param string $text -- text ke skupině - popisný text ke skupině
    * @return string -- název skupiny
    */
   public function addGroup($name, $label = null, $text = null, $after = self::GRP_POS_END) {
      if(!isset ($this->elementsGroups[$name])) {
         if($after == self::GRP_POS_END){ // na konec
            $this->elementsGroups[$name]['elements'] = array();
            $this->elementsGroups[$name]['label'] = $label;
            $this->elementsGroups[$name]['text'] = $text;
         } else {
            if($after == self::GRP_POS_BEGIN){ // na začátek
               $endPart = array_splice($this->elementsGroups,0);
               $this->addGroup($name, $label, $text);
               $this->elementsGroups = array_merge($this->elementsGroups, $endPart);
               unset ($endPart);
            } else { // za skupinu
               $position = array_search($after, array_keys($this->elementsGroups))+1; // after
               $endPart = array_splice($this->elementsGroups, $position);
               $this->addGroup($name, $label, $text);
               $this->elementsGroups = array_merge($this->elementsGroups, $endPart);
               unset ($endPart);
            }
         }
      }
      return $name;
   }

   /*
    * Metody pro zpracování obsahu formuláře
   */

   /*
     * Metody pro render formuláře
   */

   /**
    * Metoda vyrenderuje formulář podle zadaného typu
    * @param Form_Decorator $decorator -- objekt Form dekorátoru
    */
   public function render(Form_Decorator_Interface $decorator = null) {
      echo ($this->creatString($decorator));
   }

   /**
    * Metoda vrací objekt html elemntu formuláře, vhodné pro úpravu tříd, přidání
    * vlastností, atd
    * @return Html_Element
    */
   public function html() {
      return $this->htmlElement;
   }

   /**
    * Metoda vrací element formulářového checkeru
    * @return Form_Element_Hidden
    */
   public function getFormChecker() {
      return $this->elementCheckForm;
   }

   /*
     * Metody pro exporty
   */
   
   /**
    * metoda pro export dat ve formuláři
    * @return Form_Data 
    */
   public function getData(Form_Data $object = null)
   {
      if($object == null){
         $object = new Form_Data();
      }
      foreach ($this->elementsGroups as $key => $value) {
         // je skupina
         if(is_array($value)){
            $object->{'grp_'.$key} = new Form_Data_Header($value['label']);
            foreach ($value['elements'] as $ekey => $name) {
               $this->dataAddItem($ekey, $object);
            }            
         } else {
            $this->dataAddItem($key, $object);
         }
      }
      return $object;
   }
   
   private function dataAddItem($key, Form_Data $container)
   {
      $e = $this->{$key};
      if($e instanceof Form_Element_Text || $e instanceof Form_Element_TextArea || $e instanceof Form_Element_Password){
         $container->{$key} = new Form_Data_Item($e->getLabel(), $e->getValues(), $e->getSubLabel());
      } else if($e instanceof Form_Element_Checkbox){
         $container->{$key} = new Form_Data_Item($e->getLabel(), (bool)$e->getValues(), $e->getSubLabel());
      } else if($e instanceof Form_Element_Select){
//         if(is_array($e->getValues())){
               // tohle se chová naprosto blbě. Validace prvku v poli je již v selectu takže asi není proč to tu mít
//            $container->{$key} = new Form_Data_Item($e->getLabel(), array_search($e->getValues(), $e->getOptions()), $e->getSubLabel());
//         } else {
            $container->{$key} = new Form_Data_Item($e->getLabel(), $e->getValues(), $e->getSubLabel());
//         }
      } else if($e instanceof Form_Element_File){
         $f = $e->getValues();
         $container->{$key} = new Form_Data_Item($e->getLabel(), $f['name'], $e->getSubLabel());
      }
   }
}

?>
