<?php
class Search extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	
	public function config($indexname=""){
		if (!empty($indexname)){
			$this->load->database();
			$query_ep = $this->db->query('SELECT ep FROM info');
			$row_ep = $query_ep->row();
			$ep = $row_ep->ep;
			$pFile = $ep."index/".$indexname."/$indexname.xml";
			//echo $pFile;
				
			$helper = array(
						    'html',
							'url',
							'form',
							'file',
			);
			$this->load->helper($helper);
				
			$data['title'] = "Search";
			$data['scenario'] = "config";
			$data['indexname'] = $indexname;
			$data['pFile'] = $pFile;
			$data['ep'] = $ep;
			$data['jquery'] = TRUE; // enable jQuery (include jQuery script)
			
			$data['script_inline'] = "
<script type=\"text/javascript\">
$(document).ready(function() {
	var fieldname;
	var tb_name;
	var fds;
	var fds_val;
	
	// Enable SVO
	$('[id^=svo_]').click(function(){
		
		fieldname = $(this).attr('name');
		tb_name = 'tb_in_$indexname';
		
		fds = fieldname + '_style';
		//alert($('#'+fds+':checked').val());
		//alert('fieldname:'+fieldname);
		fds_val = $('#'+fds+':checked').val();
		//alert(fds_val);
		// Set Ajax to synchronous
		$.ajaxSetup({async:false}); 
		
		$.post('/ess/search/svo',{fieldname:fieldname,tb_name:tb_name,svo:'check'},function(data){
			//alert(data);
			if (data == 1){
				c = confirm(fieldname+' has more than 20 different records, are you sure to make SVO for it?');
				if (!c){
					return false;
				}
				else{
					$.post('/ess/search/svo',{fieldname:fieldname,svo:'create',indexname:'$indexname',ep:'$ep',fds:fds_val,tb_name:tb_name},function(data){
						if (data!='false'){
							alert('SVO created.');
							window.location.reload();
						}
					});
				}
			}
			else
			{
			
				$.post('/ess/search/svo',{fieldname:fieldname,svo:'create',indexname:'$indexname',ep:'$ep',fds:fds_val,tb_name:tb_name},function(data){
					if (data!='false'){
						alert('SVO created.');
						window.location.reload();
					}
				});
			}
		});
	});
	
	// Disable SVO
	$('button[id^=dis_svo]').click(function(){
		fieldname  = $(this).attr('name');
		$.post('/ess/search/svo',{fieldname:fieldname,svo:'disable',indexname:'$indexname',ep:'$ep'},function(data){
		if (data == 1)
			alert('SVO deleted.');
			window.location.reload();
		});
	});
	
});
</script>\n";

			$this->load->view('templates/header',$data);
			$this->load->view('search/search_view',$data);
			$this->load->view('templates/footer');
			
		}
	
	}
	
	// Ajax call function for SVO
	
	public function svo(){
		$this->load->database();
		$fieldname = $this->input->post('fieldname');
		
		if ($this->input->post('svo') == "check"){
			
			$tb_name = $this->input->post('tb_name');
			$query = $this->db->query("SELECT DISTINCT $fieldname from `$tb_name`");
			if ($query->num_rows()>20)
				echo "1";
			else
				echo "0";
		}
		
		if ($this->input->post('svo') == "create"){
			// Create SVO	
			//echo "Created";
			$indexname = $this->input->post('indexname');
			$ep = $this->input->post('ep');
			$fds_val = $this->input->post('fds');
			$tb_name = $this->input->post('tb_name');
			//echo $tb_name;
			$fds_flag = "C";
			if ($fds_val == "dropdown")
				$fds_flag = "D";
			$svoFile = $ep."index/".$indexname."/svo_$fieldname.txt";
			$svoInfo = "#[$fieldname][$fds_flag]=";
			$query = $this->db->query("SELECT DISTINCT `$fieldname` from `$tb_name`");
			//$result = $query->result;
			foreach($query->result() as $row){
				if (!empty($row->$fieldname))
					$svoInfo .= $row->$fieldname.";";
			}
			$file = fopen($svoFile,"w");
			$fin = fwrite($file,$svoInfo);
			//fread($file,filesize($svoFile));
			fclose($file);
			echo $fin;
		}
		
		if ($this->input->post('svo') == "disable"){
			$indexname = $this->input->post('indexname');
			$ep = $this->input->post('ep');
			$svoFile = $ep."index/".$indexname."/svo_$fieldname.txt";
			$fin = unlink($svoFile);
			echo $fin;
		}
		
		
	}
	
	public function index($indexname=""){
		if (!empty($indexname)){
			$this->load->database();
			$query_ep = $this->db->query('SELECT ep FROM info');
			$row_ep = $query_ep->row();
			$ep = $row_ep->ep;
			$pFile = $ep."index/".$indexname."/$indexname.xml";

			$helper = array(
								    'html',
									'url',
									'form',
									'file',
			);
			$this->load->helper($helper);
			
			$data['title'] = "Search";
			$data['scenario'] = "search";
			$data['indexname'] = $indexname;
			$data['pFile'] = $pFile;
			$data['ep'] = $ep;
			
			
			$data['jquery'] = TRUE; // enable jQuery (include jQuery script)
			$data['script_tags'][] = script_tag('javascript/search.js');
			$this->load->view('templates/header',$data);
			$this->load->view('search/search_view',$data);
			$this->load->view('templates/footer');

		}
		
		else
		{
			
			$this->load->model("Listing_model");
			$result = $this->Listing_model->get_indexes();
			$helper = array(
							    'html',
								'url',
								'form',
								'file',
			);
			$this->load->helper($helper);
				
			$data['title'] = "Search";
			$data['result'] = $result;
			$data['scenario'] = "empty";
			$this->load->view('templates/header',$data);
			$this->load->view('search/search_view',$data);
			$this->load->view('templates/footer');
		}
	}
	
	
}
