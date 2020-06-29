<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
	

	function get_val_from_key($key){
		global $setting_rows;
		foreach($setting_rows as $row){
			if($row['setting_key'] == $key) return $row['setting_val'];
		}
		return false;
	}

	$table_streams = "streams";
	$table_streams_sys = "streams_sys";
	$table_series = "series";
	$table_series_episodes = "series_episodes";

	$cron_job_streams = select_rows($pdo1, $table_local_cron_jobs, "`is_run` = 1");
	$setting_rows = select_rows($pdo1, $table_local_settings, "1 ORDER BY `created_at` DESC");

	$now_time = time();
	$now_day = date("d", $now_time);
	$now_date = date("w", $now_time);
	$now_hour = date("G", $now_time);
	$now_minute = date("i", $now_time);

	$cron_check_stream_step_time = get_val_from_key("check_stream");
	if(!$cron_check_stream_step_time) $cron_check_stream_step_time = DEFAULT_CHECK_STREAM_TIME;
	$cron_check_stream_step_time *= 60;

	foreach($cron_job_streams as $cron){
		if($cron['page'] == 'stream'){
			// cron check stream
			if(cron_check_stream($cron['last_time'], $now_time, $cron_check_stream_step_time)){
				// send message
				$resp = msg_check_stream($cron['param_id']);
				if($resp['count'] > 0){
					// success
					$result_msg = "Sending " . $resp['count'] . " messages about Check Stream " . $resp['channel'];
					$result_status = 1;
				}
				else{
					// fail
					// $result_msg = "Failed! Sending a message about Check Stream " . $resp['channel'];
					$result_msg = "";
					$result_status = 0;
				}
				// update
				$cron_query = "UPDATE `".$table_local_cron_jobs."` SET `last_time` = '".$now_time."' WHERE `id` = '".$cron['id']."';";
				$result1 = update_row($pdo1, $table_local_cron_jobs, $cron_query);
				if($cron['is_log'] && $result_msg != ""){
					$cron_query = "INSERT INTO `".$table_local_cron_logs."` SET `cron_id` = '".$cron['id']."', `message` = '".$result_msg."', `keywords` = '".$resp['channel']."', `status` = '".$result_status."', `created_at` = '".$now_time."';";
					$result2 = insert_row($pdo1, $table_local_cron_logs, $cron_query);
				}
				echo $result_msg . "\n";
			}
		}
		else if($cron['page'] == 'youtube'){
			// cron YouTube
			if(cron_youtube($cron['last_time'], $now_day, $now_date, $now_hour, $now_minute, 
				get_val_from_key("check_youtube_".$cron['param_id']."_day"), 
				get_val_from_key("check_youtube_".$cron['param_id']."_hour"),
				get_val_from_key("check_youtube_".$cron['param_id']."_minute"))){
				// download YouTube
				$resp = auto_download_youtube($cron['param_id']);
				if($resp['status'] == "success"){
					// success
					// $result_msg = "Downloading from YouTube Channel " . $cron['param_id'];
					$result_msg = $resp['message'];
					$result_status = 1;
				}
				else{
					// fail
					// $result_msg = "Failed! Downloading from YouTube Channel " . $cron['param_id'] . " [ " . $resp['message'] . " ]";
					$result_msg = $resp['message'];
					$result_status = 0;
				}
				// update
				$cron_query = "UPDATE `".$table_local_cron_jobs."` SET `last_time` = '".$now_time."' WHERE `id` = '".$cron['id']."';";
				$result1 = update_row($pdo1, $table_local_cron_jobs, $cron_query);
				if($cron['is_log'] && $result_msg != ""){
					$cron_query = "INSERT INTO `".$table_local_cron_logs."` SET `cron_id` = '".$cron['id']."', `message` = '".$result_msg."', `keywords` = '".$resp["keywords"]."', `status` = '".$result_status."', `created_at` = '".$now_time."';";
					$result2 = insert_row($pdo1, $table_local_cron_logs, $cron_query);
				}
				echo $result_msg . "\n";
			}
			else{
				echo "There is no cron job to download YouTube.\n";
			}
		}
	}

	$pdo1 = null;
	$pdo = null;

	die();

?>