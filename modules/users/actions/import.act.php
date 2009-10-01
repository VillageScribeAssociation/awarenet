<?

//--------------------------------------------------------------------------------------------------
//	import users from CSV file
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/users/models/users.mod.php');

	echo "<small>\n";
	echo "<table>";
	echo "<tr><td>name---------------------------------------</td><td>grade</td><td>email</td><td>username</td><td>password</td><td>surname</td><td>firstname</td></tr>\n";

	$raw = implode(file($installPath . 'interest.csv'));
	$lines = explode("\n", $raw);
	foreach ($lines as $line) {
	  if (strlen($line) > 3) {

		$line = str_replace("\"", '', $line);
		$cells = explode(",", $line);	

		$name = $cells[0];
		$grade = $cells[1];
		$email = $cells[2];
		$username = $cells[3];
		$password = genPassword();
		
		$firstname = 'import';
		$surname = 'user';

		$spacePos = strpos($name, ' ');
		if ($spacePos > 0) {
			$firstname = trim(substr($name, 0, $spacePos));
			$surname = trim(substr($name, $spacePos));
		}

		echo "<tr><td>$name</td><td>$grade</td><td>$email</td><td>$username</td><td>$password</td><td>$surname</td><td>$firstname</td></tr>\n";

		$u = new Users();
		$u->data['UID'] = createUID();
		$u->data['ofGroup'] = 'import';
		$u->data['school'] = '176670059110919836';
		$u->data['grade'] = 'Gd. ' . $grade;
		$u->data['firstname'] = $firstname;
		$u->data['surname'] = $surname;
		$u->data['username'] = $username;
		$u->data['password'] = sha1($password . $u->data['UID']);
		$u->data['lang'] = 'en';
		$u->data['createdBy'] = 'admin';
		echo "<tr><td>";
		echo $u->save();
		echo "</td></tr>";
		
	  }
	}

	echo "</table>\n";

	function genPassword() {
		$words = 'hamlet tragedy william shakespeare believed written between play 
					denmark recounts prince hamlet exacts revenge uncle claudius 
					murdered hamlet throne married hamlet play vividly charts course 
					feigned madness overwhelming seething explores treachery revenge
					moral despite much literary detective work exact year of writing
					remains dispute three different early versions play have survived
					first quarto second quarto first folio scenes missing shakespeare
					probably based hamlet legend amleth preserved century chronicler
					saxo grammaticus gesta danorum subsequently retold century scholar
					supposedly elizabethan dramatic structure depth characterization
					hamlet analyzed interpreted argued about from many perspectives
					scholars debated centuries hamlet hesitation plot device prolong
					action result pressure exerted complex philosophical ethical 
					issues surround calculated thwarted desire recently psychoanalytic
					critics examined hamlet unconscious desires feminist critics evaluated
					rehabilitated maligned characters of ophelia gertrude hamlet shakespeare
					play powerful influential tragedies language provides storyline
					capable of seemingly endless retelling adaptation during shakespeare
					lifetime play popular works ranks high performed example royal shakespeare
					company list inspired writers from dickens to joyce murdoch described
					world filmed story cinderella created richard burbage tragedian of
					shakespeare hundred years played acclaimed actors sometimes actresses
					successive';

		$words = str_replace("\n", " ", $words);
		$words = str_replace("\t", " ", $words);
		$words = str_replace("  ", " ", $words);
		$words = explode(" ", $words);

		shuffle($words);
		foreach($words as $word) {
			if (strlen($word) > 3) {
				return rand(0, 99) . $word . rand(0, 999);
			}
		}

	}

?>
