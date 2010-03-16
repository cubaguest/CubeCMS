<?php
class CinemaProgram_Panel extends Panel {
	public function panelController() {
      $model = new CinemaProgram_Model_Detail();
      $toDate = new DateTime();
      $toDate->modify('+1 day');
      $this->template()->movies = $model->getTimesWithMovies(new DateTime(), new DateTime());
      if($this->template()->movies === false) return false;
	}
	
	public function panelView() {
      $this->template()->webdatadir = $this->category()->getModule()->getDataDir(true);
      $this->template()->addTplFile('panel_actual.phtml');
	}

   public static function settingsController(&$settings,Form &$form) {
   }
}
?>