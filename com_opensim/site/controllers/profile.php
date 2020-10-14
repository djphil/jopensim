<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerprofile extends OpenSimController {
	public function __construct() {
		parent::__construct();
		$model = $this->getModel('profile');
	}

	public function display() {
		parent::display(false); //true asks for caching.
	}
}
?>
