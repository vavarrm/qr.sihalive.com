<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MyFunc
{
	private $CI ;
	public function __construct() 
	{
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->load->model('AdminUser_Model', 'admin_user');
	}
	
	public function validateDate($date, $format = 'Y-m-d')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	
	public function readJson($parameter = array())
	{
		$default = array(
			'model'	=>'r'
		);
		$parameter = array_merge($default, $parameter);
		$filename = JSONPATH.$parameter['file_name'];
		if(file_exists($filename))
		{
			
			$file = fopen($filename, $parameter['model']);
			if($file != NULL){
				while (!feof($file)) {
					$str .= fgets($file);
				}
				fclose($file);
			}
		}
		return $str;
	}
	
	public function writeJson($parameter = array())
	{
		$default = array(
			'model'	=>'w'
		);

		$parameter = array_merge($default, $parameter);
		$file = fopen(JSONPATH.$parameter['file_name'], $parameter['model']);
		if($file != NULL){
			fwrite($file, $parameter['str']);
			fclose($file);
		}else{
			throw new Exception('file write error');
		}
	}
	
	public function gotoUrl($url , $msg ='')
	{
		echo "<script>";
		if($msg !="")
		{
			echo sprintf("alert('%s');", $msg );
		}
		
		echo sprintf(" location.href = '%s'" , $url);
		
		echo "</script>";
	}
	
	public function back($num = -1 , $msg ='')
	{
		echo "<script>";
		if($msg !="")
		{
			echo sprintf("alert('%s');", $msg );
		}
		
		echo sprintf("history.go(%s);" , $num);
		
		echo "</script>";
	}
	
	public function getDirAllFile($dir)
	{
		if (is_dir($dir)) 
		{
			if ($dh = opendir($dir)) 
			{
				while (($file = readdir($dh)) !== false) 
				{ 
					if(filetype($dir . $file) =="file")
					{
						
						$subfile = fopen($dir.$file, 'r');
						if($subfile != NULL)
						{
							$str="";
							while (!feof($subfile)) 
							{
								$str .= fgets($subfile);
							}
							fclose($subfile);
						}
						$temp = explode(".", $file);
						$output[$temp[0]] = $str;
					}
				}
				closedir($dh);
			}
		}
		return $output;
	}
	
	public function decryptUser($rsa_randomKey, $encrypt_data)
	{
		$randomKey =  $this->CI->token->privateDecrypt($rsa_randomKey);
		$decrypt_data = $this->CI->token->AesDecrypt($encrypt_data , $randomKey );
						
	
		$data = unserialize($decrypt_data);
		if(empty($data))
		{
			// session_destroy();
		}
		return $data;
	}
	
	public function isLogin()
	{
		$get = $this->CI->input->get();
		$urlRsaRandomKey =($get['sess'] !="")?$get['sess']:'';
		$encrypt_user_data = $_SESSION['encrypt_user_data'] ;
		$decrypt_user_data= $this->decryptUser($urlRsaRandomKey, $encrypt_user_data);
		return $decrypt_user_data;
		
	}
	
	public function getAdminUser($sess)
	{
		$urlRsaRandomKey =$sess;
		$encrypt_data = $this->CI->session->userdata('encrypt_admin_user_data');
		$decrypt_data= $this->decryptUser($urlRsaRandomKey, $encrypt_data);
		// var_dump(	$encrypt_data);
		$data = unserialize($decrypt_data);
		if(empty($data))
		{
			// session_destroy();
		}
		return $decrypt_data;
	}
	
	public function checkAdmin($gitignore)
	{
		
		$get = $this->CI->input->get();
		$default = array(
			"getUser",
		);
		
		$result = array_merge($gitignore, $default);
		if(!in_array($this->CI->uri->segment(2), $result ))
		{
			if($get['sess'] =='')
			{
				return  '002' ;
			}
			
			$admin_data = $this->getAdminUser($get['sess']);
			
			if(empty($admin_data))
			{
				return '018';
			}
			
			$ary = array(
				'ad_id'	=>$admin_data['ad_id'],
				'ar_id'	=>$admin_data['ar_id'],
				'pe_id'	=>$get['pe_id']
			);
			$row = $this->CI->admin_user->getAdminPermissions($ary);
			if(count($row) ==0)
			{
				return '007';
			}
		}
		return '200';
	}
	
	public function getUser($sess)
	{
		$urlRsaRandomKey =$sess;
		$encrypt_data = $this->CI->session->userdata('encrypt_user_data');
		$decrypt_data= $this->decryptUser($urlRsaRandomKey, $encrypt_data);
		return $decrypt_data;
	}
	
	public function checkUser($gitignore)
	{
		
		$get = $this->CI->input->get();
		$default = array(
			"getUser",
		);
		
		$result = array_merge($gitignore, $default);
		if(!in_array($this->CI->uri->segment(2), $result ))
		{
			if($get['sess'] =='')
			{
				return  '000' ;
			}
			
			$user_data = $this->getUser($get['sess']);
			
			if(empty($user_data))
			{
				return '000';
			}
			
			
		}
		return '200';
	}
	
	public function response($output)
	{
		
		$output = array(
			'body'		=>$output['body'],
			'title'		=>$output['title'],
			'status'	=>$output['status'],
			'message'	=>$output['message']
		);
		
		header('Content-Type: application/json');
		echo json_encode($output , true);
	}
}