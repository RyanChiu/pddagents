CREATE VIEW `view_clickouts` AS 
select 
	`c`.`id` AS `companyid`,`c`.`officename` AS `officename`,`a`.`agentid` AS `agentid`,
	`d`.`username` AS `username`,`a`.`clicktime` AS `clicktime`,`a`.`fromip` AS `fromip`,
	`a`.`referer` AS `referer`,`a`.`siteid` AS `siteid`,`e`.`sitename` AS `sitename`,`a`.
	`typeid` AS `typeid`,`f`.`typename` AS `typename`,`a`.`url` AS `url` 
from (((((`clickouts` `a` join `agents` `b`) join `companies` `c`) join `accounts` `d`) join `sites` `e`) join `types` `f`) where ((`a`.`agentid` = `b`.`id`) and (`b`.`id` = `d`.`id`) and (`b`.`companyid` = `c`.`id`) and (`a`.`siteid` = `e`.`id`) and (`a`.`typeid` = `f`.`id`))