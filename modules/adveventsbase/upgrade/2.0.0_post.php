<?php

$events = SvbBase_Model_Events::getAllRecords();

foreach ($events as $e) {
   $e->{SvbBase_Model_Events::COLUMN_TEXT_CLEAR} = strip_tags((string)$e->{SvbBase_Model_Events::COLUMN_TEXT});
   $e->save();
}
