<?php
/**
 * @module      OpenSim LoginURI (mod_opensim_loginuri)
 * @copyright   Copyright (C) djphil 2017, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

function getjSonStatsSSL($params)
{
    if ($params == 0) return "http://";
    else if ($params == 1) return "https://";
    return "http://";
}

function getjSonStatsURI($a, $b, $c, $d)
{
    if ($params == 0) return getjSonStatsSSL($c).$a.":".$b."/".$d."/";
    else if ($params == 1) return getjSonStatsSSL($c).$a.":".$b."/".$d."/";
    return "http://".$a.":".$b."/".$d."/";
}

/*
http://opensimulator.org/mantis/view.php?id=7632

Frame Statistics Values
The labels of the Frame Statistics values shown by the console command "show stats" are a bit cryptic. 
Here is a list of the meanings of these values:

Dilatn - time dilation
SimFPS - sim FPS
PhyFPS - physics FPS
AgntUp - # of agent updates
RootAg - # of root agents
ChldAg - # of child agents
Prims  - # of total prims
AtvPrm - # of active prims
AtvScr - # of active scripts
ScrLPS - # of script lines per second
PktsIn - # of in packets per second
PktOut - # of out packets per second
PendDl - # of pending downloads
PendUl - # of pending uploads
UnackB - # of unacknowledged bytes
TotlFt - total frame time
NetFt  - net frame time
PhysFt - physics frame time
OthrFt - other frame time
AgntFt - agent frame time
ImgsFt - image frame time
*/
?>
