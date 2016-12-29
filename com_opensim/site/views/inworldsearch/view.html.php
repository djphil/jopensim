<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

// $_REQUEST['tmpl'] = "component";

class opensimViewinworldsearch extends JViewLegacy {

	public function display($tpl = null) {
		$this->assetpath = JUri::base(true)."/components/com_opensim/assets/";
		$doc = JFactory::getDocument();
		$doc->addStyleSheet($this->assetpath.'opensim.css');
		$doc->addStyleSheet($this->assetpath.'opensim.override.css');
		$model = $this->getModel('inworldsearch');

		$this->settingsdata = $model->getSettingsData();
		$this->itemid		= JFactory::getApplication()->input->get('Itemid');

		$this->searchquery = JFactory::getApplication()->input->get('q');
		if(!$this->searchquery) {
			$this->showcase = TRUE; // disabled this message in default.php currently completely since it will take still a while
		} else {
			$this->showcase = FALSE;
			$result = $model->searchAll($this->searchquery);
		}
		$this->assignRef('result',$result);
		$this->assignRef('jopensimversion',$model->opensim->getversion());

		$task = JFactory::getApplication()->input->get('task','','method','string');

		$results = $model->getResultlines($result);
		$this->assignRef('results',$results);


		switch($task) {
			case "viewersearch":
				$searchform = TRUE;
				$tmpl = TRUE;
				JHTML::stylesheet( 'opensim_inworldsearch.css', 'components/com_opensim/assets/' );
			break;
			default:
				$tmpl = FALSE;
				$searchform = TRUE;
			break;
		}
		$this->assignRef('tmpl',$tmpl);
		$this->assignRef('searchform',$searchform);

		parent::display($tpl);
	}
}
?>