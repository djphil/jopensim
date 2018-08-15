<?php
/**
 * @module      OpenSim Panorama (mod_opensim_panorama)
 * @copyright   Copyright (C) djphil 2018, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldloadcss extends JFormField
{
	protected $type = 'loadcss';

	protected function getInput() 
	{
	   // return '';
	}

	protected function getLabel()
	{
		// Add custom admin css here
		$cssFile = $this->element['file'] ? (string) $this->element['file'] : "loadcss.css";
		$cssDir  = $this->element['path'] ? (string) $this->element['path'] : substr(__dir__, strlen(JPATH_ROOT) + 1);
		$cssPath = $cssDir . DIRECTORY_SEPARATOR . $cssFile;
		JHtml::stylesheet($cssPath);
		// return '';
	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   11.1
	 */
	protected function getTitle()
	{
		// return $this->getLabel();
	}
}