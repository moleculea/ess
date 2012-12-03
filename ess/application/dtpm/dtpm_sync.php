<?php
function dtpm_sync($indexname,$pFile,$pg_name,$changes,$pyw,$ep){

	$type = "";
	$pxml = simplexml_load_file($pFile);
	$parsed_values = array();
	
	// Parse the value
	foreach($changes as $key=>$value){
		// DTPM type
		$type = $pxml->{"$key"}->attributes();
		//echo $type;
		
		if (dtpm_sync_parse($type, $value))
			$parsed_values[$key] = dtpm_sync_parse($type, $value);
		else
			$parsed_values[$key] = "";
	}
	print_r($parsed_values);

	$replacement = "";
	$cmd = "";
	// Create pywikipedia cmd
	foreach($parsed_values as $key=>$value){
		$parameter = $pxml->{"$key"};
		
		// Append multiple replacements
		//$replacement .= " \"(\|\s*$parameter\s*)=(.+)(\s*(\}\}|\|.+=))\" \"\\1=$value\\3\" ";
		$pg_name = rawurlencode($pg_name);
		echo "\n";
		echo unicode_encode($parameter);
		echo "\n";
		echo unicode_encode($value);
		echo "\n";
		$cmd .= unicode_encode($parameter)."##".unicode_encode($value)."&&";


	}
	//$cmd = "/usr/bin/python ".$pyw."replace.py $replacement -page:\"$pg_name\" -regex -always >/dev/null 2>&1";
	$cmd .= ";;".$pg_name;
	echo $cmd;
	
	echo $ep;
	
	$repl_cron = $ep."output/repl_cron.txt";
	$count = $ep."output/count.id";
	
	$file = fopen($repl_cron,"w");
	fwrite($file,$cmd);
	fclose($file);
	
	$f = fopen($count,"r");
	
	$size = filesize($count);
	
	if ($size == 0)
		$size = 4;
	
	$number = fread($f,$size);
	fclose($f);
	
	echo $number;
	$num = intval($number);
	echo $num;
	$num++;
	$number = strval($num);
	
	$fw = fopen($count,"w");
	fwrite($fw,$number);
	fclose($fw);


}
function dtpm_sync_parse($type , $value){
	switch($type)
	{
		case 'NAME':
			$parsed_value = sync_parse_name($value);
			break;
		case 'DATE':
			$parsed_value = sync_parse_date($value);
			break;
		case 'WTEXT':
			$parsed_value = sync_parse_wtext($value);
			break;
		case 'NUM':
			$parsed_value = sync_parse_num($value);
			break;
		case 'UNIT':
			$parsed_value = sync_parse_unit($value);
			break;
		case 'CMT':
			$parsed_value = sync_parse_cmt($value);
			break;
		case 'IMG':
			$parsed_value = sync_parse_img($value);
			break;
		case 'CAT':
			$parsed_value = sync_parse_cat($value);
			break;
		default:
			break;
	}
	
	if ($parsed_value != false){
		return $parsed_value;
	}
	else
		return false;

}

function sync_parse_name($value){
	if ($value!=""){
		return $value;
	}
	else
		return false;
}

function sync_parse_date($value){
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
function sync_parse_wtext($value){

	if (!empty($value)){

		preg_match('/^;(.+);$/',$value,$matches);

		$parsed_value = $matches[1]; // Remove starting and ending ";"
		$array = preg_split("/;/", $parsed_value);
		$parsed_value = implode('<br/>',$array); # Original <br/>
		return $parsed_value;
	}
	else
		return false;
}

function sync_parse_unit($value){
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


function sync_parse_cmt($value){
	if (!empty($value)){

		preg_match('/^;(.+);$/',$value,$matches);

		$parsed_value = $matches[1]; // Remove starting and ending ";"
		$array = preg_split("/;/", $parsed_value);
		$parsed_array = array();
		$pattern = array('/:/');
		$replacement = array('：');

		foreach($array as $p) 
		{
			// replace ':' into '：'
			$parsed_array[] = preg_replace($pattern, $replacement, $p);
		}

		$parsed_value = implode('<br/>',$parsed_array); # Original <br/>
		return $parsed_value;
	}
	else
	return false;
}

function sync_parse_img($value){

	return false;

}

function sync_parse_cat($value){

	if ($value!="")
	{
		preg_match('/^;(.+);$/',$value,$matches);
		$parsed_value = $matches[1]; // Remove starting and ending ";"
		$array = preg_split("/;/", $parsed_value);
		$parsed_value = "";
		foreach($array as $p){
			$parsed_value .="[[Category:$p]]";
		}
		return $parsed_value;
	}
	else
		return false;

}

function unicode_encode($name)
{
	$name = iconv('UTF-8', 'UCS-2', $name);
	$len = strlen($name);
	$str = '';
	for ($i = 0; $i < $len - 1; $i = $i + 2)
	{
		$c = $name[$i];
		$c2 = $name[$i + 1];
		echo "\n";
		echo "c:".ord($c);
		echo "\n";
		echo "c2:".ord($c2);
		if (ord($c) > 0 && ord($c2)> 0)
		{
			//echo "p:".preg_match("/[a-zA-Z0-9_]/",$c);
			//echo "c:".$c;
			// 两个字节的文字
			//$str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
			$str .='\\u'.base_convert(ord($c2), 10, 16).base_convert(ord($c), 10, 16);
		}
		else
		{
			$str .= $c;
		}
	}
	return $str;
}