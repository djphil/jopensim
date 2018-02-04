<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Inworld transactions are processed by functions_currency.php of jOpenSim and this file requires this function
if(!function_exists("jOpenSimSettings")) {
	function jOpenSimSettings() {
		$params = JComponentHelper::getParams('com_opensim');

		$settings['jopensimmoneybanker']			= $params->get('jopensimmoneybanker');
		$settings['jopensimmoney_groupcreation']	= $params->get('jopensimmoney_groupcreation');
		$settings['jopensimmoney_upload']			= $params->get('jopensimmoney_upload');
		$settings['jopensimmoney_startbalance']		= $params->get('jopensimmoney_startbalance');
		$settings['jopensimmoney_groupdividend']	= $params->get('jopensimmoney_groupdividend');
		$settings['jopensimmoney_currencyname']		= $params->get('jopensimmoney_currencyname');
		$settings['jopensimmoney_bankername']		= $params->get('jopensimmoney_bankername');

		$settings['jopensimmoney_sendgridbalancewarning']	= $params->get('jopensimmoney_sendgridbalancewarning');
		$settings['jopensimmoney_warningrecipient']			= $params->get('jopensimmoney_warningrecipient');
		$settings['jopensimmoney_warningsubject']			= $params->get('jopensimmoney_warningsubject');

		$settings['currencydebug']					= $params->get('jopensim_debug_currency');

		return $settings;
	}
}

if(!function_exists("debugzeile")) {
	function debugzeile($zeile,$function = "") {
		if(!$function) $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##########\n";
		else $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##### ".$function." ##########\n";
		$zeile = var_export($zeile,TRUE);
		$logfile = JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."currency.log";
		$handle = fopen($logfile,"a+");
		$logzeile = $zeit.$zeile."\n\n";
		fputs($handle,$logzeile);
		fclose($handle);
	}
}


$settings = jOpenSimSettings();

if(!defined("_JOPENSIMMONEYDEBUG")) { // Avoid notices when functions_currency.php is getting included
	if($settings['currencydebug'] == "1") define("_JOPENSIMMONEYDEBUG",TRUE);
	else define("_JOPENSIMMONEYDEBUG",FALSE);
}

// we need the OpenSim class from jOpenSim
require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');

// import joomla controller library
jimport('joomla.application.component.controller');


// Get an instance of the controller prefixed by jOpenSimPayPal
$controller = JControllerLegacy::getInstance('jOpenSimPayPal');

// Perform the Request task
// Get the task
$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task', "jopensimpaypal", 'STR' );

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
