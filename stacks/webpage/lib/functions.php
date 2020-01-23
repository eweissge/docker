<?php

/*
	Eric Weissgerber
	PHP Functions

	OWASP Session cheat sheet
	https://github.com/OWASP/CheatSheetSeries/blob/master/cheatsheets/Session_Management_Cheat_Sheet.md

	OWASP Authentication cheat sheet
	https://github.com/OWASP/CheatSheetSeries/blob/master/cheatsheets/Authentication_Cheat_Sheet.md

	OWASP Password Storage Cheat sheet
	https://github.com/OWASP/CheatSheetSeries/blob/master/cheatsheets/Password_Storage_Cheat_Sheet.md

	1.0 - Fix COOKIES
	1.1 - Fix Sessions
	1.2 - Force HTTPS
*/

class func
{
  	public static function sanitize($str)
	{
		/* Takes a string, trims, removes slashes and special characters */
		$debug = true;
		if ($debug) echo "BEGIN sanitize function <br/>\n";
    	foreach($str as $key => $val)
    	{
			if ($debug) echo "Trimming ".$key." = ".$val." <br/>\n";
      		$val = trim($val);
			if ($debug) echo "Stripping slashes ".$key." = ".$val." <br/>\n";
      		$val = stripslashes($val);
			if ($debug) echo "Removing html special chars ".$key." = ".$val." <br/>\n";
      		$val = htmlspecialchars($val);
    	}
		if ($debug) echo "Returning ".$val." <br/>\n";
		if ($debug) echo "END sanitize function <br/>\n";
    	return $str;
	}

	public static function printResults($results)
	{
		$debug = false;
		echo "<br/>\n";
		if ($debug) echo "BEGIN runQuery function <br/>\n";
		echo "<H4>Printing query results </H4><br/>\n";
		foreach($results as $key => $val)
		{
			echo $key." = ".$val." <br/>\n";
		}
		if ($debug) echo "END runQuery function <br/>\n";
		echo "<br/>\n";
	}

	public static function dumpvars($arrvars)
	{
		//	public static function dumpvars($username, $token, $serialNum)
		echo "<br/>\n";
		if ($debug) echo "BEGIN dumpvars function <br/>\n";
		echo "<b>LOCAL VARIABLES </b><br/>\n";
		echo "Local USERID = ".$arrvars['userid']." <br/>\n";
		echo "Local USERNAME = ".$arrvars['username']." <br/>\n";
		echo "Local TOKEN = ".$arrvars['token']." <br/>\n";
		echo "Local SERIALNUM = ".$arrvars['serialNum']." <br/>\n";
		//echo "<br/>\n";
		echo "<b>SESSION VARIABLES </b><br/>\n";
		echo "Session USERID = ".$_SESSION['userid']." <br/>\n";
		echo "Session USERNAME = ".$_SESSION['username']." <br/>\n";
		echo "Session TOKEN = ".$_SESSION['token']." <br/>\n";
		echo "Session SERIALNUM = ".$_SESSION['serialNum']." <br/>\n";
		//echo "<br/>\n";
		echo "<b>COOKIE VARIABLES </b><br/>\n";
		echo "Cookie USERID = ".$_COOKIE['userid']." <br/>\n";
		echo "Cookie USERNAME = ".$_COOKIE['username']." <br/>\n";
		echo "Cookie TOKEN = ".$_COOKIE['token']." <br/>\n";
		echo "Cookie SERIALNUM = ".$_COOKIE['serialNum']." <br/>\n";
		if ($debug) echo "END dumpvars function <br/>\n";
		//print_R($_COOKIE);
		echo "<br/>\n";
	}

