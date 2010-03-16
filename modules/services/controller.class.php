<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Services_Controller extends Controller {
   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      $this->checkWritebleRights();


      /*
       *  CACHE
      */
      // velikost
      $cacheSize = 0;
      foreach (new DirectoryIterator(AppCore::getAppCacheDir()) as $fileInfo) {
         if($fileInfo->isDot() OR $fileInfo->getFilename() == '.svn'|'.git') continue;
         $cacheSize += $fileInfo->getSize();
      }

      $formClearCache = new Form('cache_clear_');
      $elemSub = new Form_Element_Submit('clear', $this->_('Vyčistit'));
      $formClearCache->addElement($elemSub);
      if($formClearCache->isValid()) {

         $dir = new DirectoryIterator(AppCore::getAppCacheDir());
         foreach ($dir as $fileinfo) {
            if($fileinfo->isDot() OR $fileinfo->getFilename() == '.svn'|'.git') continue;
            @unlink(AppCore::getAppCacheDir().$fileinfo->getFilename());
         }
         $this->infoMsg()->addMessage($this->_('Cache byla vyčištěna'));
         $this->link()->reload();
      }

      $this->view()->cacheSize = $cacheSize;
      if($cacheSize/(1024*1024) > 10) {
         $this->view()->isBigSize = true;
      }
      $this->view()->formClearCache = $formClearCache;

      /**
       * Optimalizace db tabulek
       */
      $formOptimiseDbTables = new Form('db_tables_optimise_');
      $elemSub = new Form_Element_Submit('optimise', $this->_('Optimalizovat'));
      $formOptimiseDbTables->addElement($elemSub);
      if($formOptimiseDbTables->isValid()) {

         $this->infoMsg()->addMessage($this->_('Tabulky v db byly optimalizovány'));
         $this->link()->reload();
      }
       $this->view()->formOptimizeDbTables = $formOptimiseDbTables;

   }
}

?>