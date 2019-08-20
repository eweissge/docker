<?php
	//include_once 'header.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/db/config.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/db/opendb.php';
	include_once $_SERVER["DOCUMENT_ROOT"].'/lib/functions.php';

	Session_Start();
	try  // Remove login record from sessions table
	{
		func::removeRecord($db);
	}
	catch (PDOException $e)
	{
		echo "Error removing record : ".$e->message();
	}

	try  // Destroy session
	{
		//Session_start();
		Session_unset();
		Session_destroy();
	}
	catch (PDOException $e)
	{
			echo "Error destroying session : ".$e->message();
	}

	try  // Unset cookie
	{
		setcookie("username", "", time()-3600);
		setcookie("userid", "", time()-3600);
		setcookie("token", "", time()-3600);
		setcookie("serialNum", "", time()-3600);
	}
	catch (PDOException $e)
	{
		echo "Error unsetting cookie : ".$e->message();
	}

	try  // Close or unset database
	{
		// POD doesn't have a close() method, just unset it
		$db = null;
	}
	catch (PDOException $e)
	{
		echo "Error closing database : ".$e->message();
	}

	header("Location: index.php");
?>
