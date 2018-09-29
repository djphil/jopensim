<?php
/*
 * @component com_opensim
 * @copyright Copyright © 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die;

class jOpenSimTableMisc extends JTable {
	public function __construct($_db) {
		parent::__construct('#__opensim_simulators', 'simulator', $_db);
	}

}

