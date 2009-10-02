<?


//--------------------------------------------------------------------------------------------------
//    this is the main setup file for configuring your awareNet installation
//--------------------------------------------------------------------------------------------------

//  (1) INSTALL PATH
//  This is how the project knows where it is on the system, for constructing absolute local 
//	filesystem paths.

    $installPath = '/var/www/an2google/trunk/';

//  (2) SERVER PATH
//  Tells the project where it is on the net, set to '/' if there is any doubt, or if more than 
//	one domain name will be used.

    $serverPath = 'http://awarenet.co.za/';

//  (3) DATABASE USER
//  This is the account through which the project will access the database.

    $dbUser = 'strix';
    $dbPass = '';
    $dbHost = 'localhost';
    $dbName = 'awarenet';

//  (4) Default Module
//  This is the module which handles '/' requests
    
    $defaultModule = 'static';

//  (5) BLOCK CACHE
//  Enable block cache

    $useBlockCache = 'false';

//  (6) SITE NAME
//  Website name - for page titles

    $websiteName = 'awareNet';

//  (7) THEME
//  Which of the installed themes the site is currently using.

    $defaultTheme = 'clockface';

//  (8) LOG LEVEL
//  Log website activity to the specified level (0 - none, 1 - web requests, 2 - debug)

    $logLevel = '2';
    
?>
