CREATE TABLE IF NOT EXISTS `t_stats` (
  `trxtime` date NOT NULL,
  `agentid` int(11) NOT NULL,
  `companyid` int(11) NOT NULL,
  `siteid` int(11) NOT NULL,
  `typeid` int(11) NOT NULL,
  `raws` int(11) NOT NULL,
  `uniques` int(11) NOT NULL,
  `chargebacks` int(11) NOT NULL,
  `signups` int(11) NOT NULL,
  `frauds` int(11) NOT NULL,
  `sales_type1` int(11) NOT NULL,
  `sales_type2` int(11) NOT NULL,
  `sales_type3` int(11) NOT NULL,
  `sales_type4` int(11) NOT NULL,
  `run_id` int(11) NOT NULL,
  `group_by` tinyint(4) NOT NULL,
  KEY `run_id` (`run_id`,`group_by`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin;