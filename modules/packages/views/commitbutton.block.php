<? /*

<script language='Javascript'>

	function Packages_ShowCommitModal() {
		var hWnd = kwindowmanager.createWindow(
			'Commit',
			'%%serverPath%%packages/commit/UID_%%packageUID%%/',
			570, 400,
			'themes/%%defaultTheme%%/images/icons/file.archive.png',
			true
		);

		kwindowmanager.windows[hWnd].setBanner('%%source%%');
	}

</script>

[[:theme::navtitlebox::label=Commit::toggle=divCommit:]]
<div id='divCommit'>
<small>
%%fileListHtml%%
</small>
<div class='inlinequote'>
<table noborder width='100%'>
  <tr>
	<td>
		<input type='button' onClick='Packages_ShowCommitModal();' value='Commit' />
	</td>
	<td>
		<small><a href='%%serverPath%%packages/commit/UID_%%packageUID%%/'>[direct link]</a></small>
		<small>Update files on this repository.</small>
	</td>
  </tr>
</table>
</div>
</div>
<div class='foot'></div>
<br/>

*/ ?>
