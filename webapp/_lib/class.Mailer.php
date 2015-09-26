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

class Mailer {
    /**
     * For testing purposes only; this is the name of the file the latest email gets written to.
     * @var str
     */
    const EMAIL = '/latest_email';
    /**
     * Send email from ThinkUp installation. Will attempt to send via Mandrill if the key has been set.
     * If you're running tests, just write the message headers and contents to
     * the file system in the data directory.
     * @param str $to A valid email address
     * @param str $subject
     * @param str $message
     */
    public static function mail($to, $subject, $message) {
        $config = Config::getInstance();
        $mandrill_api_key = $config->getValue('mandrill_api_key');                 
        if (isset($mandrill_api_key) && $mandrill_api_key != '') {
            self::mailViaMandrill($to, $subject, $message);
        } else {
            self::mailViaPHP($to, $subject, $message);
        }
        //Profiler::debugPoint(1,__METHOD__, __FILE__, __LINE__);
    }

    public function perform() {
        self::mail($this->args[0],$this->args[1],$this->args[2]);
    }
    /**
     * Send an HTML email from ThinkUp installation. Will only be sent if a Mandrill API key is set, as it makes use of
     * Mandrill's HTML templating system and API.
     * @param str $to A valid email address
     * @param str $subject Subject of the email
     * @param str $template_name Name of a template in the mandrill system
     * @param arr $template_params Associative array of parameters
     */
    public static function mailHTMLViaMandrillTemplate($to, $subject, $template_name, $template_params) {
        $config = Config::getInstance();
        $host = Utils::getApplicationHostName();
        $app_title = $config->getValue('app_title_prefix'). "Empoddy Labs";
        $mandrill_api_key = $config->getValue('mandrill_api_key');
        if (Utils::isEmpoddyLabs()) {
            $from_email = 'empoddy@gmail.com';
        } else {
            $from_email = "notifications@${host}";
        }

        try {
            require_once EFC_WEBAPP_PATH.'_lib/extlib/mandrill/Mandrill.php';
            $mandrill = new Mandrill($mandrill_api_key);
            $message = array('subject' => $subject, 'from_email' => $from_email,
            'from_name' => $app_title, 'to' => array( array( 'email' => $to, 'name' => $to ) ),
            'global_merge_vars' => array());

            foreach ($template_params as $key=>$val) {
                $message['global_merge_vars'][] = array('name'=>$key,
                    'content'=>$val);
            }

            //don't send email when running tests, just write it to the filesystem for assertions
            if (Utils::isTest()) {
                self::setLastMail(json_encode($message));
                if (preg_match('/keyerror/', $to)) {
                    throw new Mandrill_Invalid_Key('Invalid api key');
                } elseif (preg_match('/templateerror/', $to)) {
                    throw new Mandrill_Unknown_Template('Unknown template');
                }
            } else {
                $async = false;
                $ip_pool = 'Main Pool';
                $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message,
                $async, $ip_pool);
            }
        } catch (Mandrill_Unknown_Template $unknown_template_error) {
            // We want to be able to handle this specific error differently.
            throw $unknown_template_error;
        } catch (Mandrill_Error $e) {
            // Write contents of the email to file for easier debugging, parsing it in the log is difficult
            self::setLastMail(json_encode($message));

            throw new Exception('An error occurred while sending email to '.$to.' from '.$from_email.' via Mandrill. '
                . get_class($e) . ': ' . $e->getMessage() . '.  Message JSON written to '
                . (FileDataManager::getDataPath(Mailer::EMAIL)) );
        }
    }
    /**
     * Return the contents of the last email Mailer "sent" out.
     * For testing purposes only; this will return nothing in production.
     * @return str The contents of the last email sent
     */
    public static function getLastMail() {
        $test_email_file = FileDataManager::getDataPath(Mailer::EMAIL);
        if (file_exists($test_email_file)) {
            return file_get_contents($test_email_file);
        } else {
            return '';
        }
    }
    /**
     * Return the contents of the last email Mailer "sent" out.
     * For testing purposes only; this will return nothing in production.
     * @return str The contents of the last email sent
     */
    private static function setLastMail($message) {
        $test_email = FileDataManager::getDataPath(Mailer::EMAIL);
        $fp = fopen($test_email, 'w');
        fwrite($fp, $message);
        fclose($fp);
    }
    /**
     * Send email from ThinkUp installation via PHP's built-in mail() function.
     * If you're running tests, just write the message headers and contents to the file system in the data directory.
     * @param str $to A valid email address
     * @param str $subject
     * @param str $message
     */
    public static function mailViaPHP($to, $subject, $message) {
        $config = Config::getInstance();

        $app_title = $config->getValue('app_title_prefix'). "Empoddy Labs";
        $host = Utils::getApplicationHostName();

        $mail_header = "From: \"Empoddy Labs\" <empoddy@gmail.com>\r\n";
        //$mail_header = "From: \"{$app_title}\" <notifications@{$host}>\r\n";
        $mail_header .= "X-Mailer: PHP/".phpversion();

        //don't send email when running tests, just write it to the filesystem for assertions
        if (Utils::isTest()) {
            self::setLastMail($mail_header."\n" .
                                "to: $to\n" .
                                "subject: $subject\n" .
                                "message: $message");
        } else {
            mail($to, $subject, $message, $mail_header);
        }
    }
    /**
     * Send email from ThinkUp installation via Mandrill's API.
     * If you're running tests, just write the message headers and contents to the file system in the data directory.
     * @param str $to A valid email address
     * @param str $subject
     * @param str $message
     */
    public static function mailViaMandrill($to, $subject, $message) {
        $config = Config::getInstance();

        $app_title = $config->getValue('app_title_prefix') . "Empoddy Labs";

        $host = Utils::getApplicationHostName();

        $mandrill_api_key = $config->getValue('mandrill_api_key');
                        
        if (Utils::isEmpoddyLabs()) {
            $from_email = 'empoddy@gmail.com';
        } else {
            $from_email = "notifications@${host}";
        }

        try {
            require_once EFC_WEBAPP_PATH.'_lib/extlib/mandrill/Mandrill.php';
            $mandrill = new Mandrill($mandrill_api_key);
            $tos = array();
            foreach($to as $key => $value) {
                     $tos[] = array('email' => $value, 'name' => $value); 
            }
            //$message = array( 'text' => $message, 'subject' => $subject, 'from_email' => $from_email,
            //'from_name' => $app_title, 'to' => array( array( 'email' => $to, 'name' => $to ) ) );
            $message = array( 'text' => $message, 'subject' => $subject, 'from_email' => $from_email,
            'from_name' => $app_title, 'to' => $tos );

            //don't send email when running tests, just write it to the filesystem for assertions
            if (Utils::isTest()) {
                self::setLastMail(json_encode($message));
            } else {
                $async = true;
                $ip_pool = "Main pool";
                $result = $mandrill->messages->send($message, $async, $ip_pool);
                //DEBUG
                //print_r($result);
            }
        } catch (Mandrill_Error $e) {
            throw new Exception('An error occurred while sending email via Mandrill. ' . get_class($e) .
            ': ' . $e->getMessage());
        }
    }
}
