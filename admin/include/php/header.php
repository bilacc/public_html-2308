<?php
	require_once('../lib/functions.php');

	$user = new Admin_User;
	if( ! $user->is_logged() )
	{
		header('Location:'._SITE_URL.'admin/login');
		exit;
	}
	
	if( ! get_conf('production') )
		$page_stats = new PageStats;
	
	if( isset($_GET['odjava']) && $_GET['odjava'] == 'odjava' )
	{
		$user->logout();
		header('Location:'._SITE_URL.'admin/login/');
		exit;
	}
	
	if(!isset($_SESSION['on-page']) && empty($_SESSION['on-page']))
	{
		$_SESSION['on-page'] = 30;
	}
	
	$filename_array = explode("/", $_SERVER['SCRIPT_FILENAME']);
	$filename = array_pop($filename_array);
	$filename = str_replace(".php", "", $filename);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<title><?php print get_conf('app_name'); ?> - Adresar CMS</title>
	<meta name='robots' content='noindex,nofollow' />
	<meta name="author" content="Adresar nekretnine" />
	<meta name="viewport" content="initial-scale=1.0, width=device-width, maximum-scale=1.0, user-scalable=no" />
	
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href="images/favicon.png" rel="shortcut icon" type="image/x-icon"/>
	<link href="include/css/style.css" rel="stylesheet" type="text/css" />
	<link href="include/css/460up.css" rel="stylesheet" media="screen and (min-width: 460px)"/>
	<link href="include/css/560up.css" rel="stylesheet" media="screen and (min-width: 560px)"/>
	<link href="include/css/660up.css" rel="stylesheet" media="screen and (min-width: 660px)"/>
	<link href="include/css/760up.css" rel="stylesheet" media="screen and (min-width: 760px)"/>
	<link href="include/css/860up.css" rel="stylesheet" media="screen and (min-width: 860px)"/>
	<link href="include/css/960up.css" rel="stylesheet" media="screen and (min-width: 960px)"/>
	<link href="include/css/1280up.css" rel="stylesheet" media="screen and (min-width: 1280px)"/>
	<link href="include/css/1600up.css" rel="stylesheet" media="screen and (min-width: 1600px)"/>
	<link href="include/css/dropzone.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="include/js/jquery-1.8.3.min.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<script type="text/javascript" src="include/js/datepicker.js"></script>
	<script type="text/javascript" src="include/js/google_maps.js"></script>
	<script type="text/javascript" src="include/js/respond.js"></script>
	<script type="text/javascript" src="include/js/dropzone.js"></script>
	<script type="text/javascript" src="include/js/functions.js"></script>
	

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDAHDJyEgp3Vw-75IpWUu5B_bq8xtQNKMI&libraries=places" async defer></script>
	

	
	
	<script type="text/javascript" src="../js/sajax.js"></script>
	
	<script type="text/javascript" src="../lib/plugins/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="../lib/plugins/ckeditor/adapters/jquery.js"></script>
	
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
<div class="container">
	<div class="header">
		<a class="menu-toggle" href="javascript:;" title="Izbornik"><img src="images/icon-menu.png" alt="Menu" /></a>
		<div class="site-info">	
			<a class="logo" href="http://adresar.net" target="_blank">
				<img src="images/icon-virtus.png" alt="" />
			</a>
			<a class="title" href="<?php echo _SITE_URL; ?>" target="_blank" title="Pogledajte vašu stranicu">
				<span><?php echo _SITE_TITLE; ?></span>
				<img src="images/icon-visit-page.png" alt="<?php echo _SITE_TITLE; ?>" />
			</a>	
		</div>
		<div class="user-info">
			<a class="avatar" href="profile.php" title="Vaš profil">
				<?php 
				$avatar = Db::query_one('SELECT photo_name FROM site_photos WHERE table_name = "admin_users" AND table_id = '.$_SESSION['admin']['id'].' ORDER BY orderby DESC LIMIT 1');
				if($avatar){
				?>
				<img src="<?php echo _SITE_URL;?>lib/plugins/thumb.php?src=<?php echo _SITE_URL; ?>upload_data/site_photos/th_<?php echo $avatar; ?>&w=40&h=40&zc=1" alt="" />
				<?php }else{ ?>
				<img src="images/user-default.png" alt="" />
				<?php } ?>
				<span><img src="images/icon-mask.png" alt="" /></span>
			</a>
			<span>Prijavljeni ste kao: <?php echo $_SESSION['admin']['username']; ?></span><br/>
			<a href="profile.php">Promjena podataka</a><span class="v-separator">|</span><a href="index.php?odjava=odjava">Odjava</a>
		</div>
		<a class="logout" href="index.php?odjava=odjava" title="Odjavite se"><img src="images/icon-logout.png" alt="Logout" /></a>
	</div>
	
	<div class="menu">
		<?php
		// POPIS KLASA ZA IKONE U MENIJU:
		//   - pocetna
		//   - kategorije
		//   - stranice
		//   - novosti
		//   - media
		//   - proizvodi
		//   - narudzbe
		//   - newsletter
		//   - korisnici
		?>
		<ul>
			<li <?php echo ($filename == 'index')? 'class="mslc"':'';?>>
				<a class="pocetna" href="index.php"><span>Početna</span></a>
			</li>
			<li <?php echo ($filename=='home_page')?'class="mslc"':'';?>>
				<a class="media" href="home_page.php">
					<span>Naslovna</span>
				</a>
				<ul class="sub" <?php echo ($filename=='home_page')?'style="display:block;"':'';?>>
					<li <?php echo ($filename == 'home_page')? 'class="mslc"':'';?>>
						<a href="home_page.php"><span>Tekst</span></a>
					</li>
				</ul>
			</li>
			<li <?php echo ($filename=='our_team_pregled' ||  $filename=='about_us' || $filename=='our_team_unos' || $filename=='o_nama_pregled' || $filename=='o_nama_unos')?'class="mslc"':'';?>>
				<a class="profile-icon" href="o_nama_pregled.php">
					<span>O nama</span>
				</a>
				<ul class="sub" <?php echo ($filename=='tim' || $filename=='o_nama_pregled' || $filename=='o_nama_unos' || $filename=='our_team_pregled' || $filename=='about_us' || $filename=='our_team_unos')?'style="display:block;"':'';?>>
					<li <?php echo ($filename=='tim')? 'class="mslc"':'';?>><a href="tim.php">Naš tim - uvod</a></li>
					<li <?php echo ($filename=='our_team_pregled')? 'class="mslc"':'';?>><a href="our_team_pregled.php">Naš tim</a></li>
				</ul>
			</li>
			<?php /*<li <?php echo ($filename=='clients_uvod' || $filename=='clients_pregled' || $filename=='clients_unos')?'class="mslc"':'';?>>
				<a class="profile-icon" href="clients_uvod.php">
					<span>Klijenti o nama</span>
				</a>
				<ul class="sub" <?php echo ($filename=='clients_uvod' || $filename=='clients_pregled' || $filename=='clients_unos')?'style="display:block;"':'';?>>
					<li <?php echo ($filename == 'clients_uvod')? 'class="mslc"':'';?>>
						<a href="clients_uvod.php"><span>Uvodni tekst </span></a>
					</li>
					<hr>
					<li <?php echo ($filename=='clients_pregled')? 'class="mslc"':'';?>><a href="clients_pregled.php">Izjave</a></li>
				</ul>
			</li>*/?>
			<li <?php echo ($filename=='items_pregled' || $filename=='items_unos')?'class="mslc"':'';?>>
				<a class="icon-apartment" href="items_pregled.php">
					<span>Nekretnine</span>
				</a>
				<ul class="sub" <?php echo ($filename=='energetski_certifikat_pregled' || $filename=='energetski_certifikat_unos' || $filename=='grijanje_pregled' || $filename=='grijanje_unos' || $filename=='namjestenost_pregled' || $filename=='namjestenost_unos' || $filename=='categories_pregled' || $filename=='categories_unos' || $filename=='specifikacije_unos' || $filename=='specifikacije_pregled' || $filename=='city_pregled' || $filename=='city_unos' || $filename=='hood_pregled' || $filename=='hood_unos' || $filename=='items_pregled' || $filename=='items_uvod' || $filename=='items_unos')?'style="display:block;"':'';?>>
					
					<li <?php echo ($filename=='city_pregled' || $filename=='city_unos')? 'class="mslc"':'';?>><a href="city_pregled.php">Lokacije</a></li>
					<?php /*<li <?php echo ($filename=='hood_pregled' || $filename=='hood_unos')? 'class="mslc"':'';?>><a href="hood_pregled.php">Kvartovi</a></li>*/?>
					<hr>
					<li <?php echo ($filename=='categories_pregled' || $filename=='categories_unos')? 'class="mslc"':'';?>><a href="categories_pregled.php">Tip nekretnine</a></li>
					<li <?php echo ($filename=='items_pregled')? 'class="mslc"':'';?>><a href="items_pregled.php">Nekretnine</a></li>
					<hr>
					<!-- <li <?php echo ($filename=='specifikacije_pregled')? 'class="mslc"':'';?>><a href="specifikacije_pregled.php">Specifikacije</a></li> -->
					<li <?php echo ($filename=='namjestenost_pregled' || $filename=='namjestenost_unos')? 'class="mslc"':'';?>><a href="namjestenost_pregled.php">Namještenost</a></li>
					<li <?php echo ($filename=='grijanje_pregled' || $filename=='grijanje_unos')? 'class="mslc"':'';?>><a href="grijanje_pregled.php">Grijanje</a></li>
					<li <?php echo ($filename=='katnost_pregled' || $filename=='katnost_unos')? 'class="mslc"':'';?>><a href="katnost_pregled.php">Katnost</a></li>
					<li <?php echo ($filename=='energetski_certifikat_pregled' || $filename=='energetski_certifikat_unos')? 'class="mslc"':'';?>><a href="energetski_certifikat_pregled.php">Energetski certifikat</a></li>
				</ul>
			</li>
		
