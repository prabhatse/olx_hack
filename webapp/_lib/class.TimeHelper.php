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
class TimeHelper {
    /**
     * Override time used to simulate different times in tests
     * @var int
     */
    private static $override_time = 0;
    /**
     * Last time value returned
     * @var int
     */
    private static $last_time = 0;
    /**
     * Override day of year used to simulate different days of the year in tests
     * @var int
     */
    private static $override_day_of_year = 0;
    /**
     * Last day of year value returned
     * @var int
     */
    private static $last_day_of_year = 0;
    /**
     * Get the current day of the year or the override value.  Just like date('z'), but test-friendly.
     * @return int Day of current year
     */
    public static function getDayOfYear() {
        $ret = self::$override_day_of_year ? self::$override_day_of_year : date('z');
        self::$last_day_of_year = $ret;
        return $ret;
    }
    /**
     * Set the day of year for testing. Should ONLY be used inside tests.
     * @param int $doy Day of year
     * @return void
     */
    public static function setDayOfYear($doy) {
        self::$override_day_of_year = $doy;
    }
    /**
     * Clear the override time for testing. Should ONLY be used inside tests.
     * @return void
     */
    public static function clearDayOfYear() {
        self::setDayOfYear(0);
    }
    /**
     * Get the time.  Just like time(), but test friendly
     * @return int Unix timestamp
     */
    public static function getTime() {
        $ret = self::$override_time ? self::$override_time : time();
        self::$last_time = $ret;
        return $ret;
    }
    /**
     * Set the time for testing
     * Should ONLY be used inside tests.
     * @param int $time Unix time stamp
     */
    public static function setTime($time) {
        self::$override_time = $time;
    }
    /**
     * Clear the overriden time for testing
     * Should ONLY be used inside tests.
     */
    public static function clearTime() {
        self::setTime(0);
    }
    /**
     * Get the number of days in a given month and year.
     * @param  int $year
     * @param  int $month
     * @return int
     */
    public static function getDaysInMonth($year, $month) {
        return round((mktime(0, 0, 0, $month+1, 1, $year) - mktime(0, 0, 0, $month, 1, $year)) / 86400);
    }
    /**
     * Convert seconds to a general, non-exact, conversational unit of time, i.e., a day, 5 minutes, 2 weeks.
     * @param  int $seconds
     * @return str
     */
    public static function secondsToGeneralTime($seconds) {
        if ($seconds >= (60*60*24*7)) {
            $weeks = floor($seconds / (60*60*24*7));
            return $weeks." week".($weeks==1?'':'s');
        }
        if ($seconds >= (60*60*24)) {
            $days = floor($seconds / (60*60*24));
            return $days." day".($days==1?'':'s');
        }
        if ($seconds >= (60*60)) {
            $hours = floor($seconds / (60*60));
            return $hours." hour".($hours==1?'':'s');
        }
        if ($seconds >= 60) {
            $minutes = floor($seconds / 60);
            return $minutes." minute".($minutes==1?'':'s');
        }

        return $seconds." second".($seconds==1?'':'s');
    }
    /**
     * Get exact number of days, hours, minutes, and seconds a total number of seconds represents.
     *
     * @param int $seconds How many seconds
     * @return arr Units of time array ('d'=>$days, 'h'=> $hours, 'm'=>$minutes, 's'=>$seconds)
     */
    public static function secondsToExactTime($seconds) {
        $seconds_in_a_minute = 60;
        $seconds_in_an_hour  = 60 * $seconds_in_a_minute;
        $seconds_in_a_day    = 24 * $seconds_in_an_hour;

        // extract days
        $days = floor($seconds / $seconds_in_a_day);

        // extract hours
        $hour_seconds = $seconds % $seconds_in_a_day;
        $hours = floor($hour_seconds / $seconds_in_an_hour);

        // extract minutes
        $minute_seconds = $hour_seconds % $seconds_in_an_hour;
        $minutes = floor($minute_seconds / $seconds_in_a_minute);

        // extract the remaining seconds
        $remaining_seconds = $minute_seconds % $seconds_in_a_minute;
        $seconds = ceil($remaining_seconds);

        // return the final array
        return array(
            'd' => (int) $days,
            'h' => (int) $hours,
            'm' => (int) $minutes,
            's' => (int) $seconds,
        );
    }
    /**
     * Get number of days since January 1 of the current year.
     * @param  int $time Time to calculate from; defaults to time()
     * @return int number of days from January 1 of current year to today
     */
    public static function getDaysSinceJanFirst($time = null) {
        if (!isset($time)) {
            $time = time();
        }
        return (((int) date('z', $time)));
    }
}
