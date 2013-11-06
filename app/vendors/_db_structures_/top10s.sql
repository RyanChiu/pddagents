CREATE TABLE IF NOT EXISTS `top10s` (
  `flag` tinyint(4) NOT NULL COMMENT '0-all, 1-this week',
  `date` date NOT NULL,
  `agentid` int(11) NOT NULL,
  `username` varchar(64) COLLATE ascii_bin NOT NULL,
  `ag1stname` varchar(64) COLLATE ascii_bin NOT NULL,
  `officename` varchar(64) COLLATE ascii_bin NOT NULL,
  `sales` int(11) NOT NULL
)