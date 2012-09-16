<?php 
/** Adminer customization allowing usage of plugins
 * @link http://www.adminer.org/plugins/#use
 * @author Jakub Vrana, http://www.vrana.cz/
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class AdminerPlugin extends Adminer {
   /** @access protected */
   var $plugins;

   function _findRootClass($class) { // is_subclass_of(string, string) is available since PHP 5.0.3
      do {
         $return = $class;
      } while ($class = get_parent_class($class));
      return $return;
   }

   /** Register plugins
    * @param array object instances or null to register all classes starting by 'Adminer'
    */
   function AdminerPlugin($plugins) {
      if ($plugins === null) {
         $plugins = array();
         foreach (get_declared_classes() as $class) {
            if (preg_match('~^Adminer.~i', $class) && strcasecmp($this->_findRootClass($class), 'Adminer')) { // can use interface since PHP 5
               $plugins[$class] = new $class;
            }
         }
      }
      $this->plugins = $plugins;
      // it is possible to use ReflectionObject in PHP 5 to find out which plugins defines which methods at once
   }

   function _callParent($function, $args) {
      switch (count($args)) { // call_user_func_array(array('parent', $function), $args) works since PHP 5
         case 0: return parent::$function();
         case 1: return parent::$function($args[0]);
         case 2: return parent::$function($args[0], $args[1]);
         case 3: return parent::$function($args[0], $args[1], $args[2]);
         case 4: return parent::$function($args[0], $args[1], $args[2], $args[3]);
         case 5: return parent::$function($args[0], $args[1], $args[2], $args[3], $args[4]);
         case 6: return parent::$function($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
         default: trigger_error('Too many parameters.', E_USER_WARNING);
      }
   }

   function _applyPlugin($function, $args) {
      foreach ($this->plugins as $plugin) {
         if (method_exists($plugin, $function)) {
            switch (count($args)) { // call_user_func_array() doesn't work well with references
               case 0: $return = $plugin->$function(); break;
               case 1: $return = $plugin->$function($args[0]); break;
               case 2: $return = $plugin->$function($args[0], $args[1]); break;
               case 3: $return = $plugin->$function($args[0], $args[1], $args[2]); break;
               case 4: $return = $plugin->$function($args[0], $args[1], $args[2], $args[3]); break;
               case 5: $return = $plugin->$function($args[0], $args[1], $args[2], $args[3], $args[4]); break;
               case 6: $return = $plugin->$function($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]); break;
               default: trigger_error('Too many parameters.', E_USER_WARNING);
            }
            if ($return !== null) {
               return $return;
            }
         }
      }
      return $this->_callParent($function, $args);
   }

   function _appendPlugin($function, $args) {
      $return = $this->_callParent($function, $args);
      foreach ($this->plugins as $plugin) {
         if (method_exists($plugin, $function)) {
            $return += call_user_func_array(array($plugin, $function), $args);
         }
      }
      return $return;
   }

   // appendPlugin

   function dumpFormat() {
      $args = func_get_args();
      return $this->_appendPlugin(__FUNCTION__, $args);
   }

   function dumpOutput() {
      $args = func_get_args();
      return $this->_appendPlugin(__FUNCTION__, $args);
   }

   function editFunctions() {
      $args = func_get_args();
      return $this->_appendPlugin(__FUNCTION__, $args);
   }

   // applyPlugin

   function name() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function credentials() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function permanentLogin() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function database() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function databases() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function queryTimeout() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function headers() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function head() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function loginForm() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function login() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function tableName() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function fieldName() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectLinks() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function foreignKeys() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function backwardKeys() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function backwardKeysPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectQuery() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function rowDescription() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function rowDescriptions() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectVal() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function editVal() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectColumnsPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectSearchPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectOrderPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectLimitPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectLengthPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectActionPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectCommandPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectImportPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectEmailPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectColumnsProcess() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectSearchProcess() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectOrderProcess() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectLimitProcess() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectLengthProcess() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectEmailProcess() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function selectQueryBuild() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function messageQuery() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function editInput() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function processInput() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function dumpTable() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function dumpData() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function dumpFilename() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function dumpHeaders() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function homepage() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function navigation() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function databasesPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }

   function tablesPrint() {
      $args = func_get_args();
      return $this->_applyPlugin(__FUNCTION__, $args);
   }
}
/** Allow using Adminer inside a frame (disables ClickJacking protection)
 * @link http://www.adminer.org/plugins/#use
 * @author Jakub Vrana, http://www.vrana.cz/
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class AdminerFrames {
   /** @access protected */
   var $sameOrigin;

   /**
    * @param bool allow running from the same origin only
    */
   function AdminerFrames($sameOrigin = false) {
      $this->sameOrigin = $sameOrigin;
   }

   function headers() {
      if ($this->sameOrigin) {
         header("X-Frame-Options: SameOrigin");
      }
      header("X-XSS-Protection: 0");
      return false;
   }

}

/** Edit fields ending with "_path" by <input type="file"> and link to the uploaded files from select
 * @link http://www.adminer.org/plugins/#use
 * @author Jakub Vrana, http://www.vrana.cz/
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class AdminerFileUpload {
   /** @access protected */
   var $uploadPath, $displayPath, $extensions;

   /**
    * @param string prefix for uploading data (create writable subdirectory for each table containing uploadable fields)
    * @param string prefix for displaying data, null stands for $uploadPath
    * @param string regular expression with allowed file extensions
    */
   function AdminerFileUpload($uploadPath = "../static/data/", $displayPath = null, $extensions = "[a-zA-Z0-9]+") {
      $this->uploadPath = $uploadPath;
      $this->displayPath = ($displayPath !== null ? $displayPath : $uploadPath);
      $this->extensions = $extensions;
   }

   function editInput($table, $field, $attrs, $value) {
      if (ereg('(.*)_path$', $field["field"])) {
         return "<input type='file' name='fields-$field[field]'>";
      }
   }

   function processInput($field, $value, $function = "") {
      if (ereg('(.*)_path$', $field["field"], $regs)) {
         $table = ($_GET["edit"] != "" ? $_GET["edit"] : $_GET["select"]);
         $name = "fields-$field[field]";
         if ($_FILES[$name]["error"] || !ereg("(\\.($this->extensions))?\$", $_FILES[$name]["name"], $regs2)) {
            return false;
         }
         //! unlink old
         $filename = uniqid() . $regs2[0];
         if (!move_uploaded_file($_FILES[$name]["tmp_name"], "$this->uploadPath$table/$regs[1]-$filename")) {
            return false;
         }
         return q($filename);
      }
   }

   function selectVal($val, &$link, $field) {
      if ($val != "&nbsp;" && ereg('(.*)_path$', $field["field"], $regs)) {
         $link = "$this->displayPath$_GET[select]/$regs[1]-$val";
      }
   }

}