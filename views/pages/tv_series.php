<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    $table_series = "series";
	$table_stream_categ = "stream_categories";
	$table_streams = "streams";
	$table_series_episodes = "series_episodes";

    if(!$flag_redirect_info && $pdo){
        $series = select_rows($pdo, $table_series);
        $stream_categs = select_rows($pdo, $table_stream_categ);
		$series_episodes = select_rows($pdo, $table_series_episodes);
		$streams = select_rows($pdo, $table_streams);
    }
    else{
        $series = array();
        $stream_categs = array();
        $series_episodes = array();
        $streams = array();
    }
	
	if($page_type == "insert"){
		# Response Data Array
		$resp = array();

		$input_url = $_POST['url'];
		$input_series = $_POST['series'];
		$input_id = $_POST['id'];

		$resp['status'] = "fail";
		
		// $created_at = time();
		// $selected_series = array_values(array_filter($series, function($s){
		// 	global $input_id;
		// 	return $s['id'] == $input_id;
		// }));
		// $count_series = array_reduce($series_episodes, function($carry, $item){
		// 	global $input_id;
		// 	if($item['series_id'] == $input_id) $carry ++;
    	// 	return $carry;
		// });
		// $stream_source = '[\"'. str_replace(array("\/", "/"), array("\\\/", "\\\/"), $input_url) . '\"]';
		// $file_name = pathinfo($input_url, PATHINFO_BASENAME);
		// $file_ext = pathinfo($input_url, PATHINFO_EXTENSION);
		// $target_container  = '[\"' . $file_ext . '\"]';
		// $stream_display_name = $file_name;

		// $season_num = $input_series;
		// $series_id  = $input_id;
		// $sort = $count_series + 1;

		// if($selected_series){
		// 	$category_id = $selected_series[0]['category_id'];

		// 	$query_stream = "INSERT INTO `".$table_streams."` (`id`, `type`, `category_id`, `stream_display_name`, `stream_source`, `stream_icon`, `notes`, `created_channel_location`, `enable_transcode`, `transcode_attributes`, `custom_ffmpeg`, `movie_propeties`, `movie_subtitles`, `read_native`, `target_container`, `stream_all`, `remove_subtitles`, `custom_sid`, `epg_id`, `channel_id`, `epg_lang`, `order`, `auto_restart`, `transcode_profile_id`, `pids_create_channel`, `cchannel_rsources`, `gen_timestamps`, `added`, `series_no`, `direct_source`, `tv_archive_duration`, `tv_archive_server_id`, `tv_archive_pid`, `movie_symlink`, `redirect_stream`, `rtmp_output`, `number`, `allow_record`, `probesize_ondemand`, `custom_map`, `external_push`, `delay_minutes`) 
		// 					VALUES (NULL, '5', '".$category_id."', '".$stream_display_name."', '".$stream_source."', '', '', NULL, '0', '[]', '', '{\"movie_image\":\"\",\"plot\":\"\",\"rating\":\"\",\"releasedate\":\"\"}', '[]', '0', '".$target_container."', '0', '0', '', NULL, NULL, NULL, '0', '', '0', '', '', '1', '".$created_at."', '0', '1', '0', '0', '0', '0', '1', '0', '', '0', '512000', '', '', '0');";
	
		// 	$result1 = insert_row($pdo, $table_streams, $query_stream);
	
		// 	if($result1){
		// 		$stream_id = $result1;

		// 		$query_series = "UPDATE `".$table_series."` SET `last_modified` = '".$created_at."' WHERE `id` = '".$input_id."';";

		// 		$result2 = update_row($pdo, $table_series, $query_series);

		// 		if($result2){
		// 			$query_episodes = "INSERT INTO `".$table_series_episodes."` (`id`, `season_num`, `series_id`, `stream_id`, `sort`) VALUES (NULL, '".$season_num."', '".$series_id."', '".$stream_id."', '".$sort."');";

		// 			$result3 = insert_row($pdo, $table_series_episodes, $query_episodes);
	
		// 			if($result3){
		// 				$resp['status'] = "success";
		// 				$resp['stream'] = $query_stream;
		// 				$resp['episodes'] = $query_episodes;
		// 			}
		// 		}
		// 	}
		// }

		$pdo = null;

		echo json_encode($resp);
		die();
	}
	else if($page_type == "show"){
		$input_id = $_POST['id'];

		$selected_series = array_values(array_filter($series, function($s){
			global $input_id;
			return $s['id'] == $input_id;
		}));

		$pdo = null;

		echo json_encode($selected_series[0]);
		die();
	}
	else if($page_type == "auto"){
		# Response Data Array
		$resp = array();
		$resp['status'] = "fail";
		$resp['message'] = "";

		/*****************************************          auto insert stream & series episode        ********************************************/

		$input_id = $_POST['id'];

		$created_at = time();

		$selected_series = array_values(array_filter($series, function($s){
			global $input_id;
			return $s['id'] == $input_id;
		}));
		$category_id = $selected_series[0]['category_id'];

		$last_stream_item = get_last_episode($series_episodes, $input_id);
		$count_series = $last_stream_item['sort'];
		$season_num = $last_stream_item['season_num'];
		$last_stream_id = $last_stream_item['stream_id'];

		$selected_streams = array_values(array_filter($streams, function($s){
			global $last_stream_id;
			return $s['id'] == $last_stream_id;
		}));
		if(count($selected_streams) > 0) $selected_streams = $selected_streams[0];

		$selected_filename = $selected_streams['stream_display_name'];
		// $sort = $count_series + 1;
		$sort = get_next_sort($count_series, $selected_filename);
		
		list($selected_streams['stream_source'], $selected_streams['stream_display_name']) =  get_stream_source_modified($selected_streams['stream_source']);
		$selected_streams['id'] =  "NULL";
		$selected_streams['added'] = $created_at;
		$selected_streams['category_id'] = $category_id;
		$query_stream_values = implode(", ", array_map(function($a, $b){ return "`".$a."`='".$b."'"; }, array_keys($selected_streams), array_values($selected_streams)));

		$series_id  = $input_id;
		
		$query_stream = "INSERT INTO `".$table_streams."` SET ".$query_stream_values.";";

		$result1 = insert_row($pdo, $table_streams, $query_stream);

		if($result1){
			$stream_id = $result1;

			$query_series = "UPDATE `".$table_series."` SET `last_modified` = '".$created_at."' WHERE `id` = '".$input_id."';";

			$result2 = update_row($pdo, $table_series, $query_series);

			if($result2){
				$query_episodes = "INSERT INTO `".$table_series_episodes."` (`id`, `season_num`, `series_id`, `stream_id`, `sort`) VALUES (NULL, '".$season_num."', '".$series_id."', '".$stream_id."', '".$sort."');";

				$result3 = insert_row($pdo, $table_series_episodes, $query_episodes);

				if($result3){
					$resp['status'] = "success";
					$resp['message'] = $selected_series[0]['title'] . "(episode sort: " . $sort . ") Added Successfully.";
				}
			}
		}

		/*****************************************                    end           ********************************************/

		$pdo = null;

		echo json_encode($resp);
		die();
	}

    $pdo = null;

