<? /*
[[:theme::navtitlebox::label=Add New Notice::toggle=divAddNoticeNav:]]
<div id='divAddNoticeNav'>
<form name='frmNewNoticeNav' method='POST' action='%%serverPath%%newsletter/newnotice/'>
    <input type='hidden' name='action' value='newNotice' />
    <input type='hidden' name='UID' value=''%%UID%% />
    <table noborder='noboder'>
    <tr>
        <td><b>edition</b></td>
        <td><input type='text' name='edition' value='' /></td>
    </tr>
    <tr>
        <td><b>title</b></td>
        <td><input type='text' name='title' value='' /></td>
    </tr>
    <tr>
        <td><b>category</b></td>
        <td><input type='text' name='category' value='' /></td>
    </tr>
    </table>
<b>content:</b><br/>
[[:editor::add::name=content::width=300::height=300:]]
     <input type='submit' value='Create &gt;&gt;' />
</form>

</div>
*/ ?>
