<?php
	function get_conf($conf_val)
	{
		$conf['app_name'] = 'Adresar.net';
		$conf['email'] = 'info@adresar.net';
		$conf['use_database'] = 1;
		$conf['database_host'] = "localhost";
		$conf['database_name'] = "adresa07_elionnek_2017";
		$conf['database_username'] = "adresa07_2017";
		$conf['database_password'] = "gSuT98*71{il";
		$conf['multi_language'] = 1;
		$conf['languages'] = array('hr','en');
		$conf['production'] = 1;
		$conf['routes'] = array(
		);
		return $conf[$conf_val];
	}
	define('_SITE_TITLE', 'Adresar Nekretnine');
	if(strstr($_SERVER['SCRIPT_FILENAME'],"/htdocs/"))
	{
		define('_SITE_DIRECTORY', '/Tomislav/VirtusAdmin/');
		define('_SITE_ROOT', $_SERVER["DOCUMENT_ROOT"]._SITE_DIRECTORY);
	}
	else 
	{
		define('_SITE_DIRECTORY', '/');
		define('_SITE_ROOT', $_SERVER["DOCUMENT_ROOT"]._SITE_DIRECTORY);	
	}
	define('_GOOGLE_SITE_KEY', '6LfKurgUAAAAAC3xnvSBghgy06Ligl5Q6Z6XPFXF');
	define('_GOOGLE_SECRET_KEY', '6LfKurgUAAAAAKjR7ZlxOxSZZGfzAgCassMM4193');
	
	define('_SITE_URL', 'https://'.$_SERVER["SERVER_NAME"]._SITE_DIRECTORY);
	define('_SITE_DOMAIN', get_domain(_SITE_URL));
	define('_PHOTOS_URL', _SITE_URL.'slike');
	define('_FIRMA_EMAIL', 'info@adresar.net');
	define('_FIRMA_NAZIV', 'Adresar.net');
	define('_FIRMA_ADRESA', '');
	define('_FIRMA_OIB', '');
	define('_FIRMA_ZIRO_RACUN', '');
	define('_FIRMA_TELEFON', '');
	define('_FIRMA_MOBITEL', '');
	define('_FIRMA_FAX', '');
	define('_PDV', 25);
	define('_PDV_RACUNANJE', 1.25);
	define('_GORIVO',5);
	define('_STORE_COOKIE_NAME', 'sp_store');
	define('_STORE_SALT', 'ys#4se');
?>
