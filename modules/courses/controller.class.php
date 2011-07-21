<?php

class Courses_Controller extends Controller {
   const PAY_TYPE_ORGANISATION = 'organisation';
   const PAY_TYPE_PRIVATE = 'private';

   const TYPE_COURSE = 'kurz';
   const TYPE_CONFERENTION = 'konf';
   const TYPE_SEMINARE = 'semin';
   
   const PARAM_SEND_ADMIN_NOTIF_DEFAULT = true;
   const PARAM_SEND_ADMIN_NOTIF = 'admin_notif';
   const PARAM_ADMINS = 'admins';
   const PARAM_OTHER_RECIPIENS = 'other_recipients';
   const PARAM_NEWSLETTER_MAIL_GRP = 'newsletter_mail_grp';

   const DEFAULT_IMG_WIDTH = 400;
   const DEFAULT_IMG_HEIGHT = 300;

   const DATA_DIR = 'courses';

   protected function init() {
      parent::init();
      $this->category()->getModule()->setDataDir(self::DATA_DIR);
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      $this->checkReadableRights();

      $model = new Courses_Model_Courses();
      $this->view()->courses = $model->getCoursesFromDate(new DateTime(), !$this->category()->getRights()->isWritable());
   }

   public function listAllCoursesController() {
      $this->checkReadableRights();
      $model = new Courses_Model_Courses();
      $this->view()->courses = $model->getCourses(!$this->category()->getRights()->isWritable());
   }

