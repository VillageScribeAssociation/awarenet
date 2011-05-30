<? /*

<form name='editNotification%%UIDJsClean%%' method='POST' action='%%serverPath%%notifications/save/'>
    <input type='hidden' name='action' value='saveNotification' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <table noborder='noboder'>
    <tr>
        <td><b>refModule</b></td>
        <td><input type='text' name='refModule' value='%%refModule%%' /></td>
    </tr>
    <tr>
        <td><b>refModel</b></td>
        <td><input type='text' name='refModel' value='%%refModel%%' /></td>
    </tr>
    <tr>
        <td><b>refUID</b></td>
        <td><input type='text' name='refUID' value='%%refUID%%' /></td>
    </tr>
    <tr>
        <td><b>title</b></td>
        <td><input type='text' name='title' value='%%title%%' /></td>
    </tr>
    <tr>
        <td><b>refUrl</b></td>
        <td><input type='text' name='refUrl' value='%%refUrl%%' /></td>
    </tr>
    </table>
<b>content:</b><br/>
<textarea name='content' rows='10' cols='80'>%%content%%</textarea>
</form>
<table noborder>
  <tr>
    <td><input type='button' value='Save' onClick='document.editNotification%%UIDJsClean%%.submit()'></td>
    <td>
      <form name='cancelNotification%%UIDJsClean%%' method='GET' action='%%serverPath%%notifications/shownotification/%%UID%%'>
        <input type='hidden' name='action' value='deleteNotification' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Cancel' />
      </form>
    </td>
    <td>
      <form name='cancelNotification%%UIDJsClean%%' method='POST' action='%%serverPath%%notifications/confirmdeletenotification/'>
        <input type='hidden' name='action' value='deleteNotification' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>


*/ ?>
