<?php
	class TexasholdemInsuranceOdds_Model extends CI_Model 
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
		 
		
		public function batchUpload($ary)
		{
			try
			{
				$this->db->trans_begin();

				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>'no setting ary' ,
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
			
				foreach($ary as $key=>$value)
				{
					$sql =" UPDATE  texasholdem_insurance_odds
							SET odds_value = ?
							WHERE  odds_id =?
							";
					$bind =array(
						$value,
						$key,
					);
					$query = $this->db->query($sql, $bind);
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
						break;
					}
				}

				$this->db->trans_commit();
			}	
			catch(MyException $e)
			{
				 $this->db->trans_rollback();
				throw $e;
			}
		}
		
		public function getOddsList($ary)
		{
			try
			{
				if(empty($ary))
				{
					$MyException = new MyException();
					$array = array(
						'el_system_error' 	=>'no setting ary' ,
						'status'	=>'000'
					);
					
					$MyException->setParams($array);
					throw $MyException;
				}
			
				$sql =" SELECT 
							odds_outs,
							odds_value,
							odds_id
						FROM  texasholdem_insurance_odds
						";
				$bind =array(
					$ary['group_id'],
				);
				$query = $this->db->query($sql, $bind);
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
				$rows = $query->result_array();
				$query->free_result();
				return $rows;
			}	
			catch(MyException $e)
			{
				throw $e;
			}
		}
		
		public function getOddsByOuts($outs, $group_id =1)
		{
			try
			{
			
				$sql =" SELECT 
							odds_value
						FROM texasholdem_insurance_odds 
						WHERE odds_outs =? AND odds_group_id =?";
				$bind =array(
					$outs,
					$group_id
				);
				$query = $this->db->query($sql, $bind);
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
				$row = $query->row_array();
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