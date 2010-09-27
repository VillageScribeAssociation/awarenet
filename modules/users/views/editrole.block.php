<? /*

<form name='editRole%%UIDJsClean%%' method='POST' action='%%serverPath%%users/saverole/'>
    <input type='hidden' name='action' value='saveRole' />
    <input type='hidden' name='UID' value=''%%UID%% />
    <table noborder='noboder'>
    <tr>
        <td><b>name</b></td>
        <td><input type='text' name='name' value='%%name%%' /></td>
    </tr>
    </table>
<b>description:</b><br/>
%%descriptionJs64%%
[[:editor::base64::jsvar=%%descriptionJsVar64%%::name=description:]]<br/>
<b>permissions:</b><br/>
<textarea rows='10' cols='80' name='permissions'>%%permissions%%</textarea>
</form>
<table noborder>
  <tr>
    <td><input type='button' value='Save' onClick='document.editRole%%UIDJsClean%%.submit()'></td>
    <td>
      <form name='cancelRole%%UIDJsClean%%' method='GET' action='%%serverPath%%users/showrole/%%UID%%'>
        <input type='hidden' name='action' value='deleteRole' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Cancel' />
      </form>
    </td>
    <td>
      <form name='cancelRole%%UIDJsClean%%' method='POST' action='%%serverPath%%users/confirmdeleterole/'>
        <input type='hidden' name='action' value='deleteRole' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>


*/ ?>
