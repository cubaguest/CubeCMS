<?php
class PartnersPanel extends Panel {
   const PARAM_NUM_PARTNERS = 'numinpanel';
   const PARAM_LOGO_WIDTH = 'panellogowidth';
   const PARAM_LOGO_HEIGHT = 'panellogoheight';

   private $partnersArray = array();

   private $dirToImages = null;

   public function panelController() {
      $partnersM = new PartnersListModel();
      $numPartners = $this->getModule()->getParam(self::PARAM_NUM_PARTNERS);
      if($numPartners != 0 AND $numPartners != NULL){
         $this->partnersArray = $partnersM->getRandomPartners($this->getModule()
            ->getParam(self::PARAM_NUM_PARTNERS));
      } else {
         $this->partnersArray = $partnersM->getPartners();
      }

      $this->dirToImages = $this->getModule()->getDir()->getDataDir();

   }

   public function panelView() {
      $this->template()->addTpl("panel.tpl");

      $this->template()->addVar("PARTNERS_ARRAY", $this->partnersArray);
      $this->template()->addVar("PARTNERS_LINK", $this->getLink());
      $this->template()->addVar("PARTNERS_LINK_NAME", _("Další partneři"));
      $this->template()->addVar('DIR_TO_IMAGES', $this->dirToImages);

      $this->template()->addVar('LOGO_WIDTH', $this->getModule()->getParam(self::PARAM_LOGO_WIDTH));
      $this->template()->addVar('LOGO_HEIGHT', $this->getModule()->getParam(self::PARAM_LOGO_HEIGHT));
   }
}
?>