	public static function runQuery($db, $query, $params, $update, $debug)
	{
		//$debug = false;
		$db->beginTransaction();

		try  // Prepare, Bind params and execute the query
		{
			if ($debug) echo "BEGIN runQuery function <br/>\n";
			if ($debug) echo "Preparing the query <br/>\n";
			if ($debug) echo "<H4>".$query." </H4><br/>\n";
			try // Prepare the query
			{
				$pquery = $db->prepare($query);
			}
			catch (PDOException $e2)
			{
				echo "Error preparing query : ".$e2->getMessage()." <br/>\n";
			}
			// BIND parameters
			foreach ($params as $key => $val)
			{
				try
				{
					if ($debug) echo "BIND ".$key." => ".$val." <br/>\n";
					$pquery->bindParam($key, $val);
				}
				catch (PDOException $er)
				{
					echo "Error binding the parameter : ".$er->getMessage()." <br/>\n";
				}
			}
			if ($debug) echo "Executing the query <br/>\n";
			try // Execute the query
			{
				$success = $pquery->execute($params);
			}
			catch (PDOException $e3)
			{
				echo "Error executing the query : ".$e3->getMessage()." <br/>\n";
			}
			if (!$update)$results = $pquery->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e)
		{
			echo "Error : ".$e->getMessage()." for query - ".$query." <br/>\n";
			$db->rollback();
			throw $e;
		}
		$db->commit();
		//if ($debug) echo "Database commit SUCCESSFUL <br/>\n";
		if (!$update)
		{
			if($debug) func::printResults($results);
			return $results;
		}
		if ($debug) echo "END runQuery function <br/>\n";
	}

