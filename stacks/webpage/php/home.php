<?php
  include_once $_SERVER["DOCUMENT_ROOT"].'/lib/libs.php';
  if (!func::checkLoginState($db)) echo "Not logged in <br/>\n";
  include_once "header.php";
  //print_R($_SESSION);

  echo "<H1>Welcome ".$_SESSION['username']."! </H1><br/>\n";
  //func::dumpvars(['username' => $username, 'userid' => $userid, 'token' => $token, 'serialNum' => $serialNum]);
  //if (!func::checkLoginState($db)) echo "Not logged in <br/>\n";
  include_once "footer.php";
?>
