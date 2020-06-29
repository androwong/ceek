<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
	

	$youtubes = select_rows($pdo1, $table_local_youtube, "`is_downloaded` = '1' ORDER BY `downloaded_at` desc");

	$table_series = "series";
	$temp_series = select_rows($pdo, $table_series);
	$tv_series = array();
	if(!empty($temp_series)){
		foreach($temp_series as $temp){
			$tv_series[$temp['id']] = $temp;
		}
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
		</script>

		<div id="message-alert"></div>
		
		<table class="table table-bordered datatable" id="table-3">
			<thead>
				<tr>
					<th>ID</th>
					<th>Title</th>
					<th>URL</th>
					<th>Downloaded File</th>
					<th>TV Series</th>
					<th>Downloaded Time</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($youtubes as $s){
						$s['tv_series_title'] = (isset($tv_series[$s['tv_series']]))?$tv_series[$s['tv_series']]['title']:"";
						$s['downloaded_time'] = date("Y-m-d H:i:s", $s['downloaded_at']);
                ?>
				<tr class="odd gradeX" id="custom-datatable-tr-<?php echo $s['id']; ?>">
					<td><?php echo $s['id']; ?></td>
					<td><?php echo $s['title']; ?></td>
					<td><?php echo $s['url']; ?></td>
					<td><?php echo $s['filepath'] . $s['filename']; ?></td>
					<td><?php echo $s['tv_series_title']; ?></td>
					<td><?php echo $s['downloaded_time']; ?></td>
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
				</tr>
			</tfoot>
		</table>