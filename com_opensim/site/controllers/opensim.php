<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerOpenSim extends OpenSimController {
	public function __construct() {
		if(!array_key_exists("HTTP_USER_AGENT",$_SERVER)) $_SERVER['HTTP_USER_AGENT'] = null; // to prevent notices for the loginscreen
		parent::__construct();
		$view = $this->getView( 'opensim', 'html' );
		$view->setModel( $this->getModel( 'opensimFe' ), true );
		$view->display();
	}
}
?>
