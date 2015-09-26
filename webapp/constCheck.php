<?php

   while(1){
     echo "Check";
     sleep(2);
   }


    function makeInsertQueryArray($arr) {

        $ret = array();
        $vars = array();
        $q = '';
        $first = true;
        foreach ($arr as $key => $value) {
          if($first) {
            $field = ":".$key;
            $vars[$field] = $value;
            $q .=" ".$key."=".$field;            
            $first = false;
          } else {
            $field = ":".$key;
            $vars[$field] = $value;
            $q .=", ".$key."=".$field;  
          }
        }
        $ret['q'] = $q;
        $ret['vars'] = $vars;
        return $ret;
    }

  $univ['name'] = 'abc'; 
  $univ['type'] = 1234;


  $ret = makeInsertQueryArray($univ);


  var_dump($ret);





/*
	require_once 'init.php';

      $country_arr = array(
      'north' => 42.6560439, 
      'south' =>  42.4284926,
      'east' =>  1.786542778,
      'west' => 1.407186714
      ); 

        $bounding_box = array(
        		'coordinates'=> array(
        			 array($country_arr['north'],$country_arr['east']),
        			 array($country_arr['north'],$country_arr['west']),
        			 array($country_arr['south'],$country_arr['west']),
        			 array($country_arr['south'],$country_arr['east'])
        			) 
        	);


                $coords = $bounding_box['coordinates'];
        // build bbox string
        $points = array();

        foreach ($coords as $coord) {
            $points[] = $coord[0] . " " . $coord[1];
            echo ", ".$coord[0]." ".$coord[1];
        }
        // complete w/ first point again
        $points[] = $coords[0][0] . " " . $coords[0][1];
        $polystr = 'Polygon((' . join(',', $points) . '))';	

        Profiler::debugPoint(true,__METHOD__, __FILE__, __LINE__,$polystr);

        


var_dump(filter_var('bob1/2.12.3@example.com', FILTER_VALIDATE_EMAIL));


$email = 'gamil\\//.23@gmail.com';

$email = stripcslashes($email);

echo $email;

var_dump(get_magic_quotes_gpc());
*/
//$var = ProcessStatus::getConstants();
exit;

?>
