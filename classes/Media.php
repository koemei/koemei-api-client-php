<?php
/*
 * TODO : get should initialise the media info (e.g. uuid, etc.)
 * TODO : error checking should be done here
 * TODO : outside classes should not be using rest_request or parsing its response
 * TODO : add error_callback_url to transcribe method
 */

class Media extends BaseObject {
	
	public function __construct($rest_request){
		$this->rest_request = $rest_request;
	}
	
	public function create($audioFilename){
		$this->rest_request->audioFilename = $audioFilename;
		$this->rest_request->method = "POST";
		$this->rest_request->path = "media";
		return $this->rest_request->execute();		
	}
	public function delete($uuid){
		/* Not available */
	}
	public function get($uuid){
		$this->rest_request->method = "GET";
		$this->rest_request->path = "media/".$uuid;
		return $this->rest_request->execute();		
	}
	public function get_list(){
		$this->rest_request->method = "GET";
		$this->rest_request->path = "media/";
		return $this->rest_request->execute();
	}
	public function transcribe($transcription_success_callback_url){
		$this->rest_request->method = "POST";
		$this->rest_request->success_callback_url = $transcription_success_callback_url;
		$this->rest_request->path = "media/".$this->uuid."/transcribe";
		return $this->rest_request->execute();		
	}
	public function publish(){
		$this->rest_request->method = "PUT";
		$this->rest_request->path = "media/".$this->uuid."/publish";
		return $this->rest_request->execute();		
	}
} 
?>