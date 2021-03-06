<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Local Sign Up</title>
		<style>
			@import url("https://fonts.googleapis.com/css2?family=Signika+Negative&display=swap");

			.body {
				position: fixed;
				overflow-y: scroll;
				width: 100%;
				top: -20px;
				right: -40px;
				width: auto;
				height: auto;
				background-color: black;
			}

			.head {
				position: absolute;
				top: calc(50% - 35px);
				left: calc(50% -255px);
			}

			/* This is for the big 24/7 symbol at the top left hand corner. */
			.header div {
                position: absolute;
                top: calc(30% - 50px);
                left: calc(52% - 50px);
				color: black;
				font-family: "Signika Negative", sans-serif;
				font-size: 50px;
				font-weight: 200;
			}

            .subheader div {
                position: absolute;
                top: calc(39% - 50px);
                left: calc(49.5% - 50px);
				color: black;
				font-family: "Signika Negative", sans-serif;
				font-size: 25px;
				font-weight: 200;
			}
            
            .instruction div {
                position: absolute;
                top: calc(47% - 50px);
                left: calc(35% - 50px);
				color: black;
				font-family: "Signika Negative", sans-serif;
				font-size: 15px;
				font-weight: 200;
			}
            

			.login {
				position: absolute;
				top: calc(50% - 75px);
				left: calc(45% - 50px);
				height: 150px;
				width: 350px;
				padding: 25px;
			}

			/* for the username box. */
			.login input[type="email"] {
				width: 250px;
				height: 30px;
				background: #96d6ed;
				border: 1px solid rgba(255, 255, 255, 0.6);
				border-radius: 2px;
				color: black;
				font-family: "Signika Negative", sans-serif;
				font-size: 16px;
				font-weight: 400;
				padding: 4px;
				margin-top: 10px;
			}

			/* For the password box. */
			.login input[type="password"] {
				width: 250px;
				height: 30px;
				background: #96d6ed;
				border: 1px solid rgba(255, 255, 255, 0.6);
				border-radius: 2px;
				color: black;
				font-family: "Signika Negative", sans-serif;
				font-size: 16px;
				font-weight: 400;
				padding: 4px;
				margin-top: 10px;
			}

			.login input[type="confirm_password"] {
				width: 250px;
				height: 30px;
				background: #96d6ed;
				border: 1px solid rgba(255, 255, 255, 0.6);
				border-radius: 2px;
				color: black;
				font-family: "Signika Negative", sans-serif;
				font-size: 16px;
				font-weight: 400;
				padding: 4px;
				margin-top: 10px;
			}

			/* For the login button. */
			.login input[type="button"] {
				position: absolute;
				left: calc(35% - 50px);
				width: 130px;
				height: 30px;
				background: #e3aba1;
				border: 1px black;
				cursor: pointer;
				border-radius: 2px;
				color: black;
				font-family: "Signika Negative", sans-serif;
				font-size: 16px;
				font-weight: 400;
				padding: 6px;
				margin-top: 25px;
			}

			.login input[type="button"]:hover {
				opacity: 0.8;
			}

			.login input[type="button"]:active {
				opacity: 0.6;
			}

            /* A boarder to created to "highlight" the selected field. */
			.login input[type="text"]:focus {
				outline: none;
				border: 1px solid rgba(255, 255, 255, 0.9);
			}

			.login input[type="email"]:focus {
				outline: none;
				border: 1px solid rgba(255, 255, 255, 0.9);
			}

			.login input[type="password"]:focus {
				outline: none;
				border: 1px solid rgba(255, 255, 255, 0.9);
			}

			.login input[type="button"]:focus {
				outline: none;
			}

			.back {
				position: absolute;
				top: calc(65% - 50px);
				left: calc(54.5% - 50px);
				font-family: "Signika Negative", sans-serif;
			}

			/* For the word 'username' and 'password' in the username and password boxes. */
			::-webkit-input-placeholder {
				color: rgba(50, 50, 50, 0.3);
			}

			::-moz-input-placeholder {
				color: rgba(255, 255, 255, 0.6);
			}
		</style>
	</head>

	<body style="background-color: #f7f6f1" oncontextmenu="return false">

		<div class="body"></div>
		<div class="grad"></div>
		<div class="header">
			<div>24/7</div> <br>
		</div>

        <div class="subheader">
            <div>Password Reset</div> <br>
        </div>

        <div class="instruction">
            <div>Enter the email address you use to log in to 24/7 and a link to reset your password will be sent.</div> <br>
        </div>

		<form name="login">
			<div class="login">
				<input type="email" placeholder="Email" name="email"/><br />
				<input type="button" onclick="check(this.form)" value="Send email" />
			</div>

			<footer class="footer">
				<a class="back" href="http://localhost/login.php">Back</a>
			</footer>
		</form>

		<script language="javascript">
            function validateEmail(email) {
                var re = /^(?:[a-z0-9!#$%&amp;'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&amp;'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/;

                return re.test(email);
            }
      
			function check(form) {
                if (form.email.value.length == 0) {
                    alert("Please enter your email address.");
                } else if (validateEmail(form.email.value)){
                    alert("A link to reset your password has been sent to the email address above.");
                } else {
                    alert("Please enter a valid email address.");
                }
            }           
		</script>
	</body>
</html>