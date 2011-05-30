<? /*
<form name='editfile' method='POST' action='%%serverPath%%files/save/' >
<input type='hidden' name='action' value='savefile' />
<input type='hidden' name='UID' value='%%UID%%' />
<input type='hidden' name='return' value='%%return%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' size='60' name='title' value='%%title%%' /></td>
  </tr>
  <tr>
    <td><b>Abstract:</b></td>
    <td>
      <textarea name='caption' rows='5' cols='47'>%%caption%%</textarea></td>
  </tr>
  <tr>
    <td><b>Licence:</b></td>
    <td>
      <select name='licence'>
        <option value='%%licence%%'>%%licence%%</option>
        <option value='Copyright'>Copyright</option>
        <option value='Public Domain'>Public Domain</option>
        <option value='GNU-GPL'>GNU-GPL</option>
        <option value='GNU-LGPL'>GNU-LGPL</option>
        <option value='AFL'>AFL</option>
        <option value='GFDL'>GFDL</option>
        <option value='CC-BY-NC-SA'>CC-BY-NC-SA</option>
        <option value='CC-BY-NC-ND'>CC-BY-NC-ND</option>
        <option value='CC-BY-SA'>CC-BY-SA</option>
        <option value='CC-BY-ND'>CC-BY-ND</option>
      </select>

      <b>Weight:</b>
      <input type='text' size='5' name='weight' value='%%weight%%' />

    </td>
  </tr>
  <tr>
    <td><b>Attribution:    </b></td>
    <td><input type='text' size='60' 
         name='attribName' value='%%attribName%%'/></td>
  </tr>
  <tr>
    <td><b>Source URL:</b></td>
    <td><input type='text' size='60' 
         name='attribUrl' value='%%attribUrl%%' /></td>
  </tr>
  <tr>
    <td></td>
    <td><input type='submit' value='Save' /></td>
  </tr>
</table>
</form>
<br/>
%%returnLink%%
*/ ?>
