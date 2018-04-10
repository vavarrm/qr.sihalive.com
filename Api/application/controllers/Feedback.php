<?php

class Feedback extends CI_Controller{
    public function __construct()
    {
       parent::__construct();
       $this->load->model('Feedback_Model','back');
    }
    public function doInsert(){
        $data=array(
            'fb_title' =>$this->input->post('title'),
            'fb_tuktu_id'  =>$this->input->post('tuktuk'),
            'fb_message'=>$this->input->post('message'),
        );
        if($data){
            $result=$this->back->insert($data);
            $d['json'] = $result;
            echo json_encode($d);
        }else{

        }
    }
}