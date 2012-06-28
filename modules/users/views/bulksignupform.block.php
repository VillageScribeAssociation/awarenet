<? /*

<h2>Bulk User Signup</h2>

<p><b>Important:</b> Use with caution, once created user accounts cannot be deleted, and must be manually disabled one
at a time.  Student records may be tab or comma separated values, one per line with the following format:</p>
<div class='inlinequote'>firstname, surname, username, password</div>
<br/>

<script language='Javascript'>
	function users_updatePreview() {
		var outputDiv = document.getElementById('userPreview');
		var raw = document.getElementById('taStudents').value;		
		var lines = raw.split("\n");
		var table = ''
			+ '<h3>User Batch</h3>'
			+ "<table noborder class='wireframe' width='100%'>"
			+ '<tr>'
			+ "<td class='title'>Forename</td>"
			+ "<td class='title'>Surname</td>"
			+ "<td class='title'>Username</td>"
			+ "<td class='title'>Password</td>"
			+ '</tr>';

			//TODO: implement arrayToHtmlTable in Javascript		

		var usernames = new Array();

		for (var i = 0; i < lines.length; i++) {
			var line = lines[i];
			table = table + "<tr class='wireframe'>";
			line = line.replace(/,/g, "\t");
			var parts = line.split("\t");
			if (parts.length >= 4) {

				
				// *** temporary code for generating usernames and passwords
				//parts[2] = parts[0].substring(0, 1) + '.' + parts[1] + '.vg';

				//for(var k = 0; k < usernames.length; k++) {
				//	if (usernames[k] == parts[2]) { 
				//		parts[2] = parts[0].substring(0, 1) + '.' + parts[1] + '2.vg';
				//	}
				//}

				//for(var k = 0; k < usernames.length; k++) {
				//	if (usernames[k] == parts[2]) { 
				//		alert('collision: ' + parts[2]);
				//	}
				//}
				//
				//parts[3] = 'x' + sha1Hash(parts[2]).substring(0, 6);
		
				
				table = table + "<td class='wireframe'>" + parts[0] + '</td>';
				table = table + "<td class='wireframe'>" + parts[1] + '</td>';
				table = table + "<td class='wireframe'>" + parts[2] + '</td>';
				table = table + "<td class='wireframe'>" + parts[3] + '</td>';

				usernames[usernames.length] = parts[2];

			}
			table = table + '</tr>';
		}
		
		table = table + '</table><br/>';
		outputDiv.innerHTML = table;

	}
</script>

<form name='frmBulkSignup' method='POST' action='%%serverPath%%users/bulksignup/'>
	<input type='hidden' name='action' value='batchCreate' />
	<table noborder>
		<tr>
			<td><b>School:</b></td>
			<td>[[:schools::select::varname=school::default=%%school%%:]]</td>
		</tr>
		<tr>
			<td><b>Grade:</b></td>
			<td>[[:users::selectgrade::varname=grade::default=%%grade%%:]]</td>
		</tr>
	</table>
	<textarea 
		name='students' 
		id='taStudents' 
		rows='10' 
		cols='60' 
		onChange="users_updatePreview();"
		onKeyUp="users_updatePreview();"
	></textarea>
	<br/>
	<div id='userPreview'></div>
	<input type='submit' value='Add Batch' />
</form>

*/ ?>
