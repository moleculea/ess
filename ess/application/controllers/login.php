<?php
class Login extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	// Login function
	public function index(){
		$this->load->helper('cookie');
		if(!$this->input->post('submit') && !$this->input->cookie('username')){
			
			$data['title'] = 'Login';
			$helper = array(
									'html',
									'url',
									'form',
			);
			$this->load->helper($helper);
			$data['scenario'] = "login";
			$data['jquery'] = TRUE; // enable jQuery (include jQuery script)
				
			$data['script_inline'] = "
			<script type=\"text/javascript\">
			$(document).ready(function() {
				$('form').submit(function(){
					//alert($('input[name=password]').val());
					//return false;
					username = $('input[name=username]').val();
					username = $('input[name=password]').val();
					if ($.trim(username)==''){
						alert('Username cannot be empty.');
						return false;
					}
					
					if ($.trim(password)==''){
						alert('Password cannot be empty.');
						return false;
					}

				});
			
			});
			</script>\n";
			
			$this->load->view('templates/header',$data);
			$this->load->view('login/login_view',$data);
			$this->load->view('templates/footer');
			
		}
		
		// Login validation
		if($this->input->post('submit')=="Login"  && !$this->input->cookie('username')){
			
			$data['title'] = 'Login';
			$this->load->helper('security');
			$helper = array(
							'html',
							'url',
							'form',
			);
			$this->load->helper($helper);
			$this->load->model("Login_model");
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			
			// Encode password
			$password = do_hash($password, 'md5');
			//echo $password;
			//echo "<br/>";
			$num = $this->Login_model->login($username,$password);
			//echo $num;
			
			if ($num > 0){
				
				$group = $this->Login_model->login($username,$password);

				if ($group == "1"){
					
					$cookie = array(
					    'name'   => 'group',
					    'value'  => '1',
					    'expire' => '86500',
					);
					
					$this->input->set_cookie($cookie);
				}
				else{
					$cookie = array(
					    'name'   => 'group',
					    'value'  => '0',
					    'expire' => '86500',
					);
						
					$this->input->set_cookie($cookie);
				}
				
				$usercookie = array(
							    'name'   => 'username',
							    'value'  => $username,
							    'expire' => '86500',
				);
				$this->input->set_cookie($usercookie);
			}
			
			header("Location: login");
		
		}

		// Already logged in page
		if($this->input->cookie('username')){
			$username = $this->input->cookie('username');
			$group = $this->input->cookie('group');
			$msg = "Login succeed.";
			if ($group == "1"){
				$msg .= "<br/>Welcome admin '$username'.";
			}
			else 
				$msg .= "<br/>Welcome user '$username'.";
			
			$data['title'] = 'Login';
			$data['msg'] = $msg;
			$data['scenario'] = 'login_result';
			
			$helper = array(
							'html',
							'url',
							'form',
			);
			$this->load->helper($helper);
			$this->load->view('templates/header',$data);
			$this->load->view('login/login_view',$data);
			$this->load->view('templates/footer');
			
		}
			

	}
	
	
	// Signup function
	public function signup(){
		if(!$this->input->post('submit')){
		$data['title'] = 'Signup';
		$helper = array(
						'html',
						'url',
						'form',
		);
		$this->load->helper($helper);
		$data['scenario'] = "signup";
		
		$data['jquery'] = TRUE; // enable jQuery (include jQuery script)
		 
		$data['script_inline'] = "
<script type=\"text/javascript\">
$(document).ready(function() {
	$('form').submit(function(){
		//alert($('input[name=password]').val());
		//return false;
		username = $('input[name=username]').val();
		if ($.trim(username)==''){
			alert('Username cannot be empty.');
			return false;
		}
		
		if ($('input[name=password]').val()==''){
			alert('Password cannot be empty.');
			return false;
		}
		
		else{
			if ($('input[name=password]').val() != $('input[name=conf_password]').val()){
				alert('Please reconfirm password.');
				return false;
			}
		}
	});

});
</script>\n";
		
		$this->load->view('templates/header',$data);
		$this->load->view('login/login_view',$data);
		$this->load->view('templates/footer');
	}	
		
		if($this->input->post('submit') == "Signup"){
			
			$this->load->helper('security');
			$this->load->model("Login_model");
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			
			// Encode password
			$password = do_hash($password, 'md5');
			
			$num = $this->Login_model->register($username,$password);
			
			if ($num > 0){
				$msg = "Signup succeed.";
			}
			else 
				$msg = "Signup failed.";
			
			$data['title'] = 'Signup';
			$helper = array(
									'html',
									'url',
									'form',
			);
			$this->load->helper($helper);
			$data['msg'] = $msg;
			$data['scenario'] = "result";
			$data['num'] = $num;
			
			$this->load->view('templates/header',$data);
			$this->load->view('login/login_view',$data);
			$this->load->view('templates/footer');
			
		}
	}
	public function logout(){
		
		
		if ($this->input->cookie('username')){
			
			$this->load->helper('cookie');
			$time = time() - 3600;
			
			$logout = array(
						    'name'   => 'username',
						    'value'  => '',
						    'expire' => $time,
			);
			
			$logoutgroup = array(
							    'name'   => 'group',
							    'value'  => '',
							    'expire' => $time,
			);
			
			$this->input->set_cookie($logout); // username cookie
			$this->input->set_cookie($logoutgroup); //group cookie
			header("Location: logout");
	}
	else{
		
		$helper = array(
								'html',
								'url',
								'form',
		);
		$this->load->helper($helper);
			
		if ($this->input->cookie('username')){
			$msg = "Still logged in.";
		
		}
		else
		$msg = "Logout succeed.";
			
		$data['msg'] = $msg;
		$data['scenario'] = "logout";
		$data['title'] = "Logout";
			
		$this->load->view('templates/header',$data);
		$this->load->view('login/login_view',$data);
		$this->load->view('templates/footer');
		
	}
	}
}