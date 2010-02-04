<?php
class NavigationMenu_Panel extends Panel {
	
	public function panelController() {
	}
	
	public function panelView() {
      $model = new NavigationMenu_Models_List();
      switch ($this->panelObj()->{Model_Panel::COLUMN_TPL}) {
         case 'panel_pages.phtml':
            $this->template()->links = $model->getSubdomainsList();
            $this->template()->type = 'pages';
            break;
         case 'panel_projects.phtml':
            $this->template()->links = $model->getProjectsList();
            $this->template()->type = 'projects';
            break;
         case 'panel_groups.phtml':
            $this->template()->links = $model->getGroupsList();
            $this->template()->type = 'groups';
            break;
         case 'panel_partners.phtml':
            $this->template()->links = $model->getPartnersList();
            $this->template()->type = 'partners';
            break;
         case 'panel.phtml':
         default:
            $this->template()->links = $model->getList();
            $this->template()->type = 'all';
            break;
      }
      $this->template()->addTplFile($this->panelObj()->{Model_Panel::COLUMN_TPL});
	}
}
?>