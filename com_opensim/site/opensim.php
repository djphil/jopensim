<?php
if(!defined("DS")) define("DS",DIRECTORY_SEPARATOR);

/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

// require the opensim class
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."opensim.class.php");

global $mainframe;
$mainframe = JFactory::getApplication();

$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base( true ).'/media/jui/css/icomoon.css');


// Require the base controller
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php' );

// Require specific controller if requested
if($controller = JFactory::getApplication()->input->get('view')) {
    $path = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

// Create the controller
$classname    = 'OpenSimController'.$controller;
$controller   = new $classname();

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
