<? /*

<form name='editRoom%%UIDJsClean%%' method='POST' action='%%serverPath%%chat/saveroom/'>
    <input type='hidden' name='action' value='saveRoom' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <table noborder='noboder' width='100%'>
    <tr>
        <td width='50px'><b>Topic:</b></td>
        <td><input type='text' name='title' value='%%title%%' style='width: 100%;'/></td>
    </tr>
    </table>
	<div class='inlinequote'><small><b>Members: %%memberCount%%</b></small></div>
<b>description:</b><br/>
<div class='HyperTextArea64' title='description' width='570' height='400' style='visibility: hidden; display: none'>%%description64%%</div>
<script language='Javascript'> khta.convertDivs(); </script>
<br/>
</form>
<table noborder>
  <tr>
    <td><input type='button' value='Save' onClick='area.update(); document.editRoom%%UIDJsClean%%.submit()'></td>
    <td>
      <form name='cancelRoom%%UIDJsClean%%' method='GET' action='%%serverPath%%chat/showroom/%%UID%%'>
        <input type='hidden' name='action' value='deleteRoom' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Cancel' />
      </form>
    </td>
    <td>
      <form name='cancelRoom%%UIDJsClean%%' method='POST' action='%%serverPath%%chat/confirmdeleteroom/'>
        <input type='hidden' name='action' value='deleteRoom' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>

*/ ?>
