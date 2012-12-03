<?php
/*
* DTPM Search Engine (PHP)
* Parse P file and render search form on the Web page
*/

function dtpm_search_config($pFile,$ep,$indexname){
	$pxml = simplexml_load_file($pFile);
	foreach($pxml->children() as $parameter)
	{
		$fieldname = $parameter->getName();
		$types = $parameter->attributes();
		$type = $types['type'];
		if ($type=="NAME" || $type=="UNIT" || $type=="DATE")
		{
			echo form_label($parameter, $fieldname);

			if (!svo_exists($ep,$indexname,$fieldname)){
				$data = array(
							    'name' => $fieldname,
							    'id' => 'svo_'.$fieldname,
							    'value' => 'true',
							    'content' => 'Enable SVO',
				);
				echo form_button($data);

				$cdata = array(
							    'name' => $fieldname."_style",
							    'id' => $fieldname."_style",
							    'value' => 'checkbox',
							    'checked' => 'checked',
				);
				$ddata = array(
							    'name' => $fieldname."_style",
							    'id' => $fieldname."_style",
							    'value' => 'dropdown',
							    
				);
				echo form_label("Dropdown", 'dropdown');
				echo form_radio($ddata);
				echo form_label("Checkbox", 'checkbox');
				echo form_radio($cdata);

			}
			else{
				$data = array(
							    'name' => $fieldname,
							    'id' => 'dis_svo_'.$fieldname,
							    'value' => 'true',
							    'content' => 'Disable SVO',
				);
				echo form_button($data);
			}
			echo "\n<br/>";
		}
	}
	
	
}
function dtpm_search($pFile,$ep,$indexname){
	$pxml = simplexml_load_file($pFile);
	foreach($pxml->children() as $parameter)
	{
	
		$fieldname = $parameter->getName();
		$types = $parameter->attributes();
		$type = $types['type'];
		
		$indexdir = $ep."index/".$indexname;
		
		if ($type == "IMG"){
			echo form_label('包含图像: ', 'img_label');
			echo form_checkbox('img','yes');
			echo "<br/>";
		}
		
		else if ($type == "CAT"){
			echo form_label('分类: ', 'cat_label');
			$render1 = array('name'=>'cat[]','size'=>24);
			$render2 = array('name'=>'cat[]','size'=>24);
			$render3 = array('name'=>'cat[]','size'=>24);
			echo form_input($render1);
			echo form_input($render2);
			echo form_input($render3);
			echo "<br/>";
		}
		else if ($type == "DATE" && !svo_exists($ep,$indexname,$fieldname)){
			echo form_label($parameter.': ', 'start_date_label');
			
			$options_year[0]='----';
			for($i=1900;$i<2012;$i++){
				$options_year[$i]=$i;
			}
			$options_month[0]='--';
			for($i=1;$i<13;$i++){
				$options_month[$i]=$i;
			}
			$options_day[0]='--';
			
			for($i=1;$i<32;$i++){
				$options_day[$i]=$i;
			}
			echo form_dropdown($fieldname."_dyear[]", $options_year,'',"id ='".$fieldname."_start_year'");
			echo "年";

			echo form_dropdown($fieldname."_dmonth[]", $options_month,'',"id ='".$fieldname."_start_month'");
			echo "月";

			echo form_dropdown($fieldname."_dday[]", $options_day,'',"id ='".$fieldname."_start_day'");
			echo "日";
			echo "至";
			echo form_checkbox('enable_'.$fieldname,'enabled',TRUE);

			echo form_dropdown($fieldname."_dyear[]", $options_year,'',"id ='".$fieldname."_end_year'");
			echo "年";

			echo form_dropdown($fieldname."_dmonth[]", $options_month,'',"id ='".$fieldname."_end_month'");
			echo "月";

			echo form_dropdown($fieldname."_dday[]", $options_day,'',"id ='".$fieldname."_end_day'");
			echo "日";
				
			echo "<br/>";
			
			
		}
		else { // SVO
			echo form_label($parameter.": ", $fieldname);
			$svoFile = $indexdir."/svo_$fieldname.txt";
			if (file_exists($svoFile)){
				parse_svo($svoFile,$fieldname);
				echo "<br/>";
			}
			else{
				$render = array('name'=>$fieldname,'size'=>24);
				echo form_input($render);
				echo "<br/>";
			}
		}
	}

}

