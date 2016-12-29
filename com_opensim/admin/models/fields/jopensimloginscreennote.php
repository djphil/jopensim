<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('note');

class JFormFieldjopensimloginscreennote extends JFormFieldNote
{
	protected $type = 'jopensimloginscreennote';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		$this->element['description']	= JText::_('JOPENSIM_ROBUSTURL').": <a href='".JURI::root()."index.php?option=com_opensim' target='_blank'>".JURI::root()."index.php?option=com_opensim</a>";
		return parent::getLabel();
	}
}
?>