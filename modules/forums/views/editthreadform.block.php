<? /*

<form name='editThread%%UIDJsClean%%' method='POST' action='%%serverPath%%forums/savethread/'>
    <input type='hidden' name='action' value='saveThread' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <table noborder='noboder'>
    <tr>
        <td><b>Title:</b> </td>
        <td><input type='text' name='title' value='%%title%%' size='50' /></td>
    </tr>
    </table>
%%contentJs64%%
[[:editor::base64::jsvar=%%contentJsVar64%%::name=content:]]<br/>
</form>
<table noborder>
  <tr>
    <td>
		<input type='button' value='Save' 
			onClick='area.update(); document.editThread%%UIDJsClean%%.submit();'
		>
	</td>
    <td>
      <form name='cancelThread%%UIDJsClean%%' method='GET' action='%%serverPath%%forums/showthread/%%UID%%'>
        <input type='hidden' name='action' value='deleteThread' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Cancel' />
      </form>
    </td>
    <td>
      <form name='cancelThread%%UIDJsClean%%' method='POST' action='%%serverPath%%forums/confirmdeletethread/'>
        <input type='hidden' name='action' value='deleteThread' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>


*/ ?>
