<?php
/***********************************************************************

Class for OpenSimulator Joomla-Module

started 2010-08-30 by FoTo50 (Powerdesign) info@foto50.com
 * @component jOpenSim Component
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

***********************************************************************/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

class opensim {
    public static $version = "0.3.0.10 RCmin";
    // OpenSim Grid database connection
    public $osgriddbhost;
    public $osgriddbuser;
    public $osgriddbpasswd;
    public $osgriddbname;
    public $osgriddbport;
    public $_osgrid_db; // the object containing the connection to the external (grid) DB
    public $connectionerror = FALSE; // Trigger to avoid double error messages
    public $silent = FALSE; // If TRUE, no error messages at all will be enqueued
    // for the remoteAdmin connection
    private $remoteAdminHost;
    private $remoteAdminPort;
    private $remoteAdminPass;
    // define tables and fields here to avoid changing all over if something changes later
    public $userquery;
    public $defaultval = array();
    // table and fields for general info from user
    public $usertable                       = "UserAccounts";
    public $usertable_field_id              = "PrincipalID";
    public $usertable_field_ScopeID         = "ScopeID";
    public $usertable_field_firstname       = "FirstName";
    public $usertable_field_lastname        = "LastName";
    public $usertable_field_email           = "Email";
    public $usertable_field_ServiceURLs     = "ServiceURLs";
    public $usertable_field_created         = "Created";
    public $usertable_field_UserLevel       = "UserLevel";
    public $usertable_field_UserFlags       = "UserFlags";
    public $usertable_field_UserTitle       = "UserTitle";
    // table and fields for info about user in the grid
    public $gridtable                       = "GridUser";
    public $gridtable_field_id              = "UserID";
    public $gridtable_field_homeregion      = "HomeRegionID";
    public $gridtable_field_homeposition    = "HomePosition";
    public $gridtable_field_homelookat      = "HomeLookAt";
    public $gridtable_field_online          = "Online";
    public $gridtable_field_login           = "Login";
    public $gridtable_field_logout          = "Logout";
    // table and fields for user athentification
    public $authtable                       = "auth";
    public $authtable_field_id              = "UUID";
    public $authtable_field_passwordHash    = "passwordHash";
    public $authtable_field_passwordSalt    = "passwordSalt";
    public $authtable_field_webLoginKey     = "webLoginKey";
    public $authtable_field_accountType     = "accountType";
    // table and fields for friends
    public $friendstable                    = "Friends";
    public $friendstable_field_id           = "PrincipalID";
    public $friendstable_friend             = "Friend";
    public $friendstable_flags              = "Flags";
    public $friendstable_offered            = "Offered";
    // region table
    public $regiontable                     = "regions";
    public $regiontable_field_id            = "uuid";
    public $regiontable_regionname          = "regionName";
    public $regiontable_serverIP            = "serverIP";
    public $regiontable_serverPort          = "serverPort";
    public $regiontable_locationX           = "locX";
    public $regiontable_locationY           = "locY";
    // presence table
    public $presencetable                   = "presence";
    public $presencetable_regionid          = "RegionID";

    public function __construct($osdbhost,$osdbuser,$osdbpasswd,$osdbname,$osdbport = '3306', $silent = FALSE) {
        $this->osgriddbhost     = $osdbhost;
        $this->osgriddbuser     = $osdbuser;
        $this->osgriddbpasswd   = $osdbpasswd;
        $this->osgriddbname     = $osdbname;
        $this->osgriddbport     = $osdbport;
        $this->silent           = $silent;
        $this->connect2osgrid();
    }

    public function connect2osgrid() {
        // check if another port is used
        if($this->osgriddbport && $this->osgriddbport != "3306") $externalhost = $this->osgriddbhost.":".$this->osgriddbport;
        else $externalhost = $this->osgriddbhost;

        $option['driver']   = 'mysql';					// Database driver name
        $option['host']     = $externalhost;			// Database host name and port
        $option['user']     = $this->osgriddbuser;		// User for database authentication
        $option['password'] = $this->osgriddbpasswd;	// Password for database authentication
        $option['database'] = $this->osgriddbname;		// Database name
        $option['prefix']   = '';						// Database prefix (may be empty)

        try {
            $osgrid_db = JDatabaseDriver::getInstance($option);
            $this->_osgrid_db = $osgrid_db;
            $test = $osgrid_db->connect();
            return $this->_osgrid_db;
        } catch (Exception $e) {
            if($this->silent === FALSE) {
                if ($this->connectionerror === FALSE) {
                    $message = $e->getMessage();
                    $errormsg = JText::sprintf('JOPENSIM_ERROR_DB_MIN',$message);
                    JFactory::getApplication()->enqueueMessage($errormsg,"error");
                    $this->connectionerror = TRUE;
                }
            }
            $this->_osgrid_db = null;
            return null;
        }
    }

    public function countActiveUsers() {
        if(empty($this->_osgrid_db)) return FALSE;

        $query = sprintf("SELECT COUNT(DISTINCT %1\$s.%2\$s) AS anzahl FROM %1\$s WHERE %1\$s.%3\$s >= 0",
        $this->usertable,
        $this->usertable_field_id,
        $this->usertable_field_UserLevel);
        $this->_osgrid_db->setQuery($query);
        $this->_osgrid_db->query();

        if($this->_osgrid_db->getNumRows() == 1) { // The region seems to be registered, lets get up2date data from there
            $count	= $this->_osgrid_db->loadAssoc();
            return $count['anzahl'];
        } else {
            return FALSE;
        }
    }

    // HG User count
    public function countHypergridUsers()
    {
        if (empty($this->_osgrid_db)) return FALSE;

        $zero_uuid = "00000000-0000-0000-0000-000000000000";

        $query = sprintf("SELECT COUNT(DISTINCT %1\$s.%2\$s) AS number FROM %1\$s WHERE %2\$s = $zero_uuid",
        $this->presencetable,
        $this->presencetable_regionid,
        $this->_osgrid_db->setQuery($query);
        $this->_osgrid_db->query();

        // The user region_id seems to be zero uuid
        // rowCount();
        if ($this->_osgrid_db->getNumRows() > 0)
        {
            $count = $this->_osgrid_db->loadAssoc();
            return $count['number'];
        } else {
            return FALSE;
        }
    }

    public function externalDBerror() {
        // JFactory::getApplication()->enqueueMessage(JText::_(ERROR_NOSIMDB),"error");
        return FALSE;
    }

    public function __destruct() {
    }
}
?>