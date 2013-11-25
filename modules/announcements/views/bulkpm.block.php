<? /*

[[:theme::navtitlebox::label=Bulk PM::toggle=divBulkPM:]]
<div id='divBulkPM'>

<script>
	function announcements_bulkpm() {
		$('#spanSendMsg').show();
		$('#btnSendAnnouncements').hide();
		$('#frmBulkPM').submit();
	}
</script>

<div class='spacer'></div>
<p>Announcements are automatically sent to all members in their feeds.  This feature allows important announcements to be sent to user PM inboxes as well.  Please use it sparingly and do not spam users.</p>

<form name='BulkPM' id='frmBulkPM' method='POST' action='%%serverPath%%announcements/bulkpm/'>
	<input type='hidden' name='action' value='sendBulkPM' />
	<input type='hidden' name='UID' value='%%UID%%' />
	<input
		type='button'
		id='btnSendAnnouncements'
		value='Send to all members &gt;&gt;'
		onClick='announcements_bulkpm();'
	/>
	<span id='spanSendMsg' style='display: none'>
	<small>
		Sending bulk pm, this may pake a few minutes for large groups, please wait.
		<img src='%%serverPath%%themes/%%defaultTheme%%/images/throbber-inline.gif' />
	</small>
	</span>
</form>
</div>
<div class='foot'></div>
<br/>

*/ ?>
