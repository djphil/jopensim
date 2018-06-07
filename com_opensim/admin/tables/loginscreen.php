<?php
/*
 * @component com_opensim
 * @copyright Copyright © 2018 FoTo50 https://www.jopensim.com/
 */

// no direct access
defined('_JEXEC') or die;

class jOpenSimTableLoginscreen extends JTable {
	public function __construct($_db) {
		parent::__construct('#__opensim_loginscreen', 'id', $_db);
	}

}

