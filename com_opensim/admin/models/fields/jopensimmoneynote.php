<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('note');

class JFormFieldjopensimmoneynote extends JFormFieldNote
{
	protected $type = 'jopensimmoneynote';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		$this->element['description']	= JText::sprintf('JOPENSIM_MONEYNOTE',"<a href='https://www.jopensim.com/jopensimmoney' target='_blank'>jOpenSim.com</a>");
		return parent::getLabel();
	}
}
?>