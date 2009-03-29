<?php
/**
 * Třída JsPluginu ProgressBarJs.
 * Třída slouží pro práci s progressbarem. Ten je otevřen v novém okně 
 * a zobrazuje průběh zpracování dat.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: progressbar.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída JsPluginu ProgressBarJs pro otevření okna s progresbarem
 */

class ProgressBarJs extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("ProgressBar");
	}

	protected function initFiles() {
		$file = new JsPluginJsFile("progress_func.js");

		$this->addJsFile($file);
	}

	protected function generateFile() {
		;
	}
}

?>