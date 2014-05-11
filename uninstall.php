<?php
require_once('SerialRelay.class.php');
$table = new SerialRelay();
$table->drop();

$conf = new Configuration();
$conf->delete(array('key'=>'plugin_serialRelay_port'));

$table_section = new Section();
$id_section = $table_section->load(array("label"=>"serial relais"))->getId();
$table_section->delete(array('label'=>'serial relais'));

$table_right = new Right();
$table_right->delete(array('section'=>$id_section));

?>
