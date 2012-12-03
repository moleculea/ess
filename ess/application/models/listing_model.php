<?php 
class Listing_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    // Get the count number of a table
    function get_count($tb_name){
    	$this->load->database();

    	return $this->db->count_all($tb_name);
    }
    
    function get_count_where($tb_name,$where){
    	$this->load->database();
    	$query = $this->db->query("SELECT `pg_id`, `pg_name`, `v_path` FROM `$tb_name` WHERE $where");
    	return $query->num_rows();
    }
    
    function get_pages($tb_name, $num, $offset, $where, $order){
    	$this->load->database();
    	$query = $this->db->query("SELECT `pg_id`, `pg_name`, `v_path`, `in_time`, `up_time` FROM `$tb_name` WHERE $where LIMIT $offset, $num");
    	return $query->result();
    }
    
    
    function get_single_page($tb_name, $pg_id){
    	$this->load->database();
    	$query = $this->db->query("SELECT `pg_id`, `pg_name`, `v_path`, `in_time`, `up_time` FROM `$tb_name` WHERE `pg_id` = '$pg_id'");
    	return $query->row();
    }
    
    function get_single_page_all($tb_name, $pg_id){
    	$this->load->database();
    	$query = $this->db->query("SELECT * FROM `$tb_name` WHERE `pg_id` = '$pg_id'");
    	return $query->row();
    }
    
    function get_indexes(){
    	$this->load->database();
    	$this->db->select('in_name');
    	$query= $this->db->get('indextb');
    	return $query->result();
    }
}