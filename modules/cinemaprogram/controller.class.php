<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class CinemaProgram_Controller extends Controller {
   const IMDB_LINK_ID = 'http://www.imdb.com/title/tt{ID}/';
   const CSFD_LINK_ID = 'http://www.csfd.cz/film/{ID}-nazev-filmu/';

   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      $model = new CinemaProgram_Model_Detail();
      if($this->getRights()->isWritable()) {
         $delForm = new Form('movie_');

         $elemId = new Form_Element_Hidden('id');
         $delForm->addElement($elemId);

         $elemSubmit = new Form_Element_SubmitImage('delete');
         $delForm->addElement($elemSubmit);

         if($delForm->isValid()) {
            // načtení filmu
            $movie = $model->getMovie($delForm->id->getValues());
            if($movie == false) return false;

            if($movie->{CinemaProgram_Model_Detail::COL_IMAGE} != null) {
               $file = new Filesystem_File($movie->{CinemaProgram_Model_Detail::COL_IMAGE},
                       $this->category()->getModule()->getDataDir());
               $file->delete();
            }

            // smazání časů
            $model->deleteTimes($movie->{CinemaProgram_Model_Detail::COL_ID});
            $model->deleteMovie($movie->{CinemaProgram_Model_Detail::COL_ID});

            $this->infoMsg()->addMessage($this->_('Film byl smazán'));
            $this->link()->reload();
         }
      }

      // odkaz zpět
      $this->link()->backInit();

      $day = $this->getRequest('day', date('d'));
      $deltaDay = $this->category()->getModule()->getParam('days', 14);

      $curDate = new DateTime($this->getRequest('year', date('Y'))."-"
                      .$this->getRequest('month', date('m'))."-".$this->getRequest('day', date('d')));
      $toDate = clone $curDate;
      $toDate->modify("+".$deltaDay." day");

      // viewer
      $this->view()->template()->cmodel = $model;
//      $this->view()->template()->movies = $model->getMovies($curDate, $toDate);
      $this->view()->template()->times = $model->getTimesWithMovies($curDate, $toDate);
      $this->view()->template()->curDate = $curDate;
      $this->view()->template()->toDate = $toDate;
      $this->view()->template()->deltaDay = $deltaDay;
      $prvDate = clone $curDate;
      $prvDate->modify("-".$deltaDay." day");
      $this->view()->template()->linkBack = $this->link()->route('normaldate',
              array('day' => $prvDate->format('j') , 'month' => $prvDate->format('n'),
              'year' => $prvDate->format('Y')));
      $this->view()->template()->linkNext = $this->link()->route('normaldate',
              array('day' => $toDate->format('j') , 'month' => $toDate->format('n'),
              'year' => $toDate->format('Y')));
   }

   public function detailController() {
      $this->checkReadableRights();

      $model = new CinemaProgram_Model_Detail();
      $movie = $model->getMovie($this->getRequest('id'));
      if($movie == false) return false;

      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 0);


      $this->view()->movie = $movie;
      $this->view()->times = $model->getTimes($this->getRequest('id'));
   }

   public function addController() {
      //		Kontrola práv
      $this->checkWritebleRights();

      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 0);

      $form = $this->createForm();
      $form->removeElement('image_del');
      if($form->isValid()) {
         $model = new CinemaProgram_Model_Detail();
         $imgName = null;
         if($form->image->getValues() !== null) {
            $image = new Filesystem_File_Image($form->image, $this->getModule()->getDataDir());
            $imgName = $image->getName();
         }

         $instId = $model->saveMovie($form->name->getValues(), $form->label->getValues(),
                 $form->price->getValues(), $form->length->getValues(), $form->type->getValues(),
                 $form->originalname->getValues(), $form->filmclub->getValues(),
                 $form->access->getValues(), $form->imdbid->getValues(), 
                 $form->csfdid->getValues(), $imgName, $form->critique->getValues(),
                 $form->orderlink->getValues());

         $dates = $form->date->getValues();
         $times = $form->time->getValues();

         foreach ($dates as $key => $date) {
            $model->saveTime($date, $times[$key], $instId);
         }
         $this->infoMsg()->addMessage($this->_('Film byl uložen'));
         $this->view()->template()->linkBack->reload();
      }
      // viewer
      $this->view()->template()->form = $form;
      $this->view()->template()->edit = false;
   }

   public function editController() {
      //		Kontrola práv
      $this->checkWritebleRights();
      $form = $this->createForm();

      $model = new CinemaProgram_Model_Detail();
      $movie = $model->getMovie($this->getRequest('id'));
      if($movie == false) return false;

      $form->name->setValues($movie->{CinemaProgram_Model_Detail::COL_NAME});
      $form->originalname->setValues($movie->{CinemaProgram_Model_Detail::COL_NAME_ORIG});
      $form->label->setValues($movie->{CinemaProgram_Model_Detail::COL_LABEL});
      $form->length->setValues($movie->{CinemaProgram_Model_Detail::COL_LENGTH});
      $form->type->setValues($movie->{CinemaProgram_Model_Detail::COL_VERSION});

      $form->imdbid->setValues($movie->{CinemaProgram_Model_Detail::COL_IMDBID});
      $form->csfdid->setValues($movie->{CinemaProgram_Model_Detail::COL_CSFDID});

      $form->price->setValues($movie->{CinemaProgram_Model_Detail::COL_PRICE});
      $form->access->setValues($movie->{CinemaProgram_Model_Detail::COL_ACCESS});
      $form->filmclub->setValues($movie->{CinemaProgram_Model_Detail::COL_FC});

      $form->critique->setValues($movie->{CinemaProgram_Model_Detail::COL_CRITIQUE});
      $form->orderlink->setValues($movie->{CinemaProgram_Model_Detail::COL_ORDER_LINK});

      // časy promítání
      $times = $model->getTimes($this->getRequest('id'));

      $arr = array('ids' => array(), 'dates' => array(), 'times' => array());
      while ($time = $times->fetch()) {
         $date = new DateTime($time->{CinemaProgram_Model_Detail::COL_T_DATE});
         array_push($arr['dates'], strftime("%x", $date->format("U")));
         array_push($arr['ids'], $time->{CinemaProgram_Model_Detail::COL_T_ID});
         $t = new DateTime($time->{CinemaProgram_Model_Detail::COL_T_TIME});
         array_push($arr['times'], $t->format("H:i"));
      }
      $form->dateId->setValues($arr['ids']);
      $form->date->setValues($arr['dates']);
      $form->time->setValues($arr['times']);

      if($movie->{CinemaProgram_Model_Detail::COL_IMAGE} == null){
         $form->removeElement('image_del');
      }

      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 1);

      if($form->isValid()) {
         $model = new CinemaProgram_Model_Detail();
         $imgName = $movie->{CinemaProgram_Model_Detail::COL_IMAGE};
         // samže starý obrázek
         if($form->image->getValues() !== null OR (isset($form->image_del) AND $form->image_del->getValues() == true)) {
            $file = new Filesystem_File($movie->{CinemaProgram_Model_Detail::COL_IMAGE},
                    $this->category()->getModule()->getDataDir());

            $file->delete();
            $imgName = null;
         }
         if($form->image->getValues() !== null) {
            $image = new Filesystem_File_Image($form->image, $this->getModule()->getDataDir());
            $imgName = $image->getName();
         }

         $model->saveMovie($form->name->getValues(), $form->label->getValues(),
                 $form->price->getValues(), $form->length->getValues(), $form->type->getValues(),
                 $form->originalname->getValues(),
                 $form->filmclub->getValues(), $form->access->getValues(),
                 $form->imdbid->getValues(), $form->csfdid->getValues(),
                 $imgName, $form->critique->getValues(), $form->orderlink->getValues(), $this->getRequest('id'));

         $dates = $form->date->getValues();
         $datesIds = $form->dateId->getValues();
         $times = $form->time->getValues();
         $datesDeleted = $form->dateDeleted->getValues();

         foreach ($datesIds as $key => $date) {
            if($datesDeleted[$key] == 'false') {
               if($datesIds[$key] == 'new') {
                  // vložení nového
                  $model->saveTime($dates[$key], $times[$key], $this->getRequest('id'));
               } else {
                  // uprava
                  $model->saveTime($dates[$key], $times[$key], $this->getRequest('id'), $datesIds[$key]);
               }
            } else {
               // mazani
               $model->deleteTime($datesIds[$key]);
            }
         }
         $this->infoMsg()->addMessage($this->_('Film byl uložen'));

         $this->view()->template()->linkBack->reload();
      }
      // viewer
      $this->view()->template()->form = $form;
      $this->view()->template()->movie = $movie;
      $this->view()->template()->edit = true;
   }


   /**
    * MEtoda vytvoří formulář pro editaci filmu
    * @return Form
    */
   private function createForm() {
      $form = new Form('movie_');
      // MOVIE information
      $form->addGroup('filmdetail', $this->_('Informace o filmu'));
      $elemName = new Form_Element_Text('name', $this->_('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName,'filmdetail');

      $elemOName = new Form_Element_Text('originalname', $this->_('Originální název'));
      $form->addElement($elemOName,'filmdetail');

      $elemLabel = new Form_Element_TextArea('label', $this->_('Popis'));
      $elemLabel->setSubLabel($this->_('Režie, herci, země původu, atd.'));
      $elemLabel->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemLabel,'filmdetail');

      $elemLen = new Form_Element_Text('length', $this->_('Délka (min)'));
      $elemLen->addValidation(new Form_Validator_NotEmpty());
      $elemLen->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemLen,'filmdetail');

      $elemType = new Form_Element_Select('type', $this->_('Znění'));
      $types = array('Česky' => 'czech', 'Původní s titulky' => 'origwsubtitles',
              'Původní' => 'original','Slovensky' => 'slovakia','Dabované' => 'dabbing');
      $elemType->setOptions($types);
      $form->addElement($elemType,'filmdetail');

      $elemImage = new Form_Element_File('image', $this->_('Obrázek'));
      $elemImage->addValidation(New Form_Validator_FileExtension('jpg;jpeg;png;gif'));
      $elemImage->setUploadDir($this->getModule()->getDataDir());
      $form->addElement($elemImage,'filmdetail');

      $elemImageDel = new Form_Element_Checkbox('image_del', $this->_('Smazat uložený obrázek'));
      $form->addElement($elemImageDel,'filmdetail');

      $elemIMDBID = new Form_Element_Text('imdbid', $this->_('Id filmu na imdb'));
      $elemIMDBID->setSubLabel($this->_('ID z odkazu imdb např.: ').self::IMDB_LINK_ID);
      $elemIMDBID->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemIMDBID,'filmdetail');

      $elemCSFD = new Form_Element_Text('csfdid', $this->_('Id filmu na csfd'));
      $elemCSFD->setSubLabel($this->_('ID z odkazu čsfd např.: ').self::CSFD_LINK_ID);
      $elemCSFD->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemCSFD,'filmdetail');

      $elemCritique = new Form_Element_Text('critique', $this->_('Odkaz na recenzi'));
      $elemCritique->addValidation(new Form_Validator_Url());
      $elemCritique->addFilter(new Form_Filter_Url());
      $form->addElement($elemCritique,'filmdetail');

      $elemOrderlink = new Form_Element_Text('orderlink', $this->_('Odkaz na rezervaci'));
      $elemOrderlink->addValidation(new Form_Validator_Url());
      $elemOrderlink->addFilter(new Form_Filter_Url());
      $form->addElement($elemOrderlink,'filmdetail');

      // promítání
      $form->addGroup('filmshowdetail', $this->_('Informace o promítání'));

      $elemPrice = new Form_Element_Text('price', $this->_('Cena (Kč)'));
      $elemPrice->addValidation(new Form_Validator_NotEmpty());
      $elemPrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemPrice,'filmshowdetail');

      $elemFilmClub = new Form_Element_Checkbox('filmclub', $this->_('Filmový klub'));
      $form->addElement($elemFilmClub,'filmshowdetail');

      $accesibility = array(_('Žádná') => 0, 12 => 12, 15 => 15, 18 => 18);
      $elemAcces = new Form_Element_Select('access', $this->_('Věková hranice'));
      $elemAcces->setOptions($accesibility);
      $form->addElement($elemAcces,'filmshowdetail');

      // Časové hranice
      $elemDate = new Form_Element_Text('date', $this->_('Datum'));
      $elemDate->setDimensional();
      $elemDate->addValidation(new Form_Validator_NotEmpty());
      $elemDate->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($elemDate,'filmshowdetail');

      $elemDateDeleted = new Form_Element_Hidden('dateDeleted');
      $elemDateDeleted->setDimensional();
      $form->addElement($elemDateDeleted,'filmshowdetail');

      $elemDateId = new Form_Element_Hidden('dateId');
      $elemDateId->setDimensional();
      $form->addElement($elemDateId,'filmshowdetail');

      $elemTime = new Form_Element_Text('time', $this->_('Čas'));
      $elemTime->setDimensional();
      $elemTime->addValidation(new Form_Validator_NotEmpty());
      $elemTime->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($elemTime,'filmshowdetail');
      // EOF časové hranice
      $form->addGroup('buttons', $this->_('Akce formuláře'));
      $elemSubmit = new Form_Element_Submit('send', $this->_('Uložit'));
      $form->addElement($elemSubmit, 'buttons');

      return $form;
   }

   public function featuredListController() {
      $model = new CinemaProgram_Model_Detail();

      $this->view()->movies = $model->getFeaturedMovies();

   }

   public function showDataController() {
      $this->checkReadableRights();
            // načtení článku
      $model = new CinemaProgram_Model_Detail();
      $this->view()->movie = $model->getMovie($this->getRequest('id'));
      if($this->view()->movie == false) return false;

      $this->view()->output = $this->getRequest('output');
   }

   public function currentMovieController() {
      $model = new CinemaProgram_Model_Detail();
      $this->view()->movie = $model->getCurrentMovie();
//      var_dump($this->view()->movie);
//      if($this->view()->action === false) return false;
   }
}

?>