<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('note');

class JFormFieldjopensimdebugnote2 extends JFormFieldNote
{
	protected $type = 'jopensimdebugnote2';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
		$logfolder		= JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR;
		$this->element['description']	= "";
		$interfacelog	= $logfolder."interface.log";
		$jinput	= JFactory::getApplication()->input;
		$path	= $jinput->get('path', '');
		$return	= $jinput->get('return', '');
		if(is_file($interfacelog) && filesize($interfacelog) > 0) {
			$interfacelogsize = $this->human_filesize(filesize($interfacelog));
			$this->element['description']	.= "<p>interface.log: ".$interfacelogsize." <a href='index.php?option=com_opensim&task=truncatelog&log=interface&path=".$path."&return=".$return."'>empty now</a></p>";
			$this->element['label']	= JText::_('JOPENSIM_ATTENTION');
		}
		$currencylog	= $logfolder."currency.log";
		if(is_file($currencylog) && filesize($currencylog) > 0) {
			$currencylogsize = $this->human_filesize(filesize($currencylog));
			$this->element['description']	.= "<p>currencylog.log: ".$currencylogsize." <a href='index.php?option=com_opensim&task=truncatelog&log=currency&path=".$path."&return=".$return."'>empty now</a></p>";
			$this->element['label']	= JText::_('JOPENSIM_ATTENTION');
		}
		if($this->element['label'] == JText::_('JOPENSIM_ATTENTION')) {
			$this->element['description'] = "<p>".JText::_('JOPENSIM_DEBUG_FILEWARNING')."</p>".$this->element['description'];
		}
		return parent::getLabel();
	}

	protected function human_filesize($bytes, $decimals = 2) {
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
}
?>