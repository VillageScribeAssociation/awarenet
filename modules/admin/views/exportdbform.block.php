<? /*

<h2>Text Dump</h2>

<p>This will create a text dump of SQL statements, one per line.</p>

<form name='frmExportTxt' method='POST' action='%%serverPath%%admin/exportdb/'>
	<input type='hidden' name='action' value='export' />
	<input type='hidden' name='format' value='txt' />
	<input type='submit' value='Export SQL Dump (text file)' />
</form>

<h2>SQLite3</h2>

<p>This will create a new SQLite database file for migration / backup.</p>

<form name='frmExportSq3' method='POST' action='%%serverPath%%admin/exportdb/'>
	<input type='hidden' name='action' value='export' />
	<input type='hidden' name='format' value='sq3' />
	<input type='submit' value='Export SQLite database (PDO SQ3)' />
</form>

*/ ?>
