<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('note');

class JFormFieldjopensimdatabasenote extends JFormFieldNote
{
	protected $type = 'jopensimdatabasenote';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		$this->element['description']	= JText::_('JOPENSIM_DB_DESC1')."<br />".JText::_('JOPENSIM_DB_DESC2')."<br />".JText::_('JOPENSIM_DB_DESC3')."<ul><li><b>".JText::_('JOPENSIM_GRIDMODE').":</b> ".JText::_('JOPENSIM_DB_DESC4')."</li><li><b>".JText::_('JOPENSIM_STANDALONEMODE').":</b> ".JText::_('JOPENSIM_DB_DESC5');
		return parent::getLabel();
	}
}
?>