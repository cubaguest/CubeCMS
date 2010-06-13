<?php
/**
 * Rozhraní pro vytvoření objektu nástroje (tool) pro toolbox verze 2
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Rozhraní pro tvorbu nástroje toolboxu
 */

interface Template_Toolbox2_Tool_Interface {

   /**
    * Metoda vrací název nástroje
    * @return string
    */
   public function getName();

   /**
    * Metoda nastaví titulek
    * @param string $title -- titulek nástroje
    * @return Template_Toolbox2_Tool
    */
   public function setTitle($title);

   /**
    * Metoda vrací titulek
    * @return string
    */
   public function getTitle();

   /**
    * Metoda nastaví ikonu
    * @param string $icon -- název ikony
    * @return Template_Toolbox2_Tool
    */
   public function setIcon($icon);

   /**
    * Metoda vrací název ikony
    * @return string
    */
   public function getIcon();

   /**
    * Metoda nastaví akci
    * @param Url_Link $title -- titulek nástroje
    * @return Template_Toolbox2_Tool
    */
   public function setAction(Url_Link $action);

   /**
    * Metoda vrací akci
    * @return Url_Link/string
    */
   public function getAction();

   /**
    * Metoda nastaví název nástroje
    * @param string $label -- název nástroje
    * @return Template_Toolbox2_Tool
    */
   public function setLabel($label);

   /**
    * Metoda vrací label
    * @return label
    */
   public function getLabel();

   /**
    * Metoda přidá další proměnné, které budou odeslány spolu s formulářem
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    * @return Template_Toolbox2_Tool
    */
   public function setSubmitValue($name, $value);

   /**
    * Metoda vrátí další hodnoty odeslané s formem
    * @return array
    */
   public function getSubmitValues();
}
?>
