<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


	function analyze_response($response, $host_url, $categ, $datas){
		$special_class_name = "series-section";
		// analyze response
		libxml_use_internal_errors(true);
		$dom = new DomDocument();
		$dom->loadHTML($response['contents']);
		$finder = new DomXPath($dom);
		libxml_use_internal_errors(false);

		$spaner = $finder->query("//section[contains(@class, '$special_class_name')]");
		foreach($spaner as $section_target_container){
			$li_tags = $section_target_container->getElementsByTagName("li");
			for($i = 0; $i < $li_tags->length; $i++){
				// each li tag
				$li_tag = $li_tags->item($i);
				$a_tag = $li_tag->getElementsByTagName("a")->item(0);
				$href = $a_tag->getAttribute('href');
				$href_url = parse_url($href);
				$href_url = (isset($href_url['host']))?$url:$host_url.$href_url['path'];
				$img_tag = $li_tag->getElementsByTagName("img")->item(0);
				$image = $img_tag->getAttribute('src');
				foreach($a_tag->getElementsByTagName('div') as $div_tag){
					if($div_tag->getAttribute('class') == "title"){
						$title = $div_tag->textContent;
						break;
					}
				}
				$datas[] = array('title' => $title, 'category_id' => $categ['id'], 'url' => $href_url, 'thumbnail' => $image);
			}
		}

		return $datas;
	}


	$query_select_tvs = "(7, 8)";
	include_once("show_atv.php");

?>