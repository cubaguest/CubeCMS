<?php
/**
 * Třída pro vytvoření objektu nástroje (tool) pro toolbox verze 2
 * Třída vytváří objekt nástroje typu redirect (přesměrování). Tento
 * nástroj po kliknutí přesměruje na danou akci
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Rozhraní pro tvorbu nástroje toolboxu redirect
 */

class Template_Toolbox2_Tool_Form extends Template_Toolbox2_Tool implements Template_Toolbox2_Tool_Interface {
   /**
    * Název form checkeru
    * @var Form
    */
   private $form = null;

   public function  __construct(Form $form) {
      $this->action = $form->getAction();
      $this->name = $form->getPrefix();
      $this->form = $form;
   }

   /**
    * Metoda vrací uložený formulář podle kterého se generují data
    * @return Form
    */
   public function getForm() {
      return $this->form;
   }
   
   /**
    * Metoda nastaví formulář podle kterého se generují data
    * @param Form $form - formulář pro generování dat
    * @return Template_Toolbox2_Tool_Form
    */
   public function setForm(Form $form) {
      $this->form = $form;
      return $this;
   }

}
?>
