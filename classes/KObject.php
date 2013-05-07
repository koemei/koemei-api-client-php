<?php
use RestRequest;

class KObject extends BaseObject {
	
	public function __construct($rest_request){
		$this->rest_request = $rest_request;
	}
	
	public function create(){
		$this->rest_request->method = "POST";
		$this->rest_request->path = "kobjects";
		$this->rest_request->execute();
	}
	public function delete($uuid){
		$this->rest_request->method = "DELETE";
		$this->rest_request->path = "kobjects/"+$uuid;
		$this->rest_request->execute();
	}
	public function get($uuid){
		$this->rest_request->method = "GET";
		$this->rest_request->path = "kobjects/"+$uuid;
		return $this->rest_request->execute();		
	}
	public function get_list(){
		$this->rest_request->method = "GET";
		$this->rest_request->path = "kobjects/";
		return $this->rest_request->execute();
	}
} 
?>