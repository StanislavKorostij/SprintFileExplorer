<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PHP file explorer</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" rel="stylesheet">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>

	<?php
	session_start();
	$previous = "javascript:history.go(-1)";
	if (isset($_SERVER['HTTP_REFERER'])) {
		$previous = $_SERVER['HTTP_REFERER'];
	}
	// logout logic
	if (isset($_GET['action']) and $_GET['action'] == 'logout') {
		session_start();
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		unset($_SESSION['logged_in']);
		print('<div align="center">Logged out!</div>');
	}
	// download logic
	if (isset($_POST['download'])) {
		//ternary if statment to check if the passed on value has or has not a path
		$filename = ($_GET['path'] . $_POST['download'] == $_POST['download']) ? $_GET['path'] . $_POST['download'] : $_GET['path'] . '/' . $_POST['download'];
		//Check the file exists or not
		if (file_exists($filename)) {
			print($filename);
			//Define header information
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: 0");
			header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
			header('Content-Length: ' . filesize($filename));
			header('Pragma: public');
			flush();
			readfile($filename);
			exit;
		} else {
			echo "File does not exist.";
		}
	}
	// create logic
	if (isset($_POST['create'])) {
		$fileName = $_POST['create'];
		//ternary if statment to check if the passed on value has or has not a path
		$_GET['path'] == '' ? fopen($_GET['path'] . $fileName, "w") : fopen($_GET['path'] . '/' . $fileName, "w");
	}

	if (isset($_POST['delete'])) {
		//ternary if statment to check if the passed on value has or has not a path
		$d = ($_GET['path'] . $_POST['delete'] == $_POST['delete']) ? $_GET['path'] . $_POST['delete'] : $_GET['path'] . '/' . $_POST['delete'];
		unlink($d);
	}

	$msg = '';

	//Login check
	if (
		isset($_POST['login'])
		&& !empty($_POST['username'])
		&& !empty($_POST['password'])
	) {
		if (
			$_POST['username'] == 'test' &&
			$_POST['password'] == 'test'
		) {
			$_SESSION['logged_in'] = true;
			$_SESSION['timeout'] = time();
			$_SESSION['username'] = 'test';
		} else {
			$msg = '<div align="center">Wrong password and or usename <br> P.s. It\'s test test</div>';
		}
	}
	if ($_SESSION['logged_in'] == false) {
		$login = '<div align="center">
					<form action="./index.php" method="post">
						<h4>Please enter your Username and Password</h4>
						<h5>' . $msg . '</h5>
						<input type="text" name="username" placeholder="username = test" required autofocus></br>
						<input type="password" name="password" placeholder="password = test" required></br>
						<button class="login btn btn-primary btn-s" type="submit" name="login">Login</button>
					</form>
				</div>';
		print $login;
		//if a User is logged in the elseif kicks in
	} elseif ($_SESSION['logged_in'] == true) {
		$d = './' . $_GET['path'];
		$folder = scandir($d);
		$output = '
					<br><br>
					<div class="container">

						<h2 align="center">Files and folders from Directory</h2>
						<br>
					<div align="right">
						<form action="" method="POST">
							<input type="text" id="create" name="create" placeholder="Create your file">
							<button type="submit" class="btn btn-success btn-xs">Create</button>
						</form>
						<br>
					</div>
						<br>

					<div id="folder_table" class="table-responsive">
						<table class="table table-bordered table-striped">
							<tr>
								<th>Name</th>
								<th>Type</th>
								<th>Actions</th>
								<tr />
								';
		if (count($folder) > 0) {
			foreach ($folder as $name) {
				if (!$name || $name[0] == '.') {
					continue;
				}
				$output .= '
						<tr>
							<td>' . (is_dir($name) ? '<i class="far fa-folder"></i> <a href=?path=' . $name . '>' . $name . '</a>' : '<i class="far fa-file"></i> ' . $name) . '</td>
							<td>' . (is_dir($name) ? 'Folder' : 'File') . '</td>
							<td>
								<form action="" method="post">
									<button type="submit" name="download" value="' . $name . '" class="dowload btn btn-primary btn-s">Dowload</button>
									<button type="submit" name="' . (strpos($name, '.php') || is_dir($name) ? '' : 'delete') . '" value="' . $name . '" class="delete btn btn-danger btn-s ' . (strpos($name, '.php') || is_dir($name) ? 'disabled' : '') . '">Delete</button>
								</form>
							</td>
						</tr>
						';
			}
		} else {
			$output = '
						<tr>
							<td colspan="6"> No Folder Found</td>
						</tr>
						';
		}
		$output .= '
						</table>
					</div>
						<div>
						<br>
						<div align="left">
						<a class="btn btn-primary" href="?= $previous ?">Back</a>
						</div>
					<div align="right">Click here to <a href="index.php?action=logout"> logout.</a></div>
				</div>';
		print $output;
	}
	?>
</body>
<script>

</script>

</html>