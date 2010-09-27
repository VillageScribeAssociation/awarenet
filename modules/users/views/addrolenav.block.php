<? /*
[[:theme::navtitlebox::label=Add New Role::toggle=divAddRoleNav:]]
<div id='divAddRoleNav'>
<form name='frmNewRoleNav' method='POST' action='%%serverPath%%users/newrole/'>
    <input type='hidden' name='action' value='newRole' />
    <input type='hidden' name='UID' value=''%%UID%% />
    <table noborder='noboder'>
    <tr>
        <td><b>name</b></td>
        <td><input type='text' name='name' value='' /></td>
    </tr>
    </table>
<b>description:</b><br/>
[[:editor::add::name=description::width=300::height=300:]]
<textarea rows='10' cols='40' name='permissions'></textarea>     <input type='submit' value='Create &gt;&gt;' />
</form>

</div>
*/ ?>
