<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    $table_streams = "streams";
    $table_streams_sys = "streams_sys";
    $table_streams_categories = "stream_categories";

    if(!$flag_redirect_info && $pdo){
        $streams = select_rows($pdo, $table_streams, "`type` = 1");
        $streams_sys = select_rows($pdo, $table_streams_sys);
        $streams_categories = select_rows($pdo, $table_streams_categories);
    }
    else{
        $streams = array();
        $streams_sys = array();
        $streams_categories = array();
	}
	
	if($page_type == "show"){
		$input_id = $_POST['id'];

		$selected_stream = array_values(array_filter($streams, function($s){
			global $input_id;
			return $s['id'] == $input_id;
		}));

		$pdo = null;

		echo json_encode($selected_stream[0]);
		die();
	}

	// include check stream php
	include_once("check_streams.php");

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
		} );
		</script>
		
		<table class="table table-bordered datatable" id="table-3">
			<thead>
				<tr>
					<th>Status</th>
					<th>Stream Display Name</th>
					<th>Category Name</th>
					<!-- <th>Stream Source</th> -->
					<th>Current Source</th>
					<th>BitRate</th>
					<th>Server ID</th>
					<th>Stream Started</th>
					<th style="min-width: 300px;">Actions</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($streams as $s){
						$temp_categ = array();
						$temp_sys = array();
                        foreach($streams_categories as $u){
                            if($s['category_id'] == $u['id']){
								$temp_categ = $u;
                                break;
                            }
						}
                        foreach($streams_sys as $u){
                            if($s['id'] == $u['stream_id']){
								$temp_sys = $u;
                                break;
                            }
						}
						$diff_hours = round((time() - $temp_sys['stream_started']) / 3600, 1) . "h";
						$cron_checked = false;
                        foreach($cron_job_streams as $c){
                            if($s['id'] == $c['param_id']){
								$cron_checked = true;
                                break;
                            }
						}
                ?>
				<tr class="odd gradeX">
					<td class="text-center"><?php echo ($temp_sys['stream_status'] == 0)?'<i class="entypo-db-shape text-success"></i>':'<i class="entypo-db-shape text-danger"></i>'; ?></td>
					<td><?php echo $s['stream_display_name']; ?></td>
					<td><?php echo $temp_categ['category_name']; ?></td>
					<!-- <td style="max-width:800px; word-wrap: break-word;"><?php echo $s['stream_source']; ?></td> -->
					<td><?php echo $temp_sys['current_source']; ?></td>
					<td><?php echo $temp_sys['bitrate']; ?></td>
					<td><?php echo $temp_sys['server_id']; ?></td>
					<td><?php echo $diff_hours; ?></td>
					<td>
						<a href="#" class="btn btn-orange btn-sm btn-icon icon-left" data-toggle="modal" data-target="#modal-show-manage_streams" onclick="clicked_id=<?php echo $s['id']; ?>; showAjaxModalFromUrl('manage_streams');">
							<i class="entypo-search"></i>
							Show
						</a>
						<a href="<?php echo WEB_PATH . 'index.php?page=add_streams&id=' . $s['id']; ?>" class="btn btn-default btn-sm btn-icon icon-left">
							<i class="entypo-pencil"></i>
							Edit
						</a>
						<a href="<?php echo ($cron_checked)?"#":WEB_PATH . 'index.php?page=manage_streams&type=check&id=' . $temp_sys['stream_id']; ?>" class="btn btn-green btn-sm btn-icon icon-left" <?php echo ($cron_checked)?"disabled":""; ?> >
							<i class="entypo-check"></i>
							Check
						</a>
						<!-- <a href="#" class="btn btn-danger btn-sm btn-icon icon-left">
							<i class="entypo-cancel"></i>
							Delete
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
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>