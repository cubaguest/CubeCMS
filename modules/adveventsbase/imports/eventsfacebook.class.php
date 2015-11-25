<?php

class AdveventsBase_Imports_EventsFacebook extends AdveventsBase_Imports_Events {

   public function process()
   {
      if ($this->params['url'] == null) {
         return;
      }
      $this->importActions();
   }

   protected function importActions()
   {
      Loader::loadExternalLib('facebook');

      $params = array(
          'app_id' => '454539178063183',
          'app_secret' => '213f8683ad9b1acf06353ad050dfa3f4',
          'default_graph_version' => 'v2.5',
      );

      $fb = new Facebook\Facebook($params);
      
      
      try {
         // Returns a `Facebook\FacebookResponse` object
         $url = str_replace('https://www.facebook.com', '', $this->params['url']);
         $response = $fb->get( $url.'events', '454539178063183|0M73E_DJ2lurPKZZZ_GLgjltUN0');
         $data = $response->getDecodedBody();
         $placesIds = array();
         $now = new DateTime;
         foreach ($data['data'] as $item) {
            $date = new DateTime($item['start_time']);
            if($date < $now){
               continue;
            }
            
            // je místo v db?
            if(isset($item['place']) && !isset($placesIds[$item['place']['id']])){
               if(!AdvEventsBase_Model_Places::isPlaceByExternalID('facebook_'.$item['place']['id'])){
                  $place = AdvEventsBase_Model_Places::getNewRecord();
                  $place->{AdvEventsBase_Model_Places::COLUMN_NAME} = $item['place']['name'];
                  $place->{AdvEventsBase_Model_Places::COLUMN_ID_EXTERNAL} = $item['place']['id'];
                  $place->{AdvEventsBase_Model_Places::COLUMN_LAT} = $item['place']['location']['latitude'];
                  $place->{AdvEventsBase_Model_Places::COLUMN_LNG} = $item['place']['location']['longitude'];
                  $place->{AdvEventsBase_Model_Places::COLUMN_ADDRESS} = 
                      $item['place']['name']."\n"
                      .$item['place']['location']['street']."\n"
                      .$item['place']['location']['city'].' '.$item['place']['location']['zip']."\n"
                   ;
                   var_dump($place);
//                  $place->save();
                  $place->{AdvEventsBase_Model_Places::COLUMN_LNG} = $item['place']['location']['longitude'];
               }
               $placesIds[$item['place']['id']] = true;
            }
            
            
            
//            var_dump($item);
         }
         die;
      } catch (Facebook\Exceptions\FacebookResponseException $e) {
         echo 'Graph returned an error: ' . $e->getMessage();
         exit;
      } catch (Facebook\Exceptions\FacebookSDKException $e) {
         echo 'Facebook SDK returned an error: ' . $e->getMessage();
         exit;
      }
   }

}
