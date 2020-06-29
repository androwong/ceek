<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


	function analyze_response($response, $host_url, $categ, $datas){
		$special_class_name = "kd-docs-news";
		// analyze response
		libxml_use_internal_errors(true);
		$dom = new DomDocument();
		$dom->loadHTML($response['contents']);
		$finder = new DomXPath($dom);
		libxml_use_internal_errors(false);

		$spaner = $finder->query("//div[contains(@class, '$special_class_name')]");
		foreach($spaner as $div_target_container){
			$a_tags = $div_target_container->getElementsByTagName("a");
			for($i = 0; $i < $a_tags->length; $i++){
				// each a tag
				$a_tag = $a_tags->item($i);
				$href = $a_tag->getAttribute('href');
				$href_url = parse_url($href);
				$href_url = (isset($href_url['host']))?$url:$host_url.$href_url['path'];
				$h4_tag = $a_tag->getElementsByTagName("h4")->item(0);
				if($h4_tag) $title = $h4_tag->textContent; else $title = $a_tag->getAttribute('title');
				$img_tag = $a_tag->getElementsByTagName("img")->item(0);
				if($img_tag){
					$image = $img_tag->getAttribute('src');
					$datas[] = array('title' => $title, 'category_id' => $categ['id'], 'url' => $href_url, 'thumbnail' => $image);
				}
			}
		}

		return $datas;
	}

	
	$query_select_tvs = "(3, 4)";
	include_once("show_atv.php");

?>