?>
        <script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			var $table3 = jQuery("#table-3");
			
			var table3 = $table3.DataTable( {
				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
			} );
			
			// Initalize Select Dropdown after DataTables is created
			$table3.closest( '.dataTables_wrapper' ).find( 'select' ).select2( {
				minimumResultsForSearch: -1
			});
			
			// Setup - add a text input to each footer cell
			$( '#table-3 tfoot th' ).each( function () {
				var title = $('#table-3 thead th').eq( $(this).index() ).text();
				if(title != 'Actions'){
					$(this).html( '<input type="text" class="form-control" placeholder="Search ' + title + '" />' );
				}
			} );
			
			// Apply the search
			table3.columns().every( function () {
				var that = this;
			
				$( 'input', this.footer() ).on( 'keyup change', function () {
					if ( that.search() !== this.value ) {
						that
							.search( this.value )
							.draw();
					}
				} );
			} );


			
			// add new stream button click handler
			$("#modal-add-stream #modal_submit").click(function(){
				var url = $("#modal-add-stream #modal_url").val();
				var series = $("#modal-add-stream #modal_series").val();
				$("#message-alert").attr("class", "");
				$("#message-alert").html("");
				$.ajax({
					method : 'POST',
					url : baseurl + 'index.php?page=tv_series&type=insert',
					data : {
						url : url,
						series : series,
						id : clicked_id
					},
					success : function(result){
						var flag = false;
						try{
							result = JSON.parse(result);
							// message = result.message;
							if(result.status == "success") flag = true;	
						}
						catch(e){							
						}
						if(flag){
							$("#message-alert").attr("class", "alert alert-success");
							$("#message-alert").html("<strong>Success!</strong> Add a new stream.");
						}
						else{
							$("#message-alert").attr("class", "alert alert-danger");
							$("#message-alert").html("<strong>Warnig!</strong> Failed adding a new stream.");
						}
					},
					error : function(result){
						alert("An error occoured!");
					}
				});
			});
		} );


		// auto add new stream
		function autoAddStream(){
			$.ajax({
				method : 'POST',
				url : baseurl + 'index.php?page=tv_series&type=auto',
				data : {
					id : clicked_id
				},
				success : function(result){
					var flag = false;
					try{
						result = JSON.parse(result);
						message = result.message;
						if(result.status == "success") flag = true;	
					}
					catch(e){							
					}
					if(flag){
						$("#message-alert").attr("class", "alert alert-success");
						$("#message-alert").html("<strong>Success!</strong> " + message);
					}
					else{
						$("#message-alert").attr("class", "alert alert-danger");
						$("#message-alert").html("<strong>Warnig!</strong> Failed adding a new stream automatically.");
					}
				},
				error : function(result){
					alert("An error occoured!");
				}
			});
		}
		</script>

		<div id="message-alert"></div>
		
		<table class="table table-bordered datatable" id="table-3">
			<thead>
				<tr>
					<th>ID</th>
					<th>Title</th>
					<th>Category</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($series as $s){ 
                        $category = null;
                        foreach($stream_categs as $u){
                            if($u['id'] == $s['category_id']){
                                $category = $u;
                                break;
                            }
                        }
                        if(!$category) break;
						$s['category'] = $category['category_name'];
                ?>
				<tr class="odd gradeX">
					<td><?php echo $s['id']; ?></td>
					<td><?php echo $s['title']; ?></td>
					<td><?php echo $s['category']; ?></td>
					<td>
						<!-- <a href="#" class="btn btn-default btn-sm btn-icon icon-left" data-toggle="modal" data-target="#modal-add-stream" onclick="clicked_id=<?php echo $s['id']; ?>;"> -->
						<a href="<?php echo WEB_PATH . 'index.php?page=add_streams&series_id=' . $s['id']; ?>" class="btn btn-default btn-sm btn-icon icon-left">
							<i class="entypo-plus"></i>
							Add
						</a>
						<a href="#" class="btn btn-info btn-sm btn-icon icon-left" onclick="clicked_id=<?php echo $s['id']; ?>; autoAddStream();">
							<i class="entypo-list-add"></i>
							Auto
						</a>
						<a href="#" class="btn btn-orange btn-sm btn-icon icon-left" data-toggle="modal" data-target="#modal-show-tv_series" onclick="clicked_id=<?php echo $s['id']; ?>; showAjaxModalFromUrl('tv_series');">
							<i class="entypo-search"></i>
							Show
						</a>
						<a href="<?php echo WEB_PATH . 'index.php?page=edit_youtube&series_id=' . $s['id']; ?>" class="btn btn-primary btn-sm btn-icon icon-left">
							<i class="entypo-play"></i>
							Add YouTube
						</a>
						<!-- <a href="#" class="btn btn-info btn-sm btn-icon icon-left">
							<i class="entypo-pencil"></i>
							Edit
						</a>
						<a href="#" class="btn btn-danger btn-sm btn-icon icon-left">
							<i class="entypo-cancel"></i>
							Remove
						</a> -->
					</td>
                </tr>
                <?php } ?>
			</tbody>
			<tfoot>
                <tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>