<?php

$articles = Articles_Model::getAllRecords();

foreach ($articles as $art) {
   $art->{Articles_Model::COLUMN_DATADIR} = (string)$art->{Articles_Model::COLUMN_URLKEY};
   $art->save();
}
