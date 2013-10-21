ALTER TABLE `t_stats` 
ADD `sales_type1_payout` DECIMAL( 8.2 ) NOT NULL AFTER `sales_type4` ,
ADD `sales_type1_earning` DECIMAL( 8.2 ) NOT NULL AFTER `sales_type1_payout` ,
ADD `sales_type2_payout` DECIMAL( 8.2 ) NOT NULL AFTER `sales_type1_earning` ,
ADD `sales_type2_earning` DECIMAL( 8.2 ) NOT NULL AFTER `sales_type2_payout` ,
ADD `sales_type3_payout` DECIMAL( 8.2 ) NOT NULL AFTER `sales_type2_earning` ,
ADD `sales_type3_earning` DECIMAL( 8.2 ) NOT NULL AFTER `sales_type3_payout` ,
ADD `sales_type4_payout` DECIMAL( 8.2 ) NOT NULL AFTER `sales_type3_earning` ,
ADD `sales_type4_earning` DECIMAL( 8.2 ) NOT NULL AFTER `sales_type4_payout` ;

ALTER TABLE `t_stats` DROP `typeid`