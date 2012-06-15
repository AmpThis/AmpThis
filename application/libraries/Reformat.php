<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Reformat {
		
	public function Birthday($birthday) {
		$birthday = explode('-', str_replace(' ', '-', $birthday));
		return $birthday[1] . '/' . $birthday[2] . '/' . $birthday[0];	
	}
		
	public function Age($birthday) {
		list($year,$month,$day) = explode("-",$birthday);
		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;
		if ($month_diff < 0) $year_diff--;
		elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
		return $year_diff;
	}
		
	public function BG($next_bg_at) {
		$no_spaces = str_replace(' ', '-', $next_bg_at);
		$bg_value = explode('-', $no_spaces);
		$num = explode(':', $bg_value[3]);
		return $bg_value[3];
	}
		
	public function StripDashes($value) {
		return ereg_replace('[^A-Za-z0-9]','',$value);
	}
		
	public function RoundDecimal($value, $index) {
		return round($value, $index);
	}
	
	public function DivClass($age, $next_bg_due = NULL) {
		$class = '';
		
		if($age <= '13') $class .= 'pediatric ';
		if($next_bg_due == '00:00:00') $class .= 'alert';
		
		return $class;
	}
		
	public function RandomString() {
		//these are the valid characters in which the string is built on
		$valid_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789~!@#$%^&*()_+}{[]';
		//here is the length the random string will be
		$length = 10;
		//start with an empty random string
		$random_string = '';
		//count the number of chars in the valid chars string so we know how many choices we have
		$num_valid_chars = strlen($valid_chars);
		//repeat the steps until we've created a string of the right length
		for($i = 0; $i < $length; $i++) :
			//pick a random number from 1 up to the number of valid chars
			$random_pick = mt_rand(1, $num_valid_chars);
			//take the random character out of the string of valid chars
			//subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
			$random_char = $valid_chars[$random_pick - 1];
			//add the randomly-chosen char onto the end of our string so far
			$random_string .= $random_char;
		endfor;	
		//return our finished random string
		return $random_string;
	}
		
	public function RandomNumber() {
		//these are the valid characters in which the string is built on
		$valid_chars = '1234567890';
		//here is the length the random string will be
		$length = 10;
		//start with an empty random string
		$random_string = '';
		//count the number of chars in the valid chars string so we know how many choices we have
		$num_valid_chars = strlen($valid_chars);
		//repeat the steps until we've created a string of the right length
		for($i = 0; $i < $length; $i++) :
			//pick a random number from 1 up to the number of valid chars
			$random_pick = mt_rand(1, $num_valid_chars);
			//take the random character out of the string of valid chars
			//subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
			$random_char = $valid_chars[$random_pick - 1];
			//add the randomly-chosen char onto the end of our string so far
			$random_string .= $random_char;
		endfor;	
		//return our finished random string
		return $random_string;
	}
		
	public function ConvertDateTime($datetime) {
		$date = new DateTime($datetime);
		return $date->format('m/d/Y g:i:sA');	
	}
		
	// Time format is UNIX timestamp or
	// PHP strtotime compatible strings
	function dateDiff($time1, $time2, $precision = 6) {
		// If not numeric then convert texts to unix timestamps
		if (!is_int($time1)) {
			$time1 = strtotime($time1);
		}
		if (!is_int($time2)) {
			$time2 = strtotime($time2);
		}
		
		// If time1 is bigger than time2
		// Then swap time1 and time2
		/*if ($time1 > $time2) {
		$ttime = $time1;
		$time1 = $time2;
		$time2 = $ttime;
		}*/
		
		// Set up intervals and diffs arrays
		$intervals = array('year','month','day','hour','minute','second');
		$diffs = array();
		
		// Loop thru all intervals
		foreach ($intervals as $interval) {
			// Set default diff to 0
			$diffs[$interval] = 0;
			// Create temp time from time1 and interval
			$ttime = strtotime("+1 " . $interval, $time1);
			// Loop until temp time is smaller than time2
			while ($time2 >= $ttime) {
				$time1 = $ttime;
				$diffs[$interval]++;
				// Create new temp time from time1 and interval
				$ttime = strtotime("+1 " . $interval, $time1);
			}
		}
		
		$count = 0;
		$times = array();
		// Loop thru all diffs
		foreach ($diffs as $interval => $value) {
			// Break if we have needed precission
			if ($count >= $precision) {
				break;
			}
			// Add value and interval 
			// if value is bigger than 0
			if ($value > 0) {
				// Add s if value is not 1
				if ($value != 1) {
					$interval .= "s";
				}
				// Add value and interval to times array
				$times[] = $value;
				$count++;
			}
		}
	
		// Return string with times
		return implode(":", $times);
	}
	  
	function dateExp($timestamp){
		$timestamp = intval($timestamp);  // make sure we're dealing wit 'numbers'
		return ($timestamp > time());     // return TRUE if $timestamp is in the future
	}
	
	function CurrentTime() {
		$now = new DateTime(); 
		echo $now->format("Y-m-d H:i:s"); 
	}
	
}