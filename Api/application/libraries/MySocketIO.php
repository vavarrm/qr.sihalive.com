<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MySocketIO
{
	private $CI ;
	public function __construct() 
	{
		$this->CI =& get_instance();
	}
	
	public function push($ary)
	{
		$default = array(
			'type'	=>'publish'
		);
		
		$get = array_merge($default, $ary);
		
		if(!empty($get))
		{
			foreach($get as $key=> $value)
			{
				$get_str.=sprintf("%s=%s&", $key, $value);
			}
		}
		
		$url = $_SERVER['SERVER_NAME'].":2121?".$get_str;
 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return  $output;
	}
}