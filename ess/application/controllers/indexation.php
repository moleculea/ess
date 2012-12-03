<?php 

class Indexation extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	public function index(){
		
		$this->ib_config();
	}
	
	public function ib_config(){
		
		$data['title'] = 'Indexation configuration';
		$helper = array(
						'html',
						'url',
						'form',
		);
		$this->load->helper($helper);
		$this->load->library('table');
		$this->load->library('session');
		$this->load->database();
		$indextable = <<<STR
CREATE TABLE IF NOT EXISTS indextb
(
in_id INT NOT NULL AUTO_INCREMENT,
in_name VARCHAR(32) NOT NULL,
in_tb VARCHAR(48) NOT NULL,
ib_ref VARCHAR(128) NOT NULL,
PRIMARY KEY (in_id)
)
STR;

		$this->db->query($indextable);# create table ess_db.index
		//$this->db->query($metaindex);# create metaindex for ess_db.index
		$query_ib = $this->db->query('SELECT ib_id, ib_name FROM ib_list ORDER BY ib_id ASC');
		$query_hn = $this->db->query('SELECT hostname FROM info');
		if ($query_ib->num_rows() > 0){
		   $data['query_result'] = $query_ib->result();
		   $row_hn = $query_hn->row();
		   $hostname = $row_hn->hostname;
		   $urlprefix = "http://".$hostname."/index.php/Template:";
		   // variables for jQuery
		}
		   $type_options = array(
		   	'0' => '---',
		   	'1' => 'NAME',
		   	'2' => 'DATE',
		   	'3' => 'WTEXT',
		   	'4' => 'NUM',
		   	'5' => 'UNIT',
		   	'6' => 'CMT',
		   	'7' => 'IMG',
		   );
		   $i = "'+len+'";
		   //$type_select = form_dropdown('type_select_'.$i, $type_options, '');
		   $parameter = form_input(array('name' => 'parameter_'.$i, 'id' => 'parameter_'.$i ,'value' => '', 'size' => 24));
		   $fieldname = form_input(array('name' => 'fieldname_'.$i, 'id' => 'fieldname_'.$i ,'value' => '', 'size' => 24));
		   $optional = form_input(array('name' => 'optional_'.$i, 'id' => 'optional_'.$i ,'value' => '', 'size' => 12, 'disabled' => 'disabled'));
		   $type_select = '';
		   $type_select ="<select name=\"type_select_'+len+'\">";
		   $type_select .= '<option value="0">---</option>';
		   $type_select .= '<option value="1">NAME</option>';
		   $type_select .= '<option value="2">DATE</option>';
		   $type_select .= '<option value="3">WTEXT</option>';
		   $type_select .= '<option value="4">NUM</option>';
		   $type_select .= '<option value="5">UNIT</option>';
		   $type_select .= '<option value="6">CMT</option>';
		   $type_select .= '<option value="7">IMG</option>';
		   $type_select .='</select>';
		   $data['jquery'] = TRUE; // enable jQuery (include jQuery script)
		   $data['script_tags'] = array();
		   $data['script_tags'][] = script_tag('javascript/indexconfig.js');
		   
		   $data['script_inline'] = "
<script type=\"text/javascript\">
var urlprefix = '$urlprefix'; // statement for urlprefix usage in indexconfig.js
$(document).ready(function() {
	$('#append').click(function() {
	   	var table = $('#type_select_table tr');  
	    var len = table.length;  
	   	$('#type_select_table').append('<tr id='+len+'><td>$parameter</td><td>$fieldname</td><td id=s_'+len+'>$type_select</td><td>$optional</td></tr>');
	});

});
</script>\n";
		
		
		$this->load->view('templates/header',$data);
		$this->load->view('index/ib_config_view',$data);
		$this->load->view('templates/footer');
	}
	
	public function pg_config() {

		# Ajax validate page
		if ($this->input->post('validate') == "submit"){
			$indexname = $this->input->post('index_name');
			$indexname = str_replace(' ','_',$indexname);
			$this->load->database();
			$showtables = $this->db->query("SHOW TABLES LIKE 'tb_in_$indexname'");
			$num_rows = $showtables->num_rows();
			echo $num_rows;

			//if ($num_rows > 0)
				//echo "Validate";
		}
		
		// DTPM Engine (PHP) implemented here
		// Get and process POST data from ib_config
		if ($this->input->post('ib_config') == "submit")
		{
			// Include DTPM-MySQL config file
			#include('dtpm_mysql.php');

			$indexname = $this->input->post('index_name');
			$indexname = str_replace(' ','_',$indexname);
			$parameter = Array();
			$fieldname = Array();
			$type = Array();
			$num = Array();
			
			$i = 1;
			$n = 100; // Maximum lines in POST
			for($i = 1;$i < $n;$i++){
				if (isset($_POST['parameter_'.$i])){
					// If any parameter is empty, it means fieldname, type_select,
					// and optional (num) are also empty (due to jQuery validation)
					if ($this->input->post('parameter_'.$i))
						$parameter[] = $this->input->post('parameter_'.$i);
					else 
						continue;
					$fieldname[] = $this->input->post('fieldname_'.$i);
					$type[] = $this->input->post('type_select_'.$i);
					//Fill num with 0 if it is not NUM format
					if ($this->input->post('optional_'.$i))
					{
						$num[] = $this->input->post('type_select_'.$i);
					}
					else
						$num[] = 0;
				}
				// All lines have been processed
				else
					break;
			}
			

			$indexstr =<<<STR
CREATE TABLE `tb_in_$indexname`
(
pg_id INT(5) NOT NULL AUTO_INCREMENT,
pg_name VARCHAR(48) NOT NULL,
v_path CHAR(4) NOT NULL,
STR;
			$helper = array('file');
			$helper = array(
						    'html',
							'url',
							'form',
							'file',
			);
			$this->load->helper($helper);
			
			$esspath = realpath(".");
			include($esspath."/application/dtpm/dtpm_mysql.php");

			// Concatinate customed DTPM data
			$custom = "";
			$n = 0;
			foreach($type as $t){
				$custom .= $fieldname[$n]." ".$dtpm_mysql[$t].",";
				$n++;
			}

			$indexstr .= $custom;
			$indexstr .=<<<STR
cat VARCHAR(255),
rev_id VARCHAR(6) NOT NULL,
in_time DATETIME NOT NULL,
up_time DATETIME NOT NULL,
PRIMARY KEY (pg_id),
UNIQUE (pg_name),
INDEX (cat),
UNIQUE (rev_id)
STR;
			// Add index to DTPM fields
			$indexcustom = "";
			
			foreach($fieldname as $f){
				$indexcustom .= ",INDEX ($f)";
			}
			$indexstr .= $indexcustom;
			$indexstr .= ");";
			$this->load->database();
			$this->db->query("DROP TABLE IF EXISTS `tb_in_$indexname`");
			$this->db->query($indexstr);
			
			// Create a directory for this index at $ep/index/ indexname
			$query_ep = $this->db->query('SELECT ep FROM info');
			$row_ep = $query_ep->row();
			$ep = $row_ep->ep;
			if (!is_dir($ep."index")){
				mkdir($ep."index");
				if (!is_dir($ep."index/".$indexname)){
				mkdir($ep."index/".$indexname);
				}
			}
			else{
				if (!is_dir($ep."index/".$indexname)){
				mkdir($ep."index/".$indexname);
				}
			}
			/*
			 * Create P file
			 */
			#include('dtpm_mysql.php');
			$esspath = realpath(".");
			$this->load->file($esspath."/application/dtpm/dtpm_mysql.php");
			
			$xmlstring =<<<XML
<?xml version="1.0" encoding="utf-8"?>
<parameter>
</parameter>
XML;
			$xml = new SimpleXMLElement($xmlstring);

			// $n is total valid lines of parameters processed from POST data
			for ($i=0;$i<$n;$i++){
				$xml->addChild($fieldname[$i],$parameter[$i]);
				//echo $xml->$fieldname[$i];
				$id = $type[$i];
				$xml->$fieldname[$i]->addAttribute("type", $dtpm[$id]);
			}
			$xml->addChild("cat","");
			// Add hidden CAT field
			$xml->cat[0]->addAttribute("type", "CAT");
			// Create P file at the directory for this index
			$xml->asXML($ep."index/".$indexname."/".$indexname.".xml");
			
			
			// View for pg_config
			$query_ib = $this->db->query('SELECT ib_id, ib_name FROM ib_list ORDER BY ib_id ASC');
			$query_hn = $this->db->query('SELECT hostname FROM info');
			if ($query_ib->num_rows() > 0){
				$data['query_result'] = $query_ib->result();
				$row_hn = $query_hn->row();
				$hostname = $row_hn->hostname;
				$urlprefix = "http://".$hostname."/index.php/Template:";
				// variables for jQuery
			}

			$data['title'] = 'Page configuration';
			 $helper = array(
								    'html',
									'url',
									'form',
			);
			$this->load->helper($helper);
			$data['jquery'] = TRUE; // enable jQuery (include jQuery script)
			$data['indexname'] = $indexname;
			$data['script_tags'][] = script_tag('javascript/pageconfig.js');
			$data['script_inline'] = "
<script type=\"text/javascript\">
var urlprefix = '$urlprefix'; // statement for urlprefix usage in indexconfig.js
</script>\n";
			$data['view'] = 'page';
			$this->load->view('templates/header',$data);
			$this->load->view('index/pg_config_view',$data);
			$this->load->view('templates/footer');

		}
		
		// Get and process POST data from pg_config
		// Collect pages according to referenced infoboxes specified
		if ($this->input->post('pg_config') == "submit"){
			$iblist = $this->input->post('ib_select');
			$indexname = $this->input->post('indexname');
			$this->load->database();
			
			$ib_ref = "";  // A string of infobox id to be imported into database
			$ib_name = Array(); // List of infobox name in an array
			
			if ($iblist){
				foreach($iblist as $ib){
					$ib_ref .= $ib.";";
					$query= $this->db->query("SELECT * FROM ib_list WHERE ib_id = '$ib'");
					$row = $query->row(1);
					$ib_name[] = $row->ib_name;
				}
			}
			//echo $ib_ref;
			//print_r($ib_name);
			
			// $ib_ref is a ';' separated string for infobox id, e.g. '4;6;7'

			// Check indextb if the same in_name '$indexname' exists
			$exists = $this->db->query("SELECT * FROM indextb WHERE in_name = '$indexname'");
			$num_rows = $exists->num_rows();
			
			// If $indexname already exists, update it rather than insert a new record
			if ($num_rows > 0) {
				$indexstr =<<<STR
UPDATE indextb SET ib_ref = '$ib_ref' WHERE in_name = '$indexname'
STR;
			}
			
			// If not, insert data for this index into table 'indextb'
			else
			{
				$indexstr =<<<STR
INSERT INTO indextb (in_name,in_tb,ib_ref) VALUES ('$indexname','tb_in_$indexname','$ib_ref')
STR;
			}
			$this->db->query($indexstr);

			// Start collecting pages that referenced infoboxes in $ib_name
			$query = $this->db->query('SELECT pyw,ep FROM info WHERE id=1');
			
			// Fetch Pywikipedia and ESS-Python path
			$row = $query->row();
			$pyw =  $row->pyw;
			$ep = $row->ep;
			
			// Output path for saved page list
			$output = $ep."index/".$indexname."/";
			// Page list path
			$pagelistfile =  $output.$indexname.".txt";
			// P file path
			$pfile = $output.$indexname.".xml";
			// Open and empty the TXT for page list
			$file = fopen($pagelistfile,"w");
			fwrite($file,"");
			fclose($file);
			
			// Convert infobox name (if containing Chinese) into URL code to be passed as arguments to python
			$ib_name_en = Array();
			foreach ($ib_name as $name){
				$name = str_replace(" ","_","$name");
				$ib_name_en[] = urlencode($name);
			}

			// Pywikipedia command line
			if ($iblist) // If infobox is specified, the cmd will be executed.
			{
				$cmd = "";
				foreach($ib_name_en as $name_en){
				$cmd = "/usr/bin/python ".$pyw."replace.py \"==\" \"== \" -save:".$pagelistfile." -ref:Template:$name_en -always ";
				exec($cmd." >/dev/null 2>&1 & echo $!",$pidoutput);
				}
				$pid = $pidoutput[0];
			}
			// Return PID of this process to be monitored

			// View start
			$data['view'] = 'start';
			$data['title'] = 'Indexation Ready';
			$data['indexname'] = $indexname;
			$data['iblist'] = $iblist;

			if (file_exists($pfile))
			{
				$xml = simplexml_load_file($pfile);
			}
			else
				$xml = "";
			
			
			$helper = array(
							    'html',
								'url',
								'form',
								'file',
			);
			$this->load->helper($helper);
			

			$data['xml'] = $xml;
			$data['ib_name'] = $ib_name;
			
			$data['jquery'] = TRUE; // enable jQuery (include jQuery script)
			if ($iblist) {// If infobox is specified, the jQuery ajax scripts will be executed
			$data['script_inline'] =<<<STR
<script type="text/javascript">
var intervalID;
var intervalID2;
function autoRefresh(){
intervalID = window.setInterval("page_stat_pro()",200);
}

function autoUpdate(){
intervalID2 = window.setInterval("page_stat()",500);
setTimeout("stopautoUpdate()",10000); // Stop autoUpdate ajax in 10 seconds
}

function stopautoUpdate(){
window.clearInterval(intervalID2);
}	
	
function page_stat_pro(){
$(document).ready(function() {
	$.post('/ess/indexation/page_stat_pro',{pid:'$pid'},function(result){
    	if (result == 0) {
    		window.clearInterval(intervalID);
    		page_stat();
    		autoUpdate();
    	}
    	
  });
		
});
}

function page_stat(){
$(document).ready(function() {
	$.post('/ess/indexation/page_stat',{page:'$pagelistfile'},function(data){
		$("#pg_num").html('<li>' + data + ' pages.</li>');
	});

});
}
</script>
STR;
			}
			$data['status'] = img('images/icon/ajax-loader.gif')." Caculating.";
			$data['body_onload'] = 'onload="autoRefresh()"';
			$data['indexname'] = $indexname;
			$data['pagelistfile'] = $pagelistfile;
			$this->load->view('templates/header',$data);
			$this->load->view('index/pg_config_view',$data);
			$this->load->view('templates/footer');

		}

	}
	
	public function page_stat_pro(){
	
		$pid = $_POST['pid'];
		if (isRunning($pid))
			echo "1";
		else 
			echo "0";

	}
	
	public function page_stat(){
		
		$pagelistfile = $_POST['page'];
		$file = fopen($pagelistfile,"r");
		$size = filesize($pagelistfile);
		if ($size == 0)
			$size = 1;
		$pagelist = fread($file,$size);
		fclose($file);
		if (preg_match_all("/#\[\[([^\]]+)]]/",$pagelist,$matches)){
			echo count($matches[1]); // Number of pages
		}
		else
			echo "0";

	}
	
	// Initiate indexation
	public function init(){
		
		// POST from pg_config
		if ($this->input->post('pg_config_submit') == "Start"){

			
			$indexname = $this->input->post('indexname');
			//$pagelistfile = $this->input->post('pagelistfile');
			
			$this->load->database();
			
			$query_ep = $this->db->query('SELECT ep FROM info');
			$row_ep = $query_ep->row();
			$ep = $row_ep->ep;
			
			$output = $ep."index/".$indexname."/";
			
			// Page list path
			$pagelistfile =  $output.$indexname.".txt";
			
			// P file path
			$pfile = $output.$indexname.".xml";

			// Python script execution portal
			$cmd = "/usr/bin/python ".$ep."main.py -f -p $pfile";
			exec($cmd." >/dev/null 2>&1 & echo $! ;",$pidoutput);
			//exec($cmd,$output);
			
			//exec("$cmd 2>&1", $output, $return_val);
			//print_r($output);

			// Return PID of this process to be monitored
			$pid = $pidoutput[0];
			//$pid = 1;
			$helper = array(
										    'html',
											'url',
											'form',
											'file',
			);
			$this->load->helper($helper);
			$data['title'] = "Indexing";
			$data['status'] = img('images/icon/ajax-loader.gif')." Caculating.";
			$data['body_onload'] = 'onload="autoRefresh()"';
			$data['indexname'] = $indexname;
			
			$data['jquery'] = TRUE; // enable jQuery (include jQuery script)
			
			$data['script_inline'] =<<<STR
<script type="text/javascript">
var intervalID;
function autoRefresh(){
intervalID = window.setInterval("index_stat_pro()",200);

}
function index_stat_pro(){
$(document).ready(function() {
	$.post('/ess/indexation/index_stat_pro',{pid:'$pid'},function(result){
		index_stat();
    	if (result == 0) { // If finished, stop calling index_stat();
    		window.clearInterval(intervalID); 
    		$("#index_complete").html("<br/>Indexation complete.");
    		$("#index_list").html("Click <a href='/ess/listing/index/$indexname'>here</a> to list indexed information.");
    		return false;
    	}

  });
		
});
}

function index_stat(){
$(document).ready(function() {
	var latest_id;
	var page_name;
	var number;
	
	$.post('/ess/indexation/index_stat',{get:'latest_id',tb_name:'tb_in_$indexname'},function(data){
		latest_id = data;
		$.post('/ess/indexation/index_stat',{get:'page',tb_name:'tb_in_$indexname',latest_id: latest_id},function(data){
			page_name = data;	
			$.post('/ess/indexation/index_stat',{get:'number',pagelistfile:'$pagelistfile'},function(data){
			number = data;	
			percent = latest_id/number;
			// Reserve 2-digit fractional part
			percent = Math.round(percent*100);
			$("#index_stat").html('Indexing page "'+ page_name +'"<br/>Process:'+latest_id+'/'+ number +'('+ percent +'%)');
			});
		});
	});

});
}
</script>
STR;
			$data['indexname'] = $indexname;
			$this->load->view('templates/header',$data);
			$this->load->view('index/init_view',$data);
			$this->load->view('templates/footer');	
		}
	}
	
	public function index_stat_pro(){
	
		$pid = $_POST['pid'];
		if (isRunning($pid))
			echo "1";
		else
			echo "0";
	}
	
	public function index_stat(){
		$this->load->database();
		if ($this->input->post('get') == 'latest_id'){
			$tb_name = $this->input->post('tb_name');
			$query = $this->db->query("SELECT COUNT( * ) FROM `$tb_name` WHERE 1");
			$row = $query->row_array();
			$row = array_values($row);
			$latest_id =  $row[0];
			echo $latest_id;
		}
		
		if ($this->input->post('get') == 'page'){
			$tb_name = $this->input->post('tb_name');
			$latest_id = $this->input->post('latest_id');
			
			if ($latest_id){
				$query = $this->db->query("SELECT pg_name FROM `$tb_name` WHERE pg_id='$latest_id'");
				$row = $query->row();
				echo $row->pg_name;
			}
		}
		
		if ($this->input->post('get') == 'number'){
			$pagelistfile = $this->input->post('pagelistfile');
			$file = fopen($pagelistfile,"r");
			$size = filesize($pagelistfile);
			if ($size == 0)
				$size = 1;
			$pagelist = fread($file,$size);
			fclose($file);
			if (preg_match_all("/#\[\[([^\]]+)]]/",$pagelist,$matches)){
				echo count($matches[1]); // Number of pages
			}
			else
				echo "0";
		}
	}

	
}
// Global function for indexation.php
function isRunning($pid){
	// Determine by PID whether a progress is running
	try{
		$result = shell_exec(sprintf("ps %d", $pid));
		if( count(preg_split("/\n/", $result)) > 2){
			return true;
		}
	}catch(Exception $e){
	}

	return false;
}