ALTER TABLE `stats` ADD `companyid` INT NOT NULL AFTER `agentid`;
update `stats`
set companyid = (select companyid from agents where agents.id = stats.agentid);