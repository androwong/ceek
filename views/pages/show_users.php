<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


	$series = array();
	$stream_categs = array();
	$series_episodes = array();
	$streams = array();
	
	if($page_type == "insert"){
		# Response Data Array
		$resp = array();

		$input_url = $_POST['url'];
		$input_series = $_POST['series'];
		$input_id = $_POST['id'];

		$resp['status'] = "fail";
		
		echo json_encode($resp);
		die();
	}
	else if($page_type == "show"){
		$input_id = $_POST['id'];

		echo json_encode($selected_series[0]);
		die();
	}
	else if($page_type == "auto"){
		# Response Data Array
		$resp = array();
		$resp['status'] = "fail";
		$resp['message'] = "";

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
					url : baseurl + 'index.php?page=show_users&type=insert',
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
				url : baseurl + 'index.php?page=show_users&type=auto',
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