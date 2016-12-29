<?php
/**
 * @module      OpenSim LoginURI (mod_opensim_loginuri)
 * @copyright   Copyright (C) djphil 2016, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

function getSimulatorSSL($params)
{
    if ($params == 0) return "http://";
    else if ($params == 1) return "https://";
    return "http://";
}

function getLoginURI($params, $a, $b)
{
    if ($params == 0) return getSimulatorSSL($b).$params.":".$a."/";
    else if ($params == 1) return getSimulatorSSL($b).$params.":".$a."/";
    return "http://".$params.":".$a."/";
}
?>