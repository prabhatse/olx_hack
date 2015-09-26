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

/* The below code section is not required yet 
if ( version_compare(PHP_VERSION, '5.4', '<') ) {
    exit("ERROR: ThinkUp requires PHP 5.4 or greater. The current version of PHP is ".PHP_VERSION.".");
}

*/
//Register our lazy class loader
//require_once '../php-resque/vendor/autoload.php';
require_once '_lib/class.Loader.php';
require_once '_lib/extlib/facebook/src/Facebook/autoload.php';

Loader::register();

