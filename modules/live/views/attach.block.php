<? /*

<div class='actionbox' onClick="live_openAttachments();" >Attach files</div>

<script>

	function live_openAttachments() {
		var hWnd = kwindowmanager.createWindow(
			"Attachments",					
			"%%serverPath%%live/attachments/refModule_%%refModule%%/refModel_%%refModel%%/refUID_%%refUID%%/",
			570, 300,
			'modules/live/icons/edit-paste.png',
			true
		);

		kwindowmanager.windows[hWnd].setBanner('Attach files.');
	}

</script>

*/ ?>
