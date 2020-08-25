<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
	// if file is set, upload to gallery
	if(isset($_FILES['picToUpload']['size']))
		$file = $_FILES['picToUpload']['size'] != 0;
	else $file = false;
	$uploadSafe = true;
	$uploadComplete = false;
	if($file)
	{
		$fileDir = 'gallery/' . basename($_FILES["picToUpload"]["name"]);
		$fileType = $_FILES["picToUpload"]["type"];
		if($_FILES["picToUpload"]["size"] < 1000000 )
		{
			if($fileType == 'image/jpg' || $fileType == 'image/jpeg')
			{}
			else $uploadSafe = false;
		}
		else $uploadSafe = false;

		if($uploadSafe)
		{
			if (move_uploaded_file($_FILES["picToUpload"]["tmp_name"], $fileDir))
			{
				$uploadComplete = true;
			}
			else
    		$uploadComplete = false;
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Iandro van der Linde">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";

					echo 	"<form action='login.php' method='post' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='hidden' value='" . $email . "' name='loginEmail' />
									<input type='hidden' value='" . $pass . "' name='loginPass' />
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
						  	</form>";
					if($file)
					{
						if($uploadComplete)
						{
							echo "<div class='card bg-success'><div class='card-body'>Successfully uploaded file!</div></div>";
							$id = $row['user_id'];
							$filename = $_FILES["picToUpload"]["name"];
							if($mysqli->query("INSERT INTO tbgallery (user_id, filename) VALUES ('$id','$filename')") === FALSE)
								echo "<div class='card bg-danger'><div class='card-body'>Database insert error.</div></div>";
						}
						else if(!$uploadSafe)
							echo "<div class='card bg-danger'><div class='card-body'>Incorrect file dimensions or type. Please upload a jpeg/jpg image with less than 1MB size.</div></div>";
						else
							echo "<div class='card bg-danger'><div class='card-body'>Error uploading file!</div></div>";
					}
					echo "<h4>Image Gallery</h4><div class='row imageGallery'>";
					$id = $row['user_id'];
					$res = $mysqli->query("SELECT * FROM tbgallery WHERE user_id = '$id'");
					while($row = mysqli_fetch_array($res))
					{
						$url = 'gallery/' . $row['filename'];
						echo "<div class='col-3' style='background-image: url($url)'></div>";
					}
					echo "</div>";
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			}
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>
