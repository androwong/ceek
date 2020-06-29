<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
	

	$table_series = "series";
	$table_streams = "streams";
	$table_series_episodes = "series_episodes";
	
	$youtubes = select_rows($pdo1, $table_local_manage_youtube);
	
	if(!$flag_redirect_info && $pdo){
		$series = select_rows($pdo, $table_series);
	}
	else{
		$series = array();
	}

	$tv_series = array();
	if(!empty($series)){
		foreach($series as $temp){
			$tv_series[$temp['id']] = $temp;
		}
	}

	
	if($page_type == "auto"){
		// auto download YouTube
		if(isset($_POST['id'])) $input_id = $_POST['id']; else if($id > 0) $input_id = $id;

		$resp = auto_download_youtube($input_id);

		$pdo = null;
		$pdo1 = null;

		echo json_encode($resp);
		die();
	}
	else if($page_type == "remove"){
		// remove YouTube
		# Response Data Array
		$resp = array();

		$resp['status'] = "fail";

		$input_id = $_POST['id'];
		if(delete_rows($pdo1, $table_local_manage_youtube, "`id`='".$input_id."'")) $resp['status'] = "success";

		$pdo = null;
		$pdo1 = null;

		echo json_encode($resp);
		die();
	}

    $pdo = null;

?>
        <script type="text/javascript">
		var table3;
		jQuery( document ).ready( function( $ ) {
			var $table3 = jQuery("#table-3");
			
			table3 = $table3.DataTable( {
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
			
		});


		// auto add new youtube
		function autoAddYoutube(){
			$.ajax({
				method : 'POST',
				url : baseurl + 'index.php?page=manage_youtube&type=auto',
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
						$("#message-alert").html('<strong>Success!</strong> Add a new youtube file automatically! '+message);
					}
					else{
						$("#message-alert").attr("class", "alert alert-danger");
						$("#message-alert").html('<strong>Warning!</strong> Failed to add new youtube! '+message);
					}
				},
				error : function(result){
					alert("An error occoured!");
				}
			});
		}


		// remove Youtube
		function removeYoutube(){
			$.ajax({
				method : 'POST',
				url : baseurl + 'index.php?page=manage_youtube&type=remove',
				data : {
					id : clicked_id
				},
				success : function(result){
					var flag = false;
					try{
						result = JSON.parse(result);
						if(result.status == "success") flag = true;	
					}
					catch(e){							
					}
					if(flag){
						$("#message-alert").attr("class", "alert alert-success");
						$("#message-alert").html("<strong>Success!</strong> Removed Youtube.");
						table3
							.row( $("#custom-datatable-tr-"+clicked_id) )
							.remove()
							.draw();
					}
					else{
						$("#message-alert").attr("class", "alert alert-danger");
						$("#message-alert").html("<strong>Warnig!</strong> Failed to remove.");
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
					<th>Channel</th>
					<th>User Name</th>
					<th>Keyword</th>
					<th>TV Series</th>
					<th>Download Folder</th>
					<th style="min-width:250px;">Actions</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($youtubes as $s){
						$s['tv_series_title'] = (isset($tv_series[$s['tv_series']]))?$tv_series[$s['tv_series']]['title']:"";
                ?>
				<tr class="odd gradeX" id="custom-datatable-tr-<?php echo $s['id']; ?>">
					<td><?php echo $s['id']; ?></td>
					<td><?php echo $s['channel']; ?></td>
					<td><?php echo $s['username']; ?></td>
					<td><?php echo $s['keywords']; ?></td>
					<td><?php echo $s['tv_series_title']; ?></td>
					<td><?php echo $s['folderpath']; ?></td>
					<td>
						<a href="<?php echo WEB_PATH . 'index.php?page=edit_youtube&id=' . $s['id']; ?>" class="btn btn-default btn-sm btn-icon icon-left">
							<i class="entypo-pencil"></i>
							Edit
						</a>
						<a href="#" class="btn btn-info btn-sm btn-icon icon-left" onclick="clicked_id=<?php echo $s['id']; ?>; autoAddYoutube();">
							<i class="entypo-list-add"></i>
							Auto
						</a>
						<a href="#" class="btn btn-orange btn-sm btn-icon icon-left" onclick="clicked_id=<?php echo $s['id']; ?>; removeYoutube();">
							<i class="entypo-trash"></i>
							Remove
						</a>
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
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>