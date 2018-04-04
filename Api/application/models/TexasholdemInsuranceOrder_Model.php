<?php
	class TexasholdemInsuranceOrder_Model extends CI_Model 
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
		
		
		public function  subTotal()
		{
			
		}
		
		public function checkOut()
		{
			try
			{
				$sql ="	UPDATE texasholdem_insurance_order
						SET checkout_date =NOW() WHERE  checkout_date IS NULL
						";
				$this->db->query($sql);
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
				return $affected_rows;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function updataResult($ary)
		{
			try
			{
				$sql ="	UPDATE texasholdem_insurance_order
						SET result =? , pay_amount =? , complete =1
						WHERE u_id =? AND order_id=? AND order_number =? AND complete = 0
						";
				$bind =array(
					$ary['result'],
					$ary['payamount'],
					$ary['u_id'],
					$ary['order_id'],
					$ary['order_number'],
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
				if($affected_rows==0)
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'004'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function subtotalLastDay()
		{
			try
			{
				$sql =' SELECT 
							SUM((CASE result WHEN "pay" THEN (0-o.pay_amount) ELSE o.buy_amount END )) AS income , min(`add_datetime`) AS startdatetime , DATE_FORMAT(NOW(),"%Y-%m-%d %H:%i:%s") AS enddatetime 
						FROM texasholdem_insurance_order AS o 
						WHERE TIMESTAMPDIFF(SECOND,`add_datetime`,NOW()) <=86400 AND checkout_date IS NOT NULL';
				$query = $this->db->query($sql);
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
				$row  = $query->row_array();
				$query->free_result();
				return $row ;
				
			}catch(MyException $e)
			{
				throw $MyException;
			}
		}
		
		public function getOrderNumber($order_number)
		{
			try
			{
				$sql ="set time_zone = '+7:00';";
				$this->db->query($sql);
				$sql ="
						SELECT 
							CONCAT
							(
								DATE_FORMAT(NOW(),'%y%m%d%H%i'),
								(SELECT LPAD((SELECT IFNULL((SELECT 
										substring(order_number,11,3)
									FROM 
										`texasholdem_insurance_order` 
									WHERE 
										DATE_FORMAT(add_datetime,'%Y%m%d%H') = DATE_FORMAT(NOW(),'%Y%m%d%H') ORDER BY  `order_number` DESC LIMIT 1 
								),0)+1 AS oo),3,0)),
								IF((SELECT COUNT(order_number) FROM  texasholdem_insurance_order WHERE order_number = ?) +1 >1 , 2,1)
							) AS order_number";
				$bind = array($order_number);
				$query = $this->db->query($sql,$bind);
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
				$row =$query->row_array();
				$query->free_result();
				return $row['order_number'];
			}catch(MyException $e)
			{
				
				throw $e;
			}
		}
		
		public function insert($ary)
		{
			$output = array();
			try
			{
				$order_number = $this->getOrderNumber($ary['order_number']);
				$this->db->trans_begin();
				$sql ="	INSERT texasholdem_insurance_order(
							round,
							outs,
							odds,
							pot,
							maximun,
							maximun_p50,
							buy_amount,
							u_id,
							insured_amount,
							order_number,
							players,
							result,
							pay_amount,
							complete
						)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,1)";
				$bind =array(
					$ary['round'],
					$ary['outs'],
					$ary['odds'],
					$ary['pot'],
					$ary['maximun'],
					$ary['maximun_p50'],
					$ary['amount'],
					$ary['u_id'],
					$ary['pay'],
					$order_number ,
					$ary['players'],
					$ary['result'],
					$ary['payamount'],
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
				if($affected_rows==0)
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>$error['message'] ,
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
				$order_id =  $this->db->insert_id();
				
				$this->db->trans_commit();
				$output['order_id'] =$order_id;
				$output['order_number'] =$order_number;
				return $output ;
			}	
			catch(MyException $e)
			{
				$this->db->trans_rollback();
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
				$sql ="	SELECT order_id AS id," 
						.$fields.	
						" FROM
							texasholdem_insurance_order AS t LEFT JOIN user AS u ON t.u_id = u.u_id";
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