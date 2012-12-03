<?php
class Login_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}
	
	function register($username,$password){
		$this->load->database();
		$query = $this->db->query("INSERT INTO `user` (user_name,password) VALUES ('$username','$password')");
		$conf = $this->db->query("SELECT * FROM `user` WHERE `user_name` = '$username'");
		return $conf->num_rows();
	}
	
	function login($username,$password){
		$this->load->database();
		$query = $this->db->query("SELECT * FROM `user` WHERE `user_name` = '$username' AND `password` = '$password'");
		return $query->num_rows();
	}
	
	function status($username){
		$this->load->database();
		$query = $this->db->query("SELECT group FROM `user` WHERE `user_name` = '$username'");
		$row = $query->row();
		return $row->group;
	}
	
	
}