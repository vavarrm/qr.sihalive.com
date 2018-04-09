<?php
	class UserVerifyCode_Model extends CI_Model 
	{
		function __construct()
		{
			
			parent::__construct();
			$this->load->database();
			$query = $this->db->query("set time_zone = '+7:00'");
			$error = $this->db->error();
			if($error['message'] !="")
			{
				$MyException = new MyException();
				$array = array(
					'el_system_error' 	=>"set time_zone error" ,
					'status'	=>'000'
				);
				
				$MyException->setParams($array);
				throw $MyException;
			}
		}
		
		
		public function insert($ary)
		{
			$status ='000';
			try
			{
				$sql ="INSERT INTO user_verifycode (user_id,code,ip,phone) VALUES(?,?,?,?)";
				$bind= array(
					$ary['id'],
					$ary['VerifyCode'],
					$ary['ip'],
					$ary['phone']
				);
				
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$row['id'] = $this->db->insert_id();
				return $row;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function checkVerifyCode($ary)
		{
			$this->db->trans_begin();
			$status ='000';
			try
			{
				$sql ="SELECT id FROM user WHERE id=?";
				$bind= array(
					$ary['id'],
				);
				
				$query = $this->db->query($sql, $bind);
				$row = $query->row_array();
				$query->free_result();
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					$MyException->setParams($array);
					throw $MyException;
				}
				
				if(empty($row))
				{
					$status ='006';
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$status ,
						'status'	=>$status
					);
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$sql ="UPDATE user_verifycode SET used ='1' WHERE id =? AND user_id =? AND code=?";
				$bind =array(
					$ary['vid'],
					$ary['id'],
					$ary['verify_code'],
				);
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					$MyException->setParams($array);
					throw $MyException;
				}
				$row['affected_rows'] += $this->db->affected_rows();
				
				if($row['affected_rows'] ==0)
				{
					$status ='008';
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					$MyException->setParams($array);
					throw $MyException;
				}
				
				
				$sql ="UPDATE user SET status ='verify' WHERE id =? ";
				$bind =array(
					$ary['id']
				);
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					$MyException->setParams($array);
					throw $MyException;
				}
				
				$row['affected_rows'] += $this->db->affected_rows();
				
				
				$this->db->trans_commit();
				return $row;
			}	
			catch(MyException $e)
			{
				$this->db->trans_rollback();
				throw $e;
			}
		}
		
		public function getVerifyCode($ary)
		{
			$status ='000';
			try
			{
				$sql ="
					SELECT MIN(t.m) AS m , count(phone) AS c FROM (
						SELECT IFNULL(timestampdiff(minute,`add_datetime`,NOW()),0) AS m ,
						phone
					FROM 
						`user_verifycode`
					WHERE 
						phone = ?  
						OR ip =?
					) AS t";
				$bind= array(
					$ary['phone'],
					$ary['ip'],
				);
				
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$row = $query->row_array();
				if($row['m'] <0)
				{
					$status ='004';
					$array = array(
						'status'			=>$status,
						'el_system_error' 	=>$status ,
					);
					$MyException = new MyException();
					$MyException->setParams($array);
					throw $MyException;
				}
				
				
				$sql ="
					SELECT 
						COUNT(phone) AS c
					FROM 
						`user_verifycode`
					WHERE 
						phone = ?  
					 ";
				$bind= array(
					$ary['phone']
				);
				
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$row = $query->row_array();
				if($row['c'] >3)
				{
					$status ='007';
					$array = array(
						'status'			=>$status,
						'el_system_error' 	=>$status ,
					);
					$MyException = new MyException();
					$MyException->setParams($array);
					throw $MyException;
				}
				$row['code'] = rand(123456,999999);
				
				$query->free_result();
				return $row;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		
	}
?>