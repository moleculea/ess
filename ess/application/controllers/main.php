<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function index(){
		#echo "Main Page";
		/*
		$helper = array(
		'html',
		'url',
	);
		$this->load->helper($helper);
		$data['title'] = 'Main Page';
		$data['links'] = array();
		// Navigation list
		$data['links'][] = anchor('init', 'Initialization', 'title="Initialization"');
		$data['links'][] = anchor('indexation', 'Creation of index', 'title="Creation of index"');
		$data['links'][] = anchor('listing', 'List', 'title="List"');
		$data['links'][] = anchor('search', 'Search', 'title="Search"');
		$this->load->view('templates/header',$data);
		$this->load->view('main_view',$data);
		$this->load->view('templates/footer');
		
		
		*/
		
		header("Location: listing");
	}
	/*
	public function bar(){
		$cmd = "/usr/bin/python /var/local/ess-python/main.py";
		#$pidfile = "/home/anshichao/pid";
		#$pid = exec(sprintf("%s >/dev/null 2>&1 & echo $!", $cmd));
		#echo $pid;
		
		#exec(sprintf("%s > %s 2>&1 & echo $! > %s", $cmd, $outputfile, $pidfile));
		exec("sudo python /var/local/ess-python/main.py >/dev/null & ");
		#exec("sudo python /var/local/ess-python/main.py >/dev/null &");

	}
	*/
}