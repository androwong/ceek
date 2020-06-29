<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

?>
    <div class="sidebar-menu">

		<div class="sidebar-menu-inner">
			
			<header class="logo-env">

				<!-- logo -->
				<div class="logo">
					<a href="<?php echo WEB_PATH; ?>index.php">
						<img src="<?php echo IMAGE_PATH; ?>logo@2x.png" width="120" alt="" />
					</a>
				</div>

				<!-- logo collapse icon -->
				<div class="sidebar-collapse">
					<a href="#" class="sidebar-collapse-icon"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
						<i class="entypo-menu"></i>
					</a>
				</div>

								
				<!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
				<div class="sidebar-mobile-menu visible-xs">
					<a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
						<i class="entypo-menu"></i>
					</a>
				</div>

			</header>
			
									
			<ul id="main-menu" class="main-menu">
				<!-- add class "multiple-expanded" to allow multiple submenus to open -->
				<!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
				<?php
					$left_bar_header = array();
					$left_bar_has_sub = array();
					$left_bar_opened = array();
					$left_bar_body = array();
					$left_bar_bottom = array();
					$left_bar_hash = array();
					$class_li_name = array();
					$index = 0;
					foreach($all_pages as $p){
						if($p['is_show'] && getPermissionOfPage($p['name']) == $p['name']){
							$active_class = ($page == $p['name'])?'active':'';
							$href_link = WEB_PATH . "index.php?page=" . $p['name'];
							if($p['parent_id'] > 0){
								$parent_id = $left_bar_hash[$p['parent_id']];
								if($active_class=='active'){
									$left_bar_opened[$parent_id] = 'opened';
									$class_li_name[$parent_id] = "has-sub opened";
								}
								else{
									if(!isset($class_li_name[$parent_id]) || $class_li_name[$parent_id] != "has-sub opened") $class_li_name[$parent_id] = "has-sub";
								}
								$left_bar_header[$parent_id] = '<li class="'.$class_li_name[$parent_id].'"><a href="#">';
								$left_bar_has_sub[$parent_id] .= '<li class="'.$active_class.'"><a href="'.$href_link.'"><i class="'.$p['icon'].'"></i><span class="title">'.$p['bar_title'].'</span></a></li>';
							}
							else{
								$left_bar_hash[$p['id']] = $index;
								$left_bar_opened[$index] = '';
								$left_bar_header[$index] = '<li class="'.$active_class.'"><a href="'.$href_link.'">';
								$left_bar_body[$index] = '<i class="'.$p['icon'].'"></i><span class="title">'.$p['bar_title'].'</span></a>';
								$left_bar_bottom[$index] = '</li>';
								$left_bar_has_sub[$index] = '';
								$index ++;
							}
						}
					}
					for($i = 0; $i < $index; $i ++)
						echo $left_bar_header[$i] . $left_bar_body[$i] . ($left_bar_has_sub[$i]!=''?'<ul class="'.(str_replace('opened', 'visible', $left_bar_opened[$i])).'">'.$left_bar_has_sub[$i].'</ul>':'') . $left_bar_bottom[$i];
				?>
			</ul>
			
		</div>

	</div>