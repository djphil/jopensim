<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('note');

class JFormFieldjopensimdebugnote1 extends JFormFieldNote
{
	protected $type = 'jopensimdebugnote1';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		$this->element['description']	 = "<p>".JText::_('JOPENSIM_DEBUGGING_DESC').": <a href='".JURI::root(true)."/components/com_opensim/interface.log' target='_blank'>".JURI::root(true)."/components/com_opensim/interface.log</a> ".JText::_('JOPENSIM_OR')." <a href='".JURI::root(true)."/components/com_opensim/currency.log' target='_blank'>".JURI::root(true)."/components/com_opensim/currency.log</a></p>";
		$this->element['description']	.= "<p><b>".JText::_('JOPENSIM_ATTENTION').":</b> ".JText::_('JOPENSIM_DEBUGNOTE1')."</p>";
		return parent::getLabel();
	}
}
?>