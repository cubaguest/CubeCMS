<?php
/**
 * Interface elementu formuláře
 * Interface definující základní vlastnosti elemntu formuláře
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: form.class.php 630 2009-06-14 15:52:19Z jakub $ VVE 5.1.0 $Revision: 630 $
 * @author        $Author: jakub $ $Date: 2009-06-14 15:52:19 +0000 (Sun, 14 Jun 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-06-14 15:52:19 +0000 (Sun, 14 Jun 2009) $
 * @abstract      Třída pro obsluhu formulářů
 */
interface Form_Element_Interface {
   /**
    * Metoda provede naplnění elementu
    * @param string $method -- typ metody přes kterou je prvek odeslán (POST|GET)
    */
   public function populate();

   /**
    * Metoda provede validace
    */
   public function validate();

   /**
    * Metodda provede přefiltrování obsahu elementu
    */
   public function filter();

   /**
    * Metoda vrátí jestli je element validní
    */
   public function isValid();

   /**
    * Metoda vrátí jestli byl element naplněn validní
    */
   public function isPopulated();

   /**
    * Metoda vrací popisek k prvku (html element label)
    * @return string
    */
   public function label();

   /**
    * Metoda vrací subpopisek
    * @return string -- řetězec z html elementu
    */
   public function subLabel();

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll();

   /**
    * Metoda vrací všechny prvky, keré patří ke kontrolu, tj controll, labelValidations, subLabel
    * @return string -- včechny prvky vyrenderované
    */
   public function controllAll();

   /**
    * Metoda vrací popisek k validacím
    * @return string
    */
   public function labelValidations();

   /**
    * Funkce vygeneruje přepínač pro volbu jazyku
    * @return string
    */
   public function labelLangs();

   /**
    * Metoda pro generování scriptů. potřebných pro práci s formulářem
    * @return string
    */
   public function scripts();

   /**
    * Metoda vrací jestli je element prázdný
    */
   public function isEmpty();

   /**
    * Metoda nastaví prefix elementu
    * @param string $prefix -- prefix elementu ve formuláři
    */
   public function setPrefix($prefix);

    /**
    * Metoda vrací hodnotu prvku
    * @param $key -- (option) klíč hodnoty (pokud je pole)
    * @return mixed -- hodnota prvku
    */
   public function getValues($key = null);

   /**
    * Metoda vrací název elementu
    * @return string
    */
   public function getName();

   /**
    * Metoda vrací popis prvku
    * @return string -- popis prvku, je zadáván při vytvoření
    */
   public function getLabel();

   /**
    * Metofda vrací pole s jazyky prvku
    * @return array -- pole s jazyky
    */
   public function getLangs();

   /**
    * Metofda vrací jestli se jedná o vícerozměrný element
    * @return bool -- true pokud je element vicerozměrný
    */
   public function isMultiLang();

   /**
    * Metofda vrací jestli se jedná o vícerozměrný element
    * @param bool $multiple -- true pro nasatvení vícerozměrného elementu
    * @return bool -- true pokud je element vicerozměrný
    */
   public function isDimensional();

   /**
    * Metoda nastaví subpopisek k elementu
    * @param string $string -- subpopisek
    * @return Form_Element
    */
   public function setSubLabel($string);

   /**
    * Metoda nastaví že se jedná o jazykový prvek
    * @param array $langs -- (option) pole jazyků, pokud není zadáno jsou použity
    * interní jazyky aplikace
    * @return Form_Element -- vrací samo sebe
    */
   public function setLangs($langs = null);

   /**
    * Metoda přidá elemntu pravidlo pro validace
    * @param Form_Validator_Interface $validator -- typ validace
    */
   public function addValidation(Form_Validator_Interface $validator);

   /**
    * Metoda přidá elemntu filtr, který upraví výstup elementu
    * @param Form_Filter_Interface $filter -- typ filtru
    */
   public function addFilter(Form_Filter_Interface $filter);

   /**
    * Metoda nastaví hodnoty do prvku
    * @param mixed $values -- hodnoty
    * @return Form_Element
    */
   public function setValues($values);

   /**
    * Metoda nastaví jestli je prvek vícerozměrný
    * @param string $name -- (option) název prvku pro pole
    * @@return Form_Element
    */
   public function setDimensional($name = null);

   /**
    * Metoda přidá popisek k validaci
    * @param string $text -- popisek
    */
   public function addValidationConditionLabel($text);

}
?>
