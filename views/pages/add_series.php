<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
	
	
    $table_series = "series";
	$table_stream_categ = "stream_categories";
    $table_bouquests = "bouquets";
    $bouquet_id = 27;

    if(!$flag_redirect_info && $pdo){
        $series = select_rows($pdo, $table_series);
        // $stream_categs = select_rows($pdo, $table_stream_categ);
        $stream_categs = select_rows($pdo, $table_stream_categ, "`category_type` like 'movie'");
        $bouquests = select_rows($pdo, $table_bouquests, "`id` = '".$bouquet_id."'");
    }
    else{
        $series = array();
        $stream_categs = array();
        $bouquests = array();
	}
	
	$last_post_params = json_encode($_POST);
	
	if($page_type == "insert"){
        // create
        $input_select_categ = $_POST['select_categ'];
        $input_title = $_POST['input_title'];

        $message_success = false;
        $message = "Failed to Add new Series.";

        if($last_post == $last_post_params){
            $message = "Failed to Add new Series Because same datas.";
        }
        else{
            $query_series = "INSERT INTO `".$table_series."` (`id`, `title`, `category_id`, `cover`, `cover_big`, `genre`, `plot`, `cast`, `rating`, `director`, `releaseDate`, `last_modified`, `tmdb_id`, `seasons`, `episode_run_time`, `backdrop_path`, `youtube_trailer`) 
                            VALUES (NULL, '".$input_title."', '".$input_select_categ."', '', '', '', '', '', '', '', '', '', '', '', '0', '', '');";
    
            $result1 = insert_row($pdo, $table_series, $query_series);
    
            if($result1){
                $series_id = $result1;
                $bouquet_series = $bouquests[0]['bouquet_series'];
                $bouquet_series = str_replace(']', ',"'.$series_id.'"]', $bouquet_series);

                $query_bouquet = "UPDATE `".$table_bouquests."` SET `bouquet_series` = '".$bouquet_series."' WHERE `id` = '".$bouquet_id."';";

                $result2 = update_row($pdo, $table_bouquests, $query_bouquet);

                if($result2){
                    $message_success = true;
                    $message = "Add new Series.";
                    $_SESSION['last_post'] = $last_post_params;
                }
            }
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
							Insert New Series
						</div>
						
						<div class="panel-options">
							<a href="#sample-modal" data-toggle="modal" data-target="#sample-modal-dialog-1" class="bg"><i class="entypo-cog"></i></a>
							<a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
							<a href="#" data-rel="reload"><i class="entypo-arrows-ccw"></i></a>
							<a href="#" data-rel="close"><i class="entypo-cancel"></i></a>
						</div>
					</div>
					
					<div class="panel-body">
                        <form role="form" method="POST" action="<?php echo WEB_PATH . 'index.php?page=add_series&type=insert'; ?>" class="form-horizontal form-groups-bordered">
                            
                            <div class="form-group">
								<label class="col-sm-3 control-label">Stream Category</label>
								
								<div class="col-sm-5">
									
									<select name="select_categ" id="select_categ" class="selectboxit" data-first-option="false" required>
                                        <option value="" default selected disabled>Select Stream Category</option>
                                        <?php 
                                            foreach($stream_categs as $categ){
                                                echo '<option value="'.$categ['id'].'">'.$categ['category_name'].'</option>';
                                            }
                                        ?>
									</select>
									
								</div>
                            </div>
                            
                            <div class="form-group">
								<label for="input_title" class="col-sm-3 control-label">Series Title</label>
								
								<div class="col-sm-5">
									<input type="text" class="form-control" name="input_title" id="input_title" placeholder="Input new Series Title." required>
								</div>
                            </div>
                            
                            <div class="form-group">
								<div class="col-sm-offset-3 col-sm-5">
                                    <button type="submit" class="btn btn-success"><i class="entypo-publish"></i> Save</button>
								</div>
                            </div>
                            
						</form>
					</div>
				
				</div>
			
			</div>
		</div>