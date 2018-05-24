<?php
/**
 * Created by PhpStorm.
 * User: Sihalive
 * Date: 5/15/2018
 * Time: 12:38 PM
 */

class AdminUserDeliver extends CI_Controller
{
    private $request = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserDelivery_Model', 'delivery');
		$this->load->model('TukTuk_Model', 'tuktuk');
        $this->load->library('session');
        $this->response_code = $this->language->load('admin_response');
        $this->request = json_decode(trim(file_get_contents('php://input'), 'r'), true);
        $this->get = $this->input->get();
        $this->post = $this->input->post();

        $gitignore =array(

        );
        try
        {
            $checkAdmin = $this->myfunc->checkAdmin($gitignore);
            if($checkAdmin !="200")
            {
                $array = array(
                    'status'	=>$checkAdmin
                );
                $MyException = new MyException();
                $MyException->setParams($array);
                throw $MyException;
            }
			$this->admin = $this->myfunc->getAdminUser( $this->get['sess']);
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
	
	public function setFormPage()
	{
		$output['body']=array();
		$output['status'] = '200';
		$output['title'] ='add Form';
		
		try 
		{
			$tuktukList = $this->tuktuk->getOnTukTukList();
			$output['body']['row']['info'] = $tuktukList;
			$output['body']['row']['form'] = array(
				'action'	=> '/Api/'.__CLASS__.'/doSet',
				'pe_id'		=>$this->get['pe_id']
			);
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
	
    public function doSet()
    {
        $output['body']=array();
        $output['status'] = '200';
        $output['status'] = '200';
        $output['title'] ='Set Tuk Tuk';
        $output['message'] ='Set Tuk Tuk';
        $back =-2;
        try
        {
            if(
                empty($this->post['tuktukid']) ||
                empty($this->post['id']) 
            )
            {
                $array = array(
                    'status'	=>'002'
                );
                $MyException = new MyException();
                $MyException->setParams($array);
                throw $MyException;
            }
			$row = $this->delivery->setTukTuk($this->post);
			// if($row['affected_rows'] >0)
			// {
				$delivery =$this->delivery->getRowByID($this->post['id']);
				$to = $delivery['user_id'];
				$push_api_url  ="http://".$_SERVER['HTTP_HOST'].":2121/";
				$post_data = array(
				   "type" => "publish",
				   "action"	=>"TukTukgo",
				   "to" => $to,
				   'content'	=>json_encode($delivery )
				);
				$ch = curl_init ();
				curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
				curl_setopt ( $ch, CURLOPT_POST, 1 );
				curl_setopt ( $ch, CURLOPT_HEADER, 0 );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
				curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
				$return = curl_exec ( $ch );
				curl_close ( $ch );
			// }

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
        $this->myfunc->back($back,$output['message']);

    }

    public function getList($ary=array())
    {
        $output['body']=array();
        $output['status'] = '200';
        $output['title'] ='Restaurant List';
        try
        {
            $ary['limit'] = (isset($this->request['limit']))?$this->request['limit']:10;
            $ary['p'] = (isset($this->request['p']))?$this->request['p']:1;
            $form['inputSearchControl'] = array(

            );
            if(!empty($form['inputSearchControl']))
            {
                foreach($form['inputSearchControl'] as $key => $value)
                {
                    $$key= (isset($this->request[$key]))?$this->request[$key]:'';
                }
            }

			$form['selectSearchControl'] = array(
				'status'	=>array(
					array('value' =>'processing' ,'text'=>'processing'),
					array('value' =>'tuktukgo' ,'text'=>'tuktukgo'),
					array('value' =>'end' ,'text'=>'end'),
				)
			);
            if(!empty($form['selectSearchControl']))
            {
                foreach($form['selectSearchControl'] as $key => $value)
                {
                    $$key= (isset($this->request[$key]))?$this->request[$key]:'';
                }
            }
            $ary['order'] = (empty($this->request['order']))?array("t.id"=>'DESC'):$this->request['order'];

            // $form['datetimeSearchControl'] = true;

            $form['table_add'] = __CLASS__."/add/".__CLASS__.'Add/';
            // $form['table_del'] = "delQr";
            // $form['table_edit'] =  __CLASS__."/editQr/".__CLASS__.'editQr/';
			
			
            $temp=array(
                'pe_id' =>$this->get['pe_id'],
                'ad_id' =>$this->admin['ad_id'],
            );
            $action_list = $this->admin_user->getAdminListAction($temp);


            $ary['fields'] = array(
                'id'				    =>array('field'=>'t.id AS id','AS' =>'id'),
                'qrcode_id'		        =>array('field'=>'t.qrcode_id AS qrcode_id','AS' =>'qrcode_id'),
                'status'		        =>array('field'=>'t.status AS status','AS' =>'status'),

            );
			
			$ary['t.status'] = array(
				'value' =>$status,
				'logic' =>'AND',
				'operator' =>'=',
			);
			
            $list = $this->delivery->getList($ary);
			
            $output['body'] = $list;
            $output['body']['fields'] = $ary['fields'] ;
            $output['body']['subtotal_fields'] = $ary['subtotal'] ;
            $output['body']['form'] =$form;
            $output['body']['action_list'] =$action_list;
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