<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

?>
    <!-- Bottom scripts (common) -->
	<script src="<?php echo JS_PATH ?>gsap/TweenMax.min.js"></script>
	<script src="<?php echo JS_PATH ?>jquery-ui/js/jquery-ui-1.10.3.minimal.min.js"></script>
	<script src="<?php echo JS_PATH ?>bootstrap.js"></script>
	<script src="<?php echo JS_PATH ?>joinable.js"></script>
	<script src="<?php echo JS_PATH ?>resizeable.js"></script>
    <script src="<?php echo JS_PATH ?>neon-api.js"></script>
    
    <script src="<?php echo JS_PATH ?>jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?php echo JS_PATH ?>jquery.validate.min.js"></script>


    <!-- Imported scripts on this page -->
    <?php
        if(isset($js_files) && count($js_files) > 0){
            foreach($js_files as $js){
                echo '<script src="'.$js.'"></script>';
            }
        }
    ?>

	<!-- JavaScripts initializations and stuff -->
	<script src="<?php echo JS_PATH ?>neon-custom.js"></script>


	<!-- Demo Settings -->
	<script src="<?php echo JS_PATH ?>neon-demo.js"></script>
    
    <script>
        // base url
        var baseurl = '<?php echo WEB_PATH; ?>';
        // base url
        var currentPageTitle = '<?php echo $page; ?>';
        // clicked id value
        var clicked_id = 0;
        var clicked_id2 = 0;
        // modal show contents
        var modal_contents = {};

        // ajax process
        var showAjaxModalFromUrl = function(page, type='show'){
            showModalContents("modal-"+type+"-"+page, true);
            $.ajax({
                method : 'POST',
                url : baseurl + 'index.php?page=' + page + '&type=' + type,
                data : {
                    id : clicked_id
                },
                success : function(result){
                    try{
                        modal_contents = JSON.parse(result);
                    }
                    catch(e){
                        modal_contents = {};
                    }
                    showModalContents("modal-"+type+"-"+page);
                },
                error : function(err){
                    modal_contents = {};
                    showModalContents("modal-"+type+"-"+page);
                }
            });
        };

        // modal show
        var showModalContents = function(modal_id, flag_init=false){
            if(flag_init){
                if($("#"+modal_id).attr("data-exist") == "YES"){
                    setTimeout(function(){
                        $("#"+modal_id+" .form-group").css("display", "none");
                        $("#"+modal_id+" [data-id=message-loading-alert]").css("display", "block");
                    }, 300);
                }
                else{
                    setTimeout(function(){
                        $("#"+modal_id+" .modal-body").html('<div class="col-md-12"><h3>Loading...</h3></div>');
                    }, 300);
                }
            }
            else{
                if($("#"+modal_id).attr("data-exist") == "YES"){
                    $("#"+modal_id+" .form-group").css("display", "block");
                    $("#"+modal_id+" [data-id=message-loading-alert]").css("display", "none");
                    let key, val, selkey,selval;
                    for(key in modal_contents){
                        val = modal_contents[key];
                        if($("#"+modal_id+" input[data-id="+key+"]").length){
                            $("#"+modal_id+" input[data-id="+key+"]").val(val);
                        }
                        else if($("#"+modal_id+" select[data-id="+key+"]").length){
                            for(selkey in val){
                                selval = val[selkey];
                                if(selval == "selected"){
                                    $("#"+modal_id+" #"+selkey+"-selection").removeAttr("style");
                                    $("#"+modal_id+" #"+selkey+"-selection").addClass("ms-selected");
                                    $("#"+modal_id+" #"+selkey+"-selectable").css("display", "none");
                                } 
                                else{
                                    $("#"+modal_id+" #"+selkey+"-selection").css("display", "none");
                                    $("#"+modal_id+" #"+selkey+"-selectable").removeAttr("style");
                                } 
                            }
                        }
                        else{
                            $("#"+modal_id+" [data-id="+key+"]").html(val);
                        }
                    }
                }
                else{
                    let key, val, html = '<div class="row">';
                    for(key in modal_contents){
                        val = modal_contents[key];
                        html += '<div class="col-md-6"><div class="form-group"><label for="" class="control-label">'+key+'</label><input type="text" class="form-control" name="'+key+'" placeholder="'+key+'" value=\''+val+'\'></div></div>';
                    }
                    html += '</div>';
                    $("#"+modal_id+" .modal-body").html(html);
                }
            }
        };

        $(document).ready(function(){
            // edit mag table
            // if($('#table-edit-mag').length){
			// 	$('#table-edit-mag').Tabledit({
			// 		url: baseurl + 'index.php?page=edit_mag&type=edit',
			// 		hideIdentifier: true,
			// 		columns: {
			// 			identifier: [0, 'id'],                    
			// 			editable: [
			// 				[2, 'date']
			// 			]
            //         },
            //         buttons : {}
			// 	});
			// }
        });
    </script>

</body>
</html>