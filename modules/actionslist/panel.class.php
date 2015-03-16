<?php
class ActionsList_Panel extends Panel {
	public function panelController() {
	}
	
	public function panelView() {
      $model = new Actions_Model_List();
      $actions = $model->getFeaturedActionsByCatIds($this->panelObj()->getParam('catsid', array(0)));
      switch ($this->panelObj()->getParam('type', Actions_Panel::DEFAULT_TYPE)) {
         case 'actual':
            $this->template()->addTplFile('panel_actual.phtml');
            $this->template()->action = $actions->fetch();
            if($this->template()->action === false) return false;
            if($this->template()->action->{Model_Category::COLUMN_DATADIR} != null){
               $dataDir = Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR
               .$this->template()->action->{Model_Category::COLUMN_DATADIR}.URL_SEPARATOR;
            } else {
               $dataDir = Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR
               .$this->template()->action->{Model_Category::COLUMN_MODULE}.URL_SEPARATOR;
            }

            $this->template()->datadir = $dataDir.$this->template()
                    ->action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getLang()].URL_SEPARATOR;
            break;
         case 'list':
         default:
            $this->template()->addTplFile('panel.phtml');
            $this->template()->actions = $actions->fetchAll();
            if($this->template()->action === false) return false;
            $this->template()->count = $this->panelObj()->getParam('num', Actions_Panel::DEFAULT_NUM_ACTIONS);
            break;
      }
      $this->template()->rssLink = $this->link()->route('export', array('type' => 'rss'));
	}

   public static function settingsController(&$settings,Form &$form) {
      // kategorie
      $modules = array('actions', 'actionswgal');
      $results = array();
      foreach ($modules as $module){
         $cats = Model_Category::getCategoryListByModule($module);
         foreach($cats as $cat) {
            $results[(string)$cat->{Model_Category::COLUMN_CAT_LABEL}] = $cat->{Model_Category::COLUMN_CAT_ID};
         }
      }

      $elemSelectedCategories = new Form_Element_Select('catsid', 'Kategorie ze kterých se má výbírat');
      $elemSelectedCategories->setOptions($results);
      $elemSelectedCategories->setMultiple();
      $form->addElement($elemSelectedCategories,'basic');

      if(isset($settings['catsid'])){
         $form->catsid->setValues($settings['catsid']);
      }
      // seznam počet
      Actions_Panel::settingsController($settings, $form);
      if($form->isValid()) {
         $settings['catsid'] = $form->catsid->getValues();
      }
   }
}
?>