<?php
/**
 * @module      OpenSim LoginURI (mod_opensim_loginuri)
 * @copyright   Copyright (C) djphil 2017, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');


class mod_opensim_loginuriInstallerScript {
    /**
     * Constructor
     *
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     */
    public function __constructor(JAdapterInstance $adapter) {
    }

    /**
     * Called before any type of action
     *
     * @param   string  $route  Which action is happening (install|uninstall|discover_install)
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function preflight($route, JAdapterInstance $adapter) {
        return TRUE;
    }

    /**
     * Called after any type of action
     *
     * @param   string  $route  Which action is happening (install|uninstall|discover_install)
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function postflight($route, JAdapterInstance $adapter) {
        $this->description();
        return TRUE;
    }

    /**
     * Called on installation
     *
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function install(JAdapterInstance $adapter) {
        $this->description();
        return TRUE;
    }

    /**
     * Called on update
     *
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     *
     * @return  boolean  True on success
     */
    public function update(JAdapterInstance $adapter) {
        // $this->description();
        return TRUE;
    }

    /**
     * Called on uninstallation
     *
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     */
    public function uninstall(JAdapterInstance $adapter) {
        return TRUE;
    }

    public function description() {
        $github = "https://github.com/djphil/mod_opensim_loginuri";
        $github = "<a class='btn btn-success btn-mini' target='_blank' href=".$github."><i class='icon-download'></i> GITHUB</a>\n";
        echo "<p>".JText::_('MOD_OPENSIM_INSTALLER_SCRIPT')."</p>\n";
        echo "<p>".JText::_('MOD_OPENSIM_LOGINURI_GITHUB_URL_DESC')." ".$github."</p>\n";
        echo "<p><img src='".JUri::base(false)."../modules/mod_opensim_loginuri/assets/images/creative_80x15.png' alt='jopensim' height='15' width='80'></p>\n";
        // echo "<p>Additionnal information ...</p>\n";
    }
}
?>
