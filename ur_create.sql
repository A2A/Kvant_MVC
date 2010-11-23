CREATE TABLE `ur_dru` (
  `ID` int(11) default NULL,
  `OBJECTID` int(11) default NULL,
  `READ` tinyint(1) default NULL,
  `WRITE` tinyint(1) default NULL,
  `CREATE` tinyint(1) default NULL,
  KEY `IDWR` (`ID`,`WRITE`),
  KEY `IDRD` (`ID`,`READ`),
  KEY `IDCR` (`ID`,`CREATE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ur_division` (
  `ID` int(11) default NULL,
  `OBJECTID` int(11) default NULL,
  `READ` tinyint(1) default NULL,
  `WRITE` tinyint(1) default NULL,
  `CREATE` tinyint(1) default NULL,
  KEY `IDWR` (`ID`,`WRITE`),
  KEY `IDRD` (`ID`,`READ`),
  KEY `IDCR` (`ID`,`CREATE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ur_roles` (
  `ID` int(11) default NULL,
  `OBJECTID` int(11) default NULL,
  `READ` tinyint(1) default NULL,
  `WRITE` tinyint(1) default NULL,
  `CREATE` tinyint(1) default NULL,
  KEY `IDWR` (`ID`,`WRITE`),
  KEY `IDRD` (`ID`,`READ`),
  KEY `IDCR` (`ID`,`CREATE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ur_users` (
  `ID` int(11) default NULL,
  `OBJECTID` int(11) default NULL,
  `READ` tinyint(1) default NULL,
  `WRITE` tinyint(1) default NULL,
  `CREATE` tinyint(1) default NULL,
  KEY `IDWR` (`ID`,`WRITE`),
  KEY `IDRD` (`ID`,`READ`),
  KEY `IDCR` (`ID`,`CREATE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