	public static function checkLoginState($db)
	{
		$debug = false;
		//echo $debug." <br/>\n";
		if ($debug) echo "BEGIN checkLoginState function <br/>\n";
		/*
			checkLoginState - returns TRUE if the user is logged in or FALSE if they are not
			First check for a current active session
			If no active session, check for an active COOKIE
			Create new cookie, create new session, insert into sessions table

		*/

		if ($debug) echo "<H4>Checking if Session is set </H4><br/>\n";
		if (isset($_SESSION['userid']) &&
				isset($_SESSION['username']) &&
				isset($_SESSION['token']) &&
				isset($_SESSION['serialNum']))
		{
			// We should check the database to see if the Session already exists

			if ($debug) echo "Session variables are already set! <br/>\n";
			if ($debug) echo "Check database for match <br/>\n";

			$checkExistingQuery = "SELECT *
				FROM sessions
				WHERE session_username=:username;";

			//  Just use the updateTracking function, create a separate function to update failures
			func::updateTracking($db, $_SESSION['username'], $failure);

			//if (func::runQuery($db, $checkExistingQuery, array(':userid' => $userid), false))
			if (func::runQuery($db, $checkExistingQuery, array(':username' => $_SESSION['username']), false, $debug))
			{
				// Update session info in DATABASE
				if ($debug) echo "<H4>FOUND existing session into Database </H4><br/>\n";
				if ($debug) echo "<H4>Updating record in sessions table </H4><br/>\n";
				$updateSessionQuery = "UPDATE sessions
					SET session_token=:token, session_serial=:serialNum
					WHERE session_username=:username;";
				func::runQuery($db, $updateSessionQuery,
					array(':username' => $_SESSION['username'],
					':token' => $_SESSION['token'],
					':serialNum' => $_SESSION['serialNum']), true, $debug);
				return true;
			}
			else  // No matching session in table
			{
				// Insert new session into DATABASE	, ':theTime' => date('Y-m-d H:i:s') , :theTime	, session_date
				//die("Session is active but it is not present in the active sessions database table");
				if ($debug) echo "Session is active but it is not present in the active sessions database table <br/>\n";
				if ($debug) echo "<H4>Inserting new session into Database </H4><br/>\n";
				if ($debug) echo "<H4>This is bad behavior, active session but not authenticated </H4><br/>\n";
				$insertSessionQuery = "INSERT INTO sessions
					(session_username, session_token, session_serial)
					VALUES (:username, :token, :serialNum);";
				func::runQuery($db, $insertSessionQuery,
					array(':username' => $_SESSION['username'],
					':token' => $_SESSION['token'],
					':serialNum' => $_SESSION['serialNum']), true, $debug);
				return true;
			}
		}
		else // Session not started
		{
			if ($debug) echo "<H4>NO SESSION, Checking if Cookie is set </H4><br/>\n";
			if (isset($_COOKIE['userid']) &&
					isset($_COOKIE['username']) &&
					isset($_COOKIE['token']) &&
					isset($_COOKIE['serialNum']))
			{
				//$debug = false;
				if ($debug) echo "<H4>Confirmed cookie is SET!  Checking COOKIE against SESSION database </H4><br/>\n";

				$query = "SELECT *
					FROM sessions
					WHERE session_ID = :userid
					AND session_username = :username
					AND session_token = :token
					AND session_serial = :serialNum;";

				$results = func::runQuery($db, $query,
					array(':userid' => $_COOKIE['userid'],
					':username' => $_COOKIE['username'],
					':token' => $_COOKIE['token'],
					'serialNum' => $_COOKIE['serialNum']), false, $debug);
				if($results)
				{
					if ($debug) echo "<H4>Active cookie found in database sessions table </H4><br/>\n";
					//func::printResults($results);
					//$_SESSION['userid'] = $_COOKIE['userid'];
					if ($debug) echo "<H4>Assigning Session variables from active COOKIE </H4><br/>\n";
					$_SESSION['userid'] = $_COOKIE['userid'];
					$_SESSION['username'] = $_COOKIE['username'];
					$_SESSION['token'] = $_COOKIE['token'];
					$_SESSION['serialNum'] = $_COOKIE['serialNum'];
				}
				else  // Cookie session not in Database
				{
					if ($debug) echo "No Cookie or Session, NOT LOGGED IN <br/>\n";
					return false;
					// Failing here, unable to set cookie from NULL Session variables  FAIL FAIL FAIL FAIL FAIL FAIL FAIL FAIL
					if ($debug) echo "<H4>No matching sessions in database </H4><br/>\n";
					if ($debug) echo "<H3>Need to create new cookie record in Session table </H3><br/>\n";
					//createCookie();
					if ($debug) echo "<H4>Setting local variables from Session/Cookie variables </H4><br/>\n";
					$userid = $_SESSION['userid'] = $_COOKIE['userid'];
					$username = $_SESSION['username'] = $_COOKIE['username'];
					$token = $_SESSION['token'] = $_COOKIE['token'];
					$serialNum = $_SESSION['serialNum'] = $_COOKIE['serialNum'];
				}

				if ($debug) echo "<H4>Setting local variables from Session variables </H4><br/>\n";
				$userid = $_SESSION['userid'];
				$username = $_SESSION['username'];
				$token = $_SESSION['token'];
				$serialNum = $_SESSION['serialNum'];
				if ($debug)
				{
					echo "Cookie is set, local variables, after being set from Session <br/>\n";
					echo "USERID = ".$userid." <br/>\n";
					echo "USERNAME = ".$username." <br/>\n";
					echo "TOKEN = ".$token." <br/>\n";
					echo "SERIAL = ".$serialNum." <br/>\n";
				}
			}
			else  // Session & Cookie not set
			{
				if ($debug) echo "No Session, No Cookie - Not Logged in <br/>\n";
				return false;
			}
			// Check for query results
			/*
			if (!$results)
			{
				echo "Cookie not in DATABASE";
				// Destroy the COOKIE
				// Create another cookie
			}
			$userid = $_COOKIE['userid'];
			$token = $_COOKIE['token'];
			$serial = $_COOKIE['serial'];


			*/
			/*
			$stmt = $db->prepare($query);
			$stmt->execute(array(':userid' => $userid, ':token' => $token, ':serial' => $serial));

			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($row['session_userid'] > 0)
			{
				if ($debug) echo "COOKIE matches ACTIVE database SESSION <br/>\n";
				if ($row['session_userid'] == $_COOKIE['userid'] && $row['session_token'] == $_COOKIE['token'] && $row['session_serial'] == $_COOKIE['serial'])
				{
					if ($row['session_userid'] == $_SESSION['userid'] && $row['session_token'] == $_SESSION['token'] && $row['session_serial'] == $_SESSION['serial'])
					{
						if ($debug) echo "COOKIE, SESSION, and DATABASE MATCH! LOGGED IN! <br/>\n";
						return true;
					}
					else
					{
						if ($debug) echo "Session variables don't match<br/>";
						//func::createSession($_COOKIE['username'], $_COOKIE['userid'], $_COOKIE['token'], $_COOKIE['serial']);
						return true;
					}
				}
												}
			else  // COOKIE does NOT match DATABASE
			{
				if($debug) echo "COOKIE does NOT match DATABASE <br/>\n";
				if($debug) echo "<H2>LOGIN FAILED</H2><br/>\n";
			}
		}

		else if (!isset($_COOKIE) && !isset($_SESSION))	// Session and Cookie not set!
		{
			$debug = true;
			if ($debug)
			{
				echo " Oops - This probably shouldn't happen";
				echo "SESSION and COOKIE not set <br/>\n";
				echo "username = ".$username."<br/>\n";
				echo "password = ".$password."<br/>\n";
			}
			//$userid = func::createString(32);
			$userid = $_POST['username'];
			/*
			$token = func::createString(32);
			$serial = func::createString(32);
			// We need a userid before we can call anything here ...
			func::createSession($username, $userid, $token, $serial);
			if ($debug) echo "Creating cookie";
			func::createCookie($username, $userid, $token, $serial);
			func::createRecord($db, $username, $userid);
		}*/
		}
		if ($debug) "END checkLoginState function <br/>\n";
	}

