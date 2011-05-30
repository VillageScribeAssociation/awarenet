<? /*

<form name='editQuestion%%UIDJsClean%%' method='POST' action='%%serverPath%%polls/savequestion/'>
    <input type='hidden' name='action' value='saveQuestion' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <table noborder='noboder'>
    </table>
<textarea name='content' rows='10' cols='60'>%%content%%</textarea>
</form>
<table noborder>
  <tr>
    <td><input type='button' value='Save' onClick='document.editQuestion%%UIDJsClean%%.submit()'></td>
    <td>
      <form name='cancelQuestion%%UIDJsClean%%' method='GET' action='%%serverPath%%polls/showquestion/%%UID%%'>
        <input type='hidden' name='action' value='deleteQuestion' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Cancel' />
      </form>
    </td>
    <td>
      <form name='cancelQuestion%%UIDJsClean%%' method='POST' action='%%serverPath%%polls/confirmdeletequestion/'>
        <input type='hidden' name='action' value='deleteQuestion' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>

*/ ?>