   public function archiveController() {
      $this->checkReadableRights();
      $m = new Articles_Model_List();
      $articlesAll = $m->getListAll($this->category()->getId());
      $articles = array();

      while ($row = $articlesAll->fetch()) {
         $date = new DateTime($row->{Articles_Model_Detail::COLUMN_ADD_TIME});
         $year = $date->format("Y");
         if (!isset($articles[$year])) {
            $articles[$year] = array();
         }
         array_push($articles[$year], $row);
      }

      $this->view()->articles = $articles;
      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 0);
   }

   public function showCourseController() {
      $this->checkReadableRights();

      $courseModel = new Courses_Model_Courses();
      $this->view()->course = $courseModel->getCourse($this->getRequest('urlkey'));
      if ($this->view()->course == false)
         return false;

      $regModel = new Courses_Model_Registrations();
      // registrace
      $this->view()->countReg = $regModel->getCountRegistrations($this->view()->course->{Courses_Model_Courses::COLUMN_ID});

      $this->view()->freeSeats = $this->view()->course->{Courses_Model_Courses::COLUMN_SEATS}
              - $this->view()->course->{Courses_Model_Courses::COLUMN_SEATS_BLOCKED} - $this->view()->countReg;

      // lektoři
      $this->view()->lecturers = $courseModel->getLecturers($this->view()->course->{Courses_Model_Courses::COLUMN_ID});

      // pokud je volno přidáme registraci
      if ($this->view()->freeSeats > 0 AND strtotime($this->view()->course->{Courses_Model_Courses::COLUMN_DATE_START}) > strtotime("now")) {
         $this->courseRegistration();
      }

      // privátní zóna
      $this->view()->isPrivate = false;
      if(Auth::getGroupName() == 'admin' OR
              $courseModel->isPrivateUser(Auth::getUserId(),
                      $this->view()->course->{Courses_Model_Courses::COLUMN_ID}) == true){
         $this->view()->isPrivate = true;
      }

      if ($this->category()->getRights()->isWritable()) {
         $formDelete = new Form('course_delete_');

         $eId = new Form_Element_Hidden('id');
         $eId->setValues($this->view()->course->{Courses_Model_Courses::COLUMN_ID});
         $formDelete->addElement($eId);

         $eDelete = new Form_Element_SubmitImage('delete', $this->_('Smazat položku'));
         $formDelete->addElement($eDelete);

         if ($formDelete->isValid()) {
            $courseModel->deleteCourse($formDelete->id->getValues());

            $this->infoMsg()->addMessage($this->_('Položka byla smazána'));
            $this->link()->route()->reload();
         }
         $this->view()->formDelete = $formDelete;
      }
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addCourseController() {
      $this->checkWritebleRights();
      $addForm = $this->createCourseForm();

      if($addForm->isSend() AND $addForm->save->getValues() == false){
         $this->link()->route()->reload();
      }

      if ($addForm->isValid()) {
         $model = new Courses_Model_Courses();

         // uložení nového obrázku
         $imgName = null;
         if ($addForm->image->getValues()) {
            $image = $addForm->image->createFileObject('Filesystem_File_Image');

            $image->resampleImage($this->category()->getParam('imgw', self::DEFAULT_IMG_WIDTH),
                    $this->category()->getParam('imgh', self::DEFAULT_IMG_HEIGHT), false);
            $image->save();
            $imgName = $image->getName();
         }

         $urlkeys = $addForm->urlkey->getValues();
         $names = $addForm->name->getValues();
         $idC = $model->saveCourse($addForm->name->getValues(), vve_strip_html_comment($addForm->textShort->getValues()),
                         vve_strip_html_comment($addForm->text->getValues()), vve_strip_html_comment($addForm->textPrivate->getValues()),
                         $addForm->urlkey->getValues(),
                         $addForm->metaDesc->getValues(), $addForm->metaKeywords->getValues(),
                         $addForm->dateStart->getValues(), $addForm->dateStop->getValues(),
                         $addForm->price->getValues(), $addForm->hourseLen->getValues(), $addForm->place->getValues(),
                         $addForm->seats->getValues(), $addForm->seatsBlocked->getValues(),
                         $addForm->isNew->getValues(), $addForm->inList->getValues(), $addForm->allowReg->getValues(), $addForm->type->getValues(),
                         $imgName, $addForm->lecturers->getValues(), $addForm->privateUsers->getValues(),
                         $addForm->allowFeed->getValues(), $addForm->akredMPSV->getValues(),$addForm->akredMSMT->getValues(),
                         $addForm->targetGroups->getValues(),$addForm->timeStart->getValues() );

         $newCours = $model->getCourseById($idC);

         $this->infoMsg()->addMessage($this->_('Položka byla uložena'));
         $this->link()->route('detail', array('urlkey' => $newCours->{Courses_Model_Courses::COLUMN_URLKEY}))->reload();
      }

      $this->view()->form = $addForm;
      $this->view()->edit = false;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editCourseController() {
      $this->checkWritebleRights();

      $editForm = $this->createCourseForm();

      // načtení dat
      $model = new Courses_Model_Courses();
      $course = $model->getCourse($this->getRequest('urlkey'));

      $editForm->name->setValues($course->{Courses_Model_Courses::COLUMN_NAME});
      $editForm->textShort->setValues($course->{Courses_Model_Courses::COLUMN_TEXT_SHORT});
      $editForm->text->setValues($course->{Courses_Model_Courses::COLUMN_TEXT});
      $editForm->textPrivate->setValues($course->{Courses_Model_Courses::COLUMN_TEXT_PRIVATE});
      $dateS = new DateTime($course->{Courses_Model_Courses::COLUMN_DATE_START});
      $editForm->dateStart->setValues(strftime("%x", $dateS->format("U")));
      if ($course->{Courses_Model_Courses::COLUMN_DATE_STOP} != null) {
         $dateS = new DateTime($course->{Courses_Model_Courses::COLUMN_DATE_STOP});
         $editForm->dateStop->setValues(strftime("%x", $dateS->format("U")));
      }
      $editForm->price->setValues($course->{Courses_Model_Courses::COLUMN_PRICE});
      $editForm->hourseLen->setValues($course->{Courses_Model_Courses::COLUMN_HOURS_LEN});
      $editForm->seats->setValues($course->{Courses_Model_Courses::COLUMN_SEATS});
      $editForm->seatsBlocked->setValues($course->{Courses_Model_Courses::COLUMN_SEATS_BLOCKED});
      $editForm->place->setValues($course->{Courses_Model_Courses::COLUMN_PLACE});

      $editForm->akredMPSV->setValues($course->{Courses_Model_Courses::COLUMN_AKREDIT_MPSV});
      $editForm->akredMSMT->setValues($course->{Courses_Model_Courses::COLUMN_AKREDIT_MSMT});
      $editForm->targetGroups->setValues($course->{Courses_Model_Courses::COLUMN_TAGRT_GROUPS});
      $editForm->timeStart->setValues($course->{Courses_Model_Courses::COLUMN_TIME_START});

      // načtení lektorů
      $lecturers = $model->getLecturers($course->{Courses_Model_Courses::COLUMN_ID});
      $idl = array();
      foreach ($lecturers as $l)
         array_push($idl, $l->{Courses_Model_Courses::COLUMN_L_H_C_ID_LECTURER});

      $editForm->lecturers->setValues($idl);
      $editForm->urlkey->setValues($course->{Courses_Model_Courses::COLUMN_URLKEY});
      $editForm->allowReg->setValues($course->{Courses_Model_Courses::COLUMN_ALLOW_REG});
      $editForm->type->setValues($course->{Courses_Model_Courses::COLUMN_TYPE});
      $editForm->isNew->setValues($course->{Courses_Model_Courses::COLUMN_IS_NEW});
      $editForm->inList->setValues($course->{Courses_Model_Courses::COLUMN_IN_LIST});
      $editForm->metaDesc->setValues($course->{Courses_Model_Courses::COLUMN_DESCRIPTION});
      $editForm->metaKeywords->setValues($course->{Courses_Model_Courses::COLUMN_KEYWORDS});
      $editForm->allowFeed->setValues((bool)$course->{Courses_Model_Courses::COLUMN_FEED});
      // element pro smazání obrázku
      // doplnění id
      $iIdElem = new Form_Element_Hidden('id');
      $iIdElem->addValidation(new Form_Validator_IsNumber());
      $iIdElem->setValues($course->{Courses_Model_Courses::COLUMN_ID});
      $editForm->addElement($iIdElem);

      // přidání uživatelů
      $users = $model->getCourseUsers($course->{Courses_Model_Courses::COLUMN_ID});
      $selected = array();
      foreach ($users as $user) {
         array_push($selected, $user->{Courses_Model_Courses::COLUMN_C_H_U_ID_USER});
      }
      $editForm->privateUsers->setValues($selected);
      unset ($selected);
      unset ($users);

      // přidání elementu pro odstranění obrázku
      $this->view()->courseImage = $course->{Courses_Model_Courses::COLUMN_IMAGE};
      if ($this->view()->courseImage != null) {
         $eDelteImg = new Form_Element_Checkbox('deleteImg', $this->_('Smazat uložený obrázek'));
         $editForm->addElement($eDelteImg, 'other');
      }

      if($editForm->isSend() AND $editForm->save->getValues() == false){
         $this->link()->route('detailCourse')->reload();
      }

      if ($editForm->isValid()) {
         // smazání obrázku
         $imgName = $course->{Courses_Model_Courses::COLUMN_IMAGE};
         if ($imgName != null AND ($editForm->image->getValues() != null OR $editForm->deleteImg->getValues() == true)) {
            $file = new Filesystem_File($course->{Courses_Model_Courses::COLUMN_IMAGE}, AppCore::getAppDataDir() . self::DATA_DIR . DIRECTORY_SEPARATOR);
            $file->delete();
            $imgName = null;
         }

         // uložení nového obrázku
         if ($editForm->image->getValues()) {
            $image = $editForm->image->createFileObject('Filesystem_File_Image');
            $image->resampleImage($this->category()->getParam('imgw', self::DEFAULT_IMG_WIDTH),
                    $this->category()->getParam('imgh', self::DEFAULT_IMG_HEIGHT), false);
            $image->save();
            $imgName = $image->getName();
         }

         // generování url klíče
         $urlkeys = $editForm->urlkey->getValues();
         $names = $editForm->name->getValues();
         $model->saveCourse($editForm->name->getValues(), vve_strip_html_comment($editForm->textShort->getValues()),
                 vve_strip_html_comment($editForm->text->getValues()), vve_strip_html_comment($editForm->textPrivate->getValues()),
                 $editForm->urlkey->getValues(),
                 $editForm->metaDesc->getValues(), $editForm->metaKeywords->getValues(),
                 $editForm->dateStart->getValues(), $editForm->dateStop->getValues(),
                 $editForm->price->getValues(), $editForm->hourseLen->getValues(), $editForm->place->getValues(),
                 $editForm->seats->getValues(), $editForm->seatsBlocked->getValues(),
                 $editForm->isNew->getValues(), $editForm->inList->getValues(), $editForm->allowReg->getValues(), $editForm->type->getValues(),
                 $imgName, $editForm->lecturers->getValues(), $editForm->privateUsers->getValues(),
                 $editForm->allowFeed->getValues(),
                 $editForm->akredMPSV->getValues(),$editForm->akredMSMT->getValues(),
                 $editForm->targetGroups->getValues(),$editForm->timeStart->getValues(),
                 $course->{Courses_Model_Courses::COLUMN_ID});

         $newCours = $model->getCourseById($course->{Courses_Model_Courses::COLUMN_ID});

         $this->infoMsg()->addMessage($this->_('Položky byla uložena'));
         $this->link()->route('detailCourse', array('urlkey' => $newCours->{Courses_Model_Courses::COLUMN_URLKEY}))->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->course = $course;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createCourseForm() {
      $form = new Form('course_');

      $fGrpTexts = $form->addGroup('texts', $this->_('Texty'));

      $iName = new Form_Element_Text('name', $this->_('Název'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName, $fGrpTexts);

      $iTextShort = new Form_Element_TextArea('textShort', $this->_('Úvodní text'));
      $iTextShort->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iTextShort, $fGrpTexts);

      $iText = new Form_Element_TextArea('text', $this->_('Text'));
      $iText->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iText, $fGrpTexts);

      $fGrpParams = $form->addGroup('params', $this->_('Parametry'));

      $eDateStart = new Form_Element_Text('dateStart', $this->_('Datum začátku'));
      $eDateStart->addValidation(new Form_Validator_NotEmpty());
      $eDateStart->addValidation(new Form_Validator_Date());
      $eDateStart->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateStart, $fGrpParams);

      $eDateStart = new Form_Element_Text('dateStop', $this->_('Datum konce'));
      $eDateStart->addValidation(new Form_Validator_Date());
      $eDateStart->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eDateStart, $fGrpParams);

      $eTimeStart = new Form_Element_Text('timeStart', $this->_('Předpokládaný čas začátku'));
      $eTimeStart->addValidation(new Form_Validator_Time());
      $form->addElement($eTimeStart, $fGrpParams);

      $ePrice = new Form_Element_Text('price', $this->_('Cena (Kč)'));
      $ePrice->addValidation(new Form_Validator_IsNumber());
      $form->addElement($ePrice, $fGrpParams);

      $eHoursLeng = new Form_Element_Text('hourseLen', $this->_('Délka (hodin)'));
      $eHoursLeng->addValidation(new Form_Validator_IsNumber());
      $form->addElement($eHoursLeng, $fGrpParams);

      $eSeats = new Form_Element_Text('seats', $this->_('Počet míst'));
      $eSeats->addValidation(new Form_Validator_IsNumber());
      $form->addElement($eSeats, $fGrpParams);

      $eSeatsBlocked = new Form_Element_Text('seatsBlocked', $this->_('Počet blokovaných míst'));
      $eSeatsBlocked->addValidation(new Form_Validator_IsNumber());
      $eSeatsBlocked->setValues(0);
      $form->addElement($eSeatsBlocked, $fGrpParams);

      $ePlace = new Form_Element_Text('place', $this->_('Místo konání'));
      $ePlace->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($ePlace, $fGrpParams);

      $itargetGroups = new Form_Element_Text('targetGroups', $this->_('Cílové skupiny'));
      $form->addElement($itargetGroups, $fGrpParams);

      $eLecturers = new Form_Element_Select('lecturers', $this->_('Lektoři'));
      $eLecturers->setMultiple(true);

      $modelLecturers = new Lecturers_Model();
      $l = $modelLecturers->getList();
      foreach ($l as $lecturer) {
         $eLecturers->setOptions(array($lecturer->{Lecturers_Model::COLUMN_DEGREE}
             . ' ' . $lecturer->{Lecturers_Model::COLUMN_NAME}
             . ' ' . $lecturer->{Lecturers_Model::COLUMN_SURNAME} => $lecturer->{Lecturers_Model::COLUMN_ID}), true);
      }
      $eLecturers->addValidation(new Form_Validator_NotEmpty($this->_('Musí být vybrán alespoň jeden lektor')));
      $form->addElement($eLecturers, $fGrpParams);

      $iAkredMPSV = new Form_Element_Text('akredMPSV', $this->_('Akreditace MPSV'));
      $form->addElement($iAkredMPSV, $fGrpParams);

      $iAkredMSMT = new Form_Element_Text('akredMSMT', $this->_('Akreditace MŠMT'));
      $form->addElement($iAkredMSMT, $fGrpParams);

      $eAllowReg = new Form_Element_Checkbox('allowReg', $this->_('Povolit registraci'));
      $eAllowReg->setValues(true);
      $form->addElement($eAllowReg, $fGrpParams);

      $eIsNew = new Form_Element_Checkbox('isNew', $this->_('Označit jako Nový'));
      $form->addElement($eIsNew, $fGrpParams);
      
      $eShow = new Form_Element_Checkbox('inList', $this->_('Zobrazit v seznamu'));
      $eShow->setValues(true);
      $form->addElement($eShow, $fGrpParams);
      
      $eType = new Form_Element_Select('type', $this->_('Typ kurzu'));
      $eType->setOptions(array('Kurz' => self::TYPE_COURSE, 'Konference' => self::TYPE_CONFERENTION, 'Seminář' => self::TYPE_SEMINARE));
      $form->addElement($eType, $fGrpParams);


      $fGrpPrivate = $form->addGroup('private', $this->_('Privátní část'),
              $this->_('Položky vyditelné pouze určitým uživatelům. Administrátorům jsou tyto informace vždy viditelné.'));

      $ePrivateUsers = new Form_Element_Select('privateUsers', $this->_('Uživatelé'));
      $ePrivateUsers->setMultiple(true);

      /*$modelUsers = new Model_Users();
      foreach ($modelUsers->records() as $user) {
         $ePrivateUsers->setOptions(
                 array($user->{Model_Users::COLUMN_USERNAME}.' - '.$user->{Model_Users::COLUMN_NAME}
                 ." ".$user->{Model_Users::COLUMN_SURNAME}.' - '.$user->{Model_Users::COLUMN_GROUP_NAME}
                 => $user->{Model_Users::COLUMN_ID}), true);
      }*/
      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb()->records(PDO::FETCH_OBJ);
      foreach ($users as $usr) {
          $ePrivateUsers->setOptions(array($usr->{Model_Users::COLUMN_NAME} ." ".$usr->{Model_Users::COLUMN_SURNAME}
              .' ('.$usr->{Model_Users::COLUMN_USERNAME}.') - '.$usr->{Model_Users::COLUMN_GROUP_LABEL}.' ('.$usr->{Model_Users::COLUMN_GROUP_NAME}.')'
              => $usr->{Model_Users::COLUMN_ID}), true);
      }
      $form->addElement($ePrivateUsers, $fGrpPrivate);

      $iPrivateText = new Form_Element_TextArea('textPrivate', $this->_('Text'));
      $form->addElement($iPrivateText, $fGrpPrivate);

      $fGrpOther = $form->addGroup('other', $this->_('Ostatní'),
              $this->_('Systémová nastavení a nastavení meta tagů.'));

      $iUrlKey = new Form_Element_Text('urlkey', $this->_('Url klíč'));
      $iUrlKey->setSubLabel($this->_('Pokud není klíč zadán, je generován automaticky z názvu kurzu'));
      $form->addElement($iUrlKey, $fGrpOther);

      $eImage = new Form_Element_File('image', $this->_('Obrázek'));
      $eImage->addValidation(new Form_Validator_FileExtension('jpg'));
      $eImage->setUploadDir($this->category()->getModule()->getDataDir());
      $form->addElement($eImage, $fGrpOther);

      $iKeywords = new Form_Element_Text('metaKeywords', $this->_('Klíčová slova'));
      $iKeywords->setSubLabel($this->_('Pokud nesjou zadány, jsou použiti z kategorie'));
      $form->addElement($iKeywords, $fGrpOther);

      $iDesc = new Form_Element_TextArea('metaDesc', $this->_('Popisek'));
      $iDesc->setSubLabel($this->_('Pokud není zadán, je použit z kategorie'));
      $form->addElement($iDesc, $fGrpOther);

      $eAllowFeed = new Form_Element_Checkbox('allowFeed', $this->_('Povolit RSS export'));
      $eAllowFeed->setValues(false);
      $form->addElement($eAllowFeed, $fGrpOther);
      
      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);

      return $form;
   }

   private function courseRegistration() {
      $idc = $this->view()->course->{Courses_Model_Courses::COLUMN_ID};

      $regForm = new Form('course_reg_');
      $basicGrp = $regForm->addGroup('basic', $this->_('Základní informace'));

      $eName = new Form_Element_Text('name', $this->_('Jméno'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($eName, $basicGrp);

      $eSurName = new Form_Element_Text('surname', $this->_('Přijmení'));
      $eSurName->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($eSurName, $basicGrp);

      $eDegree = new Form_Element_Text('degree', $this->_('Titul'));
      $regForm->addElement($eDegree, $basicGrp);

      $eGrade = new Form_Element_Text('grade', $this->_('Pracovní zařazení'));
      $eGrade->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($eGrade, $basicGrp);

      $ePracticeLenght = new Form_Element_Text('practiceLength', $this->_('Délka praxe'));
      $ePracticeLenght->addValidation(new Form_Validator_NotEmpty());
      $regForm->addElement($ePracticeLenght, $basicGrp);

      $ePhone = new Form_Element_Text('phone', $this->_('Telefon'));
      $ePhone->addValidation(new Form_Validator_NotEmpty());
      $ePhone->addValidation(new Form_Validator_Regexp(Form_Validator_Regexp::REGEXP_PHONE_CZSK, $this->tr('Telefon nebyl zadán ve správném formátu (+420 123 456 789).')));
      $ePhone->setSubLabel($this->tr('Telefon ve formátu +420123456789 nebo +421 123456789'));
      $regForm->addElement($ePhone, $basicGrp);

      $eMail = new Form_Element_Text('mail', $this->_('E-mail'));
      $eMail->addValidation(new Form_Validator_NotEmpty());
      $eMail->addValidation(new Form_Validator_Email());
      $regForm->addElement($eMail, $basicGrp);

      $eRegNewsLetter = new Form_Element_Checkbox('regNewsletter', $this->_('Registrovat k odběru novinek'));
      $eRegNewsLetter->setValues(true);
      $regForm->addElement($eRegNewsLetter, $basicGrp);

      $eNote = new Form_Element_TextArea('note', $this->_('Poznámka'));
      $regForm->addElement($eNote, $basicGrp);

      $payGroup = $regForm->addGroup('pay', $this->_('Fakturační údaje'));
      switch ($this->view()->course->{Courses_Model_Courses::COLUMN_TYPE}) {
         case self::TYPE_CONFERENTION:
            $label = $this->_('Konference hrazena');
            break;
         case self::TYPE_SEMINARE:
            $label = $this->_('Seminář hrazen');
            break;
         default:
            $label = $this->_('Kurz hrazen');
            break;
      }
      $ePay = new Form_Element_Radio('payType', $label);
      $payTypes = array($this->_('Organizací') => self::PAY_TYPE_ORGANISATION,
          $this->_('Soukromě') => self::PAY_TYPE_PRIVATE);
      $ePay->setOptions($payTypes);
      $ePay->setValues(self::PAY_TYPE_ORGANISATION);
      $regForm->addElement($ePay, $payGroup);

      $eOrgName = new Form_Element_Text('orgName', $this->_('Název'));
      $regForm->addElement($eOrgName, $payGroup);

      $eOrgAddress = new Form_Element_TextArea('orgAddress', $this->_('Adresa'));
      $regForm->addElement($eOrgAddress, $payGroup);

      $eOrgICO = new Form_Element_Text('orgICO', $this->_('IČ'));
      $regForm->addElement($eOrgICO, $payGroup);

      $eOrgPhone = new Form_Element_Text('orgPhone', $this->_('Telefon'));
      $regForm->addElement($eOrgPhone, $payGroup);

      $ePrivateAddress = new Form_Element_TextArea('privateAddress', $this->_('Adresa'));
      $regForm->addElement($ePrivateAddress, $payGroup);

      $eSend = new Form_Element_Submit('send', $this->_('Odeslat'));
      $regForm->addElement($eSend);
      
      $eCheck = new Form_Element_Checkbox('check', $this->tr('Souhlas se zpracováním'));
      $eCheck->setSubLabel($this->tr('Jsem srozuměn/a se všeobecnými podmínkami registrace a souhlasím se zpracováním svých osobných údajů podle zákona č. 101/2000 Sb.'));
      $regForm->addElement($eCheck);
         
      
      if ($regForm->isSend()) {
         if($regForm->check->getValues() == false){
            $eCheck->setError($this->tr('S podmínkami musíte souhlasit, jinak nelze registraci dokončit.'));
         }
         
         if ($regForm->payType->getValues() == 'organisation') {
            $regForm->orgName->addValidation(new Form_Validator_NotEmpty());
            $regForm->orgAddress->addValidation(new Form_Validator_NotEmpty());
            $regForm->orgICO->addValidation(new Form_Validator_NotEmpty());
            $regForm->orgPhone->addValidation(new Form_Validator_NotEmpty());
         } else {
            $regForm->privateAddress->addValidation(new Form_Validator_NotEmpty());
         }
      }

      // kontrola času odeslání
      if ($regForm->isValid()) {
         $phone = preg_replace('/\s*/m', '', $regForm->phone->getValues());
         $model = new Courses_Model_Registrations();

         $model->saveRegistration($idc, $regForm->name->getValues(), $regForm->surname->getValues(),
                 $regForm->degree->getValues(), $regForm->grade->getValues(),
                 $regForm->practiceLength->getValues(), $phone,
                 $regForm->mail->getValues(), $regForm->note->getValues(),
                 $regForm->payType->getValues(), $regForm->orgName->getValues(),
                 $regForm->orgAddress->getValues(), $regForm->orgICO->getValues(),
                 $regForm->orgPhone->getValues(), $regForm->privateAddress->getValues());

         // pokud je registrace k newsletteru, přidáme jej
         if ($regForm->regNewsletter->getValues() == true) {
            $modelMails = new Mails_Model_Addressbook();
            $modelMails->saveMail($regForm->mail->getValues(), $this->category()->getParam(self::PARAM_NEWSLETTER_MAIL_GRP, Mails_Model_Groups::GROUP_ID_DEFAULT));
            unset ($modelMails);
         }

         $course = $this->view()->course;
         // odeslání emailu  s registrací na registrovaného a admina
         // řetězec mailu jako xml

         $mailXmlCnt = new XMLWriter();
//         $mailXmlCnt->openURI('php://output');
         $mailXmlCnt->openMemory();
         $mailXmlCnt->setIndent(3);
//         $mailXmlCnt->startDocument('1.0', 'UTF-8');

         $mailXmlCnt->startElement('style');
         $mailXmlCnt->writeAttribute('type', 'text/css');
         $mailXmlCnt->writeRaw('th {text-align:left;}');
         $mailXmlCnt->endElement();

         // úvod
         switch ($this->view()->course->{Courses_Model_Courses::COLUMN_TYPE}) {
         case self::TYPE_CONFERENTION:
            $mailXmlCnt->writeRaw('Toto je automatická odpověď. Byla registrována přihláška ke konferenci "'.$course->{Courses_Model_Courses::COLUMN_NAME}.'" ze stránek ');
            break;
         case self::TYPE_SEMINARE:
            $mailXmlCnt->writeRaw('Toto je automatická odpověď. Byla registrována přihláška k semináři "'.$course->{Courses_Model_Courses::COLUMN_NAME}.'" ze stránek ');
            break;
         default:
            $mailXmlCnt->writeRaw('Toto je automatická odpověď. Byla registrována přihláška ke kurzu "'.$course->{Courses_Model_Courses::COLUMN_NAME}.'" ze stránek ');
            break;
      }

         $mailXmlCnt->startElement('a');
         $mailXmlCnt->writeAttribute('href', Url_Link::getMainWebDir());
         $mailXmlCnt->writeAttribute('title', VVE_WEB_NAME);
         $mailXmlCnt->writeRaw(VVE_WEB_NAME);
         $mailXmlCnt->endElement();
         $mailXmlCnt->writeRaw('.');
         $mailXmlCnt->writeElement('br');
         $mailXmlCnt->writeElement('br');

         $mailXmlCnt->writeRaw('V případě dalších dotazů či uplatnění slev vám co nejdříve odpovíme. Podrobné informace o kurzu a fakturu obdržíte zhruba 14 dní před začátkem kurzu.');
         $mailXmlCnt->writeElement('br');
         $mailXmlCnt->writeRaw('Těšíme se na viděnou.');
         $mailXmlCnt->writeElement('br');
         $mailXmlCnt->writeElement('br');

         // odkaz na kurz
         switch ($this->view()->course->{Courses_Model_Courses::COLUMN_TYPE}) {
            case self::TYPE_CONFERENTION:
               $mailXmlCnt->writeRaw('Detail konference naleznete ');
               break;
            case self::TYPE_SEMINARE:
               $mailXmlCnt->writeRaw('Detail semináře naleznete ');
               break;
            default:
               $mailXmlCnt->writeRaw('Detail kurzu naleznete ');
               break;
         }
         
         $mailXmlCnt->startElement('a');
         $mailXmlCnt->writeAttribute('href', $this->link());
         $mailXmlCnt->writeAttribute('title', $course->{Courses_Model_Courses::COLUMN_NAME});
         $mailXmlCnt->writeRaw("zde.");
         $mailXmlCnt->endElement();
         $mailXmlCnt->writeElement('br');
         $mailXmlCnt->writeElement('br');

         // info o kurzu
         $mailXmlCnt->startElement('table'); // sof table
         // hlavička
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeAttribute('colspan', 2);
         $mailXmlCnt->writeRaw('Info o kurzu');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->endElement(); // eof tr
         // název
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeAttribute('width', 150);
         $mailXmlCnt->writeRaw('Název:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw($course->{Courses_Model_Courses::COLUMN_NAME});
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // termín
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Termín:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw(vve_date("%x", new DateTime($course->{Courses_Model_Courses::COLUMN_DATE_START})));
         if ($course->{Courses_Model_Courses::COLUMN_DATE_STOP} != null) {
            $mailXmlCnt->writeRaw(' - ' . vve_date("%x", new DateTime($course->{Courses_Model_Courses::COLUMN_DATE_STOP})));
         };
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // délka
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Délka:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw($course->{Courses_Model_Courses::COLUMN_HOURS_LEN}." hodin");
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // cena
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Cena:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw($course->{Courses_Model_Courses::COLUMN_PRICE}." Kč");
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // popisek
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Popis:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw(strip_tags($course->{Courses_Model_Courses::COLUMN_TEXT_SHORT}, 'br'));
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // místo
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Místo konání:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw($course->{Courses_Model_Courses::COLUMN_PLACE});
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         $mailXmlCnt->endElement(); // eof table

         $mailXmlCnt->writeElement('br');

         // detail  přihlášky
         $mailXmlCnt->startElement('table'); // sof table
         // hlavička
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeAttribute('colspan', 2);
         $mailXmlCnt->writeRaw('Detail registrace');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->endElement(); // eof tr
         // jméno
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeAttribute('width', 150);
         $mailXmlCnt->writeRaw('Jméno a přimení:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         if($regForm->degree->getValues() != null){
            $mailXmlCnt->writeRaw($regForm->degree->getValues()." ");
         }
         $mailXmlCnt->writeRaw($regForm->name->getValues()." ".$regForm->surname->getValues());
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // prac zařazení
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Pracovní zařazení:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw($regForm->grade->getValues());
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // délka praxe
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Délka praxe:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw($regForm->practiceLength->getValues());
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // telefon
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Telefon:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw($phone);
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // email
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('E-mail:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw($regForm->mail->getValues());
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // registrace do newsletteru
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Registrace k odběru<br /> novinek:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         if($regForm->regNewsletter->getValues() == true){
            $mailXmlCnt->writeRaw('Ano');
         } else {
            $mailXmlCnt->writeRaw('Ne');
         }
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr
         // poznámka
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeRaw('Poznámka:');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->startElement('td'); // sof td
         $mailXmlCnt->writeRaw($regForm->note->getValues());
         $mailXmlCnt->endElement(); // eof td
         $mailXmlCnt->endElement(); // eof tr

         $mailXmlCnt->endElement(); // eof table

         $mailXmlCnt->writeElement('br');

         // fakturace
         $mailXmlCnt->startElement('table'); // sof table
         // hlavička
         $mailXmlCnt->startElement('tr'); // sof tr
         $mailXmlCnt->startElement('th'); // sof th
         $mailXmlCnt->writeAttribute('colspan', 2);
         $mailXmlCnt->writeRaw('Fakturace');
         $mailXmlCnt->endElement(); // eof th
         $mailXmlCnt->endElement(); // eof tr

         switch ($regForm->payType->getValues()) {
            case self::PAY_TYPE_ORGANISATION:
               // fakturace
               $mailXmlCnt->startElement('tr'); // sof tr
               $mailXmlCnt->startElement('th'); // sof th
               $mailXmlCnt->writeAttribute('width', 150);
               $mailXmlCnt->writeRaw('Způsob fakturace:');
               $mailXmlCnt->endElement(); // eof th
               $mailXmlCnt->startElement('td'); // sof td
               $mailXmlCnt->writeRaw('Organizací');
               $mailXmlCnt->endElement(); // eof td
               $mailXmlCnt->endElement(); // eof tr
               // název
               $mailXmlCnt->startElement('tr'); // sof tr
               $mailXmlCnt->startElement('th'); // sof th
               $mailXmlCnt->writeRaw('Název:');
               $mailXmlCnt->endElement(); // eof th
               $mailXmlCnt->startElement('td'); // sof td
               $mailXmlCnt->writeRaw($regForm->orgName->getValues());
               $mailXmlCnt->endElement(); // eof td
               $mailXmlCnt->endElement(); // eof tr
               // adresa
               $mailXmlCnt->startElement('tr'); // sof tr
               $mailXmlCnt->startElement('th'); // sof th
               $mailXmlCnt->writeRaw('Adresa:');
               $mailXmlCnt->endElement(); // eof th
               $mailXmlCnt->startElement('td'); // sof td
               $mailXmlCnt->writeRaw($regForm->orgAddress->getValues());
               $mailXmlCnt->endElement(); // eof td
               $mailXmlCnt->endElement(); // eof tr
               // Ič
               $mailXmlCnt->startElement('tr'); // sof tr
               $mailXmlCnt->startElement('th'); // sof th
               $mailXmlCnt->writeRaw('IČ:');
               $mailXmlCnt->endElement(); // eof th
               $mailXmlCnt->startElement('td'); // sof td
               $mailXmlCnt->writeRaw($regForm->orgICO->getValues());
               $mailXmlCnt->endElement(); // eof td
               $mailXmlCnt->endElement(); // eof tr
               // Ič
               $mailXmlCnt->startElement('tr'); // sof tr
               $mailXmlCnt->startElement('th'); // sof th
               $mailXmlCnt->writeRaw('Telefon:');
               $mailXmlCnt->endElement(); // eof th
               $mailXmlCnt->startElement('td'); // sof td
               $mailXmlCnt->writeRaw($regForm->orgPhone->getValues());
               $mailXmlCnt->endElement(); // eof td
               $mailXmlCnt->endElement(); // eof tr

               break;
            case self::PAY_TYPE_PRIVATE:
            default:
               // fakturace
               $mailXmlCnt->startElement('tr'); // sof tr
               $mailXmlCnt->startElement('th'); // sof th
               $mailXmlCnt->writeAttribute('width', 150);
               $mailXmlCnt->writeRaw('Způsob fakturace:');
               $mailXmlCnt->endElement(); // eof th
               $mailXmlCnt->startElement('td'); // sof td
               $mailXmlCnt->writeRaw('Soukromě');
               $mailXmlCnt->endElement(); // eof td
               $mailXmlCnt->endElement(); // eof tr
               // adresa
               $mailXmlCnt->startElement('tr'); // sof tr
               $mailXmlCnt->startElement('th'); // sof th
               $mailXmlCnt->writeRaw('Adresa:');
               $mailXmlCnt->endElement(); // eof th
               $mailXmlCnt->startElement('td'); // sof td
               $mailXmlCnt->writeRaw($regForm->privateAddress->getValues());
               $mailXmlCnt->endElement(); // eof td
               $mailXmlCnt->endElement(); // eof tr
               break;
         }

         $mailXmlCnt->endElement(); // eof table

//         $mailXmlCnt->endDocument();

         $mail = new Email(true);
         // obsah
         $cnt = $mailXmlCnt->outputMemory();
         $mail->setContent($cnt);
         unset ($mailXmlCnt);
         // předmět
         switch ($this->view()->course->{Courses_Model_Courses::COLUMN_TYPE}) {
            case self::TYPE_CONFERENTION:
               $mail->setSubject('Registrace do konference "'.$course->{Courses_Model_Courses::COLUMN_NAME}.'"');
               break;
            case self::TYPE_SEMINARE:
               $mail->setSubject('Registrace do semináře "'.$course->{Courses_Model_Courses::COLUMN_NAME}.'"');
               break;
            default:
               $mail->setSubject('Registrace do kurzu "'.$course->{Courses_Model_Courses::COLUMN_NAME}.'"');
               break;
         }
         // příjemci
         $mail->addAddress($regForm->mail->getValues()); // form
         if($this->category()->getParam(self::PARAM_SEND_ADMIN_NOTIF, self::PARAM_SEND_ADMIN_NOTIF_DEFAULT) == true){
            // vytažení příjemců admin
            $mail->addAddress($this->getAdminAddreses());
         }
         $mail->sendMail();
         $this->infoMsg()->addMessage($this->tr('Registace byla uložena. E-mailem automaticky obdržíte generované potvrzení o přijetí Vaší přihlášky. Pokud potvrzovací email do 24 hodin neobdržíte, <a href="/kontakt/" title="kontakt">kontaktuje nás</a>'));
         $this->link()->param('registration', 'true')->reload();
      }

      $this->view()->formReg = $regForm;
   }

   /**
    * Metoda vrací emaily pro příjemce nových adres z nastaavení
    */
   private function getAdminAddreses() {
      $mails = array();
      $str = $this->category()->getParam(self::PARAM_OTHER_RECIPIENS, null);
      if($str != null){
         $mails = explode(';', $str);
      }
      $usersId = $this->category()->getParam(self::PARAM_ADMINS, array());

      $modelusers = new Model_Users();

      foreach ($usersId as $id) {
         $user = $modelusers->getUserById($id);
         $mails = array_merge($mails, explode(';', $user->{Model_Users::COLUMN_MAIL}));
      }
      $mails = array_unique($mails);
      return $mails;
   }

   /**
    * Kontroller pro seznam registrovaných ke kurzu
    */
   public function registrationsCourseController() {
      $this->checkWritebleRights();

      $modelC = new Courses_Model_Courses();
      $this->view()->course = $modelC->getCourse($this->getRequest('urlkey'));

      if ($this->view()->course == false)
         return false;

      $modelReg = new Courses_Model_Registrations();

      $formCancelReg = new Form('cancel_reg_');

      $eId = new Form_Element_Hidden('id');
      $eId->addValidation(new Form_Validator_IsNumber());
      $formCancelReg->addElement($eId);

      $eSubmit = new Form_Element_SubmitImage('submit');
      $formCancelReg->addElement($eSubmit);

      if ($formCancelReg->isValid()) {
         $reg = $modelReg->getRegistration($formCancelReg->id->getValues());

         $modelReg->cancelRegistration($formCancelReg->id->getValues());

         // zda mail o zrušení registrace

         $this->infoMsg()->addMessage(sprintf($this->_('Registrace na jméno %s,%s byla zrušena'),
                         $reg->{Courses_Model_Registrations::COLUMN_SURNAME},
                         $reg->{Courses_Model_Registrations::COLUMN_NAME}));

         $this->link()->reload();
      }
      $this->view()->formCancel = $formCancelReg;

      $this->view()->registrations = $modelReg->getRegistrations($this->view()->course->{Courses_Model_Courses::COLUMN_ID});
   }

   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {

   }

   /**
    * Kontroler pro seznam uložených míst
    */
   public function placesListController() {
      $model = new Courses_Model_Places();
      $searched = $this->getRequestParam('term', null);
      $this->view()->places = $model->getPlaces($searched);
   }

   /**
    * Metoda pro nastavení modulu
    */
   protected function settings(&$settings, Form &$form) {
      $formGrpUsers = $form->addGroup('users', 'Registrace');

      // odeslání upozornění
      $elemCheckAdminNotice = new Form_Element_Checkbox('admin_notice', 'Odesílat upozornění na nové registrace');
      if (!isset($settings[self::PARAM_SEND_ADMIN_NOTIF])) {
         $elemCheckAdminNotice->setValues(true);
      } else {
         $elemCheckAdminNotice->setValues($settings[self::PARAM_SEND_ADMIN_NOTIF_DEFAULT]);
      }
      $form->addElement($elemCheckAdminNotice, $formGrpUsers);

      // maily správců
      $elemEamilRec = new Form_Element_TextArea('emails_rec', 'Adresy správců');
      $elemEamilRec->setSubLabel('E-mailové  adresy správců, kterým chodí upozornění
na nové registrace do kurzu. Může jich být více a jsou odděleny středníkem. Místo tohoto boxu
lze využít následující výběr již existujících uživatelů.');
      $form->addElement($elemEamilRec, $formGrpUsers);

      if (isset($settings[self::PARAM_OTHER_RECIPIENS])) {
         $form->emails_rec->setValues($settings[self::PARAM_OTHER_RECIPIENS]);
      }

      $elemAdmins = new Form_Element_Select('admins', 'Adresy uživatelů v systému');
      // načtení uživatelů
      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb(true)->records();
      $usersIds = array();
      foreach ($users as $usr) {
         if($usr->{Model_Users::COLUMN_MAIL} != null){
            $usersIds[$usr->{Model_Users::COLUMN_NAME} ." ".$usr->{Model_Users::COLUMN_SURNAME}
              .' ('.$usr->{Model_Users::COLUMN_USERNAME}.') - '.$usr->{Model_Users::COLUMN_GROUP_LABEL}
              .' ('.$usr->{Model_Users::COLUMN_GROUP_NAME}.')'] = $usr->{Model_Users::COLUMN_ID};
         }
      }
      $elemAdmins->setOptions($usersIds);
      $elemAdmins->setMultiple();
      $elemAdmins->html()->setAttrib('size', 4);

      $form->addElement($elemAdmins, $formGrpUsers);

      if (isset($settings[self::PARAM_ADMINS])) {
         $form->admins->setValues($settings[self::PARAM_ADMINS]);
      }

      $formGrpNewsletter = $form->addGroup('newsletter', 'Nastavení registrace newsletteru');

      $modelMailsGroups = new Mails_Model_Groups();
      $groups = $modelMailsGroups->getGroups();

      $elemMailsGroups = new Form_Element_Select('newsletterGroup', 'Skupina v adresáři s maily');

      foreach ($groups as $group) {
         $elemMailsGroups->setOptions(array($group->{Mails_Model_Groups::COLUMN_NAME} => $group->{Mails_Model_Groups::COLUMN_ID}), true);
      }
      $form->addElement($elemMailsGroups, $formGrpNewsletter);

      if(isset ($settings[self::PARAM_NEWSLETTER_MAIL_GRP])){
         $form->newsletterGroup->setValues($settings[self::PARAM_NEWSLETTER_MAIL_GRP]);
      } else {
         $form->newsletterGroup->setValues(Mails_Model_Groups::GROUP_ID_DEFAULT);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings[self::PARAM_SEND_ADMIN_NOTIF] = $form->admin_notice->getValues();
         $settings[self::PARAM_ADMINS] = $form->admins->getValues();
         $settings[self::PARAM_OTHER_RECIPIENS] = $form->emails_rec->getValues();
         $settings[self::PARAM_NEWSLETTER_MAIL_GRP] = $form->newsletterGroup->getValues();
      }
   }

}
?>