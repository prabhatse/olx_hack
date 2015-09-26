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
class AppConfig {

    /**
     * Data validation array
     * @var array Collection oif validation string for data input
     */
    static $config_data = array(
        'is_registration_open' => array(
            'type' => 'checkbox',
            'title' => 'Open Registration',
            'required' => false,
            'default' => 'false',
            'match' => '/^(true|false)$/',
            'match_message' => 'Must be true or false'
            ),
        'recaptcha_enable' => array(
            'type' => 'checkbox',
            'title' => 'Enable ReCAPTCHA',
            'required' => false,
            'default' => 'false',
            'match' => '/^true$/',
            'match_message' => 'Must be true',
            'dependencies' => array('recaptcha_public_key','recaptcha_private_key')
            ),
        'recaptcha_public_key' => array(
            'type' => 'text',
            'title' => 'ReCAPTCHA Public Key',
            'required' => false,
            'match' => '/\w/',
            'match_message' => '',
            'default' => '',
            
            ),
        'recaptcha_private_key' => array(
            'type' => 'text',
            'title' => 'ReCAPTCHA Private Key',
            'required' => false,
            'match' => '/\w/',
            'match_message' => '',
            'default' => '',
            ),

            /**
             * Currently there's a bug with checkboxes which have a default value of true. When you uncheck the box,
             * and save the form, no value gets submitted for the checkbox, so the false value doesn't get saved.
             * As such, right now, checkbox default values must be false.
             * Therefore, for now, making this option 'is_api_disabled' instead of 'is_api_enabled.'
             * @TODO: Once that bug is fixed, change this to Enable JSON API with default value true.
             */
        'is_api_disabled' => array(
            'type' => 'checkbox',
            'title' => 'Disable JSON API',
            'required' => false,
            'default' => 'false',
            'match' => '/^true$/',
            'match_message' => ' be true'
            ),
        'is_embed_disabled' => array(
            'type' => 'checkbox',
            'title' => 'Disable ability to embed threads on external web pages',
            'required' => false,
            'default' => 'false',
            'match' => '/^true$/',
            'match_message' => ' be true'
            ),
        'is_log_verbose' => array(
            'type' => 'checkbox',
            'title' => 'See the verbose, unformatted developer log on the Capture Data screen',
            'required' => false,
            'default' => 'false',
            'match' => '/^(true|false)$/',
            'match_message' => 'Must be true or false'
            ),
        'is_opted_out_usage_stats' => array(
            'type' => 'checkbox',
            'title' => 'Usage reporting helps us improve ThinkUp',
            'required' => false,
            'default' => 'false',
            'match' => '/^(true|false)$/',
            'match_message' => 'Must be true or false'
            ),
        'default_instance' => array(
            'type' => 'text',
            'title' => 'The service user to display by default',
            'required' => false,
            'default' => '0',
            'match' => '/^[0-9]{1,}$/',
            'match_message' => ' be numeric'
            ),
        'is_subscribed_to_beta' => array(
            'type' => 'checkbox',
            'title' => 'Get beta upgrades',
            'required' => false,
            'default' => 'false',
            'match' => '/^true$/',
            'match_message' => 'Must be true'
            )
            );

            /**
             * Getter for db config data array
             * @return array Application settings configuration and validation data array/hash
             */
            public static function getConfigData() {
                return self::$config_data;
            }

            /**
             * Getter for db config data value
             * @param str Key for apllication value
             * @return array Application settings configuration and validation data array/hash
             */
            public static function getConfigValue($key) {
                $value = isset(self::$config_data[$key] ) ? self::$config_data[$key] : false;
                return $value;
            }
}