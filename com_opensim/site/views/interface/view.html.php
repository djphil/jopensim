<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class opensimViewinterface extends JViewLegacy {

	public function display($tpl = null) {
		$this->model	= $model = $this->getModel();
		$this->jdebug	= $model->jdebug;

		$remoteip		= $_SERVER['REMOTE_ADDR']; // who is talking with us?

		// todo: here the $opensim->checkRegionIP() to implement better:
		//			land functions coming from landtool.php and currency quote requests from currency.php which probably always gets a granted IP beeing on same server
//		$access			= $model->opensim->checkRegionIP($remoteip);
//		if($access === FALSE) {
//			if($this->model->jdebug['access']) $this->model->debuglog("No access for ".$remoteip);
//			exit;
//		} else {
//			if($this->model->jdebug['access']) $this->model->debuglog("Access granted for ".$remoteip);
//		}

		$this->input	= file_get_contents("php://input");
		if($this->model->jdebug['input']) $this->model->debuglog($this->input,"input");
		$this->xmlrpcfunctions = $model->xmlrpcfunctions;

		$profilefunctions	= $this->xmlrpcfunctions['profilefunctions'];
		$groupfunctions		= $this->xmlrpcfunctions['groupfunctions'];
		$searchfunctions	= $this->xmlrpcfunctions['searchfunctions'];
		$currencyfunctions	= $this->xmlrpcfunctions['currencyfunctions'];

		$jinput	= JFactory::getApplication()->input;
		$this->service		= $jinput->get('registerSearch',null,'STR');
		$this->test = $test	= $jinput->get('test',null,'STR');

		if($test) { // this is testing mode for output in html
			$this->retval = "";
			if(in_array($test,$profilefunctions)) {
				$this->functionblock = "profile";
				$params = $_REQUEST;
				unset($params['test']);
				$this->retval = $model->subCall($test,$params);
			} elseif(array_key_exists($test,$groupfunctions)) {
				$this->functionblock = "groups";
				$params = $_REQUEST;
				unset($params['test']);
				$this->retval = $model->subCall($test,$params);
			} elseif(array_key_exists($test,$currencyfunctions)) {
				$this->functionblock = "money";
				$params = $_REQUEST;
				unset($params['test']);
				$this->retval = $model->subCall($test,$params);
			} elseif(in_array($test,$searchfunctions)) {
				$this->functionblock = "search";
				$params = $_REQUEST;
				unset($params['test']);
				$this->retval = $model->subCall($test,$params);
			} else {
				$this->functionblock = "functionblock not found :(<br />";
			}
			$this->debug_settings = $model->getdebugsettings();
			parent::display($tpl);

		} else { // a "real" xmlrpc request, dont output in html, but return valid xml response
//			ob_start();

			// ##############################
			// ##### handling terminals #####
			// ##############################

			$action		= $jinput->get('action',null,'STR');
			switch($action) { // here we most probably receive some request from a terminal
				case "identify":
					$data['remoteip']		= $remoteip;
					$data['identString']	= $jinput->get('identString',null,'STR');
					$data['identKey']		= $jinput->get('identKey',null,'STR');
					$response				= $model->subCall("identifyTerminal",$data);
					if($model->jdebug['terminal']) $this->model->debuglog($response,"response for terminal ".$action);
					echo $response;
					exit; // we have done already everything, dont check if there could be more
				break;
				case "register":
					$response				= $model->subCall("registerTerminal",$remoteip);
					if($model->jdebug['terminal']) $this->model->debuglog($response,"response for terminal ".$action);
					echo $response;
					exit; // we have done already everything, dont check if there could be more
				break;
				case "setState":
					$response				= $model->subCall("setStateTerminal",$remoteip);
					if($model->jdebug['terminal']) $this->model->debuglog($response,"response for terminal ".$action);
					echo $response;
					exit; // we have done already everything, dont check if there could be more
				break;
			}


			// #####################################
			// ##### handling offline messages #####
			// #####################################

			$messaging = $jinput->get('messaging',null,'raw');
			switch($messaging) { // with view system offline messages need to be handled different >:(
				case "/SaveMessage/":
					$data['input']		= $this->input;
					$data['remoteip']	= $remoteip;
					$response			= $model->subCall("SaveMessage",$data);
//					if($model->jdebug['messages'])	$this->model->debuglog($response,"response for offlinemessages ".$messaging);
					echo $response;
					exit;
				break;
				case "/RetrieveMessages/":
					$data['input']		= $this->input;
					$data['remoteip']	= $remoteip;
					$response			= $model->subCall("RetrieveMessages",$data);
//					if($model->jdebug['messages'])	$this->model->debuglog($response,"response for offlinemessages ".$messaging);
					echo $response;
					exit;
				break;
			}

			$request	= $model->opensim->parseXmlRpc($this->input);
			$method		= $request['method'];
			$params		= $request['decode'][0];
//			$this->model->debuglog($model->debug,"request");
//			$this->model->debuglog($request,"request");
//			$this->debuglog($parameter,"Parameter for ".__FUNCTION__);

			$debugresponse	= TRUE;
			if(in_array($method,$profilefunctions) && $model->jdebug['profile'])			$debugresponse = TRUE;
			if(@array_key_exists($method,$groupfunctions) && $model->jdebug['groups'])		$debugresponse = TRUE;
			if(in_array($method,$searchfunctions) && $model->jdebug['search'])				$debugresponse = TRUE;
			if(@array_key_exists($method,$currencyfunctions) && $model->jdebug['currency'])	$debugresponse = TRUE;
			if(in_array($method,$profilefunctions) || @array_key_exists($method,$groupfunctions) || in_array($method,$searchfunctions) || @array_key_exists($method,$currencyfunctions)) {
				$this->retval	= $model->subCall($method,$params);
			} else {
				if($model->jdebug['any']) $debugresponse = TRUE;
				$this->retval	= array('success'		=> False,
										'errorMessage'	=> 'No subCall found for method '.$method,
										'data'			=> array());
			}

			$response = xmlrpc_encode($this->retval);
			if($debugresponse === TRUE) $this->model->debuglog($response,"response for ".$method);
			echo $response;
//			$output = ob_get_contents();
//			$this->model->debuglog($output,"output for ".$method);
//			ob_end_clean();
//			echo $output;
			exit; // no further output ... stop here
		}
	}
}
?>