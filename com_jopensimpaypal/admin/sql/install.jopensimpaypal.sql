CREATE TABLE IF NOT EXISTS `#__jopensimpaypal_currencies` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `currency` char(3) DEFAULT NULL,
  `symbol` char(3) DEFAULT NULL,
  `reihe` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET `utf8`;

INSERT INTO #__jopensimpaypal_currencies (`id`,`currency`,`symbol`,`reihe`) VALUES ('1','EUR','€','1') ON DUPLICATE KEY UPDATE `currency` = `currency`;
INSERT INTO #__jopensimpaypal_currencies (`id`,`currency`,`symbol`,`reihe`) VALUES ('2','USD','$','2') ON DUPLICATE KEY UPDATE `currency` = `currency`;
INSERT INTO #__jopensimpaypal_currencies (`id`,`currency`,`symbol`,`reihe`) VALUES ('3','CHF','CHF','3') ON DUPLICATE KEY UPDATE `currency` = `currency`;
INSERT INTO #__jopensimpaypal_currencies (`id`,`currency`,`symbol`,`reihe`) VALUES ('4','JPY','¥','4') ON DUPLICATE KEY UPDATE `currency` = `currency`;
INSERT INTO #__jopensimpaypal_currencies (`id`,`currency`,`symbol`,`reihe`) VALUES ('5','AUD','$','5') ON DUPLICATE KEY UPDATE `currency` = `currency`;
INSERT INTO #__jopensimpaypal_currencies (`id`,`currency`,`symbol`,`reihe`) VALUES ('6','GBP','£','6') ON DUPLICATE KEY UPDATE `currency` = `currency`;

CREATE TABLE IF NOT EXISTS `#__jopensimpaypal_payoutrequests` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `joomlaid` int(11) unsigned NOT NULL,
  `opensimid` char(36) NOT NULL,
  `amount_iwc` int(7) unsigned NOT NULL,
  `amount_rlc` decimal(15,2) NOT NULL,
  `currency_rlc` char(3) DEFAULT NULL,
  `xchangerate` int(5) unsigned NOT NULL,
  `transactionfee` decimal(10,2) DEFAULT NULL,
  `transactionfeetype` varchar(20) DEFAULT NULL,
  `paypalaccount` varchar(255) DEFAULT NULL,
  `requesttime` datetime DEFAULT NULL,
  `requestip` varchar(15) DEFAULT NULL,
  `lastchange` datetime DEFAULT NULL,
  `remarks` text,
  `status` tinyint(1) DEFAULT NULL,
  `transferred` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__jopensimpaypal_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `txn_id` varchar(255) DEFAULT NULL,
  `verify_sign` varchar(150) DEFAULT NULL,
  `payer_email` varchar(255) DEFAULT NULL,
  `payer_id` varchar(150) DEFAULT NULL,
  `payer_firstname` varchar(255) DEFAULT NULL,
  `payer_lastname` varchar(255) DEFAULT NULL,
  `payment_status` varchar(100) DEFAULT NULL,
  `payment_type` varchar(100) DEFAULT NULL,
  `currencyname` char(3) DEFAULT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `mc_fee` decimal(15,2) DEFAULT NULL,
  `opensimid` varchar(36) DEFAULT NULL,
  `joomlaid` int(3) unsigned DEFAULT NULL,
  `amount_rlc` decimal(15,2) DEFAULT NULL,
  `amount_iwc` int(7) DEFAULT NULL,
  `iwbalance` int(7) DEFAULT NULL,
  `fee` varchar(50) DEFAULT NULL,
  `feetype` varchar(50) DEFAULT NULL,
  `transactiontime` datetime DEFAULT NULL,
  `mode` varchar(15) DEFAULT NULL,
  `paypaldebug` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

