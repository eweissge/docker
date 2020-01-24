<?php
	//include_once 'header.php';
	include $_SERVER["DOCUMENT_ROOT"].'/lib/libs.php';
	$debug = false;

	$username = $_POST['username'];
	$password = $_POST['password'];

	if(isset($_POST['username']) && isset($_POST['password']))
	{
		$debug = false;

		$checkUserQuery = "SELECT *
			FROM users
			WHERE username=:username;";

		$Login_Query = "SELECT *
			FROM users
			WHERE username=:username
			AND password=:password
			AND LockOut=0;";


		$record = func::runQuery($db, $checkUserQuery, array(':username' => $username), false, $debug);

		if ($record)
		{
			if ($debug) echo "Username account found <br/>\n";
			$userid = $_SESSION['userid'] = $record['userid'];
			$username = $_SESSION['username'] = $record['username'];
			if ($record['password'] == $password)
			{
				if ($record['LockOut'] == 0)  // && $record['Active'] == 0
				{
					if ($debug)
					{
						echo "Account is NOT locked <br/>\n";
						echo "Setting USERID & username <br/>\n";
						echo "Setting userid = ".$userid." <br/>\n";
						echo "Setting username = ".$username." <br/>\n";
					}
				}
				else // Account Locked
				{
					if ($debug) echo "<H4>Account is LOCKED </H4><br/>\n";
				}
				if ($debug) echo "Password MATCH <br/>\n";
				func::updateTracking($db, $username, false);
				if ($debug) echo "<H1>Login Successful </H1><br/>\n";
				func::createRecord($db, $username);
				//func::dumpvars(['username' => $username, 'userid' => $userid, 'token' => $token, 'serialNum' => $serialNum]);
				//header("Location: home.php");
				header("Location: index.php");
				//echo "<H1><a href='logout.php'>LOGOUT</H1></a><br/>\n";
			}
			else  // Incorrect Password
			{
				// Need to use POST username because the local one is not set
				 echo "<H4>Password is incorrect </H4><br/>\n";
				 func::updateTracking($db, $_POST['username'], true);
				 // Update login failures
			}
		}
/*
		// Checking login credentials
		// Let's check if they at least have a valid username - even if the password is wrong
		// so we can log failures
		$results = func::runQuery($db, $Login_Query, array(':username' => $username, ':password' => $password), false);
		if(!$results)  // username AND password don't match
		{
			// need to check above if the username is valid, if no user, no tracking record to update
			func::updateTracking($db, $username, true);
			echo "<H2>Invalid username or password </H2><br/>\n";
			echo "<a href='/loginform.php'>Return to Login</a><br/>\n";
		}
		else  // username and password both MATCH
		{
			// Assign session variables from database and dump
			if ($debug)
			{
				echo "<H1><b> Login successful </b></H1><br/>";
			}
			// There may not be an active session here
			//func::updateTracking($db, $_SESSION['username'], false);
			func::updateTracking($db, $results['username'], false);
		}
			// We've successfully logged in!  Now what?
*/
			/*
			die("Check results");
			$stmt = $db->prepare($Login_Query);
			echo $Login_Query." <br/>\n";
			$stmt->execute(array(':username' => $username, ':password' => $password));

			$result = $stmt->fetchAll();
			foreach($result as $value)
			{
			//$rowCount = count($stmt->fetchAll());
			echo $rowCount." rows found ".$row['userid']." <br/>\n";
			$userid = $row['userid'];
			$_SESSION['userid'] = $userid;
			//if ($debug) print_R($row);
			//$row = $stmt->fetch(PDO::FETCH_ASSOC);
			echo "Printing result row : ";
			//if ($debug) print_R($row);
			// If more than 0 results - fix this check, it's check userid value not quantity
			//if ($row['userid'] > 0)
			}
			if ($rowCount > 0)
			{
				$updateLoginQuery = "UPDATE loginTracking
					SET trackingLastLogin='".date('Y-m-d H:i:s')."',
					trackingSuccesses = 'trackingSuccesses' + 1,
					trackingActiveLogin='trackingActiveLogin' + 1
					WHERE trackingUserid=:userid;";
				//$userid = $row['userid'];
				echo "Userid = ".$_SESSION['userid']." <br/>\n";
				//$userid = $row['userid'];
				if($debug) echo "Login successful <br/>";
				$updateLogin = $db->prepare($updateLoginQuery);
				//echo $updateLoginQuery." <br/>\n";
				echo $updateLoginQuery." <br/>\n";
				$updateLogin->execute(array(':userid' => $userid));
				if($debug)
				{
					echo "loginTracking database table UPDATED <br/>\n";
					//echo "userid = ".$userid."<br/>\n";
					//echo "username = ".$username."<br/>\n";
					//echo "userid = ".$password."<br/>\n";
					echo "Creating Record <br/>\n";
				}
				func::createRecord($db, $username, $userid);
				//func::createCookie($username, $userid, $token, $serial);
				//func::createSession($username, $userid, $token, $serial);
				//header("location: index.php");
			}
			else
			{
				echo "No records found </br>\n";
				echo "LOGIN FAILED <br/>\n";
			}
			*/
	}
	else
	{
		echo "No username and password received <br/>\n";
	}

	//include "logout.php";

?>
