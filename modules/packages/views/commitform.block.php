<? /*

[[:theme::navtitlebox::label=Commit::toggle=divCommitForm:]]
<div id='divCommitForm'>
<h2>Files to update</h2>
%%fileListHtml%%
<br/>

<form name='commitChanges' method='POST' action='%%serverPath%%packages/commit/'>
	<input type='hidden' name='action' value='commit' />
	<input type='hidden' name='UID' value='%%UID%%' />
	<br/><b>Changelog message: <small>(required, please make it meaningful to others)</small></b><br/>
	<textarea name='message' rows='3' cols='80'></textarea>
	<input type='submit' value='Commit Changes to Repository &gt;&gt;' />
</form>
</div>
<br/>

*/ ?>
