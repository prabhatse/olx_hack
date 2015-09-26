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
class Session {
    /**
     * Name for Long-Session Cookie
     * @var str
     */
    const COOKIE_NAME = 'FSIN';

    static $prevCookie = null;

    /**
     * Check if we have an active session.
     * If not, check if we have a long term sessions cookie and activate a session.
     * @return bool Is user logged into ThinkUp
     */
    public static function isLoggedIn() {
        //First check if $_COOKIE is still in database.

        if (SessionCache::get('user_set') || SessionCache::get('fb_token')) {
                return true;
        }
        if (!empty($_COOKIE[self::COOKIE_NAME])) {

            $cookie_dao = DAOFactory::getDAO('CookieDAO');
            $email = $cookie_dao->getEmailByCookie($_COOKIE[self::COOKIE_NAME]);
            if ($email) {
                $user_dao = DAOFactory::getDAO('UserDAO');
                $user = $user_dao->getByEmail($email);
                if ($user) {
                    self::completeLogin($user);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return bool Is user logged into ThinkUp an admin
     * @TODO: we can change this function to return User 
     * type to get authentication in system
     * function getUserType
     */

    
    public static function getUserType() {
        if (SessionCache::isKeySet('user_type')) {
            return SessionCache::get('user_type');
        } else {
            return false;
        }
    }


    /* We can skip this function */
    public static function isSuperAdmin() {

        if (SessionCache::isKeySet('user_type') ) {

            $config = App::getInstance('UserType');            
            //Profiler::debugPoint(true,__METHOD__, __FILE__, __LINE__);
            if (SessionCache::get('user_type') == $config->getValue('SUPER_ADMIN') ) {

                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @return str Currently logged-in stern username (email address)
     */
    public static function getLoggedInUser() {
        if (self::isLoggedIn()) {
            return SessionCache::get('user_id');
        } else {
            return null;
        }
    }


    /**
     * Complete login action using Facebook
     * @param User $user
     * @return void
     */
    public static function completeLoginUsingFb($fb_token) {
        
        $email = 'coolprit.prabhat@gmail.com';
        SessionCache::put('fb_token',$fb_token);
        // set a CSRF token
        $cookie_dao = DAOFactory::getDAO('CookieDAO');
        $set_long_term = true;

        if (!empty($_COOKIE[self::COOKIE_NAME])) {
            $email = $cookie_dao->getEmailByCookie($_COOKIE[self::COOKIE_NAME]);
            $set_long_term = $email != $email;
        }

        if ($set_long_term) {
            $cookie = $cookie_dao->generateForEmail($email);
            if (!headers_sent()) {
                setcookie(self::COOKIE_NAME, $cookie, time()+(60*60), '/', self::getCookieDomain());
                SessionCache::put('cookie',$cookie);
            }
        }
    }



    /**
     * Complete login action
     * @param User $user
     * @return void
     */
    public static function completeLogin($user) {
        SessionCache::put('user_id',$user->id);
        SessionCache::put('user_set',true);
        SessionCache::put('first_name',$user->first_name);
        SessionCache::put('last_name',$user->last_name);
        SessionCache::put('user_email',$user->email);
        // set a CSRF token
        SessionCache::put('csrf_token', uniqid(mt_rand(), true));
        if (Utils::isTest()) {
            SessionCache::put('csrf_token', 'TEST_CSRF_TOKEN');
        }

        // check for and validate an existing long-term cookie before creating one
        $cookie_dao = DAOFactory::getDAO('CookieDAO');
        $set_long_term = true;

        if (!empty($_COOKIE[self::COOKIE_NAME])) {
            $email = $cookie_dao->getEmailByCookie($_COOKIE[self::COOKIE_NAME]);
            $set_long_term = $email != $user->email;
        }

        if ($set_long_term) {
            $cookie = $cookie_dao->generateForEmail($user->email);
            if (!headers_sent()) {
                setcookie(self::COOKIE_NAME, $cookie, time()+(60*60), '/', self::getCookieDomain());
                SessionCache::put('cookie',$cookie);
            }
        }
    }

    /**
     * Log out and kill long-term cookie.
     * @return void
     */
    public static function logout() {
    	
        
        SessionCache::unsetKey('user_id');
        SessionCache::unsetKey('first_name');
        SessionCache::unsetKey('last_name');

        SessionCache::unsetKey('user_email');
        SessionCache::unsetKey('user_set');


        if (!empty($_COOKIE[self::COOKIE_NAME])) {
            if (!headers_sent()) {
                setcookie(self::COOKIE_NAME, '', time() - (60*60), '/', self::getCookieDomain());
            }
            $cookie_dao = DAOFactory::getDAO('CookieDAO');
            $cookie_dao->deleteByCookie($_COOKIE[self::COOKIE_NAME]);
        }

        
        //var_dump($_SESSION);
        //SessionCache::unsetPermission();
        //var_dump($_SESSION);

        //session_destroy();
    }

    /**
     * Generate a domain for setting cookies
     * @return str domain to use
     */
    public static function getCookieDomain() {
        if (empty($_SERVER['HTTP_HOST'])) {
            return false;
        }
        $parts = explode('.', $_SERVER['HTTP_HOST']);
        if (count($parts) == 1) {
            return $parts[0];
        }

        return '.'.$parts[count($parts)-2].'.'.$parts[count($parts)-1];
    }

    /**
     * Returns a CSRF token that should be used whith _GETs and _POSTs requests.
     * @return str CSRF token
     */
    public static function getCSRFToken() {
        if (self::isLoggedIn()) {
            return SessionCache::get('csrf_token');
        } else {
            return null;
        }
    }
}
