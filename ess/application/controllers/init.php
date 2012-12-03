<?php 

class Init extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	public function index(){
		
		$this->pyw_config(); //default function
	}
	
	public function pyw_config(){
		$data['title'] = 'Pywikipedia configuration';
		$helper = array(
				'html',
				'url',
				'form',
		);
		$this->load->helper($helper);
		$this->load->library('table');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('hostname', 'hostname', 'required');
		$this->form_validation->set_rules('portnumber', 'port number', 'required');
		$this->form_validation->set_rules('pyw_path', 'path to Pywikipedia', 'required');	
		$this->form_validation->set_rules('ep_path', 'path to ESS-Python', 'required');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('templates/header',$data);
			$this->load->view('pyw/config_view',$data);
			$this->load->view('templates/footer');
		}	
		else
		{ 	
			$pyw = trim($this->input->post('pyw_path'));
			$hostname = trim($this->input->post('hostname'));
			$hostname_array = array('hostname' => $hostname);
			$ep = trim($this->input->post('ep_path'));
			$port = trim($this->input->post('portnumber'));
			
			$this->session->set_userdata($hostname_array); // save hostname to session

			
			if (!is_dir($pyw)){
				echo "<script>alert('Invalid path to Pywikipedia');</script>";
				echo "<script>window.history.back();</script>";
			}
			
			else if (!is_dir($ep)){
				echo "<script>alert('Invalid path to ESS-Python');</script>";
				echo "<script>window.history.back();</script>";
			}
			
			else{
				
				$pyw = trim_slashes($pyw);
				$pyw = "/".$pyw."/";
				$ep = trim_slashes($ep);
				$ep = "/".$ep."/";
				
				if (is_file($pyw."login.py") and is_file($ep."main.py")){
				$pyw_array = array('pyw_path' => $pyw);
				$this->session->set_userdata($pyw_array); // save pyw path to session
				$ep_array = array('ep_path' => $ep);
				$this->session->set_userdata($ep_array); // save ep (ess-python) path to session
				$port_array = array('portnumber' => $port);
				$this->session->set_userdata($port_array); // save ep (ess-python) path to session
				
				
				$exec_stdout =  exec(sprintf("python %slogin.py -test",$pyw));

				if (stristr($exec_stdout,"You are logged in") === FALSE)
			    	header("Location: pyw_login"); // failed to log in, redirect to pyw_login
				else
					header("Location: pyw_init");
				}
				
				else {
					echo "<script>alert('Incomplete Pywikipedia parts');</script>";
					echo "<script>window.history.back();</script>";
				}
			}				
		}
		
	}
	
	public function pyw_login(){

		$this->load->library('session');
		$data['title'] = 'Pywikipedia login';
		$helper = array(
					'html',
					'url',
					'form',
		);
		$this->load->helper($helper);
		$this->load->library('table');
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('mw_username', 'Username', 'required');
		$this->form_validation->set_rules('mw_psw', 'Password', 'required');
		
		
		if ($this->form_validation->run() == FALSE)
		{
		$this->load->view('templates/header',$data);
		$this->load->view('pyw/login_view',$data);
		$this->load->view('templates/footer');
		}
		else
		{
			$pyw = $this->session->userdata('pyw_path');
			//echo $pyw."user-config.py";
			if (is_file( $pyw."user-config.py")){
				$exec_stdout =  exec(sprintf("python %slogin.py",$pyw),$sd);
				$username = $this->input->post('mw_username');
				$password = $this->input->post('mw_psw');
				exec(sprintf("python %slogin.py -pass:%s",$pyw,$password));
				$exec_stdout =  exec(sprintf("python %slogin.py -test",$pyw));
				if ( stristr($exec_stdout,"You are logged in") === FALSE )
					echo "Password is incorrect";
				else
					header("Location: pyw_init");
			}
			else
				echo "user-config.py file does not exist.<br/>Please configure this file.";
		}
		
	}
	public function pyw_init(){

		$data['title'] = 'Pywikipedia initialiaztion';
		$helper = array(
							'html',
							'url',
							'form',
		);
		$this->load->helper($helper);
		$this->load->library('session');
		$this->load->library('table');

		if ($this->input->post('pyw_init_form_hidden') == FALSE) // before clicking "Continue"
		{
		$this->load->view('templates/header',$data);
		$this->load->view('pyw/init_view',$data);
		$this->load->view('templates/footer');
		}
		
		else if ($this->input->post('pyw_init_form_hidden') == 'Hidden') // after clicking "Continue"
		{
			// temporary config for linking database
			$config['hostname'] = "localhost";
			$config['username'] = DB_USERNAME;
			$config['password'] = DB_PASSWORD;
			$config['database'] = "";
			$config['dbdriver'] = "mysql";
			$config['dbprefix'] = "";
			$config['pconnect'] = FALSE;
			$config['db_debug'] = TRUE;
			$config['cache_on'] = FALSE;
			$config['cachedir'] = "";
			$config['char_set'] = "utf8";
			$config['dbcollat'] = "utf8_unicode_ci";
			
			$init_db = $this->load->database($config, TRUE);
			
			$query = $init_db->query('CREATE DATABASE IF NOT EXISTS `ess_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
			
			$pyw = $this->session->userdata('pyw_path');
			$ep = $this->session->userdata('ep_path');
			$port = $this->session->userdata('portnumber');
			$hostname = $this->session->userdata('hostname');
			if ($query){
				$init_db->query('USE `ess_db`;');
				$init_db->query('CREATE TABLE IF NOT EXISTS ib_list(`ib_id` INT(4) AUTO_INCREMENT ,`ib_name` VARCHAR(40) NOT NULL , PRIMARY KEY (ib_id), INDEX(ib_name)) ENGINE = INNODB;');
				$init_db->query('CREATE TABLE IF NOT EXISTS info(`id` INT(4), `hostname` VARCHAR(40), `port` INT(8), `pyw` VARCHAR(40), `ep` VARCHAR(40), PRIMARY KEY(id)) ENGINE = INNODB;');
				// update if record already exists (duplicate key)
				$init_db->query("INSERT INTO info VALUES('1','".$hostname."','".$port."','".$pyw."','".$ep."') ON DUPLICATE KEY UPDATE id=1");
				/*
				$data = array(
				               'id' => '1',
				               'hostname' => $hostname,
				               'pyw' => $pyw
				);
				$init_db->insert('info',$data);
				*/
			}
			$init_db->close();
			header("Location: ib_config");
		}
	}
	
	public function ib_config()
	{
		$data['title'] = 'Infobox configuration';
		$helper = array(
									'html',
									'url',
									'form',
		);
		
		$this->load->helper($helper);
		$this->load->library('table');	
		$this->load->library('form_validation');

		$this->form_validation->set_rules('ib_cat', 'Infobox category', 'required');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('templates/header',$data);
			$this->load->view('ib/config_view',$data);
			$this->load->view('templates/footer');
		}
		else
		{
			$category = trim($this->input->post('ib_cat'));
			$prefix = trim($this->input->post('ib_prefix'));
			$this->ib_scan($category,$prefix);
		}
		
	}
	
	private function ib_scan($category,$prefix) {
		
		$this->load->database();
		$this->load->library('session');

		$query = $this->db->query('SELECT pyw,ep FROM info WHERE id=1');
		
		// fetch Pywikipedia and ESS-Python path
		$row = $query->row();
		$pyw =  $row->pyw;
		$ep = $row->ep;
		// output path for saved infoboxlist.txt
		$output = $ep."output/";
		// convert category (Chinese characters) into URL code to be passed as arguments to python
		$category = urlencode($category); 
		// Pywikipedia command line
		$cmd = "/usr/bin/python ".$pyw."replace.py \"{{{\" \"{{{ \" -savenew:".$output."infoboxlist.txt -requiretitle:".$prefix." -cat:".$category." -always";
		exec($cmd." >/dev/null 2>&1 & echo $!",$pidoutput);	
		// return PID of this process to be monitored 
		$pid = $pidoutput[0];
		
		$pid_array = array('pid' => $pid);
		$this->session->set_userdata($pid_array);
		header("Location: ib_exec");

	}
	
	
	// progress of exec()
	public function ib_exec_pro(){

		$this->load->library('session');
		$pid = $this->session->userdata('pid');
 		
		if (isRunning($pid))
			echo "1";
		else 
			echo "0";
	
	}
	
	public function ib_exec(){
		$this->load->library('session');
		$pid = $this->session->userdata('pid');
		
		if (isRunning($pid))
			$flag = 1;
		else 
			$flag = 0;
		
		if ( $flag == 1){
			$data['title'] = 'Infobox configuration';
			$helper = array(
								'html',
								'url',
								'form',
								'file',
			);
			$this->load->helper($helper);

			$data['flag'] = $flag; // view flag for ib_exec/run
			$data['script_tags'] = array();
			//$data['script_tags'][] = script_tag('javascript/getprogress.js');
			$data['status'] = img('images/icon/ajax-loader.gif')." Running.";
			$data['jquery'] = TRUE;
			$data['script_inline']=<<<STR
<script type="text/javascript">
var intervalID;
function autoRefresh(){
intervalID = window.setInterval("autoProgress()",200);
}
function autoProgress(){
$(document).ready(function() {
	$.get("/ess/init/ib_exec_pro", function(result){
    	if (result == 0) {
    		window.clearInterval(intervalID);
    		location.href = "/ess/init/ib_exec";
    	}
  });
});
}
/*
function autoProgress()
{
	var flag;
	flag = getProgress();
	if (flag == 0){
	window.clearInterval(intervalID);
	location.href = "/ess/init/ib_exec";
	}

}
*/
</script>		
STR;
			$data['body_onload'] = 'onload="autoRefresh()"';
			$this->load->view('templates/header',$data);
			$this->load->view('ib/exec_view',$data);
			$this->load->view('templates/footer');
		}
		
		else if ( $flag == 0)
		{
			$data['title'] = 'Infobox configuration';
			$helper = array(
												'html',
												'url',
												'form',
												'file',
			);
			$this->load->helper($helper);
			$this->load->library('session');
			//$pid = $this->session->userdata('pid');
			
			$data['flag'] = $flag;	// view flag for ib_exec/fin 
			$this->load->database();
			$this->load->library('session');
			$query = $this->db->query('SELECT ep FROM info WHERE id=1');
			$row = $query->row();
			$ep = $row->ep;
				
			$infoboxfile = $ep."output/infoboxlist.txt";
			$infoboxlist = read_file($infoboxfile);
			$data['iblist'] =  $infoboxlist;
			$data['script_tags'] = array();
			$data['script_tags'][] = script_tag('javascript/checkall.js');
			$data['script_inline'] = "
<script type=\"text/javascript\">
$(document).ready(function() {
	$.getScript('http://localhost/ess/javascript/checkall.js');
});
</script>\n";
			$data['jquery'] = TRUE; // enable jQuery (include jQuery script)

			
			$this->load->view('templates/header',$data);
			$this->load->view('ib/exec_view',$data);
			$this->load->view('templates/footer');

		}

	}
	public function ib_save($save = NULL){
		
		$data['title'] = 'Infobox configuration';
		$helper = array(
										'html',
										'url',
										'form',
										'file',
		);
		$this->load->helper($helper);
		$this->load->library('session');
		
		if ($this->input->post('infobox')){ // save infobox_array into session at the first stage
			$infobox_array = $this->input->post('infobox'); // 
			$infobox_session =array('infobox' => $infobox_array);
			$this->session->set_userdata($infobox_session);
		}
		
		if (!$this->input->post('ib_save_form_hidden')){ // show post data at the first stage
			if (is_array($this->input->post('infobox')))
				$data['infobox_array'] = $this->session->userdata('infobox');
			else
				$data['infobox_array'] = NULL;

			$this->load->view('templates/header',$data);
			$this->load->view('ib/save_view',$data);
			$this->load->view('templates/footer');
		}
		if ($this->input->post('ib_save_form_hidden') == "Hidden"){ // save data into database at the second stage
			
			// import list of selected infobox into database
			
			$infobox_array = $this->session->userdata('infobox');
			if ($infobox_array){
			$this->load->database();
			$this->db->query('TRUNCATE ib_list');
			foreach ($infobox_array as $infobox){
				$ib_array[] = array(
				'ib_id' => '',
				'ib_name' => $infobox,
				);
			}
			$query = $this->db->insert_batch('ib_list',$ib_array);
			if ($query) {
				$data['title'] = "Initialization complete";
				$data['save_complete'] = "Infobox list has been successfully saved.<br/><br/>";
				$indexation = anchor('indexation', 'Creation of index', '');
				$data['init_complete'] = "Initialization complete. <br/>Please go to $indexation for indexation process.";
			
				$this->load->view('templates/header',$data);
				$this->load->view('ib/save_view_complete',$data);
				$this->load->view('templates/footer');
			}
			}
			else 
			{
				$data['title'] = "Initialization complete";
				$data['save_complete'] = "No infobox has been saved.<br/><br/>";
				$indexation = anchor('indexation', 'Creation of index', '');
				$data['init_complete'] = "Initialization complete. <br/>Please go to $indexation for indexation process.";
					
				$this->load->view('templates/header',$data);
				$this->load->view('ib/save_view_complete',$data);
				$this->load->view('templates/footer');
			}

		}	
		
	}
	
}


function isRunning($pid){ // determine by PID whether a progress is running
    try{
        $result = shell_exec(sprintf("ps %d", $pid));
        if( count(preg_split("/\n/", $result)) > 2){
            return true;
        }
    }catch(Exception $e){}

    return false;
}


