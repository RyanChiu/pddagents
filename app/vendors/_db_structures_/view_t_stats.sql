drop VIEW `view_t_stats`;
create view `view_t_stats` as 
select `t`.`trxtime` AS `trxtime`,`t`.`agentid` AS `agentid`,`t`.`siteid` AS `siteid`,`s`.`sitename` AS `sitename`,`t`.`raws` AS `raws`,`t`.`uniques` AS `uniques`,`t`.`chargebacks` AS `chargebacks`,`t`.`signups` AS `signups`,`t`.`frauds` AS `frauds`,`t`.`sales_type1` AS `sales_type1`,`t`.`sales_type2` AS `sales_type2`,`t`.`sales_type3` AS `sales_type3`,`t`.`sales_type4` AS `sales_type4`,`b`.`companyid` AS `companyid`,`c`.`officename` AS `officename`,`a`.`username` AS `username`,`a`.`username4m` AS `username4m`,`b`.`ag1stname` AS `ag1stname`,`b`.`aglastname` AS `aglastname`,((((`t`.`sales_type1` + `t`.`sales_type2`) + `t`.`sales_type3`) + `t`.`sales_type4`) - `t`.`chargebacks`) AS `net`,(((((`t`.`sales_type1` - `t`.`chargebacks`) * `t`.`sales_type1_payout`) + (`t`.`sales_type2` * `t`.`sales_type2_payout`)) + (`t`.`sales_type3` * `t`.`sales_type3_payout`)) + (`t`.`sales_type4` * `t`.`sales_type4_payout`)) AS `payouts`,(((((`t`.`sales_type1` - `t`.`chargebacks`) * `t`.`sales_type1_earning`) + (`t`.`sales_type2` * `t`.`sales_type2_earning`)) + (`t`.`sales_type3` * `t`.`sales_type3_earning`)) + (`t`.`sales_type4` * `t`.`sales_type4_earning`)) AS `earnings`,`t`.`run_id` AS `run_id`,`t`.`group_by` AS `group_by` from ((((`t_stats` `t` join `accounts` `a`) join `agents` `b`) join `companies` `c`) join `sites` `s`) where ((`t`.`agentid` = `a`.`id`) and (`a`.`id` = `b`.`id`) and (`b`.`companyid` = `c`.`id`) and (`t`.`siteid` = `s`.`id`));