<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
	

	function get_stream_source($str){
		if($str != "") return '[\"' . str_replace(array("\/", "/", ","), array("\\\/", "\\\/", "\",\""), $str) . '\"]';
		return '[]';
	}

	function get_url_without_special_characters($str){
		$temps = explode("?", $str);
		$result = $temps[0];
		if(count($temps) > 1){
			$re = '/[^,&]+/m';
			preg_match_all($re, $temps[1], $matches, PREG_SET_ORDER, 0);
			if(!empty($matches)) $result .= "?" . $matches[0][0];
		}
		return $result;
	}

	// stream url seperate characters
	$seperate_urls = "{*}";

    $table_series = "series";
	$table_stream_categ = "stream_categories";
	$table_streams = "streams";
	$table_series_episodes = "series_episodes";
	$table_streams_sys = "streams_sys";

    if(!$flag_redirect_info && $pdo){
        $series = select_rows($pdo, $table_series);
        $stream_categs = select_rows($pdo, $table_stream_categ);
		$series_episodes = select_rows($pdo, $table_series_episodes);
		$streams_sys = select_rows($pdo, $table_streams_sys);
    }
    else{
        $series = array();
        $stream_categs = array();
        $series_episodes = array();
        $streams_sys = array();
	}
	
	$last_post_params = json_encode($_POST);
	
	if($page_type == "insert"){
		if($id > 0){
			// update
			$stream_display_name = $_POST['stream_display_name'];
			// $current_stream_source = $_POST['current_stream_source'];
			$current_stream_category = $_POST['current_stream_category'];
			$input_multiple_stream_source = $_POST['input_multiple_stream_source'];
			$stream_icon = $_POST['stream_icon'];

			// stream source
			$temp_stream_urls = explode($seperate_urls, $input_multiple_stream_source);
			$input_multiple_stream_source = implode(",", array_map(function($url){
				return get_url_without_special_characters(trim($url));
			}, $temp_stream_urls));
			$stream_source = get_stream_source($input_multiple_stream_source);

			$message_success = false;
			$message = "Failed to save series episode.";

			if($last_post == $last_post_params){
				$message = "Failed to save series episode Because same datas.";
			}
			else{
				$query_stream = "UPDATE `".$table_streams."` SET `category_id` = '".$current_stream_category."', `stream_display_name` = '".$stream_display_name."', `stream_source` = '".$stream_source."', `stream_icon` = '".$stream_icon."' WHERE `id` = '".$id."';";
			
				$result1 = update_row($pdo, $table_streams, $query_stream);

				if($result1){
					// $query_streams_sys = "UPDATE `".$table_streams_sys."` SET `current_source` = '".$current_stream_source."' WHERE `stream_id` = '".$id."';";
			
					// $result2 = update_row($pdo, $table_streams_sys, $query_streams_sys);

					// if($result2){
						$message_success = true;
						$message = "Update series " . $stream_display_name . " episode.";
						$_SESSION['last_post'] = $last_post_params;
					// }
				}
			}
		}
		else{
			// create
			$input_url = $_POST['url'];
			$input_series = $_POST['series'];
			$input_id = $_POST['current_series_id'];

			$created_at = time();
			$selected_series = array_values(array_filter($series, function($s){
				global $input_id;
				return $s['id'] == $input_id;
			}));

			$count_series = get_last_episode($series_episodes, $input_id, $input_series)['sort'];
			$stream_source = get_stream_source($input_url);
			$file_name = pathinfo($input_url, PATHINFO_BASENAME);
			$file_ext = pathinfo($input_url, PATHINFO_EXTENSION);
			$target_container  = '[\"' . $file_ext . '\"]';
			$stream_display_name = $file_name;

			$season_num = $input_series;
			$series_id  = $input_id;
			// $sort = $count_series + 1;
			$sort = get_next_sort($count_series, $stream_display_name, true);;

			$message_success = false;
			$message = "Failed to Add new series episode.";

			if($last_post == $last_post_params){
				$message = "Failed to Add new series episode. Because same datas.";
			}
			else{
				if($selected_series){
					$category_id = $selected_series[0]['category_id'];

					$query_stream = "INSERT INTO `".$table_streams."` (`id`, `type`, `category_id`, `stream_display_name`, `stream_source`, `stream_icon`, `notes`, `created_channel_location`, `enable_transcode`, `transcode_attributes`, `custom_ffmpeg`, `movie_propeties`, `movie_subtitles`, `read_native`, `target_container`, `stream_all`, `remove_subtitles`, `custom_sid`, `epg_id`, `channel_id`, `epg_lang`, `order`, `auto_restart`, `transcode_profile_id`, `pids_create_channel`, `cchannel_rsources`, `gen_timestamps`, `added`, `series_no`, `direct_source`, `tv_archive_duration`, `tv_archive_server_id`, `tv_archive_pid`, `movie_symlink`, `redirect_stream`, `rtmp_output`, `number`, `allow_record`, `probesize_ondemand`, `custom_map`, `external_push`, `delay_minutes`) 
									VALUES (NULL, '5', '".$category_id."', '".$stream_display_name."', '".$stream_source."', '', '', NULL, '0', '[]', '', '{\"movie_image\":\"\",\"plot\":\"\",\"rating\":\"\",\"releasedate\":\"\"}', '[]', '0', '".$target_container."', '0', '0', '', NULL, NULL, NULL, '0', '', '0', '', '', '1', '".$created_at."', '0', '1', '0', '0', '0', '0', '1', '0', '', '0', '512000', '', '', '0');";
			
					$result1 = insert_row($pdo, $table_streams, $query_stream);
			
					if($result1){
						$stream_id = $result1;
		
						$query_series = "UPDATE `".$table_series."` SET `last_modified` = '".$created_at."' WHERE `id` = '".$input_id."';";
		
						$result2 = update_row($pdo, $table_series, $query_series);
		
						if($result2){
							$query_episodes = "INSERT INTO `".$table_series_episodes."` (`id`, `season_num`, `series_id`, `stream_id`, `sort`) VALUES (NULL, '".$season_num."', '".$series_id."', '".$stream_id."', '".$sort."');";
		
							$result3 = insert_row($pdo, $table_series_episodes, $query_episodes);
			
							if($result3){
								$message_success = true;
								$message = "Add title series " . $selected_series[0]['title'] . " episode (sort is " . $sort . ").";
								$_SESSION['last_post'] = $last_post_params;
							}
						}
					}
				}
			}
		}
	}

	$default_series_id = 0;
	$default_stream_display = "";
	$default_current_stream = "";
	$default_stream_sources_str = "";
	$default_stream_sources = array();
	$default_stream_icon = "";

	if(isset($_GET['series_id'])) $default_series_id = $_GET['series_id'];
	if($id > 0){
        // edit
        $selected_stream = select_rows($pdo, $table_streams, "`id`=".$id);
        if(count($selected_stream) > 0){
			$selected_stream = $selected_stream[0];
			$default_stream_display = $selected_stream['stream_display_name'];
			$default_stream_sources_str = str_replace(array("[", "]", "\\", "\""), "", $selected_stream['stream_source']);
			$default_stream_sources = array_filter(explode(",", $default_stream_sources_str), function($s){
				return $s != "";
			});
			$default_stream_sources_str = implode($seperate_urls, $default_stream_sources);
			$default_stream_icon = $selected_stream['stream_icon'];
			// foreach($streams_sys as $u){
			// 	if($selected_stream['id'] == $u['stream_id']){
			// 		$selected_sys = $u;
			// 		$default_current_stream = $selected_sys['current_source'];
			// 		break;
			// 	}
			// }
        }
    }

    $pdo = null;

