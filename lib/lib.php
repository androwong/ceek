<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


    // database connect
    function db_connect($hostname, $port, $username, $password, $database){
        if($port > 0) $dsn = "mysql:host=".$hostname.";dbname=".$database.";port=".$port.";charset=".CHARSET;
        else $dsn = "mysql:host=".$hostname.";dbname=".$database.";charset=".CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try{
            $pdo = new PDO($dsn, $username, $password, $options);
            return $pdo;
        }
        catch(PDOException $e){
            return null;
        }
    }
    
	// insert table
	function insert_row($pdo, $table, $query){
		try{
			// insert
			$stmt = $pdo->prepare($query);
			$stmt->execute();
			return $pdo->lastInsertId();
		}
		catch(PDOException $e){
			echo "error: insert row";
			var_dump($e);
			return false;
		}

		return true;
	}

	// select order by sentTime
	function select_rows($pdo, $table, $where="1"){
		try{
			// select
			$sql = "SELECT * FROM `".$table."` WHERE ".$where;
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			return $stmt->fetchAll();
		}
		catch(PDOException $e){
			echo "error: select row";
			var_dump($e);
			return false;
		}
		return true;
	}

	// update table - where key0 = value0
	function update_row($pdo, $table, $query){
		// where
		
		try{
			// update
			$stmt = $pdo->prepare($query);
			// $stmt->execute(array_push($values, $values[0]));
			$stmt->execute();
		}
		catch(PDOException $e){
			echo "error: update row";
			var_dump($e);
			return false;
		}

		return true;
	}

	// delete rows from table
	function delete_rows($pdo, $table, $where){
		try{
			// delete
			$sql = "DELETE FROM `".$table."` WHERE ".$where;
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
		}
		catch(PDOException $e){
			echo "error: delete rows";
			return false;
		}

		return true;
	}

	// redirect url
	function redirect($url){
		echo "<script>window.location='".$url."'</script>";
		exit();
    }

	// get array default|custom
	function get_customize($custom_array, $default_array=array()){
		$merged = $default_array;

		if(is_array($custom_array)){
			foreach($custom_array as $key => $value){
				if(is_array($value) && isset($merged[$key]) && is_array($merged[$key])){
					$merged[$key] = get_customize($value, $merged[$key]);
				}
				else if($value){
					$merged[$key] = $value;
				}
			}
		}

		return $merged;
    }
    
    // generate random string with length
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
	}
	
	// get connection info from log file
	function getConnectionInfoFromFile(){
		global $db_conn, $db_host, $db_port, $db_user, $db_pass, $db_database, $pdo1, $table_local_dbs;
		if(FLAG_SAVE_LOG_FILE){
			$log_file = LIB_PATH . "db_log.txt";
			// if($db_host . $db_user . $db_pass . $db_database == ""){
				// read database log file
				$fp = fopen($log_file, "r");
				$logs = fread($fp, filesize($log_file));
				fclose($fp);
				$re = '/host:([^,]+),port:([^,]+),user:([^,]+),pass:([^,]+),database:([^,]+)/m';
				preg_match_all($re, $logs, $matches, PREG_PATTERN_ORDER);
				if(count($matches) >= 6){
					$db_conn = 1;
					$db_host = $matches[1][0];	
					$db_port = $matches[2][0];	
					$db_user = $matches[3][0];	
					$db_pass = $matches[4][0];	
					$db_database = $matches[5][0];	
	
					// session & restart
					$_SESSION['db_conn'] = $db_conn;
					$_SESSION['db_host'] = $db_host;
					$_SESSION['db_port'] = $db_port;
					$_SESSION['db_user'] = $db_user;
					$_SESSION['db_pass'] = $db_pass;
					$_SESSION['db_database'] = $db_database;
	
					//redirect(WEB_PATH . "index.php" . (($next_page!='')?"?page=".$next_page:""));
					return true;
				}
			// }
		}
		else{
			// read informations to database
			$rows = select_rows($pdo1, $table_local_dbs, "1 ORDER BY `id` DESC LIMIT 0, 1");
			if(!empty($rows)){
				$db_conn = 1;
				$db_host = $rows[0]['host'];	
				$db_port = $rows[0]['port'];
				$db_user = $rows[0]['username'];
				$db_pass = $rows[0]['password'];
				$db_database = $rows[0]['db_name'];

				// session & restart
				$_SESSION['db_conn'] = $db_conn;
				$_SESSION['db_host'] = $db_host;
				$_SESSION['db_port'] = $db_port;
				$_SESSION['db_user'] = $db_user;
				$_SESSION['db_pass'] = $db_pass;
				$_SESSION['db_database'] = $db_database;

				//redirect(WEB_PATH . "index.php" . (($next_page!='')?"?page=".$next_page:""));
				return true;
			}
		}
		return false;
	}

	// user permission
	function getPermissionOfPage($page){
		global $permission, $permissionPages, $default_permission_page;
		if(!empty($permissionPages[$permission])){
			return (in_array($page, $permissionPages[$permission])?$page:$default_permission_page[$permission]);
		}
		else{
			return "login";
		}
	}
	
	// function getting url contents using curl
	function url_get_contents($url, $useragent='cURL', $headers=false, $follow_redirects=true, $debug=true) {

		// initialise the CURL library
		$ch = curl_init();

		// specify the URL to be retrieved
		curl_setopt($ch, CURLOPT_URL,$url);

		// we want to get the contents of the URL and store it in a variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

		// specify the useragent: this is a required courtesy to site owners
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

		// ignore SSL errors
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// return headers as requested
		if ($headers==true){
			curl_setopt($ch, CURLOPT_HEADER,1);
		}

		// only return headers
		if ($headers=='headers only') {
			curl_setopt($ch, CURLOPT_NOBODY ,1);
		}

		// follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
		if ($follow_redirects==true) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
		}

		// if debugging, return an array with CURL's debug info and the URL contents
		if ($debug==true) {
			$result['contents']=curl_exec($ch);
			$result['info']=curl_getinfo($ch);
		}

		// otherwise just return the contents as a variable
		else $result=curl_exec($ch);
		
		// free resources
		curl_close($ch);

		// send back the data
		return $result;
	}

	// string to stream source type 
	function get_stream_source_modified($str, $default_new_file_name=""){
		$str = str_replace(array("\\", "[", "]", "\""), "", $str);
		
		$file_name = pathinfo($str, PATHINFO_BASENAME);
		$last_pos = strrpos($str, $file_name);
		if($default_new_file_name == ""){
			$new_file_name = pathinfo($str, PATHINFO_FILENAME);
			$new_file_name ++;
			$new_file_ext = pathinfo($str, PATHINFO_EXTENSION);
		}
		else{
			$new_file_name = pathinfo($default_new_file_name, PATHINFO_FILENAME);
			$new_file_ext = pathinfo($default_new_file_name, PATHINFO_EXTENSION);
		}
		if($new_file_ext == "") $new_file_ext = "mp4";
		$new_file_name = $new_file_name.".".$new_file_ext;

		if($last_pos !== false){
			$new_str = substr_replace($str, $new_file_name, $last_pos, strlen($file_name));
			$new_str = '[\"' . str_replace(array("\/", "/", ","), array("\\\/", "\\\/", "\",\""), $new_str) . '\"]';
			$new_file_str = str_replace(array('\"', '"', ']'), "", $new_file_name);
			return [$new_str, $new_file_str];
		}
		return [$str, $str];
	}

	// check command not request
	function check_command(){
		global $argv, $argc, $flag_ajax_response;
		$result = array();
		if (defined('STDIN')) {
			// if run php via command
			// var_dump($argv, $argc);
			for($i = 1; $i < $argc; $i++){
				$temp = explode('=', $argv[$i]);
				if(count($temp) > 1){
					$result[$temp[0]] = $temp[1];
				}
			}
			// var_dump($temp_argvs);
			$flag_ajax_response = true;
		}
		return $result;
	}

	// mac address encode & validate
	function get_mac_address($str){
		if(!preg_match("/[a-zA-Z0-9]{2}:[a-zA-Z0-9]{2}:[a-zA-Z0-9]{2}:[a-zA-Z0-9]{2}:[a-zA-Z0-9]{2}:[a-zA-Z0-9]{2}/", $str, $match)) return false;
		if($match[0] != $str) return false;
		$mac_address = strtoupper($str);
		$mac_address = base64_encode($mac_address);
		return $mac_address;
	}

	// get day of week from integer
	function get_day($index){
		$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		return $days[$index % 7];
	}

	// send check stream message
	function msg_check_stream($id){
		global $pdo, $pdo1, $table_streams, $table_streams_sys, $table_local_settings;

		$result = 0;
		$sms_urls = array();
		$selected_phones = array();
		$default_sms_url = "https://semysms.net/api/3/sms.php?token=a8de8090d4e286b3e6d640b047ef8c95&device=169467&phone=";

		$selected_stream_sys = select_rows($pdo, $table_streams_sys, "`stream_id` = '".$id."' and `pid` = -1 and `stream_status` = 0");
		$selected_stream_sys_frame = select_rows($pdo, $table_streams_sys, "`stream_id` = '".$id."' and `to_analyze` = 0 and `stream_status` = 0 and `pid` IS NOT NULL and `bitrate` IS NOT NULL and `bitrate` < 200");
		$setting_check_phones = select_rows($pdo1, $table_local_settings, "`setting_key` LIKE 'check_phone_%' ORDER BY `created_at` DESC");
		$selected_stream = select_rows($pdo, $table_streams, "`id`='".$id."'");

		// phone numbers
		if($setting_check_phones){
			// phone numbers
			foreach($setting_check_phones as $val){
				$key = str_replace("check_phone_", "", $val['setting_key']);
				if(!in_array($val['setting_val'], $selected_phones) && !array_key_exists($key, $selected_phones)){
					$selected_phones[$key] = $val['setting_val'];
				}
			}
		}

		if(!empty($selected_stream)){
			// check stream sys about not working
			if($selected_stream_sys){
				$message_pattern = select_rows($pdo1, $table_local_settings, "`setting_key` = 'check_message' ORDER BY `created_at` DESC LIMIT 0, 1");
				if(!empty($message_pattern)){
					$sms_msg = str_ireplace("%s", $selected_stream[0]['stream_display_name'], $message_pattern[0]['setting_val']);
					foreach($selected_phones as $val){
						$sms_urls[]= $default_sms_url . $val . "&msg=" . $sms_msg;
					}
				}
			}

			// check stream sys about never frame
			if($selected_stream_sys_frame){
				$message_pattern = select_rows($pdo1, $table_local_settings, "`setting_key` = 'check_message_frame' ORDER BY `created_at` DESC LIMIT 0, 1");
				if(!empty($message_pattern)){
					$sms_msg = str_ireplace("%s", $selected_stream[0]['stream_display_name'], $message_pattern[0]['setting_val']);
					foreach($selected_phones as $val){
						$sms_urls[]= $default_sms_url . $val . "&msg=" . $sms_msg;
					}
				}
			}
		}

		// send sms
		foreach($sms_urls as $sms_url){
			$result1 = url_get_contents($sms_url);
			if(isset($result1['contents'])){
				$result ++;
			}
		}

		return array(
			'count' => $result,
			'channel' => $selected_stream[0]['stream_display_name']
		);
	}

	// analyze response of YouTube search page
	function analyze_response_youtube($response){
		$special_class_name = "item-section";
		// converts all special characters to utf-8
		$content = mb_convert_encoding($response['contents'], 'HTML-ENTITIES', 'UTF-8');
		// creating new document
		$dom = new DOMDocument('1.0', 'utf-8');
		//turning off some errors
		libxml_use_internal_errors(true);
		// it loads the content without adding enclosing html/body tags and also the doctype declaration
		$dom->LoadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		$finder = new DomXPath($dom);
		libxml_use_internal_errors(false);

		$spaner = $finder->query("//ol[contains(@class, '$special_class_name')]");
		foreach($spaner as $ol_target_container){
			$li_tags = $ol_target_container->getElementsByTagName("li");
			for($i = 0; $i < $li_tags->length; $i++){
				// each li tag
				$li_tag = $li_tags->item($i);
				$a_tags = $li_tag->getElementsByTagName("a");
				$a_tag = $a_tags->item(0);
				if($a_tags && !empty($a_tags) && $a_tag){
					$href = YOUTUBE_HOST . $a_tag->getAttribute('href');
					$h3_tag = $li_tag->getElementsByTagName("h3")->item(0);
					$title = "";
					if($h3_tag){
						$a_h3_tag = $h3_tag->getElementsByTagName("a")->item(0);
						if($a_h3_tag) $title = $a_h3_tag->textContent;
						$title = str_replace(array("'", '"'), array("\'", '\"'), $title);
					}
					$img_tag = $li_tag->getElementsByTagName("img")->item(0);
					$image = "";
					if($img_tag) $image = $img_tag->getAttribute('src');
					$ul_lockup_tag = $li_tag->getElementsByTagName("ul")->item(0);
					$uploaded_at = "";
					if($ul_lockup_tag){
						$li_lockup_tag = $ul_lockup_tag->getElementsByTagName("li")->item(0);
						if($li_lockup_tag) $uploaded_at = $li_lockup_tag->textContent;
					}
					$span_tags = $li_tag->getElementsByTagName("span");
					$video_time = "";
					for($j = 0; $j < $span_tags->length; $j++){
						// each span tag
						$span_tag = $span_tags->item($j);
						if($span_tag){
							$span_class = $span_tag->getAttribute('class');
							$span_text = $span_tag->textContent;
							if($span_class == "video-time"){
								$video_time = $span_text;
								break;
							}
						}
					}
					$channel_href = "";
					$user_href = "";
					$flag_temp_channel_exit = false;
					$flag_temp_user_exit = false;
					for($j = 0; $j < $a_tags->length; $j++){
						$a_tag = $a_tags->item($j);
						if($a_tag){
							$temp_href = $a_tag->getAttribute('href');
							if(!$flag_temp_channel_exit && strpos($temp_href, "channel/") > 0){
								$channel_href = $temp_href;
								$flag_temp_channel_exit = true;
							}
							else if(!$flag_temp_user_exit && strpos($temp_href, "user/") > 0){
								$user_href = $temp_href;
								$flag_temp_user_exit = true;
							}
							if($flag_temp_channel_exit && $flag_temp_user_exit) break;
						}
					}
					$datas[] = array('title' => $title, 'url' => $href, 'thumbnail' => $image, 'datetime' => $uploaded_at, 'channel_href' => $channel_href, 'user_href' => $user_href, 'video_time' => $video_time);
				} 
			}
		}

		return $datas;
	}

	//
	function auto_download_youtube($id){
		global $pdo1, $pdo, $table_local_manage_youtube, $table_local_youtube, $table_series_episodes, $table_series, $table_streams;

		// Response Data Array
		$resp = array();

		$all_youtubes = select_rows($pdo1, $table_local_youtube);

		if($pdo){
			$series_episodes = select_rows($pdo, $table_series_episodes);
			$streams = select_rows($pdo, $table_streams);
			$series = select_rows($pdo, $table_series);
		}
		else{
			$series_episodes = array();
			$streams = array();
			$series = array();
		}

		$hash_youtubes = array();
		if(!empty($all_youtubes)){
			foreach($all_youtubes as $temp){
				$hash_youtubes[$temp['url']] = $temp;
			}
		}

		$resp['status'] = "fail";
		$resp['message'] = "There is no youtube video file on YouTube page.";

		// Search Youtube
		$input_id = $id;
		$current_youtube = select_rows($pdo1, $table_local_manage_youtube, "`id` = '".$input_id."'");

		if(!empty($current_youtube)){
			// In the case of existing youtube
			$username_get = trim($current_youtube[0]['username']);
			$channel_get = trim($current_youtube[0]['channel']);
			$keyword_get = trim($current_youtube[0]['keywords']);
			$tv_series_get = $current_youtube[0]['tv_series'];
			$folderpath_get = $current_youtube[0]['folderpath'];


			// $keyword_get = $username_get." ".$keyword_get;
			$resp['keywords'] = $keyword_get;
			$keyword = urlencode($keyword_get);
			$folderpath_get = "/" . implode("/", array_filter(explode("/", $folderpath_get), function($f){ return $f != ""; })) . "/";

			// $url = YOUTUBE_HOST . "/results?search_query=" . $keyword;
			$url = YOUTUBE_HOST . "/results?search_query=" . $keyword . "&sp=CAI%253D";
			// $url = YOUTUBE_HOST . "/results?search_query=" . $keyword . "&sp=CAASBggCEAEYAg%253D%253D";
			// $url = YOUTUBE_HOST . "/results?search_query=" . $keyword . "&sp=EgIIAg%253D%253D";

			// curl to youtube
			$response = url_get_contents($url);
			// var_dump($response);
			$datas = analyze_response_youtube($response);

			if(!empty($datas)){
				// var_dump($datas);
				// search last data
				$now_time = time();
				// $start_time = 0; // 0 min
				$end_time = YOUTUBE_END_TIME; // 1 day
				$target_data= array('datetime' => 0);
				$index_data_current = $now_time;
				// $flag_temp_channel_exit = false;
				// $flag_temp_user_exit = false;
				foreach($datas as $d){
				// for($di = count($datas) - 1; $di >= 0; $di--){
				// 	$d = $datas[$di];
					// start time ~ end time
					$d_time = strtotime($d['datetime']);
					$v_times = explode(":", $d['video_time']);
					if(count($v_times) == 3){
						$v_time = $v_times[0] * 3600 + $v_times[1] * 60 + $v_times[2];
					}
					else if(count($v_times) == 2){
						$v_time = $v_times[0] * 60 + $v_times[1];
					}
					else{
						$v_time = $v_times[0];
					}
					// if(!$d_time) $d_time = $index_data_current--;
					// if($d_time >= $now_time - $end_time && $d_time <= $now_time - $start_time){
						// between start ~ end time
						// if($target_data['datetime'] < $d_time && stripos($d['url'], "/watch?") > 0){
						// 	$target_data = $d;
						// 	$target_data['datetime'] = $d_time;
						// }
					// }
					
					// echo($d['channel_href'] .", ". $channel_get. " <=:=> ".$d['user_href'].", ". $username_get." <> ".date("Y-m-d H:i:s", $d_time)."\n");
					// if($channel_get != "" && strpos($d['channel_href'], $channel_get)){
					// 	// channel matches channel
					// 	$flag_temp_channel_exit = true;
					// }
					// else if($username_get != "" && strpos($d['user_href'], $username_get)){
					// 	// username matches channel
					// 	$flag_temp_user_exit = true;
					// }
					if((($channel_get != "" && strpos($d['channel_href'], $channel_get)) 		// channel matches channel
						|| ($username_get != "" && strpos($d['user_href'], $username_get))) 	// username matches channel
						// && $d_time <= ($now_time - $start_time)								// start time
						&& $d_time > ($now_time - $end_time)									// today
						&& $d_time > $target_data['datetime']									// latest
						&& $v_time >= YOUTUBE_DUR_TIME){										// video time >= YouTube Duration Limit Time
						$target_data = $d;
						$target_data['datetime'] = $d_time;
						// echo "OK!\n";
						// break;
					}
				}
				// var_dump($target_data);

				// update or insert database 
				if(!empty($target_data) && $target_data['datetime'] > 0){
					// latest

					/*****************************************          auto insert stream & series episode        ********************************************/

					$created_at = time();

					$selected_series = array();
					foreach($series as $s){
						if($s['id'] == $tv_series_get){
							$selected_series = $s;
							break;
						}
					}
					if(!empty($selected_series)) $category_id = $selected_series['category_id'];
					
					$last_stream_item = get_last_episode($series_episodes, $tv_series_get);
					$count_series = $last_stream_item['sort'];
					$season_num = $last_stream_item['season_num'];
					$last_stream_id = $last_stream_item['stream_id'];
			
					$selected_streams = array();
					foreach($streams as $s){
						if($s['id'] == $last_stream_id){
							$selected_streams = $s;
							break;
						}
					}
					// var_dump($selected_streams);

					$selected_filename = $selected_streams['stream_display_name'];
					// $sort = $count_series + 1;
					$sort = get_next_sort($count_series, $selected_filename);
					
					list($selected_streams['stream_source'], $selected_streams['stream_display_name']) =  get_stream_source_modified($selected_streams['stream_source'], $sort.".mp4");
					$selected_streams['id'] =  "NULL";
					$selected_streams['added'] = $created_at;
					$selected_streams['category_id'] = $category_id;
					$query_stream_values = implode(", ", array_map(function($a, $b){ return "`".$a."`='".$b."'"; }, array_keys($selected_streams), array_values($selected_streams)));
			
					$series_id  = $tv_series_get;

					/*****************************************                    end           ********************************************/


					$download_file_name = $sort . ".mp4";

					if(isset($hash_youtubes[$target_data['url']])){
						// update
						$update_data_id = $hash_youtubes[$target_data["url"]]['id'];
						if($hash_youtubes[$target_data["url"]]['is_downloaded']){
							// already downloaded
							$download_file_name = $hash_youtubes[$target_data["url"]]['filename'];
							$flag_skip = true;
						}
						else $flag_skip = false;

						$youtube_query = "UPDATE `".$table_local_youtube."` SET `title` = '".$target_data["title"]."', `keywords` = '".$keyword_get."',
										`thumbnail` = '".$target_data["thumbnail"]."', `is_downloaded` = 1, `filepath` = '".$folderpath_get."',
										`filename` = '".$download_file_name."', `tv_series` = '".$tv_series_get."', `downloaded_at` = '".$now_time."' WHERE `id` = '".$update_data_id."';";
						update_row($pdo1, $table_local_youtube, $youtube_query);
					}
					else{
						// insert
						$youtube_query = "INSERT INTO `".$table_local_youtube."` SET `title` = '".$target_data["title"]."', `keywords` = '".$keyword_get."', 
										`url` = '".$target_data["url"]."', `thumbnail` = '".$target_data["thumbnail"]."', `is_downloaded` = 1, `filepath` = '".$folderpath_get."',
										`filename` = '".$download_file_name."', `tv_series` = '".$tv_series_get."', `downloaded_at` = '".$now_time."';";
						$update_data_id = insert_row($pdo1, $table_local_youtube, $youtube_query);
						$flag_skip = false;
					}

					// download
					if(!(isset($update_data_id) && $update_data_id > 0)){
						$resp['message'] = "Inserting or Updating your mysql is failed.";
					}
					else if($flag_skip){
						$resp['message'] = "There is the same file which is already downloaded. (keyword:" . $keyword_get . ", filename:" . $download_file_name . ")";
					}
					else{
						// success

						/*****************************************          auto insert stream & series episode        ********************************************/

						$query_stream = "INSERT INTO `".$table_streams."` SET ".$query_stream_values.";";
			
						$result1 = insert_row($pdo, $table_streams, $query_stream);
				
						if($result1){
							$stream_id = $result1;
				
							$query_series = "UPDATE `".$table_series."` SET `last_modified` = '".$created_at."' WHERE `id` = '".$tv_series_get."';";
				
							$result2 = update_row($pdo, $table_series, $query_series);
				
							if($result2){
								$query_episodes = "INSERT INTO `".$table_series_episodes."` (`id`, `season_num`, `series_id`, `stream_id`, `sort`) VALUES (NULL, '".$season_num."', '".$series_id."', '".$stream_id."', '".$sort."');";
				
								$result3 = insert_row($pdo, $table_series_episodes, $query_episodes);
				
								if($result3){
									$resp['status'] = "success";
								}
							}
						}

						/*****************************************                    end           ********************************************/

						if($resp['status'] == "success"){
							$download_url = $target_data["url"];
							// $download_cmd = "bahri " . $download_url . " -o " . $folderpath_get . $download_file_name . " > /dev/null 2>&1 &";
							$download_cmd = "youtube-dl -f 'bestvideo[ext=mp4]+bestaudio[ext=m4a]/bestvideo+bestaudio' --merge-output-format mp4 " . $download_url . " -o " . $folderpath_get . $download_file_name . " > /dev/null 2>&1 &";
							$output = shell_exec($download_cmd);
							$resp['message'] = "Downloading " . $download_url . " to " . $folderpath_get . $download_file_name . " is success.";
						}
						else{
							$resp['message'] = "Downloading youtube video file is failed.";
						}
					}
				}
				else{
					$resp['message'] = "There is no youtube video file which is uploaded 20min ~ 24hour.";
				}
			}
		}

		return $resp;
	}

	// cron job - check stream
	function cron_check_stream($last_time, $now_time, $cron_check_stream_step_time){
		return ($now_time >= ($last_time + $cron_check_stream_step_time));
	}

	// cron job - YouTube
	function cron_youtube($last_time, $now_day, $now_date, $now_hour, $now_minute, $check_youtube_day, $check_youtube_hour, $check_youtube_minute){
		$last_day = date("d", $last_time);
		$last_hour = date("G", $last_time);
		$last_minute = date("i", $last_time);

		// var_dump(date("Y-m-d H:i:s (w)", $last_time), date("Y-m-d H:i:s (w)", time()),
		// 	$check_youtube_day, $check_youtube_hour, $check_youtube_minute,
		// 	($last_day > $now_day),
		// 	($last_day == $now_day && $last_hour > $now_hour),
		// 	($last_day == $now_day && $last_hour == $now_hour && $last_minute > $now_minute),
		// 	in_array($now_date, explode(",", $check_youtube_day)) 
		// 		&& in_array($now_hour, explode(",", $check_youtube_hour))
		// 		&& in_array($now_minute, explode(",", $check_youtube_minute)));

		if($last_day > $now_day) return false;
		if($last_day == $now_day && $last_hour > $now_hour) return false;
		if($last_day == $now_day && $last_hour == $now_hour && $last_minute > $now_minute) return false;
		if(in_array($now_date, explode(",", $check_youtube_day)) 
			&& in_array($now_hour, explode(",", $check_youtube_hour))
			&& in_array($now_minute, explode(",", $check_youtube_minute))) return true;
		return false;
	}

	// get last episode from series episodes, series_id, current season
	function get_last_episode($series_episodes, $series_id, $season_num=false){
		$result = array(
			// 'season_num' => 1,
			// 'series_id' => 1,
			// 'stream_id' => 1,
			'sort' => 0
		);
		foreach($series_episodes as $item){
			if($season_num === false){
				if($item['series_id'] == $series_id) $result = $item;
			}
			else{
				if($item['series_id'] == $series_id && $item['season_num'] == $season_num) $result = $item;
			}
		}
		return $result;
	}

	// get next sort from last sort and last filename
	function get_next_sort($lastsort, $lastname, $flag=STREAM_SORT_FILENAME){
		$next_sort = 1;
		if($flag){
			// from sort
			$next_sort = (int)$lastsort + 1;
		}
		else{
			// from filename
			$file_name = pathinfo($lastname, PATHINFO_FILENAME);
			$file_ext = strtolower(pathinfo($lastname, PATHINFO_EXTENSION));
			$next_sort = (int)$file_name + 1;
			if($file_ext == "mp4"){
				// mp4 file
			}
			else if($file_ext == "mkv"){
				// mkv file
			}
		}
		return $next_sort;
	}

	
	// database connect
	$pdo1 = db_connect(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME);

	// set timezone AU
	date_default_timezone_set("Australia/Sydney");

?>