function parse_svo($svoFile,$fieldname){
	$file = fopen($svoFile,"r");
	$string = fread($file,filesize($svoFile));
	fclose($file);
	$fds = "C";
	preg_match('/#\[([^\[\]]+)\]\[([^\[\]]+)\]=(.*)/',$string,$matches);
	$fds = $matches[2];
	$values = $matches[3];
	if (!empty($values)){
		$array = preg_split("/;/", $values);
		// Remove empty elements
		foreach( $array as $k=>$v){
			if( !$v )
			unset( $array[$k] );
		}	
		if ($fds == "C"){
			foreach($array as $value){
				echo "\n";
				echo form_checkbox($fieldname."[]", $value);
				echo "\n";
				echo form_label($value, 'label');
				//echo "<br/>";
			}
			
		}
		else if ($fds == "D"){
			$options = array();
			$options['0'] = "----";
			foreach($array as $value){
				// Test if $value is in yyyy-mm-dd format
				$year = "";
				$month = "";
				$day ="";
				
				if (preg_match("/(\d{4})-00-00/",$value,$matches)){
					if ($matches[1] != '0000')
						$year = $matches[1]."年";
				}
				
				if (preg_match("/(\d{4})-(\d{2})-00/",$value,$matches)){
					if ($matches[1] != '0000' && $matches[2] != '00'){
						$year = $matches[1]."年";
						$month = $matches[2]."月";
					}
				}
				
				if (preg_match("/(\d{4})-(\d{2})-(\d{2})/",$value,$matches)){
					if ($matches[1] != '0000' && $matches[2] != '00' && $matches[3] != '00'){
						$year = $matches[1]."年";
						$month = $matches[2]."月";
						$day = $matches[3]."日";
					}
				}
				if (!empty($year) and !empty($month) and !empty($day)){
					$options[$value] = $year.$month.$day;	
				}
				else if (!empty($year) and !empty($month)){
					$options[$value] = $year.$month;
				}
				else if (!empty($year)){
					$options[$value] = $year;
				}
				else
					$options[$value] = $value;

			}
			echo form_dropdown($fieldname,$options);
		}
	}
}

function svo_exists($ep,$indexname,$fieldname){
	$svoFile = $ep."index/".$indexname."/svo_$fieldname.txt";
	if (file_exists($svoFile))
		return true;
	else
		return false;
}

