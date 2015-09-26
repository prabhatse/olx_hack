<?php


class SearchController extends EFCAuthController {

    /**
      *      * Query
      *           * @var str
      *                */
    var $query;


    public function authControl() {
      $this->setViewTemplate(NULL);

      var_dump($_GET);
      exit;

        if ($this->shouldRefreshCache()) {

            
        
        }
    }

    private function searchLocation() {
    
    }

}
