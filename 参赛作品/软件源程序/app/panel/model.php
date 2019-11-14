<?php
class MDL_Panel extends CTL_Panel{

	private $parent;
	function __construct($parent){
		$this->parent = $parent;

	}

	function getView($name){
		return $this->parent->G_getView($this->parent->module_name, $name);
	}

	function deleteUserMessage($id){
		$db = $this->parent->db;
		$id = $id>0 ? $id : 0;
		return $db->query("DELETE FROM user_message WHERE id=$id LIMIT 1");
	}

}
