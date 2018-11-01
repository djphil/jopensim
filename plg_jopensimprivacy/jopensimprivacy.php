<?php
/**
* jOpenSimPrivacy Plugin for Joomla 3.9
* @version $Id: jOpenSimPrivacy.php $
* @package: jOpenSimPrivacy
* ===================================================
* @author
* Name: FoTo50, www.jopensim.com
* Email: foto50@jopensim.com
* Url: https://www.jopensim.com
* ===================================================
* @copyright (C) 2018 FoTo50, (www.jopensim.com). All rights reserved.
* @license see http://www.gnu.org/licenses/gpl-2.0.html  GNU/GPL.
* You can use, redistribute this file and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation.
*/

defined('_JEXEC') or die();

if(!defined('DS')) define("DS",DIRECTORY_SEPARATOR);

jimport('joomla.plugin');
jimport('joomla.html.parameter' );
jimport('joomla.application.component.view');
jimport('joomla.application.component.helper');

JLoader::register('PrivacyPlugin', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/plugin.php');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');

class plgPrivacyjOpensimPrivacy extends PrivacyPlugin {
	public $_osgrid_db		= null;
	public $admin_model		= null;
	public $opensim			= null;
	public $_settingsData	= null;

	public function __construct($subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage();
		JFormHelper::addFieldPath(dirname(__FILE__) . '/fields');
		JLoader::import('joomla.application.component.model'); 
		JLoader::import( 'opensim', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models' );
		$this->admin_model		= JModelLegacy::getInstance( 'ModelOpenSim','OpenSim' );
		$this->opensim			= $this->admin_model->opensim;
		$this->exportOSemail	= $this->params->get('plgJopensimPrivExportOpenSimEmail',1);
		$this->exportProfile	= $this->params->get('plgJopensimPrivExportProfiledata',1);
		$this->removeOSemail	= $this->params->get('plgJopensimPrivRemoveOpenSimEmail',0);
		$this->removeOSnames	= $this->params->get('plgJopensimPrivRemoveOpenSimNames',0);
		$this->removeOSlogin	= $this->params->get('plgJopensimPrivRemoveOpenSimLogin',0);
	}

	public function getOpenSimUID($joomlaID) {
		$db			= JFactory::getDBO();
		$query		= sprintf("SELECT
							#__opensim_userrelation.opensimID
						FROM
							#__opensim_userrelation
						WHERE
							#__opensim_userrelation.joomlaID = '%d'",$joomlaID);
		$db->setQuery($query);
		$db->execute();
		$num_rows	= $db->getNumRows();
		if($num_rows == 1) return $db->loadResult();
		else return null;
	}

	public function onPrivacyCollectAdminCapabilities() {
		// If a plugin does not have its language files autoloaded, ensure you manually load the language files now otherwise the below may not be translated
		$this->loadLanguage();

		return array(
			JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_JOPENSIMTITLE') => array(
				JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_ADDONS').'<ul>',
				JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_PROFILE'),
				JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_GROUPMEMBERSHIP'),
				JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_MONEYTRANSACTIONS').'</ul>',
				JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_MODULES').'<ul>',
				JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_IP'),
			JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_TIME').'</ul>',
				
			),
			JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_OPENSIMTITLE') => array(
				JText::_('PLG_JOPENSIMPRIVACY_CAPABILITY_EMAIL'),
			),
		);
	}

	public function onPrivacyExportRequest(PrivacyTableRequest $request, JUser $user = null) {
		if (!$user) {
			return array();
		}
//		error_log("Zeile ".__LINE__.": this->exportOSemail: ".$this->exportOSemail);
//		error_log("Zeile ".__LINE__.": this->exportProfile: ".$this->exportProfile);
		$domains	= array();
		$domains[]	= $this->createUserjOpenSimDomain($user->id);
		if($this->exportOSemail) {
			$domains[]	= $this->createUserOpenSimDomain($user->id);
		}
		return $domains;
	}

	private function createUserjOpenSimDomain($joomlaid) {
		$uuid		= $this->getOpenSimUID($joomlaid);
		$domain		= $this->createDomain('jOpenSim', 'jOpenSim users table data');
		$domain->addItem($this->createItemForUserRelation($joomlaid));
		if($uuid && $this->exportProfile) {
			$profile	= $this->createItemForUserProfile($uuid);
			if($profile) $domain->addItem($profile);
		}

		return $domain;
	}

	private function createUserOpenSimDomain($joomlaid) {
		$domain			= $this->createDomain('OpenSimulator', 'OpenSimulator users table data');
		$uuid			= $this->getOpenSimUID($joomlaid);
		$opensimdata	= $this->admin_model->getUserData($uuid);
		$data['email']	= $opensimdata['email'];
		$domain->addItem($this->createItemFromArray($data, $uuid));
		return $domain;
	}


	private function createItemForUserRelation($joomlaid) {
		$data    = array();
		$data['opensimUUID'] = $this->getOpenSimUID($joomlaid);
		return $this->createItemFromArray($data, $joomlaid);
	}

	public function createItemForUserProfile($uuid) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__opensim_userprofile'))
			->where($db->quoteName('avatar_id') . ' = ' . $db->quote($uuid));
		$item = $db->setQuery($query)->loadAssoc();
		if(is_array($item)) {
			$retval = $this->createItemFromArray($item, $uuid);
			return $retval;
		}
		else return null;
	}

	public function onPrivacyRemoveData(PrivacyTableRequest $request, JUser $user = null) {
		// This plugin only processes data for registered user accounts
		if (!$user) {
			return;
		} else {
			$uuid			= $this->getOpenSimUID($user->id);
			if($this->removeOSemail) {
				$pseudoemail	= $uuid."removed@email.removed";
				$this->admin_model->updateOsField('email',$pseudoemail,$uuid);
			}
			if($this->removeOSnames) {
				$pseudofirst	= "privacyRemoved";
				$this->admin_model->updateOsField('firstname',$pseudofirst,$uuid);
				$pseudolast		= "removed_".$uuid;
				$this->admin_model->updateOsField('lastname',$pseudolast,$uuid);
			}
			if($this->removeOSlogin) {
				$this->admin_model->opensim->setUserLevel($uuid,-1);
			}
		}
	}

}