  public static function createRecord($db, $username)
	{
		/*
			Removing steps from checkLoginState
			if(!checkLoginState) createRecord($db, $username, $userid)

			What does createRecord do??
			Create a record in the sessions table and in the loginTracking table ... right?
			checkLogin function has returned that the user IS or IS NOT logged in
			NOT LOGGED IN - createSession, createCookie, insert new record into sessions and loginTracking
			LOGGED IN - Session and cookie should already exist, update sessions and loginTracking
		*/
		$debug = false;
		if ($debug) echo "<H4>BEGIN createRecord function </H4><br/>\n";

		$findSessionQuery = "SELECT *
			FROM sessions
			WHERE session_ID=:userid
			AND session_token=:token
			AND session_serial=:serialNum;";

		if ($debug) echo "Searching table for existing session <br/>\n";
		$results = func::runQuery($db, $findSessionQuery,
			array(':userid' => $_SESSION['userid'],
			':token' => $_SESSION['token'],
			':serialNum' => $_SESSION['serialNum']), false, $debug);
		if ($results)
		{
			if ($debug) echo "Matching record found in Sessions Table <br/>\n";
		}
		else
		{
			if ($debug) echo "No matching record found in Sessions Table <br/>\n";
		}

		$removeSessionQuery = "DELETE FROM sessions WHERE session_ID=:session_ID;";

		// Changed to Session userid instead of local
		if ($debug) echo "Remove Session query <br/>\n";
		func::runQuery($db, $removeSessionQuery, array(':session_ID' => $_SESSION['userid']), true, $debug);

		// Create token and serialNum
		if ($debug) echo "<H4>Creating token and serialNum (Session and local)</H4><br/>\n";
		//$_SESSION['userid'] = $userid = func::createString(32);
		$_SESSION['token'] = $token = func::createString(32);
		$_SESSION['serialNum'] = $serialNum = func::createString(32);

		//func::dumpvars(array('userid' => $_SESSION['userid'], 'username' => $username, 'token' => $token, 'serialNum' => $serialNum));
		// Insert new record into DATABASE
		// Copied Insert function
		$insertSessionQuery = "INSERT INTO sessions
			(session_ID, session_username, session_token, session_serial, session_date)
			VALUES (:userid, :username, :token, :serialNum, '".date('Y-m-d H:i:s')."');";

		if ($debug) echo "Inserting new record into sessions table <br/>\n";
		func::runQuery($db, $insertSessionQuery,
			array(':userid' => $_SESSION['userid'],
			':username' => $_SESSION['username'],
			':token' => $_SESSION['token'],
			':serialNum' => $_SESSION['serialNum']), true, $debug);


			/*
			//if ($debug) echo "<H2>Insert new record into loginTracking table </H2><br/>\n";
			// This needs fixing
			//func::updateTracking($db, $username, false);

			$insertTrackingQuery = "INSERT INTO loginTracking(trackingIPaddr, trackingLastLogin, trackingSuccesses, trackingActiveLogin)
				VALUES (".$_SERVER['REMOTE_ADDR'].", '".date('Y-m-d H:i:s')."', trackingSuccesses+1, 1);";
			func::runQuery($db, $insertTrackingQuery, array($_SERVER['REMOTE_ADDR'], time(), trackingSuccesses+1, 1), true);
			*/
			//die("Check Database Session table for updated record");
			/*
			if(!isset($_COOKIE))
			{
				if ($debug) echo "Creating cookie <br/>\n";
				func::createCookie($username, $userid, $token, $serialNum);
			}
			if(!isset($_SESSION))
			{
				if ($debug) echo "Creating Session <br/>\n";
				func::createSession($username, $userid, $token, $serial);
			}

		if (isset($_SESSION))
		{
			if ($debug) echo "Session is set <br/>\n";
			$token = $_SESSION['token'];
			$serial = $_SESSION['serial'];
			if ($debug)
			{
				echo "Session Token = ".$_SESSION['token']."<br/>";
				echo "Session serial = ".$_SESSION['serial']."<br/>";
			}
		}
		*/
		/*
		else if (isset($_COOKIE))
		{
			if ($debug) echo "Cookie is set <br/>\n";
			$token = $_COOKIE['token'];
			$serial = $_COOKIE['serial'];
			if ($debug)
			{
				echo "Cookie Token = ".$_SESSION['token']."<br/>";
				echo "Cookie serial = ".$_SESSION['serial']."<br/>";
			}
		}
		*/
		/*
		if ($debug) echo "Inserting new ASDASDOFUZ session record in sessions table <br/>\n";
		$stmt = $db->prepare("INSERT INTO sessions(session_id, session_token, session_serial, session_date) VALUES (:userid, :token, :serialNum, ".time().");");
		if ($debug) echo "Inserting Session data into DATABASE <br/>\n";
		$stmt->execute(array(':userid' => $userid, ':token' => $token, ':serialNum' => $serialNum));
		//if ($debug) echo "Fetching results data <br/>\n";
		//$row = $stmt->fetch(PDO::FETCH_ASSOC);
		//if ($debug) print_R($row);
		*/
		if ($debug) echo "<H4>END createRecord function </H4><br/>\n";
	}

