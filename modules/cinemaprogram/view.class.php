<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class CinemaProgram_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }

   public function detailView() {
      $this->template()->addTplFile("detail.phtml");
   }

   public function addView() {
      $this->template()->addTplFile("edit.phtml");
   }

   public function editView() {
      $this->addView();
   }

   // nadcházející filmy
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
            $xml->writeAttribute('xml:lang', Locale::getLang());

            while ($row = $this->movies->fetch()) {
               $time = new DateTime();
               $xml->startElement('article'); // sof article
               $xml->writeAttribute('date', $row->{CinemaProgram_Model_Detail::COL_T_DATE});
               $xml->writeElement('name', $row->{CinemaProgram_Model_Detail::COL_NAME});
               $xml->writeElement('url', $this->link()->route('detailExport',
                       array('id' => $row->{CinemaProgram_Model_Detail::COL_ID})));
               $xml->endElement(); // eof article

            }

            $xml->endElement(); // eof article
            $xml->endDocument(); //EOF document

            $xml->flush();

            break;
      }
   }

   public function showDataView() {
      switch ($this->output) {
//         case 'pdf':
//            $c = $this->createPdf($this->action);
//            // výstup
//            $c->flush($this->action->{Actions_Model_Detail::COLUMN_URLKEY}.'.pdf');
//
//            break;
         case 'xml':
         default:
            $c = $this->createMovieXml($this->movie);
            break;
      }

   }

   protected function createMovieXml($movie) {
      $api = new Component_Api_VVEArticle();

      $api->setCategory($this->category()->getName(), $this->link()->clear());

      // informace o článku/akci
      if($movie != null) {
         $img = null;
         if($movie->{CinemaProgram_Model_Detail::COL_IMAGE} != null) {
            $img = $this->category()->getModule()->getDataDir(true)
                    .$movie->{CinemaProgram_Model_Detail::COL_IMAGE};
         }

         $api->setArticle($movie->{CinemaProgram_Model_Detail::COL_NAME},
                 $this->link()->route('detail', array('id'=>$movie->{CinemaProgram_Model_Detail::COL_ID},
                 'name' => vve_cr_url_key($movie->{CinemaProgram_Model_Detail::COL_NAME}))),
                  vve_tpl_truncate($movie->{CinemaProgram_Model_Detail::COL_LABEL_CLEAR},200), $img);

         if((int)$movie->{CinemaProgram_Model_Detail::COL_PRICE} != null|0) {
            $api->setData('price', $movie->{CinemaProgram_Model_Detail::COL_PRICE});
         }
      }
      $api->flush();
   }

   public function currentMovieXmlView() {
      $this->createMovieXml($this->movie);
   }
}

?>