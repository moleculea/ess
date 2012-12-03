<?php 

class Listing extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	// Process POST data from search into WHERE-CLAUSE-URI
	public function proc($indexname){
		if ($this->input->post('search_submit')=="Search"){
		$where_clause_uri = "";
		$helper = array(
						    'html',
							'url',
							'form',
							'file',
		);
		$this->load->helper($helper);
			
		$dest = current_url();
		$dest = preg_replace('/(\/proc\/)/', '/index/', $dest);
		//	echo $dest;
		$post = $this->input->post();
		
		//print_r($post);

		foreach($post as $key => $value){
			
			// For non-array POST value
			if (!is_array($value)){
				$value = trim($value);
				if (!empty($value) && $key!="search_submit")
				$where_clause_uri .= "/$key=$value";
			}
			else{
				
				// If each element of the array is empty, ignore this array
				$v = implode("",$value);
				if (empty($v))
					break;
				
				$values = implode(";",$value);
				if ($values!="0;0")
					$where_clause_uri .= "/$key=$values";
			}

		}
		//echo $where_clause_uri;
		
		// Redirect to main list function
		header("Location: http://localhost/ess/listing/index/$indexname/1$where_clause_uri");
	
		}
	}
	public function index($indexname=""){

		// If indexname is specified as the argument in URL
		if (!empty($indexname)){
			$tb_name = "tb_in_".$indexname;
			
			
			$this->load->model("Listing_model");
			
			$helper = array(
						    'html',
							'url',
							'form',
							'file',
			);
			$this->load->helper($helper);
			
			//echo current_url();
			
			$this->load->database();
			
			$query_ep = $this->db->query('SELECT ep,hostname FROM info');
			$row_ep = $query_ep->row();
			$ep = $row_ep->ep;
			$hostname = $row_ep->hostname;
			
			//WHERE-CLAUSE-URI Conversion
			$args = func_num_args();
			//print_r($args);
			$argv = func_get_args();
			//print_r($argv);
			
			$esspath = realpath(".");
			$this->load->file($esspath."/application/dtpm/dtpm_search.php");
			
			// WHERE-CLAUSE
			

			//echo dtpm_argv_parse($argv,$ep,$indexname);
			$where  = dtpm_argv_parse($argv,$ep,$indexname);
			
			// DEBUG LINE
			// echo $where;
			$order = "pg_id";
			
			// Append clause after page segment URI
			$clause = '';
			foreach($argv as $key => $arg){
				if ($key > 1)
					$clause .= '/'.$arg;
			}
			$total_num = $this->Listing_model->get_count_where($tb_name,$where);
			//echo $total_num;
			// Load pagination
			$this->load->library('pagination');
			
			$per_page = 20;
			$config['base_url'] = site_url("listing/index/$indexname");
			$config['uri_segment'] = 4;
			$config['total_rows'] = $total_num;
			$config['suffix'] = $clause; // WHERE-CLAUSE-URI and ORDER-CLAUSE-URI
			$config['use_page_numbers'] = TRUE;
			$config['first_url'] = site_url("listing/index/$indexname").'/1'.$clause;
			$config['per_page'] = $per_page;
			$config['num_links'] = 5;
			$config['first_link'] = '首页';
			$config['last_link'] = '尾页';
			$config['prev_link'] = '上一页';
			$config['next_link'] = '下一页';
			$config['anchor_class'] = "class = 'pagination_number' ";
			$config['cur_tag_open'] = "&nbsp;<span class=\"pagination_cur\">";
			$config['cur_tag_close'] = '</span>';
			# List content enclosed tags
			$config['full_tag_open'] = "\n<div class=\"pagination\">\n";
			$config['full_tag_close'] = "\n</div>\n";
			
			$this->pagination->initialize($config);	
			if (!$this->uri->segment(4))
				$segment = 1;
			else
				$segment = $this->uri->segment(4);
			
			$offset = ($segment - 1)*$per_page;
			# Result is data fetched from database
			$result = $this->Listing_model->get_pages($tb_name, $per_page, $offset, $where, $order);
			//echo $result;
			$ess = base_url();
			
			$data['title'] = "Listing";
			$data['indexname'] = $indexname;
			$data['total_num'] = $total_num;
			$data['result'] = $result;
			$data['ep'] = $ep;
			$data['ess'] = $ess;
			$data['hostname'] = $hostname;
			$this->load->helper('cookie');
			$group = 0;
			 if ($this->input->cookie('username')){
				if ($this->input->cookie('group')=='1'){
					$group = 1;
				}
			}
			$data['group'] = $group;
			$this->load->view('templates/header',$data);
			$this->load->view('list/list_view',$data);
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
			
			$data['title'] = "Listing";
			$data['indexname'] = "";
			$data['result'] = $result;
			$this->load->view('templates/header',$data);
			$this->load->view('list/list_view',$data);
			$this->load->view('templates/footer');
			
		}
	}

	
	
}