<?

	require_once("core/kregistry.class.php");

//--------------------------------------------------------------------------------------------------
//*	intuit basic settings and then redirect to /admin/firstrunlinux/
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check if installed
	//----------------------------------------------------------------------------------------------

	$registry = new KRegistry();

	if ('yes' == $registry->get('firstrun.complete')) {
		echo ''
		 . "<p>awareNet installation is complete and this script has been disabled as a security "
		 . "precaution.  To re-run the installation please delete the firstrun.complete registry "
		 . "key, or remove the line beginning with this value from "
		 . "./core/registry/firstrun.kreg.php.</p>";
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	get/set installpath and serverpath
	//----------------------------------------------------------------------------------------------

	$script = basename($_SERVER['SCRIPT_NAME']);

	$installPath = str_replace($script, '', $_SERVER['SCRIPT_FILENAME']);
	$serverPath = ''
				. 'http://' . $_SERVER['HTTP_HOST'] 
				. str_replace($script, '', $_SERVER['SCRIPT_NAME']);


	$registry->set('kapenta.installpath', $installPath);
	$registry->set('kapenta.serverpath', $serverPath);
	$registry->set('kapenta.os', 'linux');

	echo "Set location on server: $installPath<br/>\n";
	echo "Set location on network: $serverPath<br/>\n";

	//----------------------------------------------------------------------------------------------
	//	rewrite rules
	//----------------------------------------------------------------------------------------------
	$scriptFile = $_SERVER['SCRIPT_FILENAME'];
	$docRoot = $_SERVER['DOCUMENT_ROOT'];
	$runPath = str_replace($script, '', $scriptFile);

	$htaccess = "
        ErrorDocument 404 /index.php

        RewriteEngine on
        RewriteBase /
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]
	";

	if ($runPath == $docRoot) {
		echo "document root matches working directory... installing into root...<br/>\n";
	} else {
		//------------------------------------------------------------------------------------------
		//	fix .htaccess file to rewrite to different index.php
		//------------------------------------------------------------------------------------------
		$subdir = str_replace($script, '', $_SERVER['SCRIPT_NAME']);
		$subdir = substr($subdir, 1);

		echo "Running in subdirectory $subdir ...<br/>\n";
		$htaccess = str_replace('index.php', $subdir . 'index.php', $htaccess);

		// remove traing slash to rewritebase
		//$subdirnt = substr($subdir, 0, strlen($subdir) - 1);
		//$htaccess = str_replace('RewriteBase /', 'RewriteBase /' . $subdirnt, $htaccess);

	}

	echo "<hr/><br/><h2>IMPORTANT</h2>\n";	
	echo ''
	 . "<p>Please add the following rewrite rules to your .htaccess file or Apache2 directory "
	 . "definition.</p>\n";

	echo "<small><pre>$htaccess</pre></small>";
	echo "<p>You will need to restart apache after doing so.</p>\n";

	//----------------------------------------------------------------------------------------------
	//	check for apache modules, etc
	//----------------------------------------------------------------------------------------------
	echo "<hr/><br/><h2>DEPENDENCIES</h2>\n";
	$allGood = true;


	//hat tip: http://christian.roy.name/blog/detecting-modrewrite-using-php
	if (true == function_exists('apache_get_modules')) {
		$modules = apache_get_modules();
		$mod_rewrite = in_array('mod_rewrite', $modules);
	} else {
		$mod_rewrite = getenv('HTTP_MOD_REWRITE')=='On' ? true : false ;
	}

	$mod_rewrite = true;	// temporary hack for ikamva host, TODO: remove, strix 2012-03-08

	if (false == $mod_rewrite) {
		echo ''
		 . "Apache mod_rewrite is <b>not available</b>, please install or enable it before you continue.  "
		 . "Often this can be done by moving/copying the loader:<br/>\n"
		 . "<small><pre>"
		 	. "from: /etc/apache2/mods-available/rewrite.load\n" 
			. "to: /etc/apache2/mods-enabled/rewrite.load\n"
		. "</pre></small><br/>\n";
		$allGood = false;

	} else {
		echo "mod_rewrite is enabled :-)<br/>\n";
	}

	if (false == function_exists('mysql_pconnect')) {
		echo ''
		 . "Extension php5-mysql is <b>not installed</b>, please install it before you continue."
		 . "Please check that persistent conenctions are enabled in php.ini."
		 . "<br/>\n<small><pre>some@debian-derived/$ sudo apt-get install php5-mysql</pre></small>";
		$allGood = false;

	} else {
		echo "Extension php5-mysql is installed :-)<br/>";
	}

	if (false == function_exists('curl_init')) {
		echo ''
		 . "Extension php5-curl is <b>not installed</b>, please install it before you continue."
		 . "<br/>\n<small><pre>some@debian-derived/$ sudo apt-get install php5-curl</pre></small>";
		$allGood = false;

	} else {
		echo "Extension php5-curl is installed :-)<br/>";
	}

	if (false == function_exists('imagecreatefromjpeg')) {
		echo ''
		 . "Extension php5-gd is <b>not installed</b>, please install it before you continue."
		 . "<br/>\n<small><pre>some@debian-derived/$ sudo apt-get install php5-gd</pre></small>";
		$allGood = false;

	} else {
		echo "Extension php5-gd is installed :-)<br/>";
	}

	//----------------------------------------------------------------------------------------------
	//	come general notes
	//----------------------------------------------------------------------------------------------

	echo "<br/><hr/><br/><h2>NOTES</h2>\n";

	echo '' 
	 ."<p>Since awareNet will periodically need to run batch processes for maintenance and "
	 . "may need time to transfer files if using p2p, "
	 . "please ensure that PHP allows execution times of at least 15 minutes (800 seconds), or "
	 . "these operations may be cut short.</p>\n"
	 . "<blockquote><small><pre>"
		 . "cat /etc/php5/apache2/php.ini | grep \"max_execution_time\""
	 . "</pre></small></blockquote>"
	 . "<p>Please also ensure that persistent connections are enabled for MySQL and short tags are "
	 . "turned on in php.ini.</p>\n"
	 . "<p>You will also need a cron entry to run http://yoursite.tld/admin/cron/ every 10 "
	 . "minutes or so.  If using <i>gnome-schedule</i> you should do the following:</p>\n"
	 . "<img src='" . $serverPath . "themes/clockface/images/schedule.png'>"
	 . "<p>Where the command to be executed is like:</p>"
	 . "<blockquote><small><pre>"
		 . "wget --output-document=/home/username/awarenet.cron.html "
		 . "http://yoursite.tld/admin/cron/"
	 . "</pre></small></blockquote>\n"
	 . "<p>This will run background tasks and place a report in your home directory, so that you "
	 . "can confirm that things are ticking over.</p>";

	//----------------------------------------------------------------------------------------------
	//	continue to installing and configuring database
	//----------------------------------------------------------------------------------------------

	if (true == $allGood) {
		echo ''
		 . "<hr/><br/><h2>INSTALL AND CONFIGURE</h2>\n"
		 . "<form name='runInstaller' method='POST' action='admin/firstrun.linux/'>\n"
		 . "All done? <input type='submit' value='Install awareNet!' />\n"
		 . "</form>\n";

	} else {
		echo "<p>Installation cannot proceed, please address the issues above.</p>";
	}

?>
