<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


	function analyze_response($response, $host_url, $categ, $datas){
		$special_id_name = "archive-list";
		// analyze response
		libxml_use_internal_errors(true);
		$dom = new DomDocument();
		$dom->loadHTML($response['contents']);
		libxml_use_internal_errors(false);

		$div_target_container = $dom->getElementById($special_id_name);
		$lis = $div_target_container->getElementsByTagName("li");
		for($i = 0; $i < $lis->length; $i++){
			// each li tag
			$li_tag = $lis->item($i);
			$a_tag = $li_tag->getElementsByTagName("a")->item(1);
			$href = $a_tag->getAttribute('href');
			$href_url = parse_url($href);
			$href_url = (isset($href_url['host']))?$url:$host_url.$href_url['path'];
			$title = $a_tag->textContent;
			$img_tag = $li_tag->getElementsByTagName("img")->item(0);
			$image = $img_tag->getAttribute('src');
			$datas[] = array('title' => $title, 'category_id' => $categ['id'], 'url' => $href_url, 'thumbnail' => $image);
		}

		return $datas;
	}


	$query_select_tvs = "(1, 2)";
	include_once("show_atv.php");

?>