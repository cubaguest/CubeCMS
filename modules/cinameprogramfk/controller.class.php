<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class CinameProgramFk_Controller extends Controller {

/**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      // datadir
      $modelC = new Model_Category();
      $cat = $modelC->getCategoryListByModule('cinemaprogram')->fetch();
      if($cat->{Model_Category::COLUMN_DATADIR} != null){
         $this->view()->datadir = $cat->{Model_Category::COLUMN_DATADIR};
      } else {
         $this->view()->datadir = $cat[Model_Category::COLUMN_URLKEY][Locale::getDefaultLang()];
      }

      $curentYear = $day = $this->getRequest('year', date('Y'));

      $model = new CinemaProgram_Model_Detail();
      $movies = $model->getMoviesFk($curentYear)->fetchAll();

      // pokud je prázdný tak zkusíme následující rok poue pokud se jedná o aktuální rok, v opačném případě vzniká smyčka
      if(empty ($movies) AND $curentYear == date('Y')){
         $this->link()->route('selYear', array('year' => $curentYear+1))->reload();
      }
      $this->view()->movies = $movies;
      $this->view()->currentYear = $curentYear;
   }

   public static function settingsController(&$settings,Form &$form) {
   }
}

?>