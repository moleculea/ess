<?php 
/*
 * DTPM Parse Engine (PHP)
 * Parse P file and V files and display data as a list on the Web page
 */
function dtmp_load($pFile,$result,$ess,$hostname,$indexname,$group){
	$pxml = simplexml_load_file($pFile);
	$hostpath = "http://$hostname";
	// Loop of pages
	echo "<div class='page_line' style='border:1px solid silver'>\n";
	echo "<table>";
	foreach($result as $page){

		echo "<tr>\n";
		echo "<td class='page_cell' style='border:1px solid silver'>\n";
		
		$v_path = $page->v_path;
		$pg_name = $page->pg_name;
		$pg_id = $page->pg_id;
		$in_time =  $page->in_time;
		$up_time =  $page->up_time;
		echo "<div class='page_title'>\n";
		echo '<a href="'.$hostpath.'/index.php/'.$pg_name.'">'.$pg_name.'</a>';
		echo "&nbsp;";
		
		// Check cookie group (admin check)
		if ($group==1){
			echo anchor("edit/index/$indexname/pg_id=$pg_id","[Edit]",array('class' => 'edit_tag'));
		}
		echo anchor("listing/index/$indexname/1/pg_id=$pg_id","[Show]",array('class' => 'edit_tag'));
		if ($group==1){
			echo "<div class='parameter'>\n";
			echo "创建时间: ".$in_time;
			if ($up_time!="0000-00-00 00:00:00")
				echo "&nbsp;修改时间: ".$up_time;
			echo "</div>\n";
		}
		echo "</div>\n";
		
		// Note: this is HTTP accessible address for V files
		$vFile = $ess."indexed/".$v_path.$pg_name.".xml";
		$vxml = simplexml_load_file($vFile);
		//echo $pg_name;
		foreach($pxml->children() as $parameter)
		{

			$fieldname = $parameter->getName();
			$types = $parameter->attributes();
			$type = $types['type'];
			//echo $type;
			$values = $vxml->$fieldname;
			$value = $values[0];
			$value=trim($value);
			//echo $fieldname.":".$value;
			$parsed_value = dtpm_parse($fieldname ,$type ,$value, $parameter, $hostname);
			if ($parsed_value != false){
				echo $parsed_value;
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}

			
		}
		echo "</div>\n";
		echo "</td>\n";
		echo "</tr>\n";

	}
	
	echo "</table>\n";
	echo "\n</div>\n";
}

function dtpm_parse($fieldname ,$type ,$value, $parameter,$hostname){
	
	$parsed_value = '';
	switch($type)
	{
		case 'NAME':
			$parsed_value = parse_name($value);
			break;
		case 'DATE':
			$parsed_value = parse_date($value);
			break;
		case 'WTEXT':
			$parsed_value = parse_wtext($value,$hostname);
			break;
		case 'NUM':
			$parsed_value = parse_num($value);
			break;
		case 'UNIT':
			$parsed_value = parse_unit($value);
			break;
		case 'CMT':
			$parsed_value = parse_cmt($value,$hostname);
			break;
		case 'IMG':
			$parsed_value = parse_img($value,$hostname);
			break;
		case 'CAT':
			$parsed_value = parse_cat($value,$hostname);
			break;
		default:
			break;
	}
	if ($parsed_value != false){
		if ($type=="IMG"){
			if ($parsed_value == "empty")
				return  "<div style='float:left' class='page_image'><img src='/ess/images/icon/nonimage.png' width='120px' height='80px'></div>";
			else
				return  "<div style='float:left' class='page_image'>".$parsed_value."</div>";
		}
		else if ($type =="CAT")
			return  "<span class='parameter'>分类</span>: ".$parsed_value."";
		else
			return  "<span class='parameter'>".$parameter."</span>: ".$parsed_value;
		
	}
	else
		return false;
}

function parse_name($value){
	if ($value!=""){
		return $value;
	} 
	else
		return false;
}

