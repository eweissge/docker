<?php
	include 'header.php';
?>
	<BODY>
		<!-- Create registration form! -->
		<script type="text/javascript" src="/js/validation.js"></script>
		<FORM id="regform" class="form-signin" action="registeruser.php" method="POST">
			<input id="username" type="text" class="form-control" name="username" placeholder="Username" required autofocus>
			<input id="pass" type="password" class="form-control" name="password" placeholder="Password" required>
			<input id="email" type="email" class="form-control" name="emailAddr" placeholder="Email" required>
			<input id="phone" type="text" class="form-control" name="phoneNum" placeholder="Phone Number" required>
			<input id="fname" type="text" class="form-control" name="firstName" placeholder="First Name" required>
			<input id="lname" type="text" class="form-control" name="lastName" placeholder="Last Name" required>
			<input id="addr1" type="text" class="form-control" name="addr1" placeholder="Address 1" required>
			<input id="addr2" type="text" class="form-control" name="addr2" placeholder="Address 2">
			<input id="city" type="text" class="form-control" name="city" placeholder="City" required>
			<input id="state" type="text" class="form-control" name="state" placeholder="State" required>
			<input id="zip" type="text" class="form-control" name="zip" placeholder="Zip Code" required>
			<button id="submit" class="btn btn-lg btn-primary btn-block" type="submit">Sign In</button>
		</FORM>
	</BODY>
</HTML>
