<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined("DS")) define("DS",DIRECTORY_SEPARATOR);

class com_opensimInstallerScript {
	public $deletewarning = FALSE;
	public $redirect2opensim = "index.php?option=com_config&view=component&component=com_opensim&path=&return=aW5kZXgucGhwP29wdGlvbj1jb21fb3BlbnNpbQ==";

	public function install($parent) {
		$db			= JFactory::getDBO();
		$version	= $db->getVersion();

		// create the tables if they dont exist yet

		$createquery = array();

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_clientinfo` (
		  `PrincipalID` char(36) NOT NULL,
		  `userName` varchar(255) DEFAULT NULL,
		  `grid` varchar(255) DEFAULT NULL,
		  `remoteip` varchar(50) DEFAULT NULL,
		  `lastseen` datetime DEFAULT NULL,
		  `from` char(2) DEFAULT NULL,
		  PRIMARY KEY (`PrincipalID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		if($version < "5.6") {
			$createquery[] = "
			CREATE TABLE IF NOT EXISTS `#__opensim_group` (
			  `GroupID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `Name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `Charter`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			  `InsigniaID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `FounderID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `MembershipFee`  int(11) NOT NULL DEFAULT 0 ,
			  `OpenEnrollment`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `ShowInList`  tinyint(1) NOT NULL DEFAULT 0 ,
			  `AllowPublish`  tinyint(1) NOT NULL DEFAULT 0 ,
			  `MaturePublish`  tinyint(1) NOT NULL DEFAULT 0 ,
			  `OwnerRoleID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  PRIMARY KEY (`GroupID`),
			  UNIQUE INDEX `Name` USING BTREE (`Name`) ,
			  FULLTEXT INDEX `Name_2` (`Name`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";
		} else {
			$createquery[] = "
			CREATE TABLE IF NOT EXISTS `#__opensim_group` (
			  `GroupID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `Name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `Charter`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			  `InsigniaID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `FounderID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `MembershipFee`  int(11) NOT NULL DEFAULT 0 ,
			  `OpenEnrollment`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  `ShowInList`  tinyint(1) NOT NULL DEFAULT 0 ,
			  `AllowPublish`  tinyint(1) NOT NULL DEFAULT 0 ,
			  `MaturePublish`  tinyint(1) NOT NULL DEFAULT 0 ,
			  `OwnerRoleID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
			  PRIMARY KEY (`GroupID`),
			  UNIQUE INDEX `Name` USING BTREE (`Name`) ,
			  FULLTEXT INDEX `Name_2` (`Name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";
		}


		$createquery[] = "
		CREATE TABLE IF NOT EXISTS `#__opensim_groupactive` (
		  `AgentID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `ActiveGroupID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  PRIMARY KEY (`AgentID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "
		CREATE TABLE IF NOT EXISTS `#__opensim_groupinvite` (
		  `InviteID`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `GroupID`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `RoleID`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `AgentID`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `TMStamp`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
		  PRIMARY KEY (`InviteID`),
		  UNIQUE INDEX `GroupID` USING BTREE (`GroupID`, `RoleID`, `AgentID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "
		CREATE TABLE IF NOT EXISTS `#__opensim_groupmembership` (
		  `GroupID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `AgentID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `SelectedRoleID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `Contribution`  int(11) NOT NULL DEFAULT 0 ,
		  `ListInProfile`  int(11) NOT NULL DEFAULT 1 ,
		  `AcceptNotices`  int(11) NOT NULL DEFAULT 1 ,
		  PRIMARY KEY (`GroupID`, `AgentID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "
		CREATE TABLE IF NOT EXISTS `#__opensim_groupnotice` (
		  `GroupID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `NoticeID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `Timestamp`  int(10) UNSIGNED NOT NULL DEFAULT 0 ,
		  `FromName`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `Subject`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `Message`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		  `BinaryBucket`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		  PRIMARY KEY (`GroupID`, `NoticeID`),
		  INDEX `Timestamp` USING BTREE (`Timestamp`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "
		CREATE TABLE IF NOT EXISTS `#__opensim_grouprole` (
		  `GroupID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `RoleID`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `Name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `Description`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `Title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `Powers`  bigint(20) UNSIGNED NOT NULL DEFAULT 0 ,
		  PRIMARY KEY (`GroupID`, `RoleID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "
		CREATE TABLE IF NOT EXISTS `#__opensim_grouprolemembership` (
		  `GroupID`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `RoleID`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `AgentID`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  PRIMARY KEY (`GroupID`, `RoleID`, `AgentID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "
		CREATE TABLE IF NOT EXISTS `#__opensim_inworldident` (
		  `joomlaID` int(11) unsigned NOT NULL,
		  `opensimID` varchar(36) DEFAULT NULL,
		  `inworldIdent` varchar(36) NOT NULL,
		  `created` datetime NOT NULL,
		  PRIMARY KEY (`joomlaID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_mapinfo` (
		  `regionUUID`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
		  `articleId`  int(11) UNSIGNED NULL DEFAULT NULL ,
		  `hidemap` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `public` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`regionUUID`),
		  INDEX `articleId` USING BTREE (`articleId`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_money_customfees` (
		  `PrincipalID` char(36) NOT NULL,
		  `uploadfee` smallint(1) unsigned DEFAULT NULL,
		  `groupfee` smallint(1) unsigned DEFAULT NULL,
		  PRIMARY KEY (`PrincipalID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_moneybalances` (
		  `user` varchar(128) NOT NULL,
		  `balance` int(10) NOT NULL,
		  `status` tinyint(2) DEFAULT NULL,
		  PRIMARY KEY (`user`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_moneygridbalance` (
		  `timestamp` int(11) NOT NULL,
		  `gridbalance` int(7) DEFAULT NULL,
		  PRIMARY KEY (`timestamp`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_moneysessions` (
		  `sessionid` char(36) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.7';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_moneytransactions` (
		  `UUID` varchar(36) NOT NULL,
		  `sender` varchar(128) NOT NULL,
		  `receiver` varchar(128) NOT NULL,
		  `amount` int(10) NOT NULL,
		  `objectUUID` varchar(36) DEFAULT NULL,
		  `regionHandle` varchar(36) NOT NULL,
		  `type` int(10) NOT NULL,
		  `time` int(11) NOT NULL,
		  `secure` varchar(36) NOT NULL,
		  `status` tinyint(1) NOT NULL,
		  `description` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`UUID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_offlinemessages` (
		  `imSessionID` varchar(36) NOT NULL DEFAULT '',
		  `fromAgentID` varchar(36) DEFAULT NULL,
		  `fromAgentName` varchar(128) DEFAULT NULL,
		  `toAgentID` varchar(36) DEFAULT NULL,
		  `fromGroup` varchar(5) DEFAULT NULL,
		  `message` text,
		  `remoteip` varchar(15) default NULL,
		  `sent` datetime NOT NULL,
		  KEY `from` (`fromAgentID`),
		  KEY `to` (`toAgentID`),
		  KEY `session` (`imSessionID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_partnerrequests` (
		  `from` char(36) NOT NULL,
		  `to` char(36) NOT NULL,
		  `requesttime` datetime DEFAULT NULL,
		  `updated` datetime DEFAULT NULL,
		  `status` tinyint(2) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`from`,`to`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_search_allparcels` (
		  `regionUUID` varchar(255) NOT NULL,
		  `regionName` varchar(255) NOT NULL,
		  `parcelname` varchar(255) NOT NULL,
		  `ownerUUID` char(36) NOT NULL DEFAULT '00000000-0000-0000-0000-000000000000',
		  `ownerName` varchar(255) NOT NULL,
		  `groupUUID` char(36) NOT NULL DEFAULT '00000000-0000-0000-0000-000000000000',
		  `landingpoint` varchar(255) NOT NULL,
		  `parcelUUID` char(36) NOT NULL DEFAULT '00000000-0000-0000-0000-000000000000',
		  `infoUUID` char(36) NOT NULL DEFAULT '00000000-0000-0000-0000-000000000000',
		  `parcelarea` int(11) NOT NULL,
		  PRIMARY KEY (`parcelUUID`,`regionUUID`),
		  KEY `regionUUID` (`regionUUID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.7';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_search_events` (
		  `eventid` int(11) NOT NULL AUTO_INCREMENT,
		  `owneruuid` char(40) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `creatoruuid` char(40) NOT NULL,
		  `category` int(2) NOT NULL,
		  `description` text NOT NULL,
		  `dateUTC` int(10) NOT NULL,
		  `duration` int(10) NOT NULL,
		  `covercharge` int(10) NOT NULL,
		  `coveramount` int(10) NOT NULL,
		  `simname` varchar(255) NOT NULL,
		  `globalPos` varchar(255) NOT NULL,
		  `parcelUUID` char(40) NOT NULL,
		  `parcelName` varchar(255) DEFAULT NULL,
		  `landingpoint` varchar(35) DEFAULT NULL,
		  `eventflags` int(10) NOT NULL,
		  `mature` enum('true','false') NOT NULL,
		  PRIMARY KEY (`eventid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_search_hostsregister` (
		  `host` varchar(255) NOT NULL,
		  `port` int(5) NOT NULL,
		  `register` int(10) NOT NULL,
		  `lastcheck` int(10) NOT NULL,
		  `failcounter` int(1) NOT NULL,
		  PRIMARY KEY (`host`,`port`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_search_objects` (
		  `objectuuid` varchar(36) NOT NULL,
		  `parceluuid` varchar(36) NOT NULL,
		  `location` varchar(255) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `description` varchar(255) NOT NULL,
		  `regionuuid` varchar(255) NOT NULL DEFAULT '',
		  PRIMARY KEY (`objectuuid`,`parceluuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_search_options` (
		  `searchoption` varchar(50) NOT NULL,
		  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
		  `reiheintern` tinyint(2) unsigned NOT NULL DEFAULT '0',
		  `reihe` tinyint(2) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`searchoption`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_search_parcels` (
		  `regionUUID` varchar(36) NOT NULL,
		  `parcelname` varchar(255) NOT NULL,
		  `parcelUUID` varchar(36) NOT NULL,
		  `landingpoint` varchar(255) NOT NULL,
		  `description` varchar(255) NOT NULL,
		  `searchcategory` varchar(50) NOT NULL,
		  `build` enum('true','false') NOT NULL,
		  `script` enum('true','false') NOT NULL,
		  `public` enum('true','false') NOT NULL,
		  `dwell` float NOT NULL DEFAULT '0',
		  `infouuid` varchar(255) NOT NULL DEFAULT '',
		  PRIMARY KEY (`regionUUID`,`parcelUUID`),
		  KEY `name` (`parcelname`),
		  KEY `description` (`description`),
		  KEY `searchcategory` (`searchcategory`),
		  KEY `dwell` (`dwell`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_search_parcelsales` (
		  `regionUUID` varchar(36) NOT NULL,
		  `parcelname` varchar(255) NOT NULL,
		  `parcelUUID` varchar(72) NOT NULL,
		  `area` int(6) NOT NULL,
		  `saleprice` int(11) NOT NULL,
		  `landingpoint` varchar(255) NOT NULL,
		  `infoUUID` char(36) NOT NULL DEFAULT '00000000-0000-0000-0000-000000000000',
		  `dwell` int(11) NOT NULL,
		  `parentestate` int(11) NOT NULL DEFAULT '1',
		  `mature` varchar(32) NOT NULL DEFAULT 'false',
		  PRIMARY KEY (`regionUUID`,`parcelUUID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_search_regions` (
		  `regionname` varchar(255) NOT NULL,
		  `regionuuid` varchar(255) NOT NULL,
		  `regionhandle` varchar(255) NOT NULL,
		  `url` varchar(255) NOT NULL,
		  `owner` varchar(255) NOT NULL,
		  `owneruuid` varchar(255) NOT NULL,
		  `locX` int(10) DEFAULT NULL,
		  `locY` int(10) DEFAULT NULL,
		  PRIMARY KEY (`regionuuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_terminals` (
		  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
		  `terminalName` varchar(50) DEFAULT NULL,
		  `terminalDescription` text,
		  `terminalKey` varchar(36) DEFAULT NULL,
		  `terminalUrl` varchar(255) DEFAULT NULL,
		  `region` varchar(100) DEFAULT NULL,
		  `location_x` smallint(3) unsigned DEFAULT NULL,
		  `location_y` smallint(3) unsigned DEFAULT NULL,
		  `location_z` smallint(3) unsigned DEFAULT NULL,
		  `staticLocation` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_useravatars` (
		  `userid` varchar(72) NOT NULL,
		  `avatarname` varchar(255) NOT NULL,
		  `reihe` int(1) unsigned DEFAULT NULL,
		  PRIMARY KEY (`userid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_userclassifieds` (
		  `classifieduuid` char(36) NOT NULL,
		  `creatoruuid` char(36) NOT NULL,
		  `creationdate` int(20) NOT NULL,
		  `expirationdate` int(20) NOT NULL,
		  `category` varchar(20) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `description` text NOT NULL,
		  `parceluuid` char(36) NOT NULL,
		  `parentestate` int(11) NOT NULL,
		  `snapshotuuid` char(36) NOT NULL,
		  `simname` varchar(255) NOT NULL,
		  `posglobal` varchar(255) NOT NULL,
		  `parcelname` varchar(255) NOT NULL,
		  `classifiedflags` int(8) NOT NULL,
		  `priceforlisting` int(5) NOT NULL,
		  PRIMARY KEY (`classifieduuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_userlevel` (
		  `userlevel` smallint(3) NOT NULL,
		  `description` varchar(150) NOT NULL,
		  PRIMARY KEY (`userlevel`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_usernotes` (
		  `avatar_id` varchar(36) NOT NULL,
		  `target_id` varchar(36) NOT NULL,
		  `notes` text NOT NULL,
		  PRIMARY KEY (`avatar_id`,`target_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_userpicks` (
		  `pickuuid` varchar(36) NOT NULL,
		  `creatoruuid` varchar(36) NOT NULL,
		  `toppick` enum('true','false') NOT NULL,
		  `parceluuid` varchar(36) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `description` text NOT NULL,
		  `snapshotuuid` varchar(36) NOT NULL,
		  `user` varchar(255) NOT NULL,
		  `originalname` varchar(255) NOT NULL,
		  `simname` varchar(255) NOT NULL,
		  `posglobal` varchar(255) NOT NULL,
		  `sortorder` int(2) NOT NULL,
		  `enabled` enum('true','false') NOT NULL,
		  PRIMARY KEY (`pickuuid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_userprofile` (
		  `avatar_id` varchar(36) NOT NULL,
		  `partner` varchar(36) NOT NULL,
		  `image` varchar(36) NOT NULL,
		  `aboutText` text NOT NULL,
		  `allowPublish` binary(1) NOT NULL,
		  `maturePublish` binary(1) NOT NULL,
		  `url` varchar(255) NOT NULL,
		  `wantToMask` int(3) NOT NULL,
		  `wantToText` text NOT NULL,
		  `skillsMask` int(3) NOT NULL,
		  `skillsText` text NOT NULL,
		  `languagesText` text NOT NULL,
		  `firstLifeImage` varchar(36) NOT NULL,
		  `firstLifeText` text NOT NULL,
		  PRIMARY KEY (`avatar_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_userrelation` (
		  `joomlaID` int(11) NOT NULL,
		  `opensimID` char(36) NOT NULL,
		  PRIMARY KEY (`joomlaID`,`opensimID`)
		) ENGINE=InnoDB CHARACTER SET `utf8` COMMENT='jOpenSim Rev. 0.3.0.0';";

		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_usersettings` (
		  `uuid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
		  `im2email`  tinyint(1) UNSIGNED NOT NULL ,
		  `visible`  tinyint(1) UNSIGNED NOT NULL ,
		  `timezone` varchar(150) DEFAULT NULL,
		  PRIMARY KEY (`uuid`)
		) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";


		$createquery[] = "CREATE TABLE IF NOT EXISTS `#__opensim_usertemp` (
		  `joomlaid` int(11) NOT NULL,
		  `firstname` varchar(255) NOT NULL,
		  `lastname` varchar(255) NOT NULL,
		  `password` varchar(64) NOT NULL,
		  `email` varchar(255) NOT NULL,
		  PRIMARY KEY (`joomlaid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='jOpenSim Rev. 0.3.0.0';";



		foreach($createquery AS $tablequery) {
			$db->setQuery($tablequery);
			$db->execute();
		}

		// We may have some tables not needed anymore
		$droptables		= array();
		$droptables[]	= "#__opensim_config";
		$droptables[]	= "#__opensim_moneysettings";
		$droptables[]	= "#__opensim_search_classifieds";
		$droptables[]	= "#__opensim_search_popularplaces";

		foreach($droptables AS $table) {
			$dropquery = sprintf("DROP TABLE IF EXISTS `%s`",$table);
			$db->setQuery($dropquery);
			$db->execute();
		}

		// since 0.3.0.0 for money tables InnoDB is required (save transactions), send a warning if not
		$this->checkTableEngine();

		// We probably need to alter already existing older tables
		$this->newfields();
		$this->changefields();
		$this->oldfields();

		$this->tableContents(); // Some tables require special content

		$this->createFolders(); // Check for existing folders and if not present create them

		// to avoid network error messages, create the override css file only if it does not exist
		if(!is_file(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."opensim.override.css")) {
			touch(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."opensim.override.css");
		}

		$parent->getParent()->setRedirectURL($this->redirect2opensim);
	}

	public function update($parent) {
		$this->redirect2opensim = "index.php?option=com_opensim";
		$this->updatePrimary();
		$this->install($parent);
	}

	public function updatePrimary() {
		$db		= JFactory::getDBO();
		$query	= "ALTER TABLE `#__opensim_search_allparcels` DROP PRIMARY KEY, ADD PRIMARY KEY (`parcelUUID`, `regionUUID`);";
		$db->setQuery($query);
		$db->execute();
	}

	public function uninstall($parent) {
		if(is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim")) {
			if(is_file(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR.".keep")) {
				$filecontent = trim(strtolower(file_get_contents(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR.".keep")));
				if($filecontent != "database") {
					$this->removeDBtables();
					echo "jOpenSim database tables removed<br />\n";
				}
			} else {
				$this->removeDBtables();
				$this->removeFolders();
				if($this->deletewarning === TRUE) {
					JFactory::getApplication()->enqueueMessage('Error while removing image folders of jOpenSim, please check manually', 'warning');
				} else {
					echo "jOpenSim image folders removed";
				}
			}
		} else {
			$this->removeDBtables();
		}
		if(is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."user".DIRECTORY_SEPARATOR."jopensimregister")) {
			JFactory::getApplication()->enqueueMessage('After uninstalling jOpenSim, you also might want to uninstall the plugin jOpenSimRegister since this remains useless in your Joomla installation. But you need at least to disable it to avoid registration failures', 'warning');
		}
		if(is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."mod_opensim_friends")) {
			JFactory::getApplication()->enqueueMessage('After uninstalling jOpenSim, you also might want to uninstall module jOpenSimFriends since this remains useless in your Joomla installation.', 'warning');
		}
		if(is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."mod_opensim_gridstatus")) {
			JFactory::getApplication()->enqueueMessage('Allthough jOpenSimGridStatus can run without jOpenSim you might also want to uninstall it as well.', 'warning');
		}
	}

	// Helper methods
	// adding new fields in existing tables
	public function newfields() {
		$db = JFactory::getDBO();

		//$newfields['tblname'][]	= array('name' => 'fieldname', 'args' => 'arguments of field');
		$newfields = array();

		$newfields['opensim_offlinemessages'][]		= array('name' => 'remoteip',		'args' => 'varchar(15) NULL AFTER `message`');
		$newfields['opensim_offlinemessages'][]		= array('name' => 'sent',			'args' => 'datetime NOT NULL AFTER `remoteip`');

		$newfields['opensim_mapinfo'][]				= array('name' => 'hidemap',		'args' => 'tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `articleId`');
		$newfields['opensim_mapinfo'][]				= array('name' => 'public',			'args' => 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `hidemap`');

		$newfields['opensim_usersettings'][]		= array('name' => 'timezone',		'args' => 'varchar(150) NOT NULL AFTER `visible`');

		$newfields['opensim_search_allparcels'][]	= array('name' => 'regionName',		'args' => 'varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `regionUUID`');
		$newfields['opensim_search_allparcels'][]	= array('name' => 'ownerName',		'args' => 'varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `ownerUUID`');

		$newfields['opensim_search_events'][]		= array('name' => 'parcelUUID',		'args' => 'char(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `simname`');
		$newfields['opensim_search_events'][]		= array('name' => 'parcelName',		'args' => 'varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL AFTER `parcelUUID`');
		$newfields['opensim_search_events'][]		= array('name' => 'landingpoint',	'args' => 'varchar(35) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL AFTER `parcelName`');

		// are there any new fields? lets add them
		foreach($newfields AS $table => $tablenewfields) {
			$query = "DESCRIBE #__".$table;
			$db->setQuery($query);
			$existingfields = $db->loadColumn();
			foreach($tablenewfields AS $newfield) {
				if(!in_array($newfield['name'],$existingfields)) {
					$query = sprintf("ALTER TABLE #__%s ADD COLUMN `%s` %s",$table,$newfield['name'],$newfield['args']);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}

	// are there any fields to be changed?
	public function changefields() {
		$db = JFactory::getDBO();

		// changing fields in existing tables
		//$changefields['tblname'][]	= array('name' => 'fieldname', 'args' => 'arguments of field');
		$changefields = array();

		$changefields['opensim_userprofile'][]		= array('name' => 'wantToMask',		'args' => '`wantmask`  int(3) NOT NULL AFTER `url`');
		$changefields['opensim_userprofile'][]		= array('name' => 'wantToText',		'args' => '`wanttext`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `wantmask`');
		$changefields['opensim_userprofile'][]		= array('name' => 'skillsMask',		'args' => '`skillsmask`  int(3) NOT NULL AFTER `wanttext`');
		$changefields['opensim_userprofile'][]		= array('name' => 'skillsText',		'args' => '`skillstext`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `skillsmask`');
		$changefields['opensim_userprofile'][]		= array('name' => 'languagesText',	'args' => '`languages`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `skillstext`');

		$changefields['opensim_search_events'][]	= array('name' => 'eventid',		'args' => '`eventid`  int(11) NOT NULL AUTO_INCREMENT FIRST');
		$changefields['opensim_search_events'][]	= array('name' => 'globalPos',		'args' => '`globalPos`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `simname`');

		foreach($changefields AS $table => $changefield) {
			$query = "DESCRIBE #__".$table;
			$db->setQuery($query);
			$existingfields = $db->loadColumn();
			foreach($changefield AS $field) {
				if(in_array($field['name'],$existingfields)) {
					$query = sprintf("ALTER TABLE #__%s CHANGE COLUMN `%s` %s",$table,$field['name'],$field['args']);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}

	// keep the DB clean ... delete not anymore needed fields in case they still exist from a previous version :)
	public function oldfields() {
		$db = JFactory::getDBO();

		// removing fields of existing tables
		$oldfields = array();
		// $oldfields = array('opensim_settings' => array('gridserver_host'));
		foreach($oldfields AS $table => $oldfield) {
			$query = "DESCRIBE #__".$table;
			$db->setQuery($query);
			$existingfields = $db->loadColumn();
			foreach($existingfields AS $field) {
				if(in_array($field,$oldfield)) {
					$query = sprintf("ALTER TABLE #__%s DROP COLUMN `%s`",$table,$field);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}

	public function checkTableEngine() {
		// For some reason, this query does not return anything in Joomla :(
		// $query = "SHOW TABLE STATUS WHERE Name LIKE '#__opensim_money%'";
		$searchpattern = "/ENGINE=([^ ]*)/";
		$moneytables = array(	'#__opensim_money_customfees',
								'#__opensim_moneybalances',
								'#__opensim_moneygridbalance',
								'#__opensim_moneytransactions');
		$db = JFactory::getDBO();
		$tables2change = array();
		foreach($moneytables AS $table) {
			$query = "show create table ".$db->quoteName($table);
			$db->setQuery($query);
			$db->execute();
			$tableinfo = $db->loadAssoc();
			preg_match($searchpattern,$tableinfo['Create Table'],$treffer);
			if($treffer[1] != "InnoDB") {
				$tables2change[] = $table;
			}
		}
		if(count($tables2change) > 0) {
			$warning = "You have some database tables not set to the required InnoDB-Engine (".implode(",",$tables2change).")! Please convert them manually to InnoDB. jOpenSim might not work proper when these tables use a different engine!";
			JFactory::getApplication()->enqueueMessage($warning, 'warning');
		}
	}

	public function tableContents() {
		$db = JFactory::getDBO();

		// Check, if #__opensim_userlevel is new
		$query = "SELECT * FROM #__opensim_userlevel";
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows < 6) { // This table seems to be new or incomplete, insert/update initial values
			$values[0]['level']			= -3;
			$values[0]['description']	= "JOPENSIM_USERLEVEL_AVATAR";
			$values[1]['level']			= -2;
			$values[1]['description']	= "JOPENSIM_USERLEVEL_BANKER";
			$values[2]['level']			= -1;
			$values[2]['description']	= "JOPENSIM_USERLEVEL_DISABLED";
			$values[3]['level']			= 0;
			$values[3]['description']	= "JOPENSIM_USERLEVEL_REGULAR";
			$values[4]['level']			= 100;
			$values[4]['description']	= "JOPENSIM_USERLEVEL_MAINTENANCE";
			$values[5]['level']			= 200;
			$values[5]['description']	= "JOPENSIM_USERLEVEL_GOD";
			foreach($values AS $value) {
				$query = sprintf("INSERT INTO #__opensim_userlevel (userlevel,description) VALUES ('%1\$d','%2\$s') ON DUPLICATE KEY UPDATE userlevel = '%2\$s'",$value['level'],$value['description']);
				$db->setQuery($query);
				$db->execute();
			}
		}


		// Check, if #__opensim_search_options is new
		$query = "DELETE FROM #__opensim_search_options WHERE #__opensim_search_options.searchoption = 'JOPENSIM_SEARCH_POPULARPLACES'";
		$db->setQuery($query);
		$db->execute();
		$query = "SELECT * FROM #__opensim_search_options";
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows < 6) { // This table seems to be new or incomplete, insert initial values (or dummy update them)
			$svalues[0]['searchoption']	= "JOPENSIM_SEARCH_OBJECTS";
			$svalues[0]['enabled']		= "1";
			$svalues[0]['reiheintern']	= "1";
			$svalues[0]['reihe']		= "0";
			$svalues[1]['searchoption']	= "JOPENSIM_SEARCH_PARCELS";
			$svalues[1]['enabled']		= "1";
			$svalues[1]['reiheintern']	= "2";
			$svalues[1]['reihe']		= "1";
			$svalues[2]['searchoption']	= "JOPENSIM_SEARCH_PARCELSALES";
			$svalues[2]['enabled']		= "1";
			$svalues[2]['reiheintern']	= "3";
			$svalues[2]['reihe']		= "2";
			$svalues[3]['searchoption']	= "JOPENSIM_SEARCHCLASSIFIED";
			$svalues[3]['enabled']		= "1";
			$svalues[3]['reiheintern']	= "4";
			$svalues[3]['reihe']		= "3";
			$svalues[4]['searchoption']	= "JOPENSIM_SEARCHEVENTS";
			$svalues[4]['enabled']		= "1";
			$svalues[4]['reiheintern']	= "5";
			$svalues[4]['reihe']		= "4";
			$svalues[5]['searchoption']	= "JOPENSIM_SEARCH_REGIONS";
			$svalues[5]['enabled']		= "1";
			$svalues[5]['reiheintern']	= "6";
			$svalues[5]['reihe']		= "5";
			foreach($svalues AS $value) {
				$query = sprintf("INSERT INTO #__opensim_search_options (`searchoption`,`enabled`,`reiheintern`,`reihe`) VALUES ('%1\$s','%2\$d','%3\$d','%4\$d') ON DUPLICATE KEY UPDATE `searchoption` = '%1\$s'",
									$value['searchoption'],
									$value['enabled'],
									$value['reiheintern'],
									$value['reihe']);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	public function createFolders() {
		// Check for image folders and if not existing, create them
		if(!is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim")) {
			if(!is_writable(JPATH_SITE.DIRECTORY_SEPARATOR."images")) {
				$application = JFactory::getApplication();
				$application->enqueueMessage(JText::sprintf('JOPENSIM_WRITABLE_ERROR',JPATH_SITE.DIRECTORY_SEPARATOR."images"), 'warning');
				$application->enqueueMessage(JText::sprintf('JOPENSIM_COULDNT_CREATE_FOLDER',JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim"), 'warning');
			} else {
				mkdir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim");
			}
		}

		if(!is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."avatars")) {
			if(!is_writable(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim")) {
				$application = JFactory::getApplication();
				$application->enqueueMessage(JText::sprintf('JOPENSIM_WRITABLE_ERROR',JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim"), 'warning');
				$application->enqueueMessage(JText::sprintf('JOPENSIM_COULDNT_CREATE_FOLDER',JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."avatars"), 'warning');
			} else {
				mkdir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."avatars");
			}
		}

		if(!is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."profiles")) {
			if(!is_writable(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim")) {
				$application = JFactory::getApplication();
				$application->enqueueMessage(JText::sprintf('JOPENSIM_WRITABLE_ERROR',JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim"), 'warning');
				$application->enqueueMessage(JText::sprintf('JOPENSIM_COULDNT_CREATE_FOLDER',JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."avatars"), 'warning');
			} else {
				mkdir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."profiles");
			}
		}

		if(!is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."regions")) {
			if(!is_writable(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim")) {
				$application = JFactory::getApplication();
				$application->enqueueMessage(JText::sprintf('JOPENSIM_WRITABLE_ERROR',JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim"), 'warning');
				$application->enqueueMessage(JText::sprintf('JOPENSIM_COULDNT_CREATE_FOLDER',JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."regions"), 'warning');
			} else {
				mkdir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."regions");
			}
		}
	}

	public function removeFolders() {
		if(is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."regions")) {
			$this->rmdirr(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."regions");
		}
		if(is_dir(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."avatars")) {
			$this->rmdirr(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim".DIRECTORY_SEPARATOR."avatars");
		}
		$this->rmdirr(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."jopensim");
	}

	public function rmdirr($dirname) {
		// adopted from http://www.catswhocode.com/blog/snippets/delete-directory-in-php
		/**
		 * Delete a file, or a folder and its contents (recursive algorithm)
		 *
		 * @author      Aidan Lister <aidan@php.net>
		 * @version     1.0.3
		 * @link        http://aidanlister.com/repos/v/function.rmdirr.php
		 * @param       string   $dirname    Directory to delete
		 * @return      bool     Returns TRUE on success, FALSE on failure
		 */
		// many thanks :)

	    // Sanity check
	    if (!file_exists($dirname)) {
	        return false;
	    }

	    // Simple delete for a file
	    if (is_file($dirname) || is_link($dirname)) {
	    	if(!is_writable($dirname)) {
	    		$this->deletewarning = TRUE;
	    		return FALSE;
	    	} else {
		        return @unlink($dirname);
		    }
	    }

	    // Loop through the folder
	    $dir = dir($dirname);
	    while (false !== $entry = $dir->read()) {
	        // Skip pointers
	        if ($entry == '.' || $entry == '..') {
	            continue;
	        }

	        // Recurse
	        $this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
	    }

	    // Clean up
	    $dir->close();

    	if(!is_writable($dirname)) {
    		$this->deletewarning = TRUE;
    		return FALSE;
    	} else {
		    return @rmdir($dirname);
		}
	}

	public function removeDBtables() {
		$db = JFactory::getDBO();

		$droptable = array();

		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_clientinfo`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_group`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_groupactive`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_groupinvite`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_groupmembership`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_groupnotice`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_grouprole`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_grouprolemembership`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_inworldident`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_mapinfo`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_money_customfees`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_moneybalances`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_moneygridbalance`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_moneygridbalance`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_moneysessions`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_offlinemessages`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_partnerrequests`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_allparcels`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_events`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_hostsregister`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_objects`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_options`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_parcels`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_parcelsales`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_regions`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_terminals`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_useravatars`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_userclassifieds`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_userlevel`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_usernotes`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_userpicks`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_userprofile`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_userrelation`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_usersettings`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_usertemp`;";
		
		// Outdated but still might exist
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_config`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_settings`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_moneysettings`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_classifieds`;";
		$droptable[] = "DROP TABLE IF EXISTS `#__opensim_search_popularplaces`;";
		
		foreach($droptable AS $query) {
			$db->setQuery($query);
			$db->execute();
		}
	}
}
?>