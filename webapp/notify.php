<?php


require_once 'init.php';

 $_POST['add'] = true;
 $_POST['what'] = "User  is added by Prabhat";
 $_POST['who'] = "Prabhat";

                            $control = new NotifyController();
                          echo $control->go();
//$controller = new NotifyController();

//echo $controller->go();

?>
