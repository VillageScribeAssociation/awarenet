<? /*

<table noborder width='100%'>
  <tr>
	<td valign='top'>
		<b>Rotate 90 degrees clockwise or counterclockwise.</b><br/>
		<b>Note:</b> This is a lossy operation, a little image quality is 
		lost every time it is rotated.
	</td>
    <td valign='top'>
	<form name='rotateClockwise' method='POST' action='%%serverPath%%images/rotate/' >
		<input type='hidden' name='action' value='rotateImage' />
		<input type='hidden' name='UID' value='%%imageUID%%' />
		<input type='hidden' name='direction' value='anticlockwise' />
		<input
			type='image'
			alt='Rotate 90 degrees anticlockwise.'
			src='%%serverPath%%themes/%%defaultTheme%%/images/icons/rotate-clockwise.png' />
	</form>
	</td>
    <td valign='top'>
	<form name='rotateClockwise' method='POST' action='%%serverPath%%images/rotate/' >
		<input type='hidden' name='action' value='rotateImage' />
		<input type='hidden' name='UID' value='%%imageUID%%' />
		<input type='hidden' name='direction' value='clockwise' />
		<input
			type='image' 
			alt='Rotate 90 degrees clockwise.'
			src='%%serverPath%%themes/%%defaultTheme%%/images/icons/rotate-anticlockwise.png' />
	</form>
	</td>
  </tr>
</table>

*/ ?>
