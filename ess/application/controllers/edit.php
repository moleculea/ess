<?php
class Edit extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		
	}
	public function index($indexname="",$pg_id=""){
		
		if (!$this->input->post("submit")){
			// If indexname is empty, show a list of indexes to be edited
			if (empty($indexname)){
				
				$this->load->model("Listing_model");
				$result = $this->Listing_model->get_indexes();
				$helper = array(
							    'html',
								'url',
								'form',
								'file',
				);
				$this->load->helper($helper);
					
				$data['title'] = "Edit";
				$data['result'] = $result;
				$data['scenario'] = "index_list";
				
				$this->load->view('templates/header',$data);
				$this->load->view('edit/edit_view',$data);
				$this->load->view('templates/footer');
	
			}
			
			else{
				
				// If pg_id is empty, edit the index
				// If pg_id is a number, it means it is a page number
				if (empty($pg_id) or preg_match("/^\d+$/", $pg_id)){
	
					$helper = array(
									    'html',
										'url',
										'form',
										'file',
					);
					$this->load->helper($helper);
					$this->load->library('pagination');
					$this->load->model("Listing_model");
					$tb_name = "tb_in_".$indexname;
					
					$where= " 1 ";
					$order = "pg_id";
					
					$total_num = $this->Listing_model->get_count_where($tb_name,$where);
	
					$per_page = 20;
					$config['base_url'] = site_url("edit/index/$indexname");
					$config['uri_segment'] = 4;
					$config['total_rows'] = $total_num;
					//$config['suffix'] = $clause; 
					$config['use_page_numbers'] = TRUE;
					$config['first_url'] = site_url("edit/index/$indexname").'/1';
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
	
					$result = $this->Listing_model->get_pages($tb_name, $per_page, $offset, $where, $order);
	
					$data['title'] = "Edit";
					$data['result'] = $result;
					$data['scenario'] = "index";
					$data['indexname'] = $indexname;
					$data['total_num'] = $total_num;
					$this->load->view('templates/header',$data);
					$this->load->view('edit/edit_view',$data);
					$this->load->view('templates/footer');
				
				}
					
				// Else, edit the specified page
				else{
					$argv = func_get_args();
					$arg = $argv[1];
					if ($arg == "delete"){

						// Delete index
					}
					
					// Edit page
					else if (preg_match("/^pg_id=(\d+)$/", $arg, $matches)){
						$pg_id = $matches[1];
						
						$this->load->database();
						$query_ep = $this->db->query('SELECT ep,hostname FROM info');
						$row_ep = $query_ep->row();
						$ep = $row_ep->ep;
						$hostname = $row_ep->hostname;
						
						$pFile = $ep."index/".$indexname."/$indexname.xml";
						
						$this->load->model("Listing_model");
						$tb_name = "tb_in_$indexname";
						$page = $this->Listing_model->get_single_page($tb_name, $pg_id);
						$data['title'] = "Edit";
						$data['page'] = $page;
						$data['scenario'] = "page";
						$data['indexname'] = $indexname;
						$data['pFile'] = $pFile;
						$helper = array(
									    'html',
										'url',
										'form',
										'file',
						);
						$this->load->helper($helper);
						$this->load->view('templates/header',$data);
						$this->load->view('edit/edit_view',$data);
						$this->load->view('templates/footer');
					
					}
				}
			}
		}
		
		else if ($this->input->post("submit") == "Edit"){
			
			$paralist = $this->input->post();
			
			$argv = func_get_args();
			$indexname = $argv[0];
			$arg = $argv[1];

			$flag = 0; // Update flag to identify the existence changes
			$changes = array();
			
			// Edit page
			if (preg_match("/^pg_id=(\d+)$/", $arg, $matches)){
				$pg_id = $matches[1];
				$tb_name = "tb_in_$indexname";
				//echo $pg_id;
				
				// Update MySQL
				$this->load->model("Listing_model");
				$page = $this->Listing_model->get_single_page_all($tb_name, $pg_id);
				$pg_name = $page->pg_name;
				$clause = "UPDATE $tb_name SET ";
				foreach($paralist as $key=>$values){
					
					if ($key!="submit" && $key!="sync"){
						// If the selected value is different from the POST value
						// 
						//echo "trimvalue:".trim($values);
						$values = trim($values);
						if (trim($page->{"$key"})!=$values){
							$clause .= "$key ='$values', ";
							$changes[$key] = $values;
							$flag = 1;
						}
					}
					/*
					if ($i<$n-2){
						$clause .=", ";
					}
					
					$i++;
					*/
				}
				
				// Only if changes exist the update will be executed
				if ($flag == 1){
					$clause .= "up_time = now()";
					$clause .= " WHERE pg_id = '$pg_id'";
					//echo $clause;
					$this->load->database();
					$this->db->query($clause);
				
					//Update V file
					$this->load->model("Listing_model");
					$this->load->helper("url");
					$page = $this->Listing_model->get_single_page($tb_name, $pg_id);
					
					$v_path = $page->v_path;
					$pg_name = $page->pg_name;
	
					$ess = base_url();
					$vFile = $ess."indexed/".$v_path.$pg_name.".xml";

					$vxml = simplexml_load_file($vFile);

					// Update changes into XML 
					foreach($changes as $key=>$values){
						$vxml->{"$key"} = "$values";
					}
					
					$esspath = realpath(".");
					$vFilepath = $esspath."/indexed/".$v_path.$pg_name.".xml";
					//echo $vFilepath;
					echo $vxml->asXML();
					$vxml->asXML($vFilepath);
					
					if ($this->input->post("sync")=="yes"){
						$esspath = realpath(".");
						$this->load->file($esspath."/application/dtpm/dtpm_sync.php");
						$query = $this->db->query('SELECT ep,pyw FROM info WHERE id=1');
						// Fetch Pywikipedia path
						$row = $query->row();
						$pyw =  $row->pyw;
						$ep =  $row->ep;
						
						$pFile = $ep."index/".$indexname."/$indexname.xml";
						
						dtpm_sync($indexname,$pFile,$pg_name,$changes,$pyw,$ep);
						//echo $pg_name;
					}
				}

				header("Location: /ess/listing/index/$indexname/1/pg_id=$pg_id");
			}
			
			

			
			
		}
	}

	
}