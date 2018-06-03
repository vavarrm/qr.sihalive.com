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

		public function getTukTukOrder($id)
		{
			$status ='000';
			try
			{
				$sql ="	SELECT 
							ud.*,
							qr.lat,
							qr.lng,
							u.phone
						FROM 
							user_delivery AS ud  
								INNER JOIN qrcode AS qr ON ud.qrcode_id = qr.id
								INNER JOIN user AS u ON u.id = ud.user_id
						WHERE  ud.tuktuk_id=? 
						AND ud.status ='calltuktuk'
						ORDER BY ud.`add_datetime` DESC LIMIT 1";
				$bind= array(
					$id
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
		
		public function getRowByID($id)
		{
			$status ='000';
			try
			{
				$sql ="	SELECT 
							ud.*,
							tt.phone,
							tt.id AS tuktuk_id
						FROM 
							user_delivery AS ud LEFT JOIN  tuktuk AS tt ON ud.tuktuk_id = tt.id 
						WHERE  ud.id=? ";
				$bind= array(
					$id
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
		
		public function setTukTuk($ary)
		{
			$output = array();
			try
			{
				$sql =" UPDATE `user_delivery` SET `tuktuk_id` = ? , `status` = 'calltuktuk' WHERE `user_delivery`.`id` = ?";
				$bind =array(
					$ary['tuktukid'],
					$ary['id'],
				);

				$this->db->query($sql, $bind);
				$error = $this->db->error();
				if($error['message'] !="")
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'000'
					);

					$MyException->setParams($array);
					throw $MyException;
				}

				$affected_rows = $this->db->affected_rows();
				$output['affected_rows'] = $affected_rows;

				return $output ;
			}
			catch(MyException $e)
			{
				$this->db->trans_rollback();
				throw $e;
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
				$sql ="	SELECT 
							ud.*,
							tt.phone,
							tt.id AS tuktuk_id
						FROM 
							user_delivery as ud LEFT JOIN  tuktuk AS tt ON ud.tuktuk_id = tt.id
						WHERE   ud.status != 'end' AND user_id = ? ORDER BY add_datetime DESC LIMIT 1";
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
		
		public function getTicketById($id)
		{
			$status ='000';
			try
			{
				$sql ="SELECT ticket FROM user WHERE id =?";
				$bind= array(
					$id
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
		
		public function confirm($ary)
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
				
				$sql ="SELECT status FROM user_delivery WHERE  id=? ";
				$bind= array(
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
				$row = $query->row_array();
				$query->free_result();
				$sql ="UPDATE user_delivery SET status =? WHERE id = ?";
				if($row['status'] =='complete' && $ary['status'] == 'complete')
				{
					$status ='007';
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
				if($row['status'] =='complete' && $ary['status'] == 'cancel')
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
				
				if($row['status'] !='tuktukarrival' && $ary['status'] == 'complete')
				{
					$status ='006';
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>$status
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				
			
				
				if($ary['status'] == 'complete')
				{
					$sql ="UPDATE user_delivery SET status =? WHERE id = ?";
					$bind = array(
						$ary['status'],
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
					$affected_rows = $this->db->affected_rows();
					$output['affected_rows']  = $affected_rows;
					
					$sql ="UPDATE user SET ticket = ticket-1 WHERE id =?";
					$bind = array(
						$row['user_id'],
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
					
					
				}elseif($ary['status'] == 'cancel')
				{
					
					$sql ="UPDATE user_delivery SET status =? ,tuktuk_id =NULL WHERE id = ?";
					$bind = array(
						$ary['status'],
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
					$affected_rows = $this->db->affected_rows();
					$output['affected_rows']  = $affected_rows;
				}
				
				
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
				$Ticket = $this->getTicketById($ary['user_id']);
				if($Ticket['ticket'] <=0)
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
        public function getList($ary)
        {

            try
            {
                if(empty($ary))
                {
                    $MyException = new MyException();
                    $array = array(
                        'el_system_error' 	=>'no setParams' ,
                        'status'	=>'000'
                    );
                    $MyException->setParams($array);
                    throw $MyException;
                }
                if(!empty($ary['fields']))
                {
                    foreach($ary['fields'] as $value)
                    {
                        $temp[] = $value['field'];
                    }
                }
                $fields = join(',' ,$temp);

                $sql ="	SELECT "
                    . $fields.
                    " FROM user_delivery AS t 
						LEFT JOIN tuktuk AS tk ON t.tuktuk_id = tk.id
						INNER JOIN user AS u ON u.id = t.user_id";

                $ary['sql'] =$sql;
                $output = $this->getListFromat($ary);

                return 	$output  ;
            }catch(MyException $e)
            {
                throw $e;
            }
        }
	}
?>