// dtpm_argv_parse() parses URI arguments
// $argv is an array of argument fetched from URI
function dtpm_argv_parse($argv,$ep,$indexname){
	$pFile = $ep."index/".$indexname."/$indexname.xml";
	$pxml = simplexml_load_file($pFile);
	$fieldname = "";
	$dates = array(); 
	$fieldtype = array(); //fieldname => type
	$where = "";
	$clause = "";
	$dateclause = "";
	foreach($pxml->children() as $child)
	{
		$fieldtype[$child->getName()] = $child->attributes()->type;

	}
	
	//echo "<br/>";
	foreach($argv as $key => $arg){
		// $key>1 avoids processing indexname and page number
		if ($key > 1){
			//echo "<br/>arg:";
			//print_r($arg);
			$arr = preg_split('/=/', $arg);
			$fieldname = $arr[0];
			$values = urldecode($arr[1]);
			//echo $fieldname;
			
			if ($fieldname=="pg_id"){
				$where = " pg_id = '$values' AND ";
				break;
			}
			
			// For common types other than CAT and DATE
			if (isset($fieldtype[$fieldname])){
				$clause = "";
				if ($fieldtype[$fieldname] == "NAME"){
					$arr = preg_split("/;/",$values);
					
					// Remove empty elements
					foreach( $arr as $k=>$v){
						if( !$v )
						unset( $arr[$k] );
					}
					
					$n = count($arr);
					$i = 1;
					$clause .= "( ";
					foreach($arr as $a){
							if ($i<$n)
								$clause .= " ($fieldname LIKE '%$a%') OR";
							else
								$clause .= " ($fieldname LIKE '%$a%') ";
							$i++;
					}
					
					$clause .=") AND ";
				}
				
				else if ($fieldtype[$fieldname] == "WTEXT"){
					$clause = " ($fieldname LIKE '%$values%') AND ";
				}
				
				// For fomatted date (passed from SVO), yyyy-mm-dd
				else if ($fieldtype[$fieldname] == "DATE"){
					$clause = " ($fieldname LIKE '$values') AND ";
				}
				
				
				else if ($fieldtype[$fieldname] == "CMT"){
					$clause = " ($fieldname LIKE '%$values%') AND ";
				}
				
				else if ($fieldtype[$fieldname] == "UNIT"){
					$arr = preg_split("/;/",$values);
					
					// Remove empty elements
					foreach( $arr as $k=>$v){
						if( !$v )
						unset( $arr[$k] );
					}
					
					$n = count($arr);
					$i = 1;
					$clause .= "( ";
					foreach($arr as $a){
							if ($i<$n)
								$clause .= " ($fieldname LIKE '%$a%') OR";
							else
								$clause .= " ($fieldname LIKE '%$a%') ";
							$i++;
					}
					
					$clause .=") AND ";
				}
				
				else if ($fieldtype[$fieldname] == "NUM"){
					$clause = " ($fieldname LIKE '$values') AND ";
				}
				
				else if ($fieldtype[$fieldname] == "CAT"){
					$arr = preg_split("/;/",$values);
					foreach($arr as $a){
						if (!empty($a)){
							$clause .= " ($fieldname LIKE '%;$a;%') AND ";
						}
					}
				}
				$where .= $clause;
			}
			// Processing IMG
			else if ($fieldname == "img"){
				foreach($fieldtype as $key=>$values){
					if ($values == "IMG")
						$tfieldname = $key;
				}
				$clause .= " ($tfieldname <> '') AND ";

				$where .= $clause;
			}
			
			else{
			// Processing DATE, common format, not from SVO
				//$clause = "";
				if (endswith($fieldname,'_dyear')){
					//$tfieldname is the authentic fieldname
					$tfieldname = str_replace("_dyear","",$fieldname);
					$dates[$tfieldname]['year'] = $values;
				}
				
				if (endswith($fieldname,'_dmonth')){
					//$tfieldname is the authentic fieldname
					$tfieldname = str_replace("_dmonth","",$fieldname);
					$dates[$tfieldname]['month'] = $values;
				}
				
				if (endswith($fieldname,'_dday')){
					//$tfieldname is the authentic fieldname
					$tfieldname = str_replace("_dday","",$fieldname);
					$dates[$tfieldname]['day'] = $values;
				}
			}

		}
	}
		

		if ( count($dates) > 0){
			//print_r($dates);
			$syear = '0000';
			$smonth = '00';
			$sday = '00';
				
			$eyear = '0000';
			$emonth = '00';
			$eday = '00';
				
			foreach ($dates as $fieldname => $date){
				
				if (isset($date['year']))
					$ex_year = explode(";",$date['year']);
				else 
					$ex_year = array();
				
				if (isset($date['month']))
					$ex_month = explode(";",$date['month']);
				else
					$ex_month = array();
				
				if (isset($date['day']))
					$ex_day = explode(";",$date['day']);
				else 
					$ex_day = array();
				
				
				if (count($ex_year)==1){
					// only start year
					$syear = $ex_year[0];
					$eyear = $syear;
				}
				else if (count($ex_year) == 2){
					$syear = $ex_year[0];
					
					$eyear = $ex_year[1];
					if ($eyear == '0'){
						$eyear = $syear;
					} 
				}
		
				if (count($ex_month)==1){
					// only start month
		
					$smonth = $ex_month[0];
					$emonth = $smonth;
				}
				else if (count($ex_month) == 2){
					$smonth = $ex_month[0];
					$emonth = $ex_month[1];
					
					if ($eyear == $syear){
						if ($emonth < $smonth)
							$emonth = $smonth;
					}
				}
		
				if (count($ex_day)==1){
					// only start day
					$sday = $ex_day[0];
					$eday = $sday;
				}
				else if (count($ex_day) == 2){
					$sday = $ex_day[0];
					$eday= $ex_day[1];
					if (($eyear == $syear) && ($emonth == $smonth)){
						if ($eday < $sday)
							$eday = $sday;
					}
				}
			
				$syear = str_pad($syear,4,'0',STR_PAD_LEFT);
				$smonth = str_pad($smonth,2,'0',STR_PAD_LEFT);
				$sday = str_pad($sday,2,'0',STR_PAD_LEFT);
					
				$eyear = str_pad($eyear,4,'0',STR_PAD_LEFT);
				$emonth = str_pad($emonth,2,'0',STR_PAD_LEFT);
				$eday = str_pad($eday,2,'0',STR_PAD_LEFT);
				
				
				// Avoid identical start and end dates
				if ($syear == $eyear){
					if ($smonth == $emonth){
						if ($sday == $eday){
							if ($sday=='00' && $smonth=='00'){
								$emonth ='12';
								$eday = '31';
							}
							else if ($sday=='00' && $smonth!=='00'){
								$eday = '31';
							}
						}
					}
				}
				
				$sdate = "$syear-$smonth-$sday";
				$edate = "$eyear-$emonth-$eday";
				
				$dateclause = " ($fieldname BETWEEN '$sdate' AND '$edate') AND ";
				$where .= $dateclause;
			}
			
		}

			return $where."1";
}

// return true if $str ends with $sub
function endswith( $str, $sub ) {
	return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );

}