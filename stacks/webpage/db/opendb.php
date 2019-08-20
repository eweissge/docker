<?php
	try
	{
		$db = new PDO("mysql:host=$dbhost; dbname=$dbname", $dbuser, $dbpass);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//if ($debug) echo "Connected successfully";
	}
	catch(PDOException $e)
	{
		echo "Connection FAILED: " . $e->getMessage();
	}
?>
