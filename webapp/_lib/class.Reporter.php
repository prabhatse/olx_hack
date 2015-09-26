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
class Reporter {
    /**
     * Report installation version back to sternindia.com. If usage reporting is enabled, include instance username
     * and network.
     * @param Instance $instance
     * @return array ($report_back_url, $referer_url, $status, $contents)
     */
    public static function reportVersion(Instance $instance) {
        //Build URLs with appropriate parameters
        $config = Config::getInstance();
        $report_back_url = 'http://sternindia.com/version.php?v='.$config->getValue('EFC_VERSION');

        //Explicity set referer for when this is called by a command line script
        $referer_url = Utils::getApplicationURL();

        //If user hasn't opted out, report back username and network
        if ( $config->getValue('is_opted_out_usage_stats') === true) {
            $report_back_url .= '&usage=n';
        } else {
            $referer_url .= "?u=".urlencode($instance->network_username)."&n=". urlencode($instance->network);
        }

        if (!Utils::isTest()) { //only make live request if we're not running the test suite
            //Make the cURL request
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $report_back_url);
            curl_setopt($c, CURLOPT_REFERER, $referer_url);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            $contents = curl_exec($c);
            $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
            curl_close($c);
        } else {
            $contents = '';
            $status = 200;
        }
        return array($report_back_url, $referer_url, $status, $contents);
    }
}