<li <?php echo ($filename=='blog_uvod' || $filename=='blog_pregled' || $filename=='blog_unos')?'class="mslc"':'';?>>
				<a class="stranice" href="blog_pregled.php">
					<span>Blog</span>
				</a>
				
			</li>
<li <?php echo ($filename=='news_uvod' || $filename=='news_pregled' || $filename=='news_unos')?'class="mslc"':'';?>>
				<a class="stranice" href="news_pregled.php">
					<span>Novosti</span>
				</a>
				
			</li>

		
			<li <?php echo ($filename=='categories_usluge_pregled' || $filename=='categories_usluge_unos' || $filename == 'usluge_i_cjenik' || $filename=='services_pregled' || $filename=='services_unos')? 'class="mslc"':'';?>>
				<a class="stranice" href="usluge_i_cjenik.php"><span>Dodatne usluge</span></a>
				<ul class="sub" <?php echo ($filename=='categories_usluge_pregled' || $filename=='categories_usluge_unos' || $filename == 'usluge_i_cjenik' || $filename=='services_pregled' || $filename=='services_unos')?'style="display:block;"':'';?>>
					
					<li <?php echo ($filename=='categories_usluge_pregled' || $filename=='categories_usluge_unos')? 'class="mslc"':'';?>><a href="categories_usluge_pregled.php">Kategorije usluga</a></li>
					<li <?php echo ($filename=='services_pregled')? 'class="mslc"':'';?>><a href="services_pregled.php">Pregled usluga</a></li>
				</ul>
			</li>




			<li <?php echo ($filename == 'contact')? 'class="mslc"':'';?>>
				<a class="contact-icon" href="contact.php"><span>Kontakt</span></a>
			</li>

			<li <?php echo ($filename == 'komentari_pregled' || $filename == 'komentari_unos')? 'class="mslc"':'';?>>
				<a class="" href="komentari_pregled.php"><span>Komentari</span></a>
			</li>
		

			<?php /*
			<li <?php echo ($filename=='newsletter_pregled' || $filename=='newsletter_unos' || $filename=='newsletter_email_pregled')?'class="mslc"':'';?>>
				<a class="newsletter" href="newsletter_pregled.php">
					<span>Newsletter</span>
				</a>
				<ul class="sub" <?php echo ($filename=='newsletter_pregled' || $filename=='newsletter_unos' || $filename=='newsletter_email_pregled')?'style="display:block;"':'';?>>
					<li <?php echo ($filename=='newsletter_unos')? 'class="mslc"':'';?>><a href="newsletter_unos.php">Unos</a></li>
					<li <?php echo ($filename=='newsletter_pregled')? 'class="mslc"':'';?>><a href="newsletter_pregled.php">Pregled</a></li>
					<li <?php echo ($filename=='newsletter_email_pregled')? 'class="mslc"':'';?>><a href="newsletter_email_pregled.php">E-mail adrese</a></li>
				</ul>
			</li>
			*/?>
		</ul>

		<a class="virtus-logo" href="http://virtus-dizajn.com" target="_blank"><img src="images/virtus-logo.png" alt="Virtus dizajn"></a>
	</div>
	
	<div class="content">
