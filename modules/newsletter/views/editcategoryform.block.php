<? /*

<form name='editCategory%%UID%%' method='POST' action='%%serverPath%%newsletter/savecategory/'>
    <input type='hidden' name='action' value='saveCategory' />
    <input type='hidden' name='UID' value='%%UID%%' />
	<table noborder='noboder' width='100%'>
    <tr>
        <td><b>name</b></td>
        <td><input type='text' name='name' value='%%name%%' style='width: 100%;'/></td>
    </tr>
    <tr>
        <td><b>weight</b></td>
        <td><input type='text' name='weight' value='%%weight%%' style='width: 100px;' /></td>
    </tr>
    </table>
<br/>
</form>
<table noborder>
  <tr>
    <td><input type='button' value='Save' onClick='document.editCategory%%UID%%.submit()'></td>
    <td>
        <input type='button' onClick='kwnd.closeWindow();' value='Cancel' />
    </td>
    <td>
      <form name='cancelCategory%%UID%%' method='POST' action='%%serverPath%%newsletter/deletecategory/'>
        <input type='hidden' name='action' value='deleteCategory' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>


*/ ?>
