<?php
use RestRequest;

class Process extends BaseObject {
	
	public function __construct($rest_request){
		$this->rest_request = $rest_request;
	}
	
	public function create(){
		/* Not available */
	}
	public function delete($uuid){
		/* Not available */
	}
	public function get($uuid){
		$this->rest_request->method = "GET";
		$this->rest_request->path = "process/"+$uuid;
		return $this->rest_request->execute();		
	}
	public function get_list(){
		$this->rest_request->method = "GET";
		$this->rest_request->path = "process/";
		return $this->rest_request->execute();
	}
} 
?>