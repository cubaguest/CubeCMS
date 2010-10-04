<?php
/**
 * Třída JsPluginu TreeTable.
 * Třída pro vkládání pluginu zobrazení stromu v tabulce
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.4 r4 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu TreeTable
 * @see           http://github.com/ludo/jquery-plugins/tree/master/treeTable/
 */

class JsPlugin_TreeTable extends JsPlugin {
	protected function initJsPlugin() {
	}
	
	protected function setFiles() {
      $jquery = new JsPlugin_JQuery();
      $this->addDependJsPlugin($jquery);
		$this->addFile(new JsPlugin_CssFile('jquery.treeTable.css'));
		$this->addFile(new JsPlugin_JsFile("jquery.treeTable.min.js"));
	}
}
?>