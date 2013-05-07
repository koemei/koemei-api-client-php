<?php 
abstract class BaseObject {
	public $rest_request;
	abstract public function create($param);
	abstract public function delete($uuid);
	abstract public function get($uuid);
	abstract public function get_list();
}
?>