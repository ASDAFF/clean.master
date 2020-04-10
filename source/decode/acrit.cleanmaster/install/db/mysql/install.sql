CREATE TABLE IF NOT EXISTS `acrit_cleanmaster_upload_bfileindex` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `F_PATH` VARCHAR(255),
  `FILE_ID` int(11),
  PRIMARY KEY (`ID`),
  KEY `IDX_FP` (`F_PATH`),
  KEY `IDX_FID` (`FILE_ID`)
);
CREATE TABLE IF NOT EXISTS `acrit_cleanmaster_upload_filelost` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `F_PATH` VARCHAR(255),
  `SIZE` int(11),
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `acrit_cleanmaster_profiles` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `STEP_ID` int(11),
  `PARAMS` TEXT,
  KEY `IDX_SI` (`STEP_ID`),
  PRIMARY KEY (`ID`)
);
CREATE TABLE IF NOT EXISTS `acrit_cleanmaster_last_diag` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `STEP_CODE` VARCHAR(100),
  `PARAMS` LONGTEXT,
  KEY `IDX_SI` (`STEP_CODE`),
  PRIMARY KEY (`ID`)
);