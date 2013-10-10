<?php
/*
UpdraftPlus Addon: fixtime:Fix Time
Description: Allows you to specify the exact time at which backups will run
Version: 1.2
Shop: /shop/fix-time/
Latest Change: 1.6.57
*/

// TODO: Allow selection of day of week when on >daily schedule

$updraftplus_addon_fixtime = new UpdraftPlus_AddOn_FixTime;

class UpdraftPlus_AddOn_FixTime {

	function __construct() {
		add_filter('updraftplus_schedule_firsttime_files', array($this, 'starttime_files'));
		add_filter('updraftplus_schedule_firsttime_db', array($this, 'starttime_db'));
		add_filter('updraftplus_schedule_showfileopts', array($this, 'config_startfile'));
		add_filter('updraftplus_schedule_showdbopts', array($this, 'config_startdb'));
		add_filter('updraftplus_fixtime_ftinfo', array($this, 'ftinfo'));
	}

	function starttime_files($val) {
		return $this->compute('files');
	}

	function starttime_db($val) {
		return $this->compute('db');
	}

	function parse($start_time) {
		preg_match("/^(\d+):(\d+)$/", $start_time, $matches);
		if (empty($matches[1]) || !is_numeric($matches[1]) || $matches[1]>23) {
			$start_hour = 0;
		} else {
			$start_hour = (int)$matches[1];
		}
		if (empty($matches[2]) || !is_numeric($matches[2]) || $matches[1]>59) {
			$start_minute = 5;
			if ($start_minute>60) {
				$start_minute = $start_minute-60;
				$start_hour++;
				if ($start_hour>23) $start_hour=0;
			}
		} else {
			$start_minute = (int)$matches[2];
		}
		return array($start_hour, $start_minute);
	}

	function compute($whichtime) {
		// Returned value should be in UNIX time.

		$unixtime_now = time();
		// Convert to date
		$now_timestring_gmt = gmdate('Y-m-d H:i:s', $unixtime_now);

		// Convert to blog's timezone
		$now_timestring_blogzone = get_date_from_gmt($now_timestring_gmt, 'Y-m-d H:i:s');

		$int_key = ('db' == $whichtime) ? '_database' : '';
		$sched = (isset($_POST['updraft_interval'.$int_key])) ? $_POST['updraft_interval'.$int_key] : 'manual';

		// Was a particular week-day specified?
		if (isset($_POST['updraft_startday_'.$whichtime]) && ('weekly' == $sched || 'monthly' == $sched || 'fortnightly' == $sched)) {
			// Get specified day of week in range 0-6
			$startday = min(absint($_POST['updraft_startday_'.$whichtime]), 6);
			// Get today's day of week in range 0-6
			$day_today_blogzone = get_date_from_gmt($now_timestring_gmt, 'w');
			if ($day_today_blogzone != $startday) {
				if ($startday<$day_today_blogzone) $startday+=7;
				$new_startdate_unix = $unixtime_now + ($startday-$day_today_blogzone)*86400;
				$now_timestring_blogzone = get_date_from_gmt(gmdate('Y-m-d H:i:s', $new_startdate_unix), 'Y-m-d H:i:s');
			}
		}

		// HH:MM, in blog time zone
		// This function is only called from the options validator, so we don't read the current option
		//$start_time = UpdraftPlus_Options::get_updraft_option('updraft_starttime_'.$whichtime);
		$start_time = (isset($_POST['updraft_starttime_'.$whichtime])) ? $_POST['updraft_starttime_'.$whichtime] : '00:00';

		list ($start_hour, $start_minute) = $this->parse($start_time);

		// Now, convert the start time HH:MM from blog time to UNIX time
		$start_time_unix = get_gmt_from_date(substr($now_timestring_blogzone,0,11).sprintf('%02d', $start_hour).':'.sprintf('%02d', $start_minute).':00', 'U');

		// That may have already passed for today
		if ($start_time_unix<time()) {
			if  ('weekly' == $sched || 'monthly' == $sched || 'fortnightly' == $sched) {
				$start_time_unix = $start_time_unix + 86400*7;
			} else {
				$start_time_unix=$start_time_unix+86400;
			}
		}

		return $start_time_unix;
	}

	private function day_selector($id) {
		global $wp_locale;

		$day_selector = '<select name="'.$id.'" id="'.$id.'">';

		$opt = UpdraftPlus_Options::get_updraft_option($id, 0);

		for ($day_index = 0; $day_index <= 6; $day_index++) :
			$selected = ($opt == $day_index) ? 'selected="selected"' : '';
			$day_selector .= "\n\t<option value='" . esc_attr($day_index) . "' $selected>" . $wp_locale->get_weekday($day_index) . '</option>';
		endfor;
		$day_selector .= '</select>';
		return $day_selector;
	}

	function config_startfile($disp) {

		$start_time = UpdraftPlus_Options::get_updraft_option('updraft_starttime_files');
		list ($start_hour, $start_minute) = $this->parse($start_time);

		return __('starting from next time it is','updraftplus').' '.$this->day_selector('updraft_startday_files').'<input title="'.__('Enter in format HH:MM (e.g. 14:22).','updraftplus').' '.htmlspecialchars(__('The time zone used is that from your WordPress settings, in Settings -> General.', 'updraftplus')).'" type="text" style="width: 48px;" maxlength="5" name="updraft_starttime_files" value="'.sprintf('%02d', $start_hour).':'.sprintf('%02d', $start_minute).'">';

	}

	function config_startdb() {

		$start_time = UpdraftPlus_Options::get_updraft_option('updraft_starttime_db');
		list ($start_hour, $start_minute) = $this->parse($start_time);

		return __('starting from next time it is','updraftplus').' '.$this->day_selector('updraft_startday_db').'<input title="'.__('Enter in format HH:MM (e.g. 14:22).','updraftplus').' '.htmlspecialchars(__('The time zone used is that from your WordPress settings, in Settings -> General.', 'updraftplus')).'" type="text" style="width: 48px;" maxlength="5" name="updraft_starttime_db" value="'.sprintf('%02d', $start_hour).':'.sprintf('%02d', $start_minute).'">';

	}

	function ftinfo() {
		return '';
	}
}

?>