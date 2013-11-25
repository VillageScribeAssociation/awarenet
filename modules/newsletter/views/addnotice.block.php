<? /*

<div class='block'>
[[:theme::navtitlebox::label=Add New Notice::toggle=divAddNoticeNav:]]
<div id='divAddNoticeNav'>
<form name='frmNewNoticeNav' method='POST' action='%%serverPath%%newsletter/newnotice/'>
    <input type='hidden' name='action' value='newNotice' />
    <input type='hidden' name='UID' value='%%UID%%' />
	<input type='hidden' name='edition' value='%%UID%%' />
    <table noborder='noboder' width='100%'>
    <tr>
        <td><b>title</b></td>
        <td><input type='text' name='title' value='' style='width: 100%;' /></td>
    </tr>
    <tr>
        <td><b>category</b></td>
        <td>[[:newsletter::selectcategory:]]</td>
    </tr>
    </table>
	[[:editor::add::name=content::height=300::refModule=newsletter::refModel=newsletter_edition::refUID=%%UID%%:]]
     <input type='submit' value='Add Notice &gt;&gt;' />
</form>
</div>
<div class='foot'></div>
</div>
*/ ?>
