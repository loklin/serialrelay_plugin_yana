<?php
/*
@name Serial Relay
@author Yohanndesbois based on Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc s
@version 1.0.0
@description Serial Relay plugin
*/

include('SerialRelay.class.php');



function serialRelay_plugin_setting_page(){
	global $_,$myUser,$conf;
	if(isset($_['section']) && $_['section']=='serialRelay' ){

		if($myUser!=false){
			$serialRelayManager = new SerialRelay();
			$serialRelays = $serialRelayManager->populate();
			$roomManager = new Room();
			$rooms = $roomManager->populate();

			//Si on est en mode modification
			if (isset($_['id'])){
				$id_mod = $_['id'];
				$selected = $serialRelayManager->getById($id_mod);
				$description = $selected->GetName();
				$button = "Modifier";
			}
			//Si on est en mode ajout
			else
			{
				$description =  "Ajout d'un relais";
				$button = "Ajouter";
			}
			?>

			<div class="span9 userBloc">


				<h1>Relais</h1>
				<p>Gestion des relais en série</p>  
				<form action="action.php?action=serialRelay_add_serialRelay" method="POST">
					<fieldset>
						<legend><?php  echo $description ?></legend>

						<div class="left">
							<label for="nameSerialRelay">Nom</label>
							<?php  if(isset($selected)){echo '<input type="hidden" name="id" value="'.$id_mod.'">';} ?>
							<input type="text" id="nameSerialRelay" value="<?php  if(isset($selected)){echo $selected->getName();} ?>" onkeyup="$('#vocalCommand').html($(this).val());" name="nameSerialRelay" placeholder="Lumiere Canapé…"/>
							<small>Commande vocale associée : "<?php echo $conf->get('VOCAL_ENTITY_NAME'); ?>, allume <span id="vocalCommand"></span>"</small>
							<label for="descriptionSerialRelay">Description</label>
							<input type="text" value="<?php if(isset($selected)){echo $selected->getDescription();} ?>" name="descriptionSerialRelay" id="descriptionSerialRelay" placeholder="Relais sous le canapé…" />
							<label for="serialONCommandSerialRelay">Commande série ON</label>
							<input type="text" value="<?php if(isset($selected)){echo $selected->getSerialONCommand();} ?>" name="serialONCommandSerialRelay" id="serialONCommandSerialRelay" placeholder="0,1,2…" />
							<label for="serialOFFCommandSerialRelay">Commande série OFF</label>
							<input type="text" value="<?php if(isset($selected)){echo $selected->getSerialOFFCommand();} ?>" name="serialOFFCommandSerialRelay" id="serialOFFCommandSerialRelay" placeholder="0,1,2…" />
							<label for="roomSerialRelay">Pièce</label>
							<select name="roomSerialRelay" id="roomSerialRelay">
								<?php foreach($rooms as $room){ 
									if (isset($selected)){$selected_room = ($selected->getRoom());
									}else if(isset($_['room'])){
										$selected_room = $_['room'];
									}else{
										$selected_room = null;
									}			    		
									?>

									<option <?php  if ($selected_room == $room->getId()){echo "selected";} ?> value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
									<?php } ?>
								</select>
							
			    
							</div>

							<div class="clear"></div>
							<br/><button type="submit" class="btn"><?php  echo $button; ?></button>
						</fieldset>
						<br/>
					</form>

					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Nom</th>
								<th>Description</th>
								<th>Commande Serie On</th>
								<th>Commande Serie Off</th>
								<th>Pièce</th>
								<th></th>
							</tr>
						</thead>

						<?php foreach($serialRelays as $serialRelay){ 

							$room = $roomManager->load(array('id'=>$serialRelay->getRoom())); 
							?>
							<tr>
								<td><?php echo $serialRelay->getName(); ?></td>
								<td><?php echo $serialRelay->getDescription(); ?></td>
								<td><?php echo $serialRelay->getSerialONCommand(); ?></td>
								<td><?php echo $serialRelay->getSerialOFFCommand(); ?></td>
								<td><?php echo $room->getName(); ?></td>
								<td><a class="btn" href="action.php?action=serialRelay_delete_serialRelay&id=<?php echo $serialRelay->getId(); ?>"><i class="icon-remove"></i></a>
									<a class="btn" href="setting.php?section=serialRelay&id=<?php echo $serialRelay->getId(); ?>"><i class="icon-edit"></i></a></td>
								</tr>
								<?php } ?>
							</table>
						</div>

						<?php }else{ ?>

						<div id="main" class="wrapper clearfix">
							<article>
								<h3>Vous devez être connecté</h3>
							</article>
						</div>
						<?php
					}
				}

			}

			function serialRelay_plugin_setting_menu(){
				global $_;
				echo '<li '.(isset($_['section']) && $_['section']=='serialRelay'?'class="active"':'').'><a href="setting.php?section=serialRelay"><i class="icon-chevron-right"></i> Relais serie</a></li>';
			}




			function serialRelay_display($room){
				global $_;


				$serialRelayManager = new SerialRelay();
				$serialRelays = $serialRelayManager->loadAll(array('room'=>$room->getId()));

				if(count($serialRelays)>0){
				foreach ($serialRelays as $serialRelay) {



//Couleur aleatoire des blocs TODO donner de choix dans la config des pieces entre une couleur choisi ou aleatoire.

$array_background = array("violet-color","red-color","blue-color","green-color","orange-color","black-color","pink-color","brown-color","apple-color");
$rand = array_rand($array_background);
$background_rand = $array_background[$rand];

					?>

					<div class="span3">
					<div class="flatBloc <?php echo $background_rand; ?>" style="max-width:100%;">
						<h3><?php echo $serialRelay->getName() ?></h3>	
						<p><?php echo $serialRelay->getDescription(), str_repeat('&nbsp;', 1); ?>
						</p><ul>
						<li>Code ON : <code><?php echo $serialRelay->getSerialONCommand() ?></code></li>
						<li>Code OFF: <code><?php echo $serialRelay->getSerialOFFCommand() ?></code></li>
						<!--<li>Type : <span>Relais serie</span></li>-->
						<li>Emplacement : <span><?php echo $room->getName() ?></span></li>
					</ul>

					
					<a class="flatBloc" title="Activer le relais" href="action.php?action=serialRelay_change_state&engine=<?php echo $serialRelay->getId() ?>&amp;code=<?php echo $serialRelay->getSerialONCommand() ?>&amp;state=on"><i class="icon-thumbs-up"></i></a>
					
						<a class="flatBloc" title="Désactiver le relais" href="action.php?action=serialRelay_change_state&engine=<?php echo $serialRelay->getId() ?>&amp;code=<?php echo $serialRelay->getSerialOFFCommand() ?>&amp;state=off"><i class="icon-thumbs-down "></i></a>
					
					
				</div>
			
</div>

				<?php
			}
}else{
	?>Aucun relais serie ajouté dans la piece <code><?php echo $room->getName() ?></code>, <a href="setting.php?section=serialRelay&amp;room=<?php echo $room->getId(); ?>">ajouter un relais serie ?</a><?php
}


		}

		function serialRelay_vocal_command(&$response,$actionUrl){
			global $conf;
			$serialRelayManager = new SerialRelay();

			$serialRelays = $serialRelayManager->populate();
			foreach($serialRelays as $serialRelay){
				$response['commands'][] = array('command'=>$conf->get('VOCAL_ENTITY_NAME').', allume '.$serialRelay->getName(),'url'=>$actionUrl.'?action=serialRelay_change_state&engine='.$serialRelay->getId().'&state=on&webservice=true','confidence'=>'0.9');
				$response['commands'][] = array('command'=>$conf->get('VOCAL_ENTITY_NAME').', eteint '.$serialRelay->getName(),'url'=>$actionUrl.'?action=serialRelay_change_state&engine='.$serialRelay->getId().'&state=off&webservice=true','confidence'=>'0.9');
			}
		}

		function serialRelay_action_serialRelay(){
			global $_,$conf,$myUser;

	//Mise à jour des droits
			$myUser->loadRight();

			switch($_['action']){
				case 'serialRelay_delete_serialRelay':
				if($myUser->can('serial relais','d')){
					$serialRelayManager = new SerialRelay();
					$serialRelayManager->delete(array('id'=>$_['id']));
					header('location:setting.php?section=serialRelay');
				}
				else
				{
					header('location:setting.php?section=serialRelay&error=Vous n\'avez pas le droit de faire ça!');
				}

				break;
				case 'serialRelay_plugin_setting':
				$conf->put('plugin_serialRelay_port',$_['port']);
				header('location: setting.php?section=preference&block=serialRelay');
				break;

				case 'serialRelay_add_serialRelay':

				//Vérifie si on veut modifier ou ajouter un relai
				$right_toverify = isset($_['id']) ? 'u' : 'c';

				if($myUser->can('serial relais',$right_toverify)){
					$serialRelay = new SerialRelay();
					//Si modification on charge la ligne au lieu de la créer
					if ($right_toverify == "u"){$serialRelay = $serialRelay->load(array("id"=>$_['id']));}
					$serialRelay->setName($_['nameSerialRelay']);
					$serialRelay->setDescription($_['descriptionSerialRelay']);
					$serialRelay->setSerialONCommand($_['serialONCommandSerialRelay']);
					$serialRelay->setSerialOFFCommand($_['serialOFFCommandSerialRelay']);
					$serialRelay->setRoom($_['roomSerialRelay']);
					$serialRelay->save();
					header('location:setting.php?section=serialRelay');
				}
				else
				{
					header('location:setting.php?section=serialRelay&error=Vous n\'avez pas le droit de faire ça!');
				}


				break;

				case 'serialRelay_change_state':
				global $_,$myUser;

				$port = $_['port'];
				
				if($myUser->can('serial relais','u')){

					$serialRelay = new SerialRelay();
					$serialRelay = $serialRelay->getById($_['engine']);
					Event::emit('relay_change_state',array('relay'=>$serialRelay,'state'=>$_['state']));


					


					if($_['state'] == 'on'){
	$portserie = $conf->get('plugin_serialRelay_port');
	$commandserie = $serialRelay->getSerialONCommand();
        $fp =fopen($portserie, "w");
        $cmd = fwrite($fp, $commandserie) . fclose($fp);
        
}else{
	$portserie = $conf->get('plugin_serialRelay_port');
	$commandserie = $serialRelay->getSerialOFFCommand();
        $fp =fopen($portserie, "w");
        $cmd = fwrite($fp, $commandserie) . fclose($fp);

}





				//TODO ask state from serial then change bdd state
					system($cmd, $out);
					if(!isset($_['webservice'])){
						header('location:index.php?module=room&id='.$serialRelay->getRoom());
					}else{
						$affirmations = array(	'A vos ordres!',
							'Bien!',
							'Oui commandant!',
							'Avec plaisir!',
							'J\'aime vous obéir!',
							'Avec plaisir!',
							'Certainement!',
							'Je fais ça sans tarder!',
							'Avec plaisir!',
							'Oui chef!');
						$affirmation = $affirmations[rand(0,count($affirmations)-1)];
						$response = array('responses'=>array(
							array('type'=>'talk','sentence'=>$affirmation)
							)
						);

						$json = json_encode($response);
						echo ($json=='[]'?'{}':$json);
					}
				}else{
					$response = array('responses'=>array(
						array('type'=>'talk','sentence'=>'Je ne vous connais pas, je refuse de faire ça!')
						)
					);
					echo json_encode($response);
				}
				break;
			}
		}


		function serialRelay_plugin_preference_menu(){
			global $_;
			echo '<li '.(@$_['block']=='serialRelay'?'class="active"':'').'><a  href="setting.php?section=preference&block=serialRelay"><i class="icon-chevron-right"></i> Relais Serie </a></li>';
		}
		function serialRelay_plugin_preference_page(){
			global $myUser,$_,$conf;
			if((isset($_['section']) && $_['section']=='preference' && @$_['block']=='serialRelay' )  ){
				if($myUser!=false){
					?>

					<div class="span9 userBloc">
						<form class="form-inline" action="action.php?action=serialRelay_plugin_setting" method="POST">

							<p>Port série du raspberry PI branché à l'Arduino (ou autre): </p>
							<input type="text" class="input-large" name="port" value="<?php echo $conf->get('plugin_serialRelay_port');?>" placeholder="Port serie...">

						

							<button type="submit" class="btn">Enregistrer</button>
						</form>
					</div>

					<?php }else{ ?>

					<div id="main" class="wrapper clearfix">
						<article>
							<h3>Vous devez être connecté</h3>
						</article>
					</div>
					<?php

				}
			}
		}


		Plugin::addHook("preference_menu", "serialRelay_plugin_preference_menu"); 
		Plugin::addHook("preference_content", "serialRelay_plugin_preference_page"); 


		Plugin::addHook("action_post_case", "serialRelay_action_serialRelay"); 

		Plugin::addHook("node_display", "serialRelay_display");   
		Plugin::addHook("setting_bloc", "serialRelay_plugin_setting_page");
		Plugin::addHook("setting_menu", "serialRelay_plugin_setting_menu");  
		Plugin::addHook("vocal_command", "serialRelay_vocal_command");

		//Anonnce que le plugin propose un évenement à l'application lors du changement d'etat (cf Event::emit('relay_change_state') dans le code )
		Event::announce('relay_change_state', 'Changement de l\'état d\'un relais serie',array('code serie'=>'int','etat'=>'string'));

		?>
