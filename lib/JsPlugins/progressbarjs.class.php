<?php
/**
 * Plugin ProgressBarJs -- otevření ona s progressbarem
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	ProgressBarJs class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: submitform.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída JsPluginu ProgressBarJs pro otevření okna s progresbarem
 */
class ProgressBarJs extends JsPlugin {
	protected function initJsPlugin() {
//		Název pluginu
		$this->setJsPluginName("ProgressBar");
		
//		Přidání js soubrů pluginu
		$this->addJsFile("progress_func.js");
	}
}

?>