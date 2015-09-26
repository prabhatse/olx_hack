<?php
/*
 * Copyright 2014 Empodex PHP Framework.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @copyright 2014-2015 Empoddy Labs.
 * @author Prabhat Shankar <prabhat.singh88[at]gmail.com>
 */
class ResqueController {

    public function sendUserMail() {
      //Mailer::mail('prabhat@sternindia.com',$this->args[1], $this->args[0]);
        var_dump($this->args);
        Mailer::mail(array($this->args[0],array('prabhat@sternindia.com','cc')),$this->args[2], $this->args[1]);
    }

    public function makeNotification() {
    
    }

    public function updateNotification() {
    
    }

    public function incrKeyValue($key, $incr = 1) {
    
    }


    public function perform() {
        $this->{array_shift($this->args)}(true);
        //Mailer::mail('prabhat@sternindia.com',"This is test in user controller",$msg);
    }

}
