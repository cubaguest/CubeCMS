<?php
class ActionsList_View extends View {
   public function init() {
   }


   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }

   public function listCatAddView() {
      $this->template()->addTplFile("addgoto.phtml");
   }

   public function editLabelView() {
      $this->template()->addTplFile('editlabel.phtml', 'actions');
   }

   public function exportView() {
      $feed = new Component_Feed(true);

      $feed ->setConfig('type', $this->type);
      $feed ->setConfig('title', $this->category()->getName());
      $feed ->setConfig('desc', $this->category()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
      $feed ->setConfig('link', $this->link());

      while ($action = $this->actions->fetch()) {
         $startDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_START});
         $stopDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_STOP});
         $stopDateString = null;
         if($startDate != $stopDate) {
            $stopDateString = " - ".$stopDate;
         }
         $desc = "<h3>".$startDate.$stopDateString."</h3>";
         $desc .= $action->{Actions_Model_Detail::COLUMN_TEXT};

         $feed->addItem($action->{Actions_Model_Detail::COLUMN_NAME},$desc,
                 $this->link()->category($row->curlkey)->route('detail',
                 array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY})),
                 new DateTime($action->{Actions_Model_Detail::COLUMN_ADDED}),
                 $action->{Model_Users::COLUMN_USERNAME},null,null,
                 $action->{Actions_Model_Detail::COLUMN_URLKEY}."_".$action->{Actions_Model_Detail::COLUMN_ID}."_".
                 $action->{Actions_Model_Detail::COLUMN_CHANGED});
      }
      $feed->flush();
   }

   public function featuredListView() {
      switch ($this->type) {
         case 'xml':
         default:
            $xml = new XMLWriter();
            $xml->openURI('php://output');
            // hlavička
            $xml->startDocument('1.0', 'UTF-8');
            $xml->setIndent(4);

            // rss hlavička
            $xml->startElement('articles'); // SOF article
            $xml->writeAttribute('xmlns','http://www.vveframework.eu/v6/featuredarticles');
            $xml->writeAttribute('xml:lang', Locales::getLang());

            while ($row = $this->actions->fetch()) {
               $xml->startElement('article'); // sof article
               $xml->writeAttribute('starttime', $row->{Actions_Model_Detail::COLUMN_DATE_START});
               $xml->writeElement('name', $row->{Actions_Model_Detail::COLUMN_NAME});
               $xml->writeElement('url', $this->link()->category($row->curlkey)
                       ->route('detailExport',array('urlkey' => $row->{Actions_Model_Detail::COLUMN_URLKEY})));
               $xml->endElement(); // eof article

            }

            $xml->endElement(); // eof article
            $xml->endDocument(); //EOF document

            $xml->flush();

            break;
      }
   }

   public function currentActXmlView() {
      $api = new Component_Api_VVEArticle();

      $api->setCategory($this->category()->getName(), $this->link()->clear());

      if($this->action != null) {
         $img = null;
         if($this->action->{Actions_Model_Detail::COLUMN_IMAGE} != null) {
            $dataDir = Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR
            .$this->action->{Model_Category::COLUMN_DATADIR}.URL_SEPARATOR;

            $img  = $dataDir.$this->action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getLang()]
                    .URL_SEPARATOR.$this->action->{Actions_Model_Detail::COLUMN_IMAGE};
         }

         $api->setArticle($this->action->{Actions_Model_Detail::COLUMN_NAME},
                 $this->link()->category($this->action->curlkey)->route('detail',
                 array('urlkey'=>$this->action->{Actions_Model_Detail::COLUMN_URLKEY})),
                 vve_tpl_truncate(vve_strip_tags($this->action->{Actions_Model_Detail::COLUMN_TEXT}),400), $img);

         if((int)$this->action->{Actions_Model_Detail::COLUMN_PRICE} != null|0) {
            $api->setData('price', $this->action->{Actions_Model_Detail::COLUMN_PRICE});
         }
         if((int)$this->action->{Actions_Model_Detail::COLUMN_PREPRICE} != null|0) {
            $api->setData('preprice', $this->action->{Actions_Model_Detail::COLUMN_PREPRICE});
         }
         $api->flush();
      }
   }
}

?>