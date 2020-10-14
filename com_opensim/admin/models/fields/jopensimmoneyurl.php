<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('hidden');

class JFormFieldjopensimmoneyurl extends JFormFieldHidden
{
	protected $type = 'jopensimmoneyurl';
	
	protected function getInput() {
		$this->default = JURI::root();
		$this->value = JURI::root();
		// Trim the trailing line in the layout file
		return rtrim($this->getRenderer($this->layout)->render($this->getLayoutData()), PHP_EOL);
	}
	protected function getLabel() {
		return '';
	}
}
?>