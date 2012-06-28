<? /*

<div class='inlinequote'>
<table noborder>
  <tr>
    <td>

		<input
			type='button'
			value='Update Package Lists &gt;&gt;'
			onClick="
				var tempHWnd = kwindowmanager.createWindow(
					'Downloading Package Lists',
					'/packages/update/',
					800, 400, 
					'%%serverPath%%themes/%%defaultTheme%%/images/icons/file.archive.png',
					true
				);

				var tempFn = function() {
					klive.bindDivToBlock(
						'divInstalledList',
						'[' + '[:packages::installedpackages:]]',
						false
					);
				}

				kwindowmanager.windows[tempHWnd].setBanner('Updating all packages');
				kwindowmanager.setOnClose(tempHWnd,tempFn);
			"
		/>

    </td>
    <td>
		Scan repositories for packages, and for newer versions of installed packages.
		<small><a href='%%serverPath%%packages/update/'>[direct link]</a></small>
    </td>
  </tr>
</table>
</div>

*/ ?>
