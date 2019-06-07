<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model{
	private $table;

	function __construct(){
		parent::__construct();
		$this->table = 'tb_users';
	}

	function login($email,$password){
		$this->db->where(array('email'=>$email,'password'=>md5($password)));
		$q = $this->db->get($this->table);
		return $q->row();
	}

	function get_cookie($cookie){
		$this->db->where('cookie',$cookie);
		$q = $this->db->get($this->table);
		return $q->row();
	}

	function find($field,$cond){
		$this->db->select($field);
		$this->db->where($cond);
		$q = $this->db->get($this->table);
		return $q;
	}

	function find_one($email,$pass){
		$q = $this->db->get_where('ap_users',array('email'=>$email,'password'=>md5($pass)));
		return $q->row();
	}

	function update($id,$value){
		$this->db->set($value);
		$this->db->where($id);
		$q = $this->db->update($this->table);
		return $q;
	}
}