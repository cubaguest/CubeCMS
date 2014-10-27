<?php
class Actions_Routes extends Routes {
   function initRoutes() {
      // přidání akce
      $this->addRoute('add', "add", 'add', "add/");
      // archiv akcí
      $this->addRoute('archive', "archive", 'archive', "archive/");
      // preview
      $this->addRoute('preview', "(?P<id>[0-9]+)/preview", 'preview', "{id}/preview/");
      // akce podle datumů
      $this->addRoute('list',  
          "(?P<fromyear>[0-9]{4})(?:-(?P<frommonth>[0-1]?[0-9]))?(?:-(?P<fromday>[0-3]?[0-9]{1}))?" // OD
          ."(?:/(?P<toyear>[0-9]{4})(?:-(?P<tomonth>[0-1]?[0-9]))?(?:-(?P<today>[0-3]?[0-9]{1}))?)?/?", // DO
          'main'//,
          );
      // editace akce
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');

      // editace akce
      $this->addRoute('editlabel', "editlabel", 'editLabel','editlabel/');
      // list nadcházejiících akcí v xml
      $this->addRoute('feturedlist', "featuredlist.(?P<output>(?:xml))", 'featuredList', 'featuredlist.{output}');
      // list s právě probíhající akcí
      $this->addRoute('current', "current.(?P<output>(?:xml))", 'currentAct', 'current.{output}');
      // detail akce
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      // export článku
      $this->addRoute('detailExport', "::urlkey::\.(?P<output>(?:pdf)|(?:xml))", 'showData','{urlkey}.{output}');
      
   }
   
   /**
    * Generuje url část pro zadaná data akce list
    * @param array $params
    * @return string
    */
   protected function listRouteParams($params)
   {
      $str = null;
      if(isset($params['fromyear'])){
         $str  .= $params['fromyear'];
         if(isset($params['frommonth'])){
            $str  .= '-'.$params['frommonth'];
            if(isset($params['fromday'])){
               $str  .= '-'.$params['fromday'];
            }
         }
         if(isset($params['toyear'])){
            $str  .= '/'.$params['toyear'];
            if(isset($params['tomonth'])){
               $str  .= '-'.$params['tomonth'];
               if(isset($params['today'])){
                  $str  .= '-'.$params['today'];
               }
            }
         }
         $str .= '/';
      }
      return $str;
   }
}