<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
	
	function get_stream_source($str){
		if($str != "") return '[\"' . str_replace(array("\/", "/", ","), array("\\\/", "\\\/", "\",\""), $str) . '\"]';
		return '[]';
	}

    $table_series = "series";
	$table_stream_categ = "stream_categories";
	$table_streams = "streams";
	$table_series_episodes = "series_episodes";
	$table_streams_sys = "streams_sys";
	$table_bouquests = "bouquets";

    if(!$flag_redirect_info && $pdo){
        $stream_categs = select_rows($pdo, $table_stream_categ, "`category_type` like 'movie'");
		$streams_sys = select_rows($pdo, $table_streams_sys);
		$bouquests = select_rows($pdo, $table_bouquests);
    }
    else{
        $stream_categs = array();
        $streams_sys = array();
        $bouquests = array();
	}
	
	$last_post_params = json_encode($_POST);
	
	if($page_type == "insert"){
		if($id > 0){
			// update
			$stream_display_name = $_POST['stream_display_name'];
			$current_stream_category = $_POST['current_stream_category'];
			$input_multiple_stream_source = $_POST['input_multiple_stream_source'];
			$stream_icon = $_POST['stream_icon'];

			$stream_source = get_stream_source($input_multiple_stream_source);

			$message_success = false;
			$message = "Failed to Update selected Stream.";

			if($last_post == $last_post_params){
				$message = "Failed to Update Stream Because same datas.";
			}
			else{
				$query_stream = "UPDATE `".$table_streams."` SET `category_id` = '".$current_stream_category."', `stream_display_name` = '".$stream_display_name."', `stream_source` = '".$stream_source."', `stream_icon` = '".$stream_icon."' WHERE `id` = '".$id."';";
			
				$result1 = update_row($pdo, $table_streams, $query_stream);

				if($result1){
					$message_success = true;
					$message = "Update selected Stream.";
					$_SESSION['last_post'] = $last_post_params;
				}
			}
		}
		else{
			// create
			$input_url = $_POST['url'];
			$input_name = $_POST['stream_display_name'];
			$input_category = $_POST['current_stream_category'];
			$input_bouquet = $_POST['unknown_bouquet_id'];

			$created_at = time();
			$stream_source = get_stream_source($input_url);
			$file_name = pathinfo($input_url, PATHINFO_BASENAME);
			$file_ext = pathinfo($input_url, PATHINFO_EXTENSION);
			$target_container  = '[\"' . $file_ext . '\"]';
			// $stream_display_name = $file_name;
			$stream_display_name = $input_name;

			$message_success = false;
			$message = "Failed to Add new VOD.";

			if($input_name == ""){
				$message = "Failed to Add new VOD Because empty data.";
			}
			else if($last_post == $last_post_params){
				$message = "Failed to Add new VOD Because same datas.";
			}
			else{
				$category_id = $input_category;
	
				$query_stream = "INSERT INTO `".$table_streams."` (`id`, `type`, `category_id`, `stream_display_name`, `stream_source`, `stream_icon`, `notes`, `created_channel_location`, `enable_transcode`, `transcode_attributes`, `custom_ffmpeg`, `movie_propeties`, `movie_subtitles`, `read_native`, `target_container`, `stream_all`, `remove_subtitles`, `custom_sid`, `epg_id`, `channel_id`, `epg_lang`, `order`, `auto_restart`, `transcode_profile_id`, `pids_create_channel`, `cchannel_rsources`, `gen_timestamps`, `added`, `series_no`, `direct_source`, `tv_archive_duration`, `tv_archive_server_id`, `tv_archive_pid`, `movie_symlink`, `redirect_stream`, `rtmp_output`, `number`, `allow_record`, `probesize_ondemand`, `custom_map`, `external_push`, `delay_minutes`) 
								VALUES (NULL, '2', '".$category_id."', '".$stream_display_name."', '".$stream_source."', '', '', NULL, '0', '[]', '', '{\"movie_image\":\"\",\"plot\":\"\",\"rating\":\"\",\"releasedate\":\"\"}', '[]', '0', '".$target_container."', '0', '0', '', NULL, NULL, NULL, '0', '', '0', '', '', '1', '".$created_at."', '0', '1', '0', '0', '0', '0', '1', '0', '', '0', '512000', '', '', '0');";
				
				$result1 = insert_row($pdo, $table_streams, $query_stream);
		
				if($result1){
					// update bouquet table
					$bouquet_pattern = array_values(array_filter($bouquests, function($b){ global $input_bouquet; return $input_bouquet == $b['id']; }));
					if(!empty($bouquet_pattern)) $bouquet_pattern = $bouquet_pattern[0]['bouquet_channels'];
					if(strpos($bouquet_pattern, "]") !== false) $bouquet_pattern = str_replace("]", ",\"".$result1."\"]", $bouquet_pattern);

					$query_bouquet = "UPDATE `".$table_bouquests."` SET `bouquet_channels` = '".$bouquet_pattern."' WHERE `id`='".$input_bouquet."';";

					$result2 = update_row($pdo, $table_bouquests, $query_bouquet);

					if($result2){
						$message_success = true;
						$message = "Add new VOD.";
						$_SESSION['last_post'] = $last_post_params;
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
			$default_stream_sources_str = implode(",", $default_stream_sources);
			$default_stream_icon = $selected_stream['stream_icon'];
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
							Insert New VOD
						</div>
						
						<div class="panel-options">
							<a href="#sample-modal" data-toggle="modal" data-target="#sample-modal-dialog-1" class="bg"><i class="entypo-cog"></i></a>
							<a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
							<a href="#" data-rel="reload"><i class="entypo-arrows-ccw"></i></a>
							<a href="#" data-rel="close"><i class="entypo-cancel"></i></a>
						</div>
					</div>
					
					<div class="panel-body">
                        <form role="form" method="POST" action="<?php echo WEB_PATH . 'index.php?page=add_vod&type=insert' . (($id>0)?'&id='.$id:''); ?>" class="form-horizontal form-groups-bordered">
							<?php if($id > 0){ ?>

								<div class="col-md-12">
									
									<div class="form-group">
										<label for="stream_display_name" class="control-label">Stream Display Name</label>
										<input type="text" class="form-control" id="stream_display_name" name="stream_display_name" placeholder="Stream Display Name" value="<?php echo $default_stream_display; ?>">
									</div>	
									
								</div>

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
										current_str = current_array.join(',');
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
										<label for="stream_display_name" class="control-label">Stream Display Name</label>
										<input type="text" class="form-control" id="stream_display_name" name="stream_display_name" placeholder="Stream Display Name">
									</div>	
									
								</div>

								<div class="col-md-12">
									
									<div class="form-group">
										<label for="url" class="control-label">URL</label>
										<input type="text" class="form-control" id="url" name="url" placeholder="URL">
									</div>	
									
								</div>

								<div class="col-md-12">

									<div class="form-group">
										<label for="unknown_bouquet_id" class="control-label">Bouquet</label>
										<select class="form-control" name="unknown_bouquet_id" id="unknown_bouquet_id" required>
											<?php 
												foreach($bouquests as $b){
													echo '<option value="'.$b['id'].'" >'.$b['bouquet_name'].'</option>';
												}
											?>
										</select>
									</div>
									
								</div>

							<?php } ?>

							<div class="row">
								<div class="col-md-12">
									
									<div class="form-group" style="margin-top: 15px;">
										<div class="col-sm-offset-4 col-sm-6">
											<button type="submit" class="btn btn-success"><i class="entypo-publish"></i> Save</button>
										</div>
									</div>	
									
								</div>
							</div>
						</form>
					</div>
				
				</div>
			
			</div>
		</div>