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
class EFCTimer {

	/**
	 * The time at which the timer was started.
	 *
	 * @var double
	 */
	protected $startTime;

	/**
	 * The time at which the timer was ended.
	 *
	 * @var double
	 */
	protected $endTime;

	/**
	 * Start the timer.
	 *
	 * @param  double|null  $startTime
	 * @return void
	 */
	public function __construct($startTime = null)
	{
		$this->startTime = $startTime ?: microtime(true);
	}

	/**
	 * End the timer.
	 *
	 * @param  double|null  $time
	 * @return void
	 */
	public function end($time = null)
	{
		// The timer should be ended only once.
		if(is_null($this->endTime))
		{
			$this->endTime = $time ?: microtime(true);
		}
	}

	/**
	 * Get the amount of time (in milliseconds) that elapsed while the timer
	 * was turned on.
	 *
	 * @return double
	 */
	public function getElapsedTime()
	{
		// Make sure the timer is turned off before we attempt
		// to measure how much time elapsed.
		$this->end();

		return round(1000 * ($this->endTime - $this->startTime), 4);
	}
}