	public static function removeRecord($db)
	{
		$removeSessionQuery = "DELETE FROM sessions WHERE session_ID=:session_ID;";

		// Changed to Session userid instead of local
		if ($debug) echo "Remove Session query <br/>\n";
		func::runQuery($db, $removeSessionQuery, array(':session_ID' => $_SESSION['userid']), true, $debug);

		$logoutQuery = "UPDATE loginTracking
			SET trackingActiveLogin=0
			WHERE trackingUserid=:userid;";

		func::runQuery($db, $logoutQuery, array(':userid' => $_SESSION['userid']), true, $debug);
	}

	public static function updateTracking($db, $username, $failure)
	{
		$debug = false;
		if ($debug) echo "<H4>BEGIN updateTracking function </H4><br/>\n";

		$checkTrackingQuery = "SELECT *
			FROM loginTracking
			WHERE trackingUserID = :userid;";

		//func::dumpvars(['userid' => $userid, 'username' => $username, 'token' => $token, 'serialNum' => $serialNum]);
		// If not matches in the loginTracking table
		if ($debug) echo "<H4>Checking database for record with userid = ".$_SESSION['userid']." </H4><br/>\n";
		if(!func::runQuery($db, $checkTrackingQuery, array(':userid' => $_SESSION['userid']), false, $debug))
		{
			echo "<H3>No existing record found, Inserting new login tracking record </H3><br/>\n";
			$insertTrackingQuery = "INSERT INTO loginTracking
				(trackingUserID, trackingUsername, trackingIPaddr,
				trackingUserAgent, trackingLastLogin, trackingLastFailure,
				trackingFailures, trackingSuccesses, trackingActiveLogin)
				VALUES (:userid, :username, :ipaddr, :userAgent, '".date('Y-m-d H:i:s')."',
				:lastFailure, :failures, :successes, :activeLogin);";
			// Updated the $userid to use the SESSION userid which is set
			if(!func::runQuery($db, $insertTrackingQuery,
				array(':userid' => $_SESSION['userid'],
				':username' => $username,
				':ipaddr' => $_SERVER['REMOTE_ADDR'],
				':userAgent' => $_SERVER['HTTP_USER_AGENT'],
				':lastFailure' => NULL,
				':failures' => 0,
				':successes' => 0,
				':activeLogin' => false), true, $debug))
			{
				if ($debug) echo "<H3>Data was NOT inserted into database </H3><br/>\n";
			}
			else
			{
				if ($debug) echo "<H4>New Tracking record created in database </H4><br/>\n";
			}
		}

		// if the Login succeeded
		if (!$failure)
		{
			if ($debug) {
				// Need to add the userid
				echo "<H4>Record found, updating the loginTracking record </H4><br/>\n";
				echo "Session Userid = ".$_SESSION['userid']." <br/>\n";
				echo "Cookie Userid = ".$_COOKIE['userid']." <br/>\n";
				echo "Local Userid = ".$userid." <br/>\n";
			}

			$updateTrackingQuery = "UPDATE loginTracking
				SET trackinguserid=:userid, trackingUsername=:username,
				trackingIPaddr=:ipaddr, trackingUserAgent=:userAgent,
				trackingLastLogin=:lastLogin, trackingFailures=0,
				trackingSuccesses=trackingSuccesses+1, trackingActiveLogin=true
				WHERE trackingUsername=:username;";

				// Updated the $userid to use the SESSION userid which is set
				func::runQuery($db, $updateTrackingQuery,
					array(':userid' => $_SESSION['userid'],
					':username' => $username,
					':ipaddr' => $_SERVER['REMOTE_ADDR'],
					':userAgent' => $_SERVER['HTTP_USER_AGENT'],
					':lastLogin' => date('Y-m-d H:i:s')), true, $debug);
		}
		else // if login FAILED
		{
			// This should be moved out to the login script on login failure
			// also remove the failure parameter for this function
			echo "<H4>Login failed </H4><br/>\n";
			func::updateFailTracking($db, $_SESSION['username']);
			// If (trackingFailures >= 3) lock account
		}
		if ($debug) echo "<H4>END updateTracking function </H4><br/>\n";
	}

