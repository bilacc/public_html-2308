<?php 
	include '../../lib/functions.php';

	$error = '';

	if($_POST)
	{
		$user = new Admin_User;
		$login = $user->login_user($_POST);
		
		//var_dump($_SESSION['user']);
		
		if( $login )
		{
			$stats = new Admin_Stats;
			$stats->create_login_logg();
			
			$_SESSION['KCFINDER'] = array();
			$_SESSION['KCFINDER']['disabled'] = false;
			
			header('Location:'._SITE_URL.'admin');
			exit;
		}
		else
		{
			$error = 'Uneseno korisničko ime ili lozinka nisu ispravni!';
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login - Virtus CMS administracija</title>
<meta name='robots' content='noindex,nofollow' />
<meta name="author" content="Virtus dizajn" />
<meta name="viewport" content="initial-scale=1.0, width=device-width, maximum-scale=1.0, user-scalable=no" />
<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
<link href="../images/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link href="../include/css/style.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]>
<meta http-equiv="refresh" content="0; url="warning" />
<script type="text/javascript">
/* <![CDATA[ */
window.top.location = '<?php echo _SITE_URL; ?>warning';
/* ]]> */
</script>
<![endif]-->

<!--[if lte IE 7]>
<meta http-equiv="refresh" content="0; url="warning" />
<script type="text/javascript">
/* <![CDATA[ */
window.top.location = '<?php echo _SITE_URL; ?>warning';
/* ]]> */
</script>
<![endif]-->

</head>

<body>
<div class="login-box">

	<h1><img src="../images/admin_logo.png" alt="Virtus dizajn - CMS web administracija" /></h1>
	
	<img class="ikona" src="../images/icon-virtus.png" alt="Virtus dizajn" /> 

	<form id="form1" name="form1" method="post" action="">
		
		<?php
		if($error != ''){
			echo '<div class="error">'.$error.'</div>';
		}
		?>
		
		<label for="user_name">Korisničko ime:</label>
		<input name="user_name" type="text" id="user_name" maxlength="20"/>
		
		<label for="password">Lozinka:</label>
		<input name="password" type="password" id="password" maxlength="20"/>
		
		<input name="Submit" type="submit" id="log_btn" value="Ulaz" />

	</form>
	
</div>

</body>
</html>