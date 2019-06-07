<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Auth_model','auth');

	}

	public function index()
	{
		$cookie = get_cookie('kucingcyber');
		$isLogged = $this->session->userdata('logged');
		if(!empty($isLogged)){
			redirect('user');
		}else if(!empty($cookie)){
			$row = $this->auth->get_cookie($cookie);
			if(!empty($row)){
				$this->_create_session($row);
			}else{
				$data = array(
					'action' => site_url('/auth/login'),
					'email' => set_value('email'),
					'password' => set_value('password'),
					'remember' => set_value('remember'),
					'message' => $this->session->flashdata('message')
				);
				$this->load->view('mainpage/auth/login',$data);
			}
		}else{
			$data = array(
				'action' => site_url('/auth/login'),
				'email' => set_value('email'),
				'password' => set_value('password'),
				'remember' => set_value('remember'),
				'message' => $this->session->flashdata('message')
			);
			$this->load->view('mainpage/auth/login',$data);
		}
	}

	public function login(){
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$remember = $this->input->post('remember');

		$req = $this->auth->login($email,$password);
		if(!empty($req)){
			if(!empty($remember)){
				$key = random_string('alnum',64);
				set_cookie('kucingcyber',$key,3600*24*30);//set expired 30 hari kedepan
				$value_key = array(
					'cookie'=>$key
				);
				$this->auth->update(array('id' => $req->id),$value_key);
			}
			$this->_create_session($req);
		}else{
			$this->session->set_flashdata('error','Email or Password is invalid');
            $this->index();
		}

	}

	public function _create_session($req){
			$userdata = array(
				'email'=>$req->email,
				'name'=>$req->name,
				'logged'=>TRUE
			);
			$req = $this->auth->update(array('id' => $req->id),array('session' => md5($req->id.date('Y-m-d H:i:s'))));
			$this->session->set_userdata($userdata);
			redirect('user');
	}

	public function logout(){
		delete_cookie('kucingcyber');
		$this->session->sess_destroy();
		redirect('auth');
	}
}
