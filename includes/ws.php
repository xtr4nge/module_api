<?php
/*
    Copyright (C) 2013-2016 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class WebService {
	
	private $global_webserver;
	private $s;
	private $username;
	private $password;
	private $token;
	
	public function __construct($token)
	{
		
		//echo 'The class "', __CLASS__, '" was initiated!<br />';
		
		// Include Requests library
		include('Requests/Requests.php');
	
		// load Requests internal classes
		Requests::register_autoloader();
		
		$this->global_webserver = "http://localhost:8000";
		
		// Set up session
		$this->s = new Requests_Session($this->global_webserver);
		$this->s->headers['Accept'] = 'application/json';
		$this->s->useragent = 'RESTful API [FruityWiFi]';
		
		// Set up login user/pass | token
		$this->token = $token;
		
	}
	
	public function __destruct()
	{
		//echo 'The class "', __CLASS__, '" was destroyed.<br />';
	}
	
	public function login()
	{
		$options = array('action' => 'login', 'token' => $this->token);

		$this->s->post($this->global_webserver."/login.php", array(), $options);
		$this->loginCheck();
	}
 
	public function loginCheck()
	{
		$response = $this->s->get($this->global_webserver."/login_check.php");
		
		if (strlen($response->body) > 0) {
			$this->login();
		}
		
		if (strlen($response->body) > 0) {
			echo json_encode(array("Logout"));
			$this->s = null;
			exit;
		}
		
		return true;
	}
	
	public function setGetRequest($data)
	{
		//$this->login();
		//$this->loginCheck();
		
		if ($this->loginCheck()) {
			return $this->s->get($this->global_webserver."/".$data);
		} else {
			return null;
		}
	}
	
	public function setPostRequest($url, $data)
	{
		//$this->login();
		//$this->loginCheck();
		
		if ($this->loginCheck()) {
			return $this->s->post($this->global_webserver."/" . $url, array(), $data);
		} else {
			return null;
		}
	}
	
	public function getAllInterfaces()
	{
		$this->loginCheck();
		
		$exec = "/sbin/ifconfig -a | cut -c 1-8 | sort | uniq -u |grep -v lo";
		exec($exec, $output);
		return json_encode($output);
	}
	
	public function getInterface($iface)
	{
		$this->loginCheck();
		
		$exec = "/sbin/ifconfig $iface | grep 'inet addr' | awk -F: '{print $2}' | awk '{print $1}'";
		exec($exec, $output);
		return json_encode($output);
	}

	public function getServiceStatus($service)
	{
		$this->loginCheck();
		
		include "../config/config.php";
	
		if ($service = "wireless") {
		
			if ($ap_mode == "1") { 
				$ismoduleup = exec("ps auxww | grep hostapd | grep -v -e grep");
			} else if ($ap_mode == "2") {
				$ismoduleup = exec("ps auxww | grep airbase | grep -v -e grep");
			} else if ($ap_mode == "3") {
				$ismoduleup = exec("ps auxww | grep hostapd | grep -v -e grep");
			} else if ($ap_mode == "4") {
				$ismoduleup = exec("ps auxww | grep hostapd | grep -v -e grep");
			}
		
		} else if ($service = "3g_4g") {
			
		} else if ($service = "karma") {
		
		} else if ($service = "mana") {
		
		} else if ($service = "supplicant") {
		
		}
		
		if ($ismoduleup != "") {
			$output[0] = "Y";
		} else {
			$output[0] = "N";    
		}
		
		return json_encode($output);
	}
	
	// START|STOP Services
	public function setServiceStatus($service, $action)
	{
		$this->loginCheck();
			
		if ($service = "wireless") {
			
			$url = "scripts/status_wireless.php?service=wireless&action=$action";
			$this->setPostRequest($url);
			
		} else if ($service = "3g_4g") {
			
		} else if ($service = "karma") {
		
		} else if ($service = "mana") {
		
		} else if ($service = "supplicant") {
		
		}
		
		if ($action == "start") {
			$output[0] = "true";
		} else {
			$output[0] = "false";    
		}
		
		return json_encode($output);
	}
	
	public function getModules()
	{
		$this->loginCheck();
		
		exec("find ../../../modules -name '_info_.php' | sort", $output);
	
		for ($i=0; $i < count($output); $i++) {
			include $output[$i];
			$module_path = str_replace("_info_.php","",$output[$i]);
			
			$modules[] = $mod_name;
		}
		
		return json_encode($modules);
	}
	
	public function getModulesAllBACK()
	{
		$this->loginCheck();
		
		exec("find ../../../modules -name '_info_.php' | sort", $output);

		for ($i=0; $i < count($output); $i++) {
			include $output[$i];
			
			$ismoduleup = exec("$mod_isup");
				
			if ($ismoduleup != "") {
				$output[0] = true;
			} else {
				$output[0] = false;
			}
			
			$modules[] = array($mod_name => $output[0]);
		}
		
		return json_encode($modules);
	}
	
	public function getModulesAll()
	{
		$this->loginCheck();
		
		exec("find ../../../modules -name '_info_.php' | sort", $output);
		
		for ($i=0; $i < count($output); $i++) {
			include $output[$i];
			
			$module = [];
			
			$ismoduleup = exec("$mod_isup");
			
			if (!isset($mod_group)) {
				$mod_group = "";
			}
				
			if ($ismoduleup != "") {
				$output[0] = true;
			} else {
				$output[0] = false;
			}
			
			$modules[] = array("name" => $mod_name,
							   "alias" => $mod_alias,
							   "status" => $output[0],
							   "panel" => $mod_panel,
							   "logs" => $mod_logs,
							   "group" => $mod_group
							   );
			
			$mod_group = "";
		}
		
		return json_encode($modules);
	
	}
	
	public function getModuleStatus($module)
	{
		$this->loginCheck();
		
		$file = "../../../modules/$module/_info_.php";
		
		if (file_exists($file)) {
			
			include $file;
			$ismoduleup = exec("$mod_isup");
			
			if ($ismoduleup != "") {
				//$output[0] = "Y";
				$output[0] = true;
			} else {
				//$output[0] = "N";
				$output[0] = false;
			}
		
		} else {
			//$output[0] = "-";
			$output[0] = null;
		}
		
		return json_encode($output);
	}
	
	// ENABLE|DISABLE Modules
	public function setModuleStatus($module, $action)
	{
		$this->loginCheck();
		
		$url = "modules/$module/includes/module_action.php?service=$module&action=$action";
		$this->setGetRequest($url);
		
		if ($action == "start") {
			$output[0] = true;
		} else {
			$output[0] = false;    
		}
		
		return json_encode($output);
	}
	
	public function getConfigInOut($io, $prop)
	{
		$this->loginCheck();
		
		include "../../../config/config.php";
		
		if ($io == "in") {
			switch ($prop) {
				case "iface":
					$output[0] = $io_in_iface; break;
				case "type":
					$output[0] = $io_in_set; break;
				case "ip":
					$output[0] = $io_in_ip; break;
				case "mask":
					$output[0] = $io_in_mask; break;
				case "gw":
					$output[0] = $io_in_gw; break;
				default:
					$output[0] = null; break;
			}
		}
		
		if ($io == "out") {
			switch ($prop) {
				case "iface":
					$output[0] = $io_out_iface; break;
				case "type":
					$output[0] = $io_out_set; break;
				case "ip":
					$output[0] = $io_out_ip; break;
				case "mask":
					$output[0] = $io_out_mask; break;
				case "gw":
					$output[0] = $io_out_gw; break;
				default:
					$output[0] = null; break;
			}
		}
		
		return json_encode($output);
	}
	
	
	public function getConfigInOutAll($io)
	{
		$this->loginCheck();
		
		include "../../../config/config.php";
		
		if ($io == "in") {
			$output[0] = $io_in_iface;
			$output[1] = $io_in_set;
			$output[2] = $io_in_ip;
			$output[3] = $io_in_mask;
			$output[4] = $io_in_gw;
		}
		
		if ($io == "out") {
			$output[0] = $io_out_iface;
			$output[1] = $io_out_set;
			$output[2] = $io_out_ip;
			$output[3] = $io_out_mask;
			$output[4] = $io_out_gw;
		}
		
		return json_encode($output);
	}
	
	
	public function setConfigInOut($io, $prop, $value)
	{
		$this->loginCheck();
		
		if ($io == "in") {
			switch ($prop) {
				case "iface":
					$data = array('io_in_iface' => $value);
					$output[0] = $io_in_iface; break;
				case "type":
					$data = array('io_in_set' => $value);
					$output[0] = $io_in_set; break;
				case "ip":
					$data = array('io_in_ip' => $value);
					$output[0] = $io_in_ip; break;
				case "mask":
					$data = array('io_in_mask' => $value);
					$output[0] = $io_in_mask; break;
				case "gw":
					$data = array('io_in_gw' => $value);
					$output[0] = $io_in_gw; break;
				default:
					$data = null;
					$output[0] = null; break;
			}
		}
		
		if ($io == "out") {
			switch ($prop) {
				case "iface":
					$data = array('io_out_iface' => $value);
					$output[0] = $io_out_iface; break;
				case "type":
					$data = array('io_out_set' => $value);
					$output[0] = $io_out_set; break;
				case "ip":
					$data = array('io_out_ip' => $value);
					$output[0] = $io_out_ip; break;
				case "mask":
					$data = array('io_out_mask' => $value);
					$output[0] = $io_out_mask; break;
				case "gw":
					$data = array('io_out_gw' => $value);
					$output[0] = $io_out_gw; break;
				default:
					$data = null;
					$output[0] = null; break;
			}
		}
		
		//if ($data != null ) {
			$url = "scripts/config_iface.php";
			//$data = array('token' => $this->token);
			$this->setPostRequest($url, $data);
			
			$output[0] = $value;
			return json_encode($output);
		//}
		
	}
	
	public function getConfigAction()
	{
		$this->loginCheck();
		
		include "../../../config/config.php";
		
		$output[0] = $io_action;
		
		return json_encode($output);
	}
	
	public function setConfigAction($value)
	{
		$this->loginCheck();
		
		$url = "scripts/config_iface.php";
		$data = array('io_action' => $value);
		$this->setPostRequest($url, $data);
		
		$output[0] = $value;
		
		return json_encode($output);
	}
	
	public function getConfigWireless()
	{
		$this->loginCheck();
		
		include "../../../config/config.php";
		
		$output[0] = $hostapd_ssid;
		
		return json_encode($output);
	}
	
	public function setConfigWireless($value)
	{
		/*
		$url = "scripts/config_iface.php";
		$data = array('hostapd_ssid' => $value);
		$this->setPostRequest($url, $data);
		
		$output[0] = $value;
		
		return json_encode($output);
		*/
	}
	
	public function getLogDHCP()
	{
		include "../../../config/config.php";
		
		$filename = "$log_path/dhcp.leases";
		$fh = fopen($filename, "r"); //or die("Could not open file.");
		if ( 0 < filesize( $filename ) ) {
			$data = fread($fh, filesize($filename)); //or die("Could not read file.");
		}
		fclose($fh);
		$data = explode("\n",$data);
		
		$output = [];
		
		for ($i=0; $i < count($data); $i++) {
			$tmp = explode(" ", $data[$i]);
			$output[] = $tmp[2] . " " . $tmp[1] . " " . $tmp[3];
		}
		
		echo json_encode($output);
	}
	
	public function getLogStation()
	{		
		include "../../../config/config.php";

		exec("/sbin/iw dev $io_in_iface station dump |grep Stat", $stations);
		
		$output = [];
		
		for ($i=0; $i < count($stations); $i++) {
			$output[] = str_replace("Station", "", $stations[$i]);
		}

		echo json_encode($output);		
	}
	
	// STATUS
	public function getStatusCPU()
	{
		//include "functions.php";
		
		$exec = "mpstat | awk '\\$12 ~ /[0-9.]+/ { print 100 - \\$12 }'";
		$out = exec_fruitywifi($exec);
		
		echo json_encode($out);
	}
	
	public function getStatusMEM()
	{
		//include "functions.php";
		
		$exec = "free | grep Mem | awk '{print \\$3/\\$2 * 100.0}'";
		$exec = "free | awk 'FNR == 3 {print \\$3/(\\$3+\\$4)*100}'";
		$out = exec_fruitywifi($exec);
		
		echo json_encode($out);
	}
	
	// GET VARS FROM CONFIG FILE
	private function loadVariables($load_file) {
        include $load_file;
        
        $all_vars = get_defined_vars();
		$exclude = ["version", "regex", "regex_extra", "codename", "root_path", "root_web", "log_path", "core_name", "core_alias", "api_token",
						"bin_", "mod_name", "mod_version", "mod_path", "mod_logs", "mod_logs_history", "mod_logs_panel", "mod_panel", "mod_isup", "mod_alias"];
		$file = fopen($load_file, "r");
		$output = [];
		
		while(!feof($file)){
			
			$line = fgets($file);
			$line = trim($line);
			
			$show_var = True;
			if (0 === strpos($line, '$')) {
				foreach ($exclude as $value) {
					if ((0 === strpos($line, "$$value"))) {
						$show_var = False;
					 }
				}
				
				if ($show_var) {
					$temp = explode("=", $line);
					$var = str_replace("$","",$temp[0]);
					$value = $all_vars[$var];
					//echo "$var | $value";
					//echo "<br>";
					$output[$var] = $value;
				}
			}
		}
		fclose($file);
		return $output;
	}
	
    // GET CONFIG ALL [CORE]
	public function getConfigCoreAll()
	{
		$output = $this->loadVariables("/usr/share/fruitywifi/www/config/config.php");
		
		//$output = ${$param};
		echo json_encode($output);
	}
    
	// GET CONFIG [CORE]
	public function getConfigCore($param)
	{
		//include "functions.php";
		include "/usr/share/fruitywifi/www/config/config.php";
		
		$output = ${$param};
		echo json_encode($output);
	}
	
	// SET CONFIG [CORE]
	public function setConfigCore($param, $value)
	{
		//include "functions.php";
		
		$exec = "/bin/sed -i 's/$param=.*/$param=\\\"".$value."\\\";/g' /usr/share/fruitywifi/www/config/config.php";
		exec_fruitywifi($exec);
		
		echo json_encode($value);
	}
	
	// GET CONFIG ALL [MODULE]
	public function getConfigModuleAll($module)
	{
		$output = $this->loadVariables("/usr/share/fruitywifi/www/modules/$module/_info_.php");
		
		//$output = ${$param};
		echo json_encode($output);
	}
	
	// GET CONFIG [MODULE]
	public function getConfigModule($module, $param)
	{
		//include "functions.php";
		include "/usr/share/fruitywifi/www/modules/$module/_info_.php";
		
		$output = ${$param};
		echo json_encode($output);
	}
	
	// SET CONFIG [MODULE]
	public function setConfigModule($module, $param, $value)
	{
		//include_once "functions.php";
		
		$exec = "/bin/sed -i 's/$param=.*/$param=\\\"".$value."\\\";/g' /usr/share/fruitywifi/www/modules/$module/_info_.php";
		exec_fruitywifi($exec);
		
		echo json_encode($value);
	}
	
	// SET MONITOR MODE
	public function setMonitorMode($iface, $action)
	{
		//include "../../../functions.php";
		
		if ($action == "start") start_monitor_mode($iface);
		if ($action == "stop") stop_monitor_mode($iface);
		
		echo json_encode(true);
	}
	
}



?>