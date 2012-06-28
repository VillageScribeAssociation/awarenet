<? /*

<div id='divCommitForm'>
<h2>Files to update</h2>

<form name='commitChanges' method='POST' action='%%serverPath%%packages/commit/UID_%%UID%%/'>
	<input type='hidden' name='action' value='commit' />
	<input type='hidden' name='UID' value='%%UID%%' />

	%%fileListHtml%%

	<br/><b>Changelog message: <small>(required, please make it meaningful to others)</small></b><br/>
	<textarea name='message' rows='3' style='width: 100%'></textarea>
	<input type='submit' value='Commit Changes to Repository &gt;&gt;' />
</form>
</div>
<br/>

*/ ?>