	public static function updateFailTracking($db, $username)
	{
		$debug = false;
		if($debug) echo "<H4>Update the tracking table failures </H4><br/>\n";

		$checkTrackingQuery = "SELECT *
			FROM loginTracking
			WHERE trackingUserID = :userid;";

		$updateTrackingQuery = "UPDATE loginTracking
			SET trackingUsername=:username, trackingIPaddr=:ipaddr,
			trackingLastFailure=:lastFailure, trackingFailures=trackingFailures+1,
			trackingActiveLogin=false
			WHERE trackingUsername=:username;";

		func::runQuery($db, $updateTrackingQuery, array(':username' => $username, ':ipaddr' => $_SERVER['REMOTE_ADDR'], ':lastFailure' => date('Y-m-d H:i:s')), true, $debug);
		if ($debug) echo "<H1>username = ".$username." </H1><br/>\n";
		$record = func::runQuery($db, $checkTrackingQuery, array(':username' => $username), false, $debug);
		// Check login failures and lockout the account
		if ($record['trackingFailures'] >= 3)
		{
			if ($debug) echo "<H1>Your account has been locked for too many failed login attempts </H1><br/>\n";
			$lockoutQuery = "UPDATE users
				SET LockOut=1
				WHERE username=:username;";
			// Lock the account!
			func::runQuery($db, $lockoutQuery, array(':username' => $username), true, $debug);
		}
	}

