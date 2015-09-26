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
class Captcha {
    /**
     * ReCAPTCHA type
     * @var int
     */
    const RECAPTCHA_CAPTCHA = 1;
    /**
     * ThinkUp-powered CAPTCHA
     * @var int
     */
    const THINKUP_CAPTCHA = 2;
    /**
     * Type of CAPTCHA being used; should be equal to either self::RECAPTCHA_CAPTCHA or THINKUP_CAPTCHA.
     * @var int
     */
    var $type;

    public function __construct() {
        $config = Config::getInstance();

        if ($config->getValue('recaptcha_enable')) {
            $this->type = self::RECAPTCHA_CAPTCHA;
            Loader::definePathConstants();
            require_once EFC_WEBAPP_PATH.'_lib/extlib/recaptcha-php-1.10/recaptchalib.php';
        } else {
            $this->type = self::THINKUP_CAPTCHA;
        }
    }

    /**
     * Generate CAPTCHA HTML code
     * @return str CAPTCHA HTML
     */
    public function generate() {
        switch ($this->type) {
            case self::RECAPTCHA_CAPTCHA:
                $config = Config::getInstance();
                $pub_key = $config->getValue('recaptcha_public_key');
                $priv_key = $config->getValue('recaptcha_private_key');
                $code = recaptcha_get_html($pub_key);
                return $code;
                break;
            default:
                $config = Config::getInstance();
                return
                "<label class=\"control-label\" for=\"user_code\">".
                "<img src=\"".$config->getValue('site_root_path'). "session/captcha-img.php\" class=\"img-responsive\" style=\"\">".
                "</label>".
                "<input name=\"user_code\" id=\"user_code\" type=\"text\" class=\"form-control\" required ".
                "placeholder=\"Please enter the code.\">";
                break;
        }
    }

    /**
     * Check the $_POST'ed CAPTCHA inputs match the contents of the CAPTCHA.
     * @return bool
     */
    public function doesTextMatchImage() {
        //if in test mode, assume check is good if user_code is set to 123456
        if (Utils::isTest()) {
            if (isset($_POST['user_code']) && $_POST['user_code'] == '123456') {
                return true;
            } else {
                return false;
            }
        }

        switch ($this->type) {
            case self::RECAPTCHA_CAPTCHA:
                $config = Config::getInstance();
                $priv_key = $config->getValue('recaptcha_private_key');
                $resp = recaptcha_check_answer($priv_key, $_SERVER["REMOTE_ADDR"],
                $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
                if (!$resp->is_valid) {
                    return false;
                } else {
                    return true;
                }
                break;
            default:
                if (strcmp(md5($_POST['user_code']), SessionCache::get('ckey'))) {
                    return false;
                } else {
                    return true;
                }
                break;
        }
    }
}
