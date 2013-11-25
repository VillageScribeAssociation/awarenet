<?php

	if ('admin' !== $kapenta->user->role) { $kapenta->page->do403(); }
	$keyFile = '/var/www/awarenet/data/mypubkey.txt';
	$listFile = '/var/www/awarenet/data/keylist.txt';

	$pubKeyring = '/var/www/awarenet/data/pubkeyring.gpg';
	$priKeyring = '/var/www/awarenet/data/prikeyring.gpg';

	//echo implode(file($keyFile));
	
	$shellCmd = 'gpg --batch --no-tty --yes --import "' . $keyFile . '"';
	echo $shellCmd . "<br/>\n";

	echo shell_exec($shellCmd);
	//echo shell_exec('gpg --version');

	echo "<br/>listing keys:<br/>\n";
	echo shell_exec('gpg --batch --list-keys');


	echo "<br/>keyring:<br/>\n";
	echo shell_exec('gpg --list-options show-keyring');
	//echo "file: " . implode(file($listFile));
?>
