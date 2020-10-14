<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('note');

class JFormFieldjopensimshowcaselinknote1 extends JFormFieldNote
{
	protected $type = 'jopensimshowcaselinknote1';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		$this->element['description']	 = "<p>".JText::_('JOPENSIM_SHOWCASE_LINKING_DESC')."</p>";
		$this->element['description']	.= "<ul>";
		$this->element['description']	.= "<li><b>".JText::_('JOPENSIM_SHOWCASE_MAINLINK_WEB').":</b> ".JText::_('JOPENSIM_SHOWCASE_MAINLINK_WEB_DESC')."</li>";
		$this->element['description']	.= "<li><b>".JText::_('JOPENSIM_SHOWCASE_MAINLINK_LOCAL').":</b> ".JText::_('JOPENSIM_SHOWCASE_MAINLINK_LOCAL_DESC')."</li>";
		$this->element['description']	.= "<li><b>".JText::_('JOPENSIM_SHOWCASE_MAINLINK_HG').":</b> ".JText::_('JOPENSIM_SHOWCASE_MAINLINK_HG_DESC')."</li>";
		$this->element['description']	.= "<li><b>".JText::_('JOPENSIM_SHOWCASE_MAINLINK_HGV3').":</b> ".JText::_('JOPENSIM_SHOWCASE_MAINLINK_HGV3_DESC')."</li>";
		$this->element['description']	.= "<li><b>".JText::_('JOPENSIM_SHOWCASE_MAINLINK_HOP').":</b> ".JText::_('JOPENSIM_SHOWCASE_MAINLINK_HOP_DESC')."</li>";
		$this->element['description']	.= "</ul>";
		return parent::getLabel();
	}
}
?>