<?php 
// Remove admin categories and rights - is in admmenu.xml

$modelCats = new Model_Category();
$modelModule = new Model_Module();
$cats = $modelCats->columns(array(Model_Category::COLUMN_MODULE))->records();

foreach ($cats as $cat) {
   $modC = $modelModule
           ->columns(array(Model_Module::COLUMN_NAME))
           ->where(Model_Module::COLUMN_NAME.' = :name', array('name' => $cat->{Model_Category::COLUMN_MODULE}))
                   ->count();
   if($modC == 0){
      $mrec = $modelModule->newRecord();
      $mrec->{Model_Module::COLUMN_NAME} = $cat->{Model_Category::COLUMN_MODULE};
      $mrec->{Model_Module::COLUMN_VERSION_MAJOR} = 1;
      $mrec->{Model_Module::COLUMN_VERSION_MINOR} = 0;
      $mrec->{Model_Module::COLUMN_VERSION} = '1.0.0';
      $mrec->save();
   }
}

//var_dump(CoreErrors::getErrors());die;