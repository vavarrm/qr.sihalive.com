<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Api extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		$this->load->model('User_Model', 'user');
		$newdata= array('d');
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		$this->response_code = $this->language->load('response');
		$gitignore =array(
			'login',
			'logout'
		);
			
		try 
		{
			$checkUser = $this->myfunc->checkUser($gitignore);
			if($checkUser !="200")
			{
				$array = array(
					'status'	=>$checkAdmin
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
			$this->myfunc->response($output);
			exit;
		}
		
    }
	
	public function getUser($urlRsaRandomKey='')
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='取得登入訊息';
		try 
		{	$get = $this->input->get();
			if(isset($get['sess']))
			{
				$urlRsaRandomKey =$get['sess'];
			}
			$encrypt_user_data = $_SESSION['encrypt_user_data'] ;
			$decrypt_user_data= $this->myfunc->decryptUser($urlRsaRandomKey, $encrypt_user_data);
			
			if(!$decrypt_user_data)
			{
				$array = array(
					'status'	=>'011'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$user_data = $this->user->getUserForId($decrypt_user_data['u_id']);
			$output['body']['user_data'] =$user_data  ;
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
		}
		$this->myfunc->response($output);
	}
	
	public function logout()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='登出';
		try 
		{
			$this->session->unset_userdata('encrypt_user_data');
				
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
		}
		$this->myfunc->response($output);
	}
	
	public function login()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='登入';
		try 
		{
			$account = (isset($this->request['account']))?$this->request['account']:'';
			$password = (isset($this->request['password']))?$this->request['password']:'';

			if(
				$account=="" ||
				$password=="" 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$row = $this->user->getUserByaccount($account);
			if(empty($row))
			{
				$array = array(
					'status'	=>'002'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			if(md5($password )!= $row['u_password'])
			{
				$array = array(
					'status'	=>'003'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$randomKey = $this->token->getRandomKey();
			$rsaRandomKey = $this->token->publicEncrypt($randomKey);
			$data = array(
				'u_id'  =>$row['u_id'],
				'expire'	=>time()+86400*3
			);
			$encrypt_user_data = $this->token->AesEncrypt(serialize($data), $randomKey);
			$this->session->set_userdata('encrypt_user_data', $encrypt_user_data);
			$urlRsaRandomKey = urlencode($rsaRandomKey) ;
			$output['body']['user_sess'] = $urlRsaRandomKey ;
			
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
		}
		$this->myfunc->response($output);
	}

	
	
}
