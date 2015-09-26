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

class URLProcessor {
    /**
     * For a given post, extract URLs and store them, including image_src if that's from a known source like Twitpic,
     * Twitgoo, Yfrog, Instagr.am.
     * @param str $post_text
     * @param int $post_id
     * @param str $network
     * @param Logger $logger
     * @param arr $urls Array of URLs, optionally set, defaults to null
     */
    public static function processPostURLs($post_text, $post_id, $network, $logger, $urls=null) {
        if (!$urls) {
            $urls = Post::extractURLs($post_text);
        }
        if ($urls) {
            $link_dao = DAOFactory::getDAO('LinkDAO');
            $post_dao = DAOFactory::getDAO('PostDAO');
            $post = $post_dao->getPost($post_id, $network);
            if (isset($post->id)) {
                foreach ($urls as $url) {
                    $logger->logInfo("Processing URL $url", __METHOD__.','.__LINE__);
                    $image_src = self::getImageSource($url);

                    //if we have an image_src, the URL is a known image source not in need of expansion
                    $expanded_url = ($image_src!=='')?$url:'';
                    $link_array = array('url'=>$url, 'expanded_url'=>$expanded_url, "image_src"=>$image_src,
                    'post_key'=>$post->id);
                    $link = new Link($link_array);
                    try {
                        $link_dao->insert($link);
                        $logger->logSuccess("Inserted ".$url." ".(($image_src=='')?'':"(thumbnail ".$image_src.") ").
                        "into links table", __METHOD__.','.__LINE__);
                    } catch (DuplicateLinkException $e) {
                        $logger->logInfo($url." ".(($image_src=='')?'':"(thumbnail ".$image_src.") ").
                        " already exists in links table", __METHOD__.','.__LINE__);
                    } catch (DataExceedsColumnWidthException $e) {
                        $logger->logInfo($url." ".(($image_src=='')?'':"(thumbnail ".$image_src.") ").
                        " data exceeds table column width", __METHOD__.','.__LINE__);
                    }
                }
            }
        }
        return $urls;
    }

    /**
     * Get a direct link to an image thumbnail for a given URL if it exists. Currently supports Twitpic, Twitgoo,
     * Picplz, Yfrog, Instagr.am and Lockerz.
     * @param str $url
     * @return str $image_src
     */
    public static function getImageSource($url) {
        $image_src = '';
        if (substr($url, 0, strlen('http://twitpic.com/')) == 'http://twitpic.com/') {
            $image_src = 'http://twitpic.com/show/thumb/'.substr($url, strlen('http://twitpic.com/'));
        } elseif (substr($url, 0, strlen('http://yfrog.com/')) == 'http://yfrog.com/') {
            $image_src = $url.'.th.jpg';
        } elseif (substr($url, 0, strlen('http://twitgoo.com/')) == 'http://twitgoo.com/') {
            $image_src = 'http://twitgoo.com/show/thumb/'.substr($url, strlen('http://twitgoo.com/'));
        } elseif (substr($url, 0, strlen('http://picplz.com/')) == 'http://picplz.com/') {
            $image_src = $url.'/thumb/';
        } elseif (substr($url, 0, strlen('http://instagr.am/')) == 'http://instagr.am/') {
            // see: http://instagr.am/developer/embedding/ for reference
            // the following does a redirect to the actual jpg
            // make a check for an end slash in the url -- if it is there (likely) then adding a second
            // slash prior to the 'media' string will break the expanded url
            if ($url[strlen($url)-1] == '/') {
                $image_src = $url . 'media/';
            } else {
                $image_src = $url . '/media/';
            }
        } elseif (substr($url, 0, strlen('http://lockerz.com/')) == 'http://lockerz.com/') {
            $url = str_replace('lockerz.com/s/', 'plixi.com/p/', $url);
            $image_src = 'http://api.plixi.com/api/tpapi.svc/imagefromurl?url='.$url.'&size=thumbnail';
        }
        return $image_src;
    }
    /**
     * Get final URL if there's a single 302 redirect.
     * @param str $url
     * @param bool $verify_ssl_cert Defaults to true
     * @return str Final URL
     * @throws Exception if there's a cURL error
     */
    public static function getFinalURL($url, $verify_ssl_cert=true) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // seconds
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        if (!$verify_ssl_cert) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception ("cURL error ".curl_errno($ch) ." fetching ".$url." - ".curl_error($ch));
        }
        curl_close($ch);

        $lines = explode("\r\n", $response);
        foreach ($lines as $line) {
            if (stripos($line, 'Location:') === 0) {
                list(, $location) = explode(':', $line, 2);
                return ltrim($location);
            }
        }
        return $url;
    }
}
