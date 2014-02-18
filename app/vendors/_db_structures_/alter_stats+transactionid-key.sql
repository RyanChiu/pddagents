UPDATE stats CROSS JOIN (SELECT @transactionid:=-50000) AS init SET stats.transactionid=@transactionid:=@transactionid-1
where transactionid is null;

UPDATE stats CROSS JOIN (SELECT @transactionid:=-20000) AS init SET stats.transactionid=@transactionid:=@transactionid+1
where transactionid = 0;
