<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


	function analyze_response($response, $host_url, $categ, $datas){
		$special_class_name = "newsItem";
		// analyze response
		libxml_use_internal_errors(true);
		$dom = new DomDocument();
		$dom->loadHTML($response['contents']);
		$finder = new DomXPath($dom);
		libxml_use_internal_errors(false);

		$spaner = $finder->query("//div[contains(@class, '$special_class_name')]");
		foreach($spaner as $div_target_container){
			// each div tag
			$a_tags = $div_target_container->getElementsByTagName("a");
			if($a_tags->length > 1){
				$a_tag = $a_tags->item(0);
				$img_tag = $a_tag->getElementsByTagName("img")->item(0);
				if($img_tag){
					$image = $img_tag->getAttribute('data-original');
					$href = $a_tag->getAttribute('href');
					$href_url = parse_url($href);
					$href_url = (isset($href_url['host']))?$url:$host_url.$href_url['path'];
					$a_tag = $a_tags->item(1);
					$h5_tag = $a_tag->getElementsByTagName("h5")->item(0);
					if($h5_tag){
						$title = $h5_tag->textContent;
						$datas[] = array('title' => $title, 'category_id' => $categ['id'], 'url' => $href_url, 'thumbnail' => $image);
					}
				}
			}
		}

		return $datas;
	}


	$query_select_tvs = "(5, 6)";
	include_once("show_atv.php");

?>