function parse_date($value){
	if (preg_match('/(\d{4})-00-00/',$value,$matches)){
		$parsed_value = $matches[1]."年";
	}
	
	else if (preg_match('/(\d{4})-(\d{2})-00/',$value,$matches)){
		$parsed_value = $matches[1]."年".$matches[2]."月";
	}
	
	else if (preg_match('/(\d{4})-(\d{2})-(\d{2})/',$value,$matches)){
		$parsed_value = $matches[1]."年".$matches[2]."月".$matches[3]."日";
	}
	
	else if ($value == '0000-00-00'){
		$parsed_value = false;
	}
	
	else{
		$parsed_value = false;
	}
	return $parsed_value;
}

function parse_wtext($value,$hostname){

	if (!empty($value)){

		$hostpath = "http://$hostname";
		
		preg_match('/^;(.+);$/',$value,$matches);
		
		$parsed_value = $matches[1]; // Remove starting and ending ";"
		$array = preg_split("/;/", $parsed_value);
		$parsed_array = array();
		foreach($array as $p) // $p: e.g. 'apple[[value|foo]][[value1]]'
		{
			$pattern = array(
					'/\[\[([^\]\|]+)\|([^\]\|]+)\]\]/', # e.g. '[[value|foo]]'
					'/\[\[([^\]\|]+)\]\]/', # e.g. [[value]]
					); 
			$replacement = array('<a href="'.$hostpath.'/index.php/$1">$2</a>','<a href="'.$hostpath.'/index.php/$1">$1</a>');
			$parsed_array[] = preg_replace($pattern, $replacement, $p);
		}
		
		$parsed_value = implode(';',$parsed_array); # Original <br/>
		return $parsed_value;
	}
	else 
		return false;
}
function parse_num($value){
	
	if ($value!=""){
		return $value;
	} 
	else
		return false;
}

function parse_unit($value){
	$num ="";
	$cls ="";
	if (!empty($value)){
		if (preg_match('/^(\d+)$/',$value,$matches)){
			$num = $matches[1];
			$parsed_value = $num;
			return $parsed_value;
		}
		
		if (preg_match('/(.+);(.*)/',$value,$matches))
			$num = $matches[1];
			$cls = $matches[2];
			$parsed_value = $num.$cls;
			$parsed_value = $num;
			return $parsed_value;
	}
	else
		return false;
}

// Modified from parse_wtext()
function parse_cmt($value,$hostname){
	if (!empty($value)){
		$hostpath = "http://$hostname";
		preg_match('/^;(.+);$/',$value,$matches);
		
		$parsed_value = $matches[1]; // Remove starting and ending ";"
		$array = preg_split("/;/", $parsed_value);
		$parsed_array = array();
		$pattern = array(
							'/\[\[([^\]\|]+)\|([^\]\|]+)\]\]/', # e.g. '[[value|foo]]'
							'/\[\[([^\]\|]+)\]\]/', # e.g. [[value]]
							'/^([^:]+):/', # e.g. 'titlename:[[person]]'
		);
		$replacement = array('<a href="'.$hostpath.'/index.php/$1">$2</a>','<a href="'.$hostpath.'/index.php/$1">$1</a>','<span class="cmt">$1</span>: ');
		
		foreach($array as $p) // $p: e.g. 'apple[[value|foo]][[value1]]'
		{
			$parsed_array[] = preg_replace($pattern, $replacement, $p);
		}
		
		$parsed_value = implode(';',$parsed_array); # Original <br/>
		return $parsed_value;
	}
	else
		return false;
}

function parse_img($value,$hostname){
	
	if ($value !="")
	{
		if (preg_match('/^(.+);(.+)$/',$value,$matches)){
		$img_name = $matches[1];
		$img_path = $matches[2];
		$hostpath = "http://$hostname";
		$parsed_value = "<a href=\"$hostpath/index.php/File:$img_name\"><img src=\"$hostpath/$img_path\"></a>";
		return $parsed_value;
		}
		else
			return "empty";
	}
		
	else
		return "empty";
	
}

function parse_cat($value,$hostname){
	
	if ($value!="")
	{
		preg_match('/^;(.+);$/',$value,$matches);
		$parsed_value = $matches[1]; // Remove starting and ending ";"
		$array = preg_split("/;/", $parsed_value);
		$parsed_value = "";
		$hostpath = "http://$hostname";
		foreach($array as $p){
			$parsed_value .="<a href=\"$hostpath/index.php/Category:$p\">$p</a>;";
		}
		return $parsed_value;
	}
	else
		return false;
	
}