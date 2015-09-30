<?php
/*
    Copyright (C) 2013-2015 xtr4nge [_AT_] gmail.com

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
		$this->s->useragent = 'RESTful API [FruityWifi]';
		
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
	
	public function setConfigInOut($io, $prop, $value)
	{
		$this->loginCheck();
		
		if ($io == "in") {
			switch ($prop) {
				case "iface":
					$data = array('io_in_iface' => $value);
					$output[0] = $io_in_iface; break;
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
	
}

?>