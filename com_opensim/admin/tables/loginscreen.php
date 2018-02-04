<?php
/*
 * @component gsp-vorteilspartner
 * @copyright Copyright © 2017 FoTo50 https://www.jopensim.com/
 */

// no direct access
defined('_JEXEC') or die;

class jOpenSimTableLoginscreen extends JTable {
	public function __construct($_db) {
		parent::__construct('#__opensim_loginscreen', 'id', $_db);
	}

}

