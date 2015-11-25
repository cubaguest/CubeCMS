<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class AdvEventsAdmEvents_View extends AdvEventsBase_View {

   public function mainView()
   {
      parent::mainView();
      $this->template()->addFile('tpl://main.phtml');
      Template_Core::setFullWidth(true);

      // toolbo itemu
      if (!empty($this->events)) {
         $itemToolbox = new Template_Toolbox2();

         $toolEdit = new Template_Toolbox2_Tool_Redirect('editEvent', $this->tr('Upravit'));
         $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
         $itemToolbox->addTool($toolEdit);
         
         if ($this->formEventDelete) {
            $toolDelete = new Template_Toolbox2_Tool_Form($this->formEventDelete);
            $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
            $toolDelete->setImportant(true);
            $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat událost?'));
            $itemToolbox->addTool($toolDelete);
         }
         
         foreach ($this->events as $event) {
            $event->toolbox = clone $itemToolbox;
            $event->toolbox->editEvent->setAction($this->link()->route('editEvent', array('id' => $event->getPK())));

            if (isset($itemToolbox->advevent_event_remove)) {
               $event->toolbox->advevent_event_remove->getForm()->id->setValues($event->getPK());
            }
         }
      }
   }

   public function addEventView()
   {
      $this->setTinyMCE($this->formEdit->text, 'advanced');
      $this->template()->addFile('tpl://edit.phtml');
      Template_Navigation::addItem($this->tr('Přidání nové události'), $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($this->tr('Přidání nové události'));
   }

   public function editEventView()
   {
      $this->setTinyMCE($this->formEdit->text, 'advanced');
      $this->template()->addFile('tpl://edit.phtml');
      $titName = sprintf($this->tr('Úprava události %s'), $this->event->{AdvEventsBase_Model_Events::COLUMN_NAME});
      Template_Navigation::addItem($titName, $this->link(), true);
      Template::addPageTitle($this->category()->getName());
      Template::addPageTitle($titName);

      if (!empty($this->imagesEvent)) {
         $itemToolbox = new Template_Toolbox2();

         if ($this->formImageDelete) {
            $toolDelete = new Template_Toolbox2_Tool_Form($this->formImageDelete);
            $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
            $toolDelete->setImportant(true);
            $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat obrázek?'));
            $itemToolbox->addTool($toolDelete);
         }
         
         if ($this->formImageTitle) {
            $toolTitle = new Template_Toolbox2_Tool_Form($this->formImageTitle);
            $toolTitle->setIcon(Template_Toolbox2::ICON_ENABLE);
            $itemToolbox->addTool($toolTitle);
         }

         foreach ($this->imagesEvent as $img) {
            $img->toolbox = clone $itemToolbox;
            if (isset($itemToolbox->advevent_img_remove)) {
               $img->toolbox->advevent_img_remove->getForm()->id->setValues($img->getPK());
            }
            if (isset($itemToolbox->advevent_img_title) && $img->{AdvEventsBase_Model_EventsImages::COLUMN_IS_TITLE} == 0) {
               $img->toolbox->advevent_img_title->getForm()->id->setValues($img->getPK());
            } else {
               unset($img->toolbox->advevent_img_title);
            }
         }
      }
      
      if (!empty($this->eventTimes)) {
         $itemToolbox = new Template_Toolbox2();

         if ($this->formTimeDelete) {
            $toolDelete = new Template_Toolbox2_Tool_Form($this->formTimeDelete);
            $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
            $toolDelete->setImportant(true);
            $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat období?'));
            $itemToolbox->addTool($toolDelete);
         }

         foreach ($this->eventTimes as $time) {
            $time->toolbox = clone $itemToolbox;
            if (isset($itemToolbox->advevent_time_remove)) {
               $time->toolbox->advevent_time_remove->getForm()->id->setValues($time->getPK());
            }
         }
      }
   }

   public function detailEventView()
   {
      $this->template()->addFile('tpl://detail.phtml');
//      Template_Navigation::addItem($this->evcat->{AdvEventsBase_Model_Categories::COLUMN_NAME}, $this->link(), true);
      Template::addPageTitle($this->category()->getName());
//      Template::addPageTitle($this->evcat->{AdvEventsBase_Model_Categories::COLUMN_NAME});
   }

}