	public static function createCookie()
	//public static function createCookie($username, $userid, $token, $serialNum)
	{	// Cookies valid for 7 days
		// Creating the username for user input POST (risky)
		if(!isset($_COOKIE['userid']) && !isset($_COOKIE['token']) && !isset($_COOKIE['serialNum']))
		{
			$userid = func::createString(32);
			$token = func::createString(32);
			$serialNum = func::createString(32);
			setcookie('userid', $userid, time() + 86400 * 7, "/");
			setcookie('username', $_POST['username'], time() + 86400 * 7, "/");
			setcookie('token', $token, time() + 86400 * 7, "/");
			setcookie('serialNum', $serialNum, time() + 86400 * 7, "/");
			$debug = false;
			if ($debug)
			{
				echo "Starting createCookie<br/>";
				echo "COOKIE userid = ".$userid."<br/>\n";
				echo "COOKIE username = ".$_POST['username']."<br/>\n";
				echo "COOKIE token= ".$token."<br/>\n";
				echo "COOKIE serial= ".$serialNum."<br/>\n";
			}
		}
		else
		{
			if ($debug) echo "Cookie is already set <br/>\n";
		}
	}

	public static function createSession()
	//public static function createSession($username, $userid, $token, $serial)
	{

		$debug = false;

		// If NO session
		if(!isset($_SESSION['userid']) &&
			!isset($_SESSION['token']) &&
			!isset($_SESSION['serialNum']))
		{
			/*
			$username = func::createString(32);
			$userid = func::createString(32);
			$token = func::createString(32);
			$serialNum = func::createString(32);
			*/
			try  // Starting session
			{
				Session_Start();
			}
			catch(PDOExeception $e)
			{
				echo "Unable to create Session : ".$e->message()." <br/>\n";
			}
		}

		// if ACTIVE session
		else if (isset($_SESSION['userid']) &&
				isset($_SESSION['token']) &&
				isset($_SESSION['serialNum']))
		{
			//$_SESSION['username'] = $username;
			$_SESSION['userid'] = $userid;
			$_SESSION['token'] = $token;
			$_SESSION['serialNum'] = $serialNum;
			if ($debug)
			{
				echo "SESSION userid= ".$_SESSION['userid']." <br/>\n";
				echo "SESSION username= ".$_SESSION['username']." <br/>\n";
				echo "SESSION token= ".$_SESSION['token']." <br/>\n";
				echo "SESSION serial = ".$_SESSION['serialNum']." <br/>\n";
				echo "Session variables have been set <br/>\n";
			}
		}
		else
		{
			echo "Session still not set <br/>\n";
		}
	}

	public static function createString($len)
	{
		$debug = false;
		if ($debug) echo "BEGIN createString function <br/>\n";
		$string = "asdkjfpaoiszASLDKJxklcvnsadfnmkASDLKJasdpozXWERfiqweWEAlkasdd";

		$s = substr(str_shuffle($string),0, $len);
		if ($debug) echo "Generated - ".$s." <br/>\n";
		return $s;
	}

	public static function registerUser()
	{
		// Intake of form POST VARIABLES
		print_R($_POST);

		// Validate user input in the POST VARIABLES

		// Check for existing user data - username or email address - no duplicates

		// Create new user record
	}

	public static function unlockAccount($db)
	{
		// Still Designing this function
		$debug = true;
		if ($debug) echo "Starting function unlockAccount <br/>\n";
		// Have client confirm email address
		// Verify the email address on record
		// Send an re-activation link to the email address on file
		// Verify activation link against database record to Confirm
		// Enable the account and log the user in
		if ($debug) echo "Leaving function unlockAccount <br/>\n";
	}
}
