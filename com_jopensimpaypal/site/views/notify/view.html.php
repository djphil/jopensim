<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
use Joomla\CMS\Factory;

//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');
//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'interface.php');

class jopensimpaypalViewnotify extends JViewLegacy {
	public function display($tpl = null) {
		$model			= $this->getModel('notify');
		$cparams		= $model->getParam("all");

		// do we want to get notified?
		$notifytransaction	= $model->getParam('notifytransaction');
		$notifyerror		= $model->getParam('notifyerror');
		$notifywarning		= $model->getParam('notifywarning');
		$notifyemail		= $model->getParam('notifyemail');

		// how do we want to log?
		$logsetting		= $model->getParam('log2file');
		if(($logsetting & 2) == 2) $log2file		= TRUE; // log everything
		else $log2file		= FALSE;
		if(($logsetting & 1) == 1) $failure2file	= TRUE; // log errors and warnings
		else $failure2file	= FALSE;

		// get the components parameters
		$paypaltype		= $model->getParam('sandboxmode');
		$paypalaccount	= $model->getParam('paypal_account');
		$sslverify		= $model->getParam('paypal_nossl');

		// get jOpenSim interface model
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_opensim/models', '');
		$imodel = JModelLegacy::getInstance('Interface', 'OpenSimModel',array());
//		$imodel = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php';
//		$imodel	= new opensimModelInterface();
		$imodel->setPayPalCall();
		$model->setMoneyModel($imodel);
		$logpath			= $model->getParam('logpath');
		$paypallogfile		= "jopensimpaypal.log";
		$logsetting			= $model->getParam('log2file');

		if(($logsetting & 2) == 2) $log2file		= TRUE; // log everything
		else $log2file		= FALSE;
		if(($logsetting & 1) == 1) $failure2file	= TRUE; // log errors and warnings
		else $failure2file	= FALSE;

		$jinput				= Factory::getApplication()->input;
		$this->test			= $jinput->get('test',null,'STR');
		$getvals			= $jinput->get->getArray();

		if($this->test) { // this is testing mode for output in html
			echo "this is only a test!<br />";
			echo "notifyemail: ".$notifyemail."<br />\n";
			echo "paypalaccount: ".$paypalaccount."<br />\n";
			echo "logpath: ".$logpath.DIRECTORY_SEPARATOR.$paypallogfile."<br />\n";
			echo "<pre>\n";
			echo "test:\n\n";
			var_dump($this->test);
			if($this->test == "verify") {
				$model->handleResponse('VERIFIED',TRUE);
			}

			$minpay		= $model->getParam('minbuy');
			$maxpay		= $model->getParam('maxbuy');
			echo "minpay:\n\n";
			var_dump($minpay);
			echo "maxpay:\n\n";
			var_dump($maxpay);

			echo "getvals:\n\n";
			var_dump($getvals);

			echo "</pre>\n";
			exit;
		}

		$this->input = file_get_contents("php://input");
		if($log2file === TRUE) $model->simpledebugzeile($this->input);

		if($log2file === TRUE) $model->debugzeile($_REQUEST,"\$_REQUEST");
		if($log2file === TRUE) $model->debugzeile($_SERVER,"\$_SERVER");

		// read the post from PayPal
		$res	= $model->validate();
		if($log2file === TRUE) $model->debugzeile($res,"\$res");

		if (strcmp ($res, "VERIFIED") == 0) { // answer from _notify-validate was "VERIFIED"
			$model->handleResponse($res);
		} else if (strcmp ($res, "INVALID") == 0) { // answer from _notify-validate was "INVALID" ... ???
			$report['response_notify-validate']	= $res;
			$report['post']	= $model->postvals;
			if($failure2file === TRUE) debugzeile($report,"ERROR: _notify-validate was \"INVALID\"!");
			if($notifyerror == 1) {
				$msg = "ERROR: _notify-validate was \"INVALID\"!!\n\n".var_export($report,TRUE);
				$model->sendnotify(1,3,$msg);
			}
		} else { // answer from _notify-validate was not "VERIFIED" and not "INVALID" ... ???
			$report['response_notify-validate']	= $res;
			$report['post']	= $model->postvals;
			if($failure2file === TRUE) debugzeile($report,"ERROR: _notify-validate was not \"VERIFIED\" and not \"INVALID\"!");
			if($notifyerror == 1) {
				$msg = "ERROR: _notify-validate was not \"VERIFIED\" and not \"INVALID\"!!\n\n".var_export($report,TRUE);
				$model->sendnotify(1,3,$msg);
			}
		}


		parent::display($tpl);
	}
}
?>