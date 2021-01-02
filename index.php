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

	if (isset($_POST['download'])) {
		print('Path to download: ' . './' . $_GET["path"] . $_POST['download']);
		$ﬁle = './' . $_GET["path"] . $_POST['download'];
		$ﬁleToDownloadEscaped = str_replace("&nbsp;", " ", htmlentities($ﬁle, null, 'utf-8'));
		ob_clean();
		ob_start();
		header('Content-Description: File Transfer');
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; ﬁlename=' . basename($ﬁleToDownloadEscaped));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($ﬁleToDownloadEscaped));
		ob_end_flush();
		readfile($ﬁleToDownloadEscaped);
		exit;
	}

	if (isset($_POST['delete'])) {
		unlink($_POST['delete']);
	}
	$msg = '';
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
		$login = '<div align="center"><form action="./index.php" method="post">
				<h4>Please enter your Username and Password</h4>
				<h5>' . $msg . '</h5>
				<input type="text" name="username" placeholder="username = test" required autofocus></br>
				<input type="password" name="password" placeholder="password = test" required></br>
				<button class = "login btn btn-primary btn-s" type="submit" name="login">Login</button>
			</form>
			</div>';
		print $login;
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
					<button type="button" class="btn btn-success btn-xs">Create</button>
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
	<tr/>
';
		if (count($folder) > 0) {
			foreach ($folder as $name) {
				if (!$name || $name[0] == '.') {
					continue;
				}
				$output .= '
				<tr>
					<td>' . (is_dir($name) ? '<i class="far fa-folder"></i>  <a id="view_files" href=?path=' . $name . '>' . $name . '</a>' : '<i class="far fa-file"></i>  ' . $name) . '</td>
					<td>' . (is_dir($name) ? 'Folder' : 'File') . '</td>
					<td>
					<form action="" method="post">
					<button type="submit" name="dowload" data-name="' . $name . '" class="dowload btn btn-primary btn-s">
					Dowload</button>
					 <button type="submit" name="delete" value="' . $name . '" class="delete btn btn-danger btn-s ' . (strpos($name, '.php') || is_dir($name) ? 'disabled' : '') . '">
					Delete</button></td>
					</form>
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
		$output .= '</table></div>	<div>
		<br>
		<div align="left">
		<a class="btn btn-primary" href="?= $previous ?">Back</a>
		</div>
		<div align="right">Click here to <a href = "index.php?action=logout"> logout.</a></div>
		</div>';
		print $output;
	}
	?>
</body>
<script>

</script>

</html>