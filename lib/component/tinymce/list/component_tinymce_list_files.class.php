<?php
/**
 * Třída pro komnponenty Wysiwing editoru TinyMCE
 */
class Component_TinyMCE_List_Files extends Component_TinyMCE_List {
   protected $filesFilter = '(?:doc|dot|docx|dotx|xls|xlt|xlsx|xltx|ppt|pot|pptx|potx|odf|otf|ods|ots|odp|otp|zip|rar|pdf)';

   protected function  loadItems() {

      // user files
      $dir = AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_HOME.DIRECTORY_SEPARATOR.Auth::getUserName().DIRECTORY_SEPARATOR;
      if(is_dir($dir)){
         $dirIter = new RecursiveDirectoryIterator($dir);
         $iterator = new RecursiveIteratorIterator($dirIter);
         $files = new RegexIterator($iterator, '/^.+\.'.$this->filesFilter.'$/i',RecursiveRegexIterator::GET_MATCH);

         foreach($files as $file)
         {
            $file = str_replace(array(AppCore::getAppDataDir(), DIRECTORY_SEPARATOR), array('', '/'), $file[0]);
            $this->addItem($this->tr('Data osobní: ').$file, Url_Request::getBaseWebDir().VVE_DATA_DIR.'/'.$file);
         }
      }

      if(is_dir(AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_PUBLIC)){
         $dirIter = new RecursiveDirectoryIterator(AppCore::getAppDataDir().Component_TinyMCE_Browser::DIR_PUBLIC);
         $iterator = new RecursiveIteratorIterator($dirIter);
         $files = new RegexIterator($iterator, '/^.+\.'.$this->filesFilter.'$/i',RecursiveRegexIterator::GET_MATCH);
         // public files
         foreach($files as $file) {
            $file = str_replace(array(AppCore::getAppDataDir(), DIRECTORY_SEPARATOR), array('', '/'), $file[0]);
            $this->addItem($this->tr('Data veřejná: ').$file, Url_Request::getBaseWebDir().VVE_DATA_DIR.'/'.$file);
         }
      }
   }

}
?>
