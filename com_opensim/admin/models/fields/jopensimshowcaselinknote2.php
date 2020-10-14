<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('note');

class JFormFieldjopensimshowcaselinknote2 extends JFormFieldNote
{
	protected $type = 'jopensimshowcaselinknote2';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		$this->element['description']	 = "<p>".JText::_('JOPENSIM_SHOWCASE_LINKING2_DESC')."</p>";
		return parent::getLabel();
	}
}
?>