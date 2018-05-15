<?php
	class UserDelivery_Model extends CI_Model 
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

		public function getDeliveryByNew($ary = array())
		{
			$status ='000';
			try
			{
				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$sql ="SELECT *  FROM user_delivery WHERE   status != 'end' AND user_id = ? ORDER BY add_datetime DESC LIMIT 1";
				$bind= array(
					$ary['id'],
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
				$query->free_result();
				return $row;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getCountByStatus($st)
		{
			$status ='000';
			try
			{
				$sql ="SELECT COUNT(*) AS value FROM user_delivery WHERE   status = ?";
				$bind= array(
					$st
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
				$query->free_result();
				return $row;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getRowByUserIDAndEnd($user_id)
		{
			$status ='000';
			try
			{
				$sql ="SELECT * FROM user_delivery WHERE  user_id=? AND status = 'END'";
				$bind= array(
					$user_id
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
				$query->free_result();
				return $row;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function insert($ary)
		{
			$status='000';
			try
			{
				$sql ="SELECT COUNT(*) AS c FROM user_delivery WHERE status='end'";
				$bind= array(
					$ary['qrcode_id'],
					$ary['user_id']
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
				$query->free_result();
				
				if($row['c'] >= $ary['max'])
				{
					$status ='013';
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				
				$sql ="
							SELECT min(t.m) AS m FROM (SELECT timestampdiff(minute,`add_datetime`,NOW()) AS m 
						FROM 
							`user_delivery`
						WHERE 
							qrcode_id = ?  
							AND user_id =?
							AND status !='end'
						) AS t";
				$bind= array(
					$ary['qrcode_id'],
					$ary['user_id']
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
				$query->free_result();
				if($row['m'] <=1 && $row['m'] != null)
				{
					$status ='014';
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				
				$sql ="INSERT INTO user_delivery(qrcode_id,user_id) VALUES(?,?)";
				$bind= array(
					$ary['qrcode_id'],
					$ary['user_id']
				);
				$query = $this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					if($error['code'] =='1062')
					{
						$status ='002';
					}
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$affected_rows = $this->db->affected_rows();
				$output['affected_rows']  = $affected_rows;
				return $output;
			}catch(MyException $e)
			{
				throw $e;
			}
		}
	}
?>