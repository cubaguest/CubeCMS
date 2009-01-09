<?php
/**
 * Třída pro obsluhu formuláře
 * Třída implementuje řešení pro obsluhu formulářových prvku. Umožňuje kontrolu
 * jejich odeslání, správného vyplnění zadaných dat, jejich načtení a upráva.
 * Lze pomocí ní také vybrat data z formuláře a rovnou předat modelu pro zápis.
 * Umožňuje také generování podle jazykového nastavení
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: form.class.php 434 2008-12-30 00:35:31Z jakub $ VVE3.3.0 $Revision: 434 $
 * @author		$Author: jakub $ $Date: 2008-12-30 01:35:31 +0100 (Tue, 30 Dec 2008) $
 *				$LastChangedBy: jakub $ $LastChangedDate: 2008-12-30 01:35:31 +0100 (Tue, 30 Dec 2008) $
 * @abstract 	Třída pro obsluhu formulářových prvků
 * @todo		Dodělat další validace, implementovat ostatní prvky formulářů
 */
class Form {


    /**
     * Proměná obsahuje, jestli bylo v zadání formuláře chyba
     * @var boolean
     */
    private $isError = false;

    /**
     * Název prvku, ve kterém byla provedena chyba
     * @var string
     */
    private $errorItem = null;

    	/**
	 * Obejt pro informační hlášky
	 * @var Messages
	 */
	private $infomsg = null;

	/**
	 * Obejt pro chybové hlášky hlášky
	 * @var Messages
	 */
	private $errmsg = null;

	/**
	 * Objekt modulu
	 * @var Module
	 */
	private $module = null;

    /**
     * Prefix pro formulářové prvky první úrovně
     * @var string
     */
    private $formPrefix = null;

	/**
	 * Konstruktor nastaví základní parametry
     * @param string $formPrefix -- prefix formulářových prvků první úrovně
	 */
	final public function  __construct($formPrefix = null) {
		if(AppCore::getSelectedModule() instanceof Module){
			$this->module = AppCore::getSelectedModule();
		}

		if(AppCore::getModuleMessages() instanceof Messages){
			$this->infomsg = AppCore::getModuleMessages();
		}

		if(AppCore::getModuleErrors() instanceof Messages){
			$this->errmsg = AppCore::getModuleErrors();
		}

        $this->formPrefix = $formPrefix;
	}

	/**
	 * Metoda vrací objekt s informačními zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function infoMsg() {
		return $this->infomsg;
	}

	/**
	 * Metoda vrací objekt s chybovými zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function errMsg() {
		return $this->errmsg;
	}

}
?>
