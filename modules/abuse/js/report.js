
//--------------------------------------------------------------------------------------------------
//*	utility javascript for createing abuse repor windows
//--------------------------------------------------------------------------------------------------

function Abuse_ReportModal(refModule, refModel, refUID, wTitle) {
	var windowUrl = ''
	 + jsServerPath + 'abuse/abusewindow'
	 + '/refUID_' + refUID
	 + '/refModule_' + refModule
	 + '/refModel_' + refModel + '/';
	
	var hWnd = kwindowmanager.createWindow(
		'Abuse',
		windowUrl,
		570, 350,
		jsServerPath + 'modules/live/icons/abuse.png',
		true
	);

	kwindowmanager.windows[hWnd].setBanner('Re: ' + wTitle);
}
