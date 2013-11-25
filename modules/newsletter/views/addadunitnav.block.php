<? /*

<div class='block'>
[[:theme::navtitlebox::label=Add New Adunit::toggle=divAddAdunitNav:]]
<div id='divAddAdunitNav'>
<div class='spacer'></div>
<form name='frmNewAdunitNav' method='POST' action='%%serverPath%%newsletter/newadunit/'>
    <input type='hidden' name='action' value='newAdunit' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <table noborder='noboder' width='100%'>
    <tr>
        <td><b>Title</b></td>
        <td><input type='text' name='title' value='' style='width: 100%;' /></td>
    </tr>
    </table>
	<textarea name='tagline' style='width: 100%;' rows='4'></textarea>
    <table noborder='noboder' width='100%'>

    <tr>
        <td><b>linktext</b></td>
        <td><input type='text' name='linktext' value='' style='width: 100%;' /></td>
    </tr>
    <tr>
        <td><b>linkurl</b></td>
        <td><input type='text' name='linkurl' value='' style='width: 100%;' /></td>
    </tr>
    <tr>
        <td><b>pinned</b></td>
        <td>
			<select name='pinned'/>
				<option value='no'>no</option>
				<option value='yes'>yes</option>
			</select>
			<small>(pinned ads stick to the top of the list)</small>
		</td>
    </tr>
    <tr>
        <td><b>weight</b></td>
        <td>
			<input type='text' name='weight' value='10' size='4' />
			<small>(lightest at the top)</small>
		</td>
    </tr>
    </table>
    <input type='submit' value='Create &gt;&gt;' />
</form>
</div>
</div>
<div class='foot'></div>
*/ ?>
