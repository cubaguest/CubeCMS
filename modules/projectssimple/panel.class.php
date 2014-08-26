<?php
class ProjectsSimple_Panel extends Panel {
   const DEFAULT_NUM_ARTICLES = 3;
   const DEFAULT_TYPE = 'list';
   const PARAM_TPL_PANEL = 'tplpanel';


   public function panelController() {
      $modelProjects = new Projects_Model_Projects();
      
      // načteme sekci, pokud žádná sekce není, není ani projekt
      $projects = $modelProjects->joinFK(Projects_Model_Projects::COLUMN_ID_SECTION, array(Projects_Model_Sections::COLUMN_ID_CATEGORY))
         ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY.' = :ids', array( 'ids' => $this->category()->getId() ))
         ->order(array( Projects_Model_Projects::COLUMN_ORDER))
         ->limit(0, 50)
         ->records();
      
      $this->template()->projects = $projects;
      $this->template()->dataDir = $this->category()->getModule()->getDataDir(true);
   }

   public function panelView() {
      $this->template()->addFile('tpl://'.$this->category()->getParam(self::PARAM_TPL_PANEL, 'panel.phtml'));
      $this->template()->rssLink = $this->link()->clear()->route().Url_Request::URL_FILE_RSS;
   }

//   public static function settingsController(&$settings,Form &$form) {
//   }
}