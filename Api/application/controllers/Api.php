<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Api extends CI_Controller {
	
	private $request = array();
	
	public function __construct() 
	{
		parent::__construct();	
		
		$this->load->model('User_Model', 'user');
		$this->load->model('UserVerifycode_Model', 'userVC');
		$this->load->model('UserDelivery_Model', 'delivery');
		$this->load->model('Qrcode_Model', 'qrcode');
		$this->load->library('session');
		
		$this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
		$this->get = $this->input->get();
		$this->post = $this->input->post();
		$this->response_code = $this->language->load('response');
		 
		$this->user_sess = $this->session->userdata('user_sess');
		
		$gitignore =array(
			'login',
			'logout',
			'register',
			'registerForm'
		);

		try 
		{
			
			$checkUser = $this->myfunc->checkUser($gitignore, $this->user_sess );
			
			if($checkUser !="200")
			{
				$array = array(
					'status'	=>$checkUser
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			

		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['message'] = $parames['message']; 
			$output['status'] = $parames['status']; 
			$this->myLog->error_log($parames);
			$this->myfunc->response($output);
			exit;
		}
		
    }

	
	public function calltuktuk()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='';
		try 
		{
			
			if(
				$this->request['id'] ==''
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$ary = array(
				'qrcode_id' =>$this->request['id'] ,
				'user_id' =>$this->user_sess['id'],
				'max'		=>1
			);
			$row = $this->delivery->insert($ary);
			if($row['affected_rows'] == 0)
			{
				$array = array(
					'status'	=>'012'
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
			$output['status'] = $parames['status']; 
			$output['message'] = $parames['message']; 
			$this->myLog->error_log($parames);
		}
		
		$this->myfunc->response($output);
	}
	
	public function resendVerifyCode()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='';
		try 
		{
			
			$row = $this->user_sess;
			$row['ip'] =  $this->myfunc->getIP();
			$VerifyCode  = $this->userVC->getVerifyCode($row);
			$row['VerifyCode'] = $VerifyCode['code'];
			$code = $this->userVC->insert($row);
			
			$ary = array(
				'smstext' 	=>sprintf('This is your VerifyCode',$row['VerifyCode']),
				'gsm'		=>$this->user_sess['phone']
			);
			$sendSms = $this->myfunc->sendSms($ary);
			$sendSms.="[Success]";
			if(!strpos($sendSms,"[Success]"))
			{
				$array = array(
					'status'	=>'005'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$output['body'] = $code ;
			$output['message'] =  $this->response_code['203']; 

		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['status'] = $parames['status']; 
			$output['message'] = $parames['message']; 
			$this->myLog->error_log($parames);
		}
		
		$this->myfunc->response($output);
	}
	
	public function verifyCode()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='';
		try 
		{
			if(
				$this->request['vid'] =='' ||
				$this->request['verify_code'] =='' 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$this->request['id'] =$this->user_sess['id'];
			$verifyCode = $this->userVC->checkVerifyCode($this->request);
			$output['message'] =  $this->response_code['202']; 
			
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['status'] = $parames['status']; 
			$output['message'] = $parames['message']; 
			$this->myLog->error_log($parames);
		}
		
		$this->myfunc->response($output);
	}
	
	public function registerForm()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='';
		try 
		{
		
			if(
				$this->request['id'] ==''
			)
			{
				$array = array(
					'status'	=>'011'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			$row = $this->qrcode->getRow($this->request['id']);
			if(empty($row))
			{
				$array = array(
					'status'	=>'011'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$output['body'] = $row;
			
			
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['status'] = $parames['status']; 
			$output['message'] = $parames['message']; 
			$this->myLog->error_log($parames);
		}
		
		$this->myfunc->response($output);
	}
	
	public function register()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='註冊';
		try 
		{
			if(empty($this->request))
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
		
			if(
				empty($this->request['fname']) ||
				empty($this->request['lname']) ||
				empty($this->request['phone']) ||
				empty($this->request['password']) ||
				empty($this->request['c_password']) 
			)
			{
				$array = array(
					'status'	=>'001'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			
			if(!preg_match('/^[A-Za-z0-9]{8,12}$/', $this->request['password'])){
				$array = array(
					'status'	=>'010'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			
			if(
				!preg_match("/^[0-9]{8,9}$/", $this->request['phone'])
			)
			{
				$array = array(
					'status'	=>'013'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
		
			$phoneNumberOne = substr($this->request['phone'] ,0,1 );
			if($phoneNumberOne==0)
			{
				$this->request['phone'] = substr($this->request['phone'] ,1,8 );
			}
			$this->request['phone'] = "855".$this->request['phone'];
			$ip = $this->myfunc->getIP();
		
			$ary = array(
				'phone' =>$this->request['phone'],
				'ip'	=>$ip
			);
			$VerifyCode  = $this->userVC->getVerifyCode($ary);
			$row = $this->user->register($this->request);

			if($row['affected_rows'] <0)
			{
				$array = array(
					'status'	=>'003'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$row['user']['ip'] = $ip;
			$this->session->set_userdata('user_sess', $row['user']);
			
			$ary = array(
				'smstext' 	=>sprintf('This is your VerifyCode',$VerifyCode),
				'gsm'		=>$this->request['phone']
			);
			$sendSms = $this->myfunc->sendSms($ary);
			$sendSms.="[Success]";
			if(!strpos($sendSms,"[Success]"))
			{
				$array = array(
					'status'	=>'005'
				);
				$MyException = new MyException();
				$MyException->setParams($array);
				throw $MyException;
			}
			$row['user']['VerifyCode'] = $VerifyCode['code'] ;
			$code = $this->userVC->insert($row['user']);
			
			$output['body']['user'] = array(
				'id'			=>$row['user']['id'],
				'vid'			=>$code['id'],
			);
			$output['message'] =$this->response_code['201'];
		}catch(MyException $e)
		{
			$parames = $e->getParams();
			$parames['class'] = __CLASS__;
			$parames['function'] = __function__;
			$parames['message'] =  $this->response_code[$parames['status']]; 
			$output['status'] = $parames['status']; 
			$output['message'] = $parames['message']; 
			$this->myLog->error_log($parames);
		}
		
		$this->myfunc->response($output);
	}
	
	/*
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

	*/
	
}
