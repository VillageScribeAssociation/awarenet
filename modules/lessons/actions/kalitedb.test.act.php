<?php

//--------------------------------------------------------------------------------------------------
//* test connectivity to kalite database
//--------------------------------------------------------------------------------------------------

    if ('admin' !== $kapenta->user->role) { $kapenta->page->do403(); }

    $dbFile = 'data/export/data.sqlite';
    $connected = false;
    $msg = '';

    //$query = "SELECT * FROM sqlite_master WHERE type='table';";
    $query = "SELECT * FROM auth_user";

	try {
		$dbh = new PDO('sqlite:' . $dbFile);
    	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	$connected = true;

	} catch(PDOException  $e) {

		$msg = "SQLite PDO Connection failed: " . $e->getMessage();
		if (true == isset($session)) { $kapenta->session->msgAdmin($msg, 'bad'); }
		else { echo $msg . "<br/>\n"; }
		die();
	}

    if (true === $connected) {
        echo "Connected to SQLite database.<br/>";

        try { $sth = $dbh->prepare($query); }
		catch(PDOException $e) {
			$msg = "Failed to prepare SQL Statement: $query<br/>\n" . $e->getMessage();
			if (true == isset($session)) { $kapenta->session->msgAdmin($msg, 'bad'); }
			else { echo $msg . "<br/>\n"; }
			die();
		}

        echo "We have prepared a query.<br/>";


		//------------------------------------------------------------------------------------------
		//	execute the query
		//------------------------------------------------------------------------------------------
		try { $check = $sth->execute(); }
		catch(PDOException $e) {
			$msg = "Failed to execute SQL Statement: $query<br/>\n" . $e->getMessage();
			if (true == isset($session)) { $kapenta->session->msgAdmin($msg, 'bad'); }
			else { echo $msg . "<br/>\n"; }
			die();
		}

		if (false === $check) {
			$msg = "Could not execute database query:<br/>" . $query . "<hr/><br/>" . mysql_error();
			if (true == isset($session)) { $kapenta->session->msgAdmin($msg, 'bad'); }
			die();
		}

        echo "Now we have executed a query<br/>";

        while ($row = $sth->fetch()) {
            echo "<pre>";
            print_r($row);
            echo "</pre>";

            echo "Password string: " . $row['password'] . "<br/>";
            echo "Password components:<br/><pre>";
            print_r(explode('$', $row['password']));
            echo "</pre>";

        }

    } else {
        echo "Could not connect: $msg<br/>";
    }


?>
