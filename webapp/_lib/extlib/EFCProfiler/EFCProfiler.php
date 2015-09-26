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
class EFCProfiler {

	/**
	 * Wether the profiler is enabled or not.
	 *
	 * @var bool
	 */
	public $enabled;

	/**
	 * The logger.
	 *
	 * @var 
	 */
	public $log;

	/**
	 * All of the stored timers.
	 *
	 * @var array
	 */
	protected $timers = array();
	protected $memory = array();

	/**
	 * The included files.
	 *
	 * @var array
	 */
	protected $includedFiles = array();

	/**
	 * Register the logger and application start time.
	 *
	 * @param  \Psr\Log\EFCLogger  $logger
	 * @param  mixed  $startTime
	 * @param  bool  $on
	 * @return void
	 */
	public function __construct(EFCLogger $logger, $startTime = null, $on = true)
	{
		$this->setLogger($logger);
		$this->startTimer('application', $startTime);
		$this->enable($on);
	}

	/**
	 * Set the logger.
	 *
	 * @param  \Psr\Log\EFCLogger  $logger
	 * @return void
	 */
	public function setLogger(EFCLogger $logger)
	{
		$this->log = $logger;
	}

	/**
	 * Enable the profiler.
	 *
	 * @param  bool  $on
	 * @return void
	 */
	public function enable($on = true)
	{
		$this->enabled = $on;
	}

	/**
	 * Disable the profiler.
	 *
	 * @return void
	 */
	public function disable()
	{
		$this->enable(false);
	}

	/**
	 * Check if profiler is enabled
	 *
	 * @return boolean
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Start a timer.
	 *
	 * @param  string  $timer
	 * @param  mixed  $startTime
	 * @return \Profiler\Profiler
	 */
	public function startTimer($timer, $startTime = null)
	{
		$this->timers[$timer] = new EFCTimer($startTime);

    $this->memory[$timer][0] = memory_get_usage();

		return $this;
	}

	/**
	 * End a timer.
	 *
	 * @param string $timer
	 * @param mixed $endTime
	 * @return \Profiler\Profiler
	 */
	public function endTimer($timer, $endTime = null)
	{
		$this->timers[$timer]->end($endTime);

    $this->memory[$timer][1] = memory_get_usage();
		return $this;
	}

	/**
	 * Get the amount of time that passed during a timer.
	 *
	 * @param  string  $timer
	 * @return double
	 */
	public function getElapsedTime($timer)
	{
		return $this->timers[$timer]->getElapsedTime();
	}

	/**
	 * Get a timer.
	 *
	 * @param  string  $timer
	 * @return double
	 */
	public function getTimer($timer)
	{
		return $this->timers[$timer];
	}

	/**
	 * Get all of the timers.
	 *
	 * @return array
	 */
	public function getTimers()
	{
		return $this->timers;
	}

	/**
	 * Get the current application execution time in milliseconds.
	 *
	 * @return int
	 */
	public function getLoadTime()
	{
		return $this->endTimer('application')->getElapsedTime('application');
	}

	/**
	 * Get the current memory usage in a readable format.
	 *
	 * @return string
	 */
	public function getMemoryUsage()
	{
		return $this->readableSize(memory_get_usage(true));
	}

	/**
	 * Get the peak memory usage in a readable format.
	 *
	 * @return string
	 */
	public function getMemoryPeak()
	{
		return $this->readableSize(memory_get_peak_usage());
	}

	/**
	 * Get all of the files that have been included.
	 *
	 * @return array
	 */
	public function getIncludedFiles()
	{
		// We'll cache this internally to avoid running this
		// multiple times.
		if(empty($this->includedFiles))
		{
			$files = get_included_files();

			foreach($files as $filePath)
			{
				$size = $this->readableSize(filesize($filePath));

				$this->includedFiles[] = compact('filePath', 'size');
			}
		}

		return $this->includedFiles;
	}

	/**
	 * A helper to convert a size into a readable format
	 *
	 * @param  int     $size
	 * @param  string  $format
	 * @return string
	 */
	protected function readableSize($size, $format = null)
	{
		// adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
		$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		if(is_null($format))
		{
			$format = '%01.2f %s';
		}

		$lastsizestring = end($sizes);

		foreach ($sizes as $sizestring)
		{
			if ($size < 1024)
			{
				break;
			}

			if ($sizestring != $lastsizestring)
			{
				$size /= 1024;
			}
		}

		// Bytes aren't normally fractional
		if($sizestring == $sizes[0])
		{
			$format = '%01d %s';
		}

		return sprintf($format, $size, $sizestring);
	}

	/**
	 * Render the profiler.
	 *
	 * @return string
	 */
	public function render()
	{
		if($this->enabled)
		{
			$profiler = $this;
			$logger = $this->log;
			$assetPath = __DIR__.'/assets/';

			ob_start();

			include __DIR__ .'/profiler.php';

			return ob_get_clean();
		}
	}

	/**
	 * Render the profiler.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}
}
