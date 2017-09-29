<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Routing class of com_opensim
 *
 * @since  3.3
 */
class OpensimRouter extends JComponentRouterView {
	/**
	 * jOpenSim Component router constructor
	 *
	 * @param   JApplicationCms  $app   The application object
	 * @param   JMenu            $menu  The menu object to work with
	 */
	public function __construct($app = null, $menu = null) {
		$this->registerView(new JComponentRouterViewconfiguration('profile'));
		parent::__construct($app, $menu);
	}

	public function build(&$query) {
		$segments	= array();
		if (isset($query['view'])) {
			$segments[] = $query['view'];
			switch($query['view']) {
				case "profile":
					$segments[] = $query['uid'];
					unset($query['uid']);
				break;
				case "inworld":
					$segments[] = $query['task'];
					unset($query['task']);
				break;
				case "events":
					if(isset($query['task'])) {
						$segments[] = $query['task'];
						if($query['task'] == "deleteevent" && isset($query['eventid'])) {
							$segments[] = $query['eventid'];
							unset($query['eventid']);
						}
//						if($query['task'] == "inserterror") {
//							$segments[] = $query['eventname'];
//							$segments[] = $query['eventdate'];
//							$segments[] = $query['eventtime'];
//							$segments[] = $query['eventtimezone'];
//							$segments[] = $query['eventduration'];
//							$segments[] = $query['eventlocation'];
//							$segments[] = $query['eventcategory'];
//							$segments[] = $query['covercharge'];
//							$segments[] = urlencode($query['description']);
//							$segments[] = $query['eventflags'];
//							unset($query['eventname'],$query['eventdate'],$query['eventtime'],$query['eventtimezone'],$query['eventduration'],$query['eventlocation'],$query['eventcategory'],$query['covercharge'],$query['description'],$query['eventflags']);
//						}
						unset($query['task']);
					}
				break;
			}
			unset($query['view']);
		}
		return $segments;
	}

	public function parse(&$segments) {
		$vars = array();
		switch($segments[0]) {
			case 'profile':
				$vars['view']	= 'profile';
				$vars['uid']	= $segments[1];
			break;
			case 'inworld':
				$vars['view']	= 'inworld';
				$vars['task']	= $segments[1];
			break;
			case "events":
				$vars['view']	= 'events';
				if(isset($segments[1])) {
					$vars['task']	= $segments[1];
					if($segments[1] == "deleteevent") {
						$vars['eventid']	= $segments[2];
					}
//					if($segments[1] == "inserterror") {
//						$vars['eventname']		= $segments[2];
//						$vars['eventdate']		= $segments[3];
//						$vars['eventtime']		= $segments[4];
//						$vars['eventtimezone']	= $segments[5];
//						$vars['eventduration']	= $segments[6];
//						$vars['eventlocation']	= $segments[7];
//						$vars['eventcategory']	= $segments[8];
//						$vars['covercharge']	= $segments[9];
//						$vars['description']	= urldecode($segments[10]);
//						$vars['eventflags']		= $segments[11];
//						error_log("description: ".$vars['description']);
//					}
				}
			break;
		}
		return $vars;
	}
}