<?php
  include 'header.php';
  $debug = true;

  if ($debug) echo "Register user page <br/>\n";

  func::sanitize($_POST);

  $username = $_POST['username'];
  $password = $_POST['password'];
  $emailAddr = $_POST['emailAddr'];
  $phoneNum = $_POST['phoneNum'];
  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $addr1 = $_POST['addr1'];
  $addr2 = $_POST['addr2'];
  $city = $_POST['city'];
  $state = $_POST['state'];
  $zip = $_POST['zip'];

  // Validate length of all of our POST variables
  if(strlen($username) < 3 || strlen($username) > 32)
    echo "Username must be between 3 and 32 characters <br/>\n";

  if(strlen($password) < 3 || strlen($password) > 32)
    echo "Password must be between 3 and 32 characters <br/>\n";

  if(strlen($emailAddr) < 3 || strlen($emailAddr) > 32)
    echo "emailAddr must be between 3 and 32 characters <br/>\n";

  if(strlen($phoneNum) < 3 || strlen($phoneNum) > 32)
    echo "phoneNum must be between 3 and 32 characters <br/>\n";

  if(strlen($firstName) < 3 || strlen($firstName) > 32)
    echo "firstName must be between 3 and 32 characters <br/>\n";

  if(strlen($lastName) < 3 || strlen($lastName) > 32)
    echo "lastName must be between 3 and 32 characters <br/>\n";

  if(strlen($addr1) < 3 || strlen($addr1) > 32)
    echo "addr1 must be between 3 and 32 characters <br/>\n";

  if($addr2 === '') $addr2 = NULL;
  // END LENGTH VALIDATION

  // Validate Email
  if ($debug) echo "Validating email address <br/>\n";
  if (!filter_var($emailAddr, FILTER_VALIDATE_EMAIL))
  {
    echo "Invalid Email address <br/>\n";
  }

  if ($debug) echo "Validating First name <br/>\n";
  if (!preg_match("/^[a-zA-Z ]*$/",$firstName))
  {
    echo "Only letters and white space allowed <br/>\n";
  }

  if ($debug) echo "Validating Last name <br/>\n";
  if (!preg_match("/^[a-zA-Z ]*$/",$lastName))
  {
    echo "Only letters and white space allowed <br/>\n";
  }

  /*
  users Table structure
  ------------------------------------------
  userid	varchar(32)
  AccountName	varchar(50) NULL
  username	varchar(25)
  password	varchar(25)
  email	varchar(50)
  phone	varchar(10) NULL
  FirstName	varchar(100) NULL
  LastName	varchar(100) NULL
  Photo	varbinary(200) NULL
  Address1	varchar(50) NULL
  Address2	varchar(50) NULL
  City	varchar(50) NULL
  State	varchar(2) NULL
  Zip	varchar(10) NULL
  Active	bit(1) [b'0']
  LockOut	bit(1) [b'0']
  CreatedDate	datetime [current_timestamp()]
  --------------------------------------------
  */

// Need to generate a userid
$userid = func::createString(32);

  $registerQuery = "INSERT INTO users
    (userid, username, password, email, phone, FirstName, LastName, Address1, Address2, City, State, Zip)
    VALUES (:userid, :username, :password, :emailAddr, :phoneNum, :firstName, :lastName, :addr1, :addr2, :city, :state, :zip);";

  func::runQuery($db, $registerQuery,
    array(
      ':userid' => $userid,
      ':username' => $username,
      ':password' => $password,
      ':emailAddr' => $emailAddr,
      ':phoneNum' => $phoneNum,
      ':firstName' => $firstName,
      ':lastName' => $lastName,
      ':addr1' => $addr1,
      ':addr2' => $addr2,
      ':city' => $city,
      ':state' => $state,
      ':zip' => $zip
    ),
  true, $debug);

?>
<script type="text/javascript" src="js/validation.js"></script>
