<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class airZone extends eqLogic {
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */

    
     // Fonction exécutée automatiquement toutes les minutes par Jeedom
	 //Seul 5min est activé par défaut
      public static function cron() {
			airZone::SyncAirzone();
      }
      public static function cron5() {
			airZone::SyncAirzone();
      }
	  public static function cron15() {
			airZone::SyncAirzone();
      }
	


    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave() {
        
    }

    public function preUpdate() {
   
    }

    public function postUpdate() {
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }
	
	public function SyncAirzone() {
		//Lancement de la synchronisation pour tous les systèmes
		$nb = config::byKey('nbSystems', 'airZone');
		if ($nb >0){
			for ($i = 1; $i <= $nb; $i++) {
			airZone::SyncSystem($i);
			}
		}
		else {
			log::add('airZone', 'debug', 'Erreur de paramétrage sur le nombre de système : ' . $nb);
		}
	}
	
	public function Integration() {
		$url = "http://192.168.0.235:3000/api/v1/integration";
		$request = array("driver" => "Jeedom");
		$data_string = json_encode($request);
		
		$options = array(
		    CURLOPT_URL            => $url,
		    CURLOPT_CUSTOMREQUEST => "PUT",
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		    CURLOPT_AUTOREFERER    => true,
		    CURLOPT_CONNECTTIMEOUT => 120,
		    CURLOPT_TIMEOUT        => 120,
		    CURLOPT_MAXREDIRS      => 10,
		);
		curl_setopt_array( $ch, $options );
		$response = curl_exec($ch); 
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ( $httpCode != 200 ){
		    log::add('airZone', 'debug', 'SyncAirzone - Return code is {'.$httpCode.'} '.curl_error($ch));
		} else {
		    log::add('airZone', 'debug', 'SyncAirzone - Return data : {'.htmlspecialchars($response));
		}

		curl_close($ch);
		
		
	}
	
	public function SyncSystem($idSystem) {
		
		airZone::Integration();
		//Config de Prod
		$url = config::byKey('addr', 'airZone');
		$systemID = $idSystem;
		$zoneID = 0;
		$request = array("systemID" => $systemID, "zoneID" => $zoneID);
		$data_string = json_encode($request);
		log::add('airZone', 'debug', 'SyncAirzone ' .$url." Request : chaine ".$data_string);
		$ch = curl_init();

		$options = array(
		    CURLOPT_URL            => $url,
		    CURLOPT_CUSTOMREQUEST => "POST",
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		    CURLOPT_AUTOREFERER    => true,
		    CURLOPT_CONNECTTIMEOUT => 120,
		    CURLOPT_TIMEOUT        => 120,
		    CURLOPT_MAXREDIRS      => 10,
		);
		curl_setopt_array( $ch, $options );
		$response = curl_exec($ch); 
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ( $httpCode != 200 ){
		    log::add('airZone', 'debug', 'SyncAirzone - Return code is {'.$httpCode.'} '.curl_error($ch));
		} else {
		    log::add('airZone', 'debug', 'SyncAirzone - Return data : {'.htmlspecialchars($response));
		}

		curl_close($ch);
		
		//$data = curl -i -X POST -H "Content-Type: application/json" -d "" $url;
	
/*
	//Config de Test
    //$Addr = 'http://' . $this->getConfiguration('addr', '') . ':3000/api/v1/hvac';
	$url = config::byKey('addr', 'airZone');
	log::add('airZone', 'debug', 'SyncAirzone ' . $url);

	//Récupération eqLogics de airZone
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	curl_close($ch);
*/	log::add('airZone', 'debug', "Retour HTTP : ".$httpcode);
	//log::add('airZone', 'debug', "Retour CH : ".json_decode($data));
	log::add('airZone', 'debug', "Retour API : ".$data." json : ".json_decode($data));
	//Récupération eqLogics de jeedom
	$eqLogics = eqLogic::byType('airZone');
    $datas = json_decode($data, true);
    if (json_last_error() == JSON_ERROR_NONE) {
		log::add('airZone', 'debug', "conversion en tableau : ". print_r($datas, true));
		
		//On récupère tout les registres
		foreach ($datas["data"] as $registre) {
			//Pour chaque registre, on test si il existe en base
			log::add('airZone', 'debug', print_r($registre, true));
			$eqRecherche = $registre["systemID"]."-".$registre["zoneID"];
			log::add('airZone', 'debug', "Recherche: " .$eqRecherche);
			
			$found = false;

			foreach ($eqLogics as $eqLogic) {
				
				if ( $eqRecherche == $eqLogic->getConfiguration('deviceID')) {
					$eqLogic_found = $eqLogic;
					$found = true;
					break;
				}
			}
			//On ajoute un eqLogics
			if (!$found) {
				
				//traitement des commandes action
				$eqLogic = new eqLogic();
				$eqLogic->setEqType_name('airZone');
				$eqLogic->setIsEnable(1);
				$eqLogic->setIsVisible(1);
				$eqLogic->setName($eqRecherche);
				$eqLogic->setConfiguration('deviceID', $eqRecherche);
				$eqLogic->setConfiguration('systemID', $registre["systemID"]);
				$eqLogic->setConfiguration('zoneID', $registre["zoneID"]);
				$eqLogic->save();
				$eqLogic = self::byId($eqLogic->getId());
				
				//traitement des commandes action
				//on 
				$airZoneCmd = new airZoneCmd();
				$airZoneCmd->setType('action');
				$airZoneCmd->setSubType('slider');
				$airZoneCmd->setName('set_On');
				$airZoneCmd->setEqLogic_id($eqLogic->getId());
				$airZoneCmd->setConfiguration('commandName', 'set_On');
				$airZoneCmd->setConfiguration('nparams', 1);
				$airZoneCmd->setConfiguration('parameters', '#slider#');
				$airZoneCmd->setConfiguration('minValue', '0');
				$airZoneCmd->setConfiguration('maxValue', '1');
				$airZoneCmd->setDisplay('generic_type', 'FLAP_SLIDER');
				$airZoneCmd->save();
				//setpoint 
				$airZoneCmd = new airZoneCmd();
				$airZoneCmd->setType('action');
				$airZoneCmd->setSubType('slider');
				$airZoneCmd->setName('set_Temp');
				$airZoneCmd->setEqLogic_id($eqLogic->getId());
				$airZoneCmd->setTemplate('dashboard', 'thermostat');
				$airZoneCmd->setTemplate('mobile', 'thermostat');
				$airZoneCmd->setUnite('°C');
				$airZoneCmd->setConfiguration('commandName', 'set_Temp');
				$airZoneCmd->setConfiguration('nparams', 1);
				$airZoneCmd->setConfiguration('parameters', '#slider#');
				$airZoneCmd->setConfiguration('minValue', '15');
				$airZoneCmd->setConfiguration('maxValue', '30');
				$airZoneCmd->save();
				//name 
				$airZoneCmd = new airZoneCmd();
				$airZoneCmd->setType('action');
				$airZoneCmd->setSubType('other');
				$airZoneCmd->setName('set_Name');
				$airZoneCmd->setEqLogic_id($eqLogic->getId());
				$airZoneCmd->setConfiguration('commandName', 'set_Name');
				$airZoneCmd->setConfiguration('parameters', '#message#');
				$airZoneCmd->setConfiguration('nparams', 1);
				$airZoneCmd->setIsVisible('0');
				$airZoneCmd->save();
				
				if (config::byKey('VAF', 'airZone')=="1")
				{
				//coolsetpoint 
				$airZoneCmd = new airZoneCmd();
				$airZoneCmd->setType('action');
				$airZoneCmd->setSubType('slider');
				$airZoneCmd->setName('set_coolTemp');
				$airZoneCmd->setEqLogic_id($eqLogic->getId());
				$airZoneCmd->setTemplate('dashboard', 'thermostat');
				$airZoneCmd->setTemplate('mobile', 'thermostat');
				$airZoneCmd->setUnite('°C');
				$airZoneCmd->setConfiguration('commandName', 'set_coolTemp');
				$airZoneCmd->setConfiguration('nparams', 1);
				$airZoneCmd->setConfiguration('parameters', '#slider#');
				$airZoneCmd->setConfiguration('minValue', '18');
				$airZoneCmd->setConfiguration('maxValue', '30');
				$airZoneCmd->setIsVisible('0');
				$airZoneCmd->save();
				//heatsetpoint 
				$airZoneCmd = new airZoneCmd();
				$airZoneCmd->setType('action');
				$airZoneCmd->setSubType('slider');
				$airZoneCmd->setName('set_headTemp');
				$airZoneCmd->setEqLogic_id($eqLogic->getId());
				$airZoneCmd->setConfiguration('commandName', 'set_headTemp');
				$airZoneCmd->setConfiguration('nparams', 1);
				$airZoneCmd->setTemplate('dashboard', 'thermostat');
				$airZoneCmd->setTemplate('mobile', 'thermostat');
				$airZoneCmd->setUnite('°C');
				$airZoneCmd->setConfiguration('parameters', '#slider#');
				$airZoneCmd->setConfiguration('minValue', '15');
				$airZoneCmd->setConfiguration('maxValue', '30');
				$airZoneCmd->setIsVisible('0');
				$airZoneCmd->save();
				}
				//mode 
				$airZoneCmd = new airZoneCmd();
				$airZoneCmd->setType('action');
				$airZoneCmd->setSubType('select');
				$airZoneCmd->setName('set_mode');
				$airZoneCmd->setEqLogic_id($eqLogic->getId());
				$airZoneCmd->setConfiguration('commandName', 'set_mode');
				$airZoneCmd->setConfiguration('nparams', 1);
				$airZoneCmd->setConfiguration('parameters', '#select#');
				$airZoneCmd->setConfiguration('listValue', '1|STOP;2|CLIMATISATION;3|CHAUFFAGE;4|VENTILATION;5|DESHUMIDIFICATION;7|AUTO');
				$airZoneCmd->save();
				//speed 
				$airZoneCmd = new airZoneCmd();
				$airZoneCmd->setType('action');
				$airZoneCmd->setSubType('slider');
				$airZoneCmd->setName('set_speed');
				$airZoneCmd->setEqLogic_id($eqLogic->getId());
				$airZoneCmd->setConfiguration('commandName', 'set_speed');
				$airZoneCmd->setConfiguration('nparams', 1);
				$airZoneCmd->setConfiguration('parameters', '#slider#');
				$airZoneCmd->setConfiguration('minValue', '0');
				$airZoneCmd->setConfiguration('maxValue', '7');
				$airZoneCmd->save();
				//coldstage 
				$airZoneCmd = new airZoneCmd();
				$airZoneCmd->setType('action');
				$airZoneCmd->setSubType('slider');
				$airZoneCmd->setName('set_coldstage');
				$airZoneCmd->setEqLogic_id($eqLogic->getId());
				$airZoneCmd->setConfiguration('commandName', 'set_coldstage');
				$airZoneCmd->setConfiguration('nparams', 1);
				$airZoneCmd->setConfiguration('parameters', '#slider#');
				$airZoneCmd->setConfiguration('minValue', '1');
				$airZoneCmd->setConfiguration('maxValue', '3');
				$airZoneCmd->setIsVisible('0');
				$airZoneCmd->save();
				//heatstage
				$airZoneCmd = new airZoneCmd();
				$airZoneCmd->setType('action');
				$airZoneCmd->setSubType('slider');
				$airZoneCmd->setName('set_heatstage');
				$airZoneCmd->setEqLogic_id($eqLogic->getId());
				$airZoneCmd->setConfiguration('commandName', 'set_heatstage');
				$airZoneCmd->setConfiguration('nparams', 1);
				$airZoneCmd->setConfiguration('parameters', '#slider#');
				$airZoneCmd->setConfiguration('minValue', '1');
				$airZoneCmd->setConfiguration('maxValue', '3');
				$airZoneCmd->setIsVisible('0');
				$airZoneCmd->save();
				
				//traitement des commandes info
				foreach($registre as $name => $value) {
					$eqLogic->checkCmdOk($eqLogic->getId(), $name);
					$eqLogic->checkAndUpdateCmd($name, $value);
					$linkedCmdName = '';
					switch($name){
						case "on":
							$linkedCmdName = 'set_On';
							break;
						case "setpoint":
							$linkedCmdName = 'set_Temp';
							break;
						case "name":
							$linkedCmdName = 'set_Name';
							break;
						case "coolsetpoint":
							$linkedCmdName = 'set_coolsetpoint';
							break;
						case "heatsetpoint":
							$linkedCmdName = 'set_heatsetpoint';
							break;
						case "mode":
							$linkedCmdName = 'set_mode';
							break;
						case "speed":
							$linkedCmdName = 'set_speed';
							break;
						case "coldstage":
							$linkedCmdName = 'set_coldstage';
							break;
						case "heatstage":
							$linkedCmdName = 'set_heatstage';
							break;
						case "units":
							break;
						case "errors" :
							//Gestion des erreurs et warning à terminer
							$eqLogic->checkAndUpdateCmd($name, json_encode($value));
							//log::add('airZone', 'debug', "Commande Erreur : ".json_encode($value));
							break;
						default:
						$eqLogic->checkAndUpdateCmd($name, $value);
						break;
					}

					if ($linkedCmdName !== '') {
						foreach ($eqLogic->getCmd() as $action) {
							if ($action->getConfiguration('commandName') == $linkedCmdName) {
								$action->setValue($airZoneCmd->getId());
								$action->save();
							}
						}
					}		
					
					// On récupère le nom de la zone pour le 1er ajout
					if($name == "name"){
						$eqLogic->setName($value);
						$eqLogic->save();
					}
				}

				log::add('airZone', 'info', "Ajout de l'EqLogic : ". print_r($eqLogic->getId(), true)." et insertion des commandes terminée");
				
				
			}
			else{
				$eqLogic = $eqLogic_found;
					
				//On mets à jour l'eqLogic
					
				foreach($registre as $name => $value) {
					$eqLogic->checkCmdOk($eqLogic->getId(), $name);
					switch($name)
					{
					case "errors" :
						//Gestion des erreurs et warning à terminer
						$eqLogic->checkAndUpdateCmd($name, json_encode($value));
						//log::add('airZone', 'debug', "Commande Erreur : ".json_encode($value));
						break;
					case "on" :
						log::add('airZone', 'debug', "Commande ON : ".json_encode($value));
						if (json_encode($value)){$eqLogic->checkAndUpdateCmd($name, 1);}else{$eqLogic->checkAndUpdateCmd($name, 0);}
						break;
					default:
						$eqLogic->checkAndUpdateCmd($name, $value);
						break;
					}
				}
				log::add('airZone', 'debug', "Mise à jour  de l'EqLogic : ". print_r($eqLogic->getId(), true));	
			}
		}
		
		
    }
    else{
		log::add('airZone', 'debug', "Error Json : ". json_last_error()); 
		log::add('airZone', 'debug', "Datas : ".$datas);
	    	log::add('airZone', 'debug', "Datas decode : ".json_encode($datas));
		}

	}
  
    public function checkCmdOk($_id_eqlogics, $_name) {
    $airZoneCmd = airZoneCmd::byEqLogicIdAndLogicalId($_id_eqlogics,$_name);
    if (!is_object($airZoneCmd)) {
      log::add('airZone', 'debug', 'Création de la commande ' . $_name);
      $airZoneCmd = new airZoneCmd();
      $airZoneCmd->setName($_name);
      $airZoneCmd->setEqLogic_id($this->getId());
      $airZoneCmd->setEqType('airZone');
      $airZoneCmd->setLogicalId($_name);
      $airZoneCmd->setType('info');
      $airZoneCmd->setSubType('numeric');
      $airZoneCmd->setIsVisible('0');
      $airZoneCmd->setIsHistorized(0);
      $airZoneCmd->setTemplate("mobile",'line' );
      $airZoneCmd->setTemplate("dashboard",'line' );
      //$airZoneCmd->setDisplay('icon', '<i class="fas fa-flash"></i>');
      $airZoneCmd->setConfiguration('type', 'command');
	  
	  switch ($_name) {
		case "SystemID":
			break;
		case "ZoneID":
			break;
		case "name":
			$airZoneCmd->setSubType('string');
			break;
		case "on":
			$airZoneCmd->setSubType('binary');
			$airZoneCmd->setIsHistorized(1);
			break;
		case "setpoint":
			$airZoneCmd->setUnite('°C');
			break;
		case "roomTemp":
			$airZoneCmd->setIsVisible('1');
			$airZoneCmd->setIsHistorized(1);
			$airZoneCmd->setUnite('°C');
			break;
		case "maxTemp":
			$airZoneCmd->setIsHistorized(1);
			break;
		case "minTemp":
			$airZoneCmd->setIsHistorized(1);
			break;
		case "coolsetpoint":
			$airZoneCmd->setUnite('°C');
			break;
		case "coolmaxtemp":
			$airZoneCmd->setUnite('°C');
			break;
		case "coolmintemp":
			$airZoneCmd->setUnite('°C');
			break;
		case "heatsetpoint":
			$airZoneCmd->setUnite('°C');
			break;
		case "heatmaxtemp":
			$airZoneCmd->setUnite('°C');
			break;
		case "heatmintemp":
			$airZoneCmd->setUnite('°C');
			break;
		case "mode":
			$airZoneCmd->setIsVisible('1');
			$airZoneCmd->setIsHistorized(1);
			break;
		case "speed":
			$airZoneCmd->setIsVisible('1');
			$airZoneCmd->setIsHistorized(1);
			break;
		case "coldstage":
			break;
		case "heatstage":
			break;
		case "units":
			break;
		case "errors":
			$airZoneCmd->setSubType('string');
			break;
	  }
	  
      $airZoneCmd->save();
      $airZoneCmd->event(0);
    }
  }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class airZoneCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
     
	 public function dontRemoveCmd() {
      return true;
      }
*/
    public function execute($_options = array()) {
		
	
		$eqLogic = airZone::byId($this->getEqLogic_id());
		log::add('airZone', 'debug', "SystemID : ".$eqLogic->getConfiguration('systemID')." - zoneID : ".$eqLogic->getConfiguration('zoneID'));
		
		$parameters = $this->getConfiguration('parameters');
		
		//par defaut on traite toutes les commandes comme des #slider#
		$value = str_replace('#slider#', $_options['slider'], $parameters);
		log::add('airZone', 'debug', "Commande avant Api : ".$this->getName()." -> ".$value);
		
		switch ($this->getName()) {
		case "set_On":
			$params = "on";
			$eqLogic->checkAndUpdateCmd($params, $value);
			if($_options['slider']=="1"){
				$value = true;
				log::add('airZone', 'debug', "Commande ON Envoyée à l'API : ".$params." -> true");
			}else{
				$value = false;
				log::add('airZone', 'debug', "Commande OFF Envoyée à l'API : ".$params." -> false");
			}
			break;
		case "set_Temp":
			$params = "setpoint";
			$eqLogic->checkAndUpdateCmd($params, $value);
			break;
		case "set_Name":
			$params = "name";
			$value = str_replace('#message#', $_options['message'], $parameters);
				//hack pour forcer select option en integer
			$eqLogic->checkAndUpdateCmd($params, "+".$value);
			log::add('airZone', 'debug', "Commande Name avant Api : ".$this->getName()." -> ".$value);
			break;
		case "set_coolTemp":
			$params = "coolsetpoint";
			$eqLogic->checkAndUpdateCmd($params, $value);
			break;
		case "set_headTemp":
			$params = "heatsetpoint";
			$eqLogic->checkAndUpdateCmd($params, $value);
			break;
		case "set_mode":
			$params = "mode";
			$value = str_replace('#select#', $_options['select'], $parameters);
			$eqLogic->checkAndUpdateCmd($params, $value);
			log::add('airZone', 'debug', "Commande Mode avant Api : ".$this->getName()." -> ".$value);
			break;
		case "set_speed":
			$params = "speed";
			$eqLogic->checkAndUpdateCmd($params, $value);
			break;
		case "set_coldstage":
			$params = "coldstage";
			$eqLogic->checkAndUpdateCmd($params, $value);
			break;
		case "set_heatstage":
			$params = "heatstage";
			$eqLogic->checkAndUpdateCmd($params, $value);
			break;
		}
		
		/*
				foreach ($eqLogic->getCmd() as $command) {
					if ($command->getType() == 'info') {
						if ($command->getName() == $params) {
							//$command->setCollectDate('');
							//$command->event($value);
							//$command->save();
							$eqLogic->checkAndUpdateCmd($params, $value);
							log::add('airZone', 'debug', "Commande  : ".$command->getName()." -> ".$command->getValue());
						}
					}
				}
	    */
	  //Affiche la commande sauf ON qui est déjà traité
	  log::add('airZone', 'debug', "Commande Envoyée à l'API : ".$params." -> ".$value);
	 
	  
	  // Config de prod
		$url = config::byKey('addr', 'airZone');
		$systemID = $eqLogic->getConfiguration('systemID');
		$zoneID = $eqLogic->getConfiguration('zoneID');
		$data = array("systemID" => "$systemID", "zoneID" => "$zoneID", "$params" => "$value");
		$data_string = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

		//execute post
		$result = curl_exec($ch);
		
		log::add('airZone', 'debug', "Retour Api : ".json_decode($result));
		//close connection
		curl_close($ch);
	  
	  
	 return; 
		
    }

    /*     * **********************Getteur Setteur*************************** */
	
	
	
}

?>
