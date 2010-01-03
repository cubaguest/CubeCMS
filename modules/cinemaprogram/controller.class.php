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

      $day = $this->getRequest('day', date('d'));

      $curDate = new DateTime($this->getRequest('year', date('Y'))."-"
              .$this->getRequest('month', date('m'))."-".$this->getRequest('day', date('d')));
      $toDate = clone $curDate;
      $toDate->modify("+1 month");

      
      // viewer
      $this->view()->template()->cmodel = $model;
      $this->view()->template()->movies = $model->getMovies($this->category()->getId(), $curDate, $toDate);
      $this->view()->template()->curDate = $curDate;
      $this->view()->template()->toDate = $toDate;
   }

   public function addController() {
   //		Kontrola práv
      $this->checkWritebleRights();

      $form = $this->createForm();

      //      $form->dateId->setValues($arr);
      //      $form->datestop->setValues($arrDates);
      //      $form->datestart->setValues($arrDates);

      if($form->isValid()) {
         $model = new CinemaProgram_Model_Detail();
         $imgName = null;
         if($form->image->getValues() !== null) {
            $image = new Filesystem_File_Image($form->image, $this->getModule()->getDataDir());
            $imgName = $image->getName();
         }

         $instId = $model->saveMovie($form->name->getValues(), $form->label->getValues(),
            $form->price->getValues(), $form->length->getValues(), $form->type->getValues(),
            $this->category()->getId(), $form->originalname->getValues(), $form->filmclub->getValues(),
             $form->access->getValues(), $form->imdbid->getValues(), $form->csfdid->getValues(), $imgName);

         $datesFrom = $form->datestart->getValues();
         $datesTo = $form->datestop->getValues();
         $times = $form->time->getValues();

         $timeM = new CinemaProgram_Model_TimeDetail();
         foreach ($datesFrom as $key => $dateF) {
            if($datesTo[$key] == null){
               $datesTo[$key] = $dateF;
            }
            $timeM->saveTime($dateF, $datesTo[$key], $times[$key], $instId);
         //             $dateFrom = new DateTime($dateF);
//                      print ($dateF->format("Y-m-d")." - ");
//         ////             print ($dateFrom->format("Y-m-d")." - ");
//         ////             if($datesTo[$key] != null){
//         ////               $dateTo = new DateTime($datesTo[$key]);
//         ////               print ($dateTo->format("Y-m-d")." - ");
//                        if($datesTo[$key] != null){
//                        print ($datesTo[$key]->format("Y-m-d")." - ");
//                        } else {
//                        print (" null - ");
//                        }
//         //             }
//         //             $time = new DateTime($times[$key]);
//                      print ($times[$key]->format("H:i")."<br >");
         }

      //          var_dump($datesFrom);
      //          var_dump($datesTo);
      //          var_dump($times);

                $this->infoMsg()->addMessage($this->_('Film byl uložen'));
                $this->link()->route()->reload();
      }


      // viewer
      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile("edit.phtml");
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
      $types = array('Česky' => 'czech', 'Originální s titulky' => 'origwsubtitles',
          'Originální' => 'original','Dabované' => 'dabbing');
      $elemType->setOptions($types);
      $form->addElement($elemType,'filmdetail');



      $elemImage = new Form_Element_File('image', $this->_('Obrázek'));
      $elemImage->addValidation(New Form_Validator_FileExtension('jpg;jpeg; png; gif'));
      $elemImage->setUploadDir($this->getModule()->getDataDir());
      $form->addElement($elemImage,'filmdetail');

      $elemIMDBID = new Form_Element_Text('imdbid', $this->_('Id filmu na imdb'));
      $elemIMDBID->setSubLabel($this->_('ID z odkazu imdb např.: ').self::IMDB_LINK_ID);
      $elemIMDBID->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemIMDBID,'filmdetail');

      $elemCSFD = new Form_Element_Text('csfdid', $this->_('Id filmu na csfd'));
      $elemCSFD->setSubLabel($this->_('ID z odkazu čsfd např.: ').self::CSFD_LINK_ID);
      $elemCSFD->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemCSFD,'filmdetail');

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
      $elemDateStart = new Form_Element_Text('datestart', $this->_('Od'));
      $elemDateStart->setDimensional();
      $elemDateStart->addValidation(new Form_Validator_NotEmpty());
      $elemDateStart->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($elemDateStart,'filmshowdetail');

      $elemDateStop = new Form_Element_Text('datestop', $this->_('Do'));
      $elemDateStop->setDimensional();
      $elemDateStop->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($elemDateStop,'filmshowdetail');

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
}

?>