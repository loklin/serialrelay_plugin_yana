<?php

/*
 @nom: SerialRelay
 @auteur: Yohanndesbois based on Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des pieces
 */

class SerialRelay extends SQLiteEntity{

	protected $id,$name,$description,$radioCode,$room,$pulse;
	protected $TABLE_NAME = 'plugin_serialRelay';
	protected $CLASS_NAME = 'SerialRelay';
	protected $object_fields = 
	array(
		'id'=>'key',
		'name'=>'string',
		'description'=>'string',
		'serialONCommand'=>'int',
		'serialOFFCommand'=>'int',
		'room'=>'int',
	);

	function __construct(){
		parent::__construct();
	}

	function setId($id){
		$this->id = $id;
	}
	
	function getId(){
		return $this->id;
	}

	function getName(){
		return $this->name;
	}

	function setName($name){
		$this->name = $name;
	}

	function getDescription(){
		return $this->description;
	}

	function setDescription($description){
		$this->description = $description;
	}

	function getSerialONCommand(){
		return $this->serialONCommand;
	}

	function setSerialONCommand($serialONCommand){
		$this->serialONCommand = $serialONCommand;
	}

	function getSerialOFFCommand(){
		return $this->serialOFFCommand;
	}

	function setSerialOFFCommand($serialOFFCommand){
		$this->serialOFFCommand = $serialOFFCommand;
	}

	function getRoom(){
		return $this->room;
	}

	function setRoom($room){
		$this->room = $room;
	}


}

?>
