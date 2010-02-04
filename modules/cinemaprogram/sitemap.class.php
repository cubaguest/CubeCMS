<?php
class CinemaProgram_SiteMap extends SiteMap {
	public function run() {
      // mrknout na jiná kina jak mají sitemp
      $this->addCategoryItem(new DateTime($this->category()->getCatDataObj()->{Model_Category::COLUMN_CHANGED}));
	}
}
?>