?>

		<div class="row">
			<div class="col-md-12">
                <?php if($message != ""){
                        echo '<div class="alert '.(($message_success)?'alert-success':'alert-danger').'">
                            <strong>'.(($message_success)?'Success!':'Warning!').'</strong> '.$message.'</div>';
                    } 
                ?>
				
				<div class="panel panel-primary" data-collapsed="0">
				
					<div class="panel-heading">
						<div class="panel-title">
							Insert TV Series Episode
						</div>
						
						<div class="panel-options">
							<a href="#sample-modal" data-toggle="modal" data-target="#sample-modal-dialog-1" class="bg"><i class="entypo-cog"></i></a>
							<a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
							<a href="#" data-rel="reload"><i class="entypo-arrows-ccw"></i></a>
							<a href="#" data-rel="close"><i class="entypo-cancel"></i></a>
						</div>
					</div>
					
					<div class="panel-body">
                        <form role="form" method="POST" action="<?php echo WEB_PATH . 'index.php?page=add_streams&type=insert' . (($id>0)?'&id='.$id:''); ?>" class="form-horizontal form-groups-bordered">
							<input type="hidden" name="current_series_id" value="<?php echo $default_series_id; ?>">
							<?php if($id > 0){ ?>

								<div class="col-md-12">
									
									<div class="form-group">
										<label for="stream_display_name" class="control-label">Stream Display Name</label>
										<input type="text" class="form-control" id="stream_display_name" name="stream_display_name" placeholder="Stream Display Name" value="<?php echo $default_stream_display; ?>">
									</div>	
									
								</div>

								<!-- <div class="col-md-12">
									
									<div class="form-group">
										<label for="current_stream_source" class="control-label">Current Stream Source</label>
										<input type="text" class="form-control" id="current_stream_source" name="current_stream_source" placeholder="Current Stream Source" value="<?php echo $default_current_stream; ?>">
									</div>	
									
								</div> -->

								<div class="col-md-12">
									<div class="form-group">
										<label for="current_stream_category" class="control-label">Category Name</label>
										<select class="form-control" name="current_stream_category" id="current_stream_category" required>
											<?php 
												foreach($stream_categs as $u){
													echo '<option value="'.$u['id'].'" '.(($selected_stream['category_id']==$u['id'])?"selected":"").' >'.$u['category_name'].'</option>';
												}
											?>
										</select>
									</div>	
									
								</div>

								<div class="col-md-12">
									
									<div class="form-group">
										<label for="add_multiple_stream_source" class="control-label">Multiple Stream Sources</label>
										<div class="input-group" style="margin-bottom:10px;">
											<input type="text" class="form-control" id="add_multiple_stream_source" name="add_multiple_stream_source" placeholder="ADD Stream Sources">
											<span class="input-group-btn">
												<button class="btn btn-success btn-icon icon-left" id="btn_add_ul_multiple" type="button"><i class="entypo-list-add"></i>ADD</button>
											</span>
										</div>
										<input type="hidden" name="input_multiple_stream_source" value="<?php echo $default_stream_sources_str; ?>">
										<ul class="list-group" id="ul_multiple_stream_source">
											<?php foreach($default_stream_sources as $li){ ?>
												<li class="list-group-item"><span class="badge badge-primary" style="cursor: pointer;">&times;</span><?php echo $li; ?></li>
											<?php } ?>
										</ul>
									</div>	
									
								</div>

								<div class="col-md-12">
									
									<div class="form-group">
										<label for="stream_icon" class="control-label">Stream Icon URL</label>
										<input type="text" class="form-control" id="stream_icon" name="stream_icon" placeholder="Stream Icon URL" value="<?php echo $default_stream_icon; ?>">
									</div>	
									
								</div>

							<script>
								var seperate_urls = '<?php echo $seperate_urls; ?>';

								$(document).ready(function(){
									var refreshMultipleStreamSources = function(){
										var current_str = '';
										var current_array = [];
										$("#ul_multiple_stream_source li").each(function(){
											var node = $(this).contents().filter(function() {
												return this.nodeType == 3; // text node
											});
											current_array.push(node.text());
										});
										current_str = current_array.join(seperate_urls);
										$("input[name=input_multiple_stream_source]").val(current_str);
									};
									$("#btn_add_ul_multiple").click(function(){
										// add list
										var input_str = $("#add_multiple_stream_source").val();
										$("#ul_multiple_stream_source").append('<li class="list-group-item"><span class="badge badge-primary" style="cursor: pointer;">&times;</span>'+input_str+'</li>');
										refreshMultipleStreamSources();
									});
									$("#ul_multiple_stream_source").delegate("span", "click", function(){
										// remove list
										$(this).parent().remove();
										refreshMultipleStreamSources();
									});
								});
							</script>

							<?php } else { ?>

								<div class="col-md-12">
									
									<div class="form-group">
										<label for="url" class="control-label">URL</label>
										<input type="text" class="form-control" id="url" name="url" placeholder="URL">
									</div>	
									
								</div>

								<div class="col-md-12">
									
									<div class="form-group">
										<label for="series" class="control-label">SERIES NUMBER</label>
										<input type="text" class="form-control" id="series" name="series" placeholder="SERIES NUMBER">
									</div>	
									
								</div>

							<?php } ?>

							<div class="row">
								<div class="col-md-12">
									
									<div class="form-group" style="margin-top: 15px;">
										<div class="col-sm-offset-4 col-sm-6">
											<button type="submit" class="btn btn-success"><i class="entypo-publish"></i> Save</button>
											<a href="<?php echo WEB_PATH . 'index.php?page=tv_series'; ?>" type="button" class="btn btn-primary"> TV Series</a>
										</div>
									</div>	
									
								</div>
							</div>
						</form>
					</div>
				
				</div>
			
			</div>
		</div>