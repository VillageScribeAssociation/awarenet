<? /*
<h2>edit page: %%pageName%% (%%moduleName%%)</h2>
<form name='editPage' method='POST' action='/pages/save/'>
<input type='hidden' name='action' value='savePage' />
<input type='hidden' name='page' value='%%pageName%%' />
<input type='hidden' name='module' value='%%moduleName%%' />

<table noborder>
  <tr>
    <td><b>title: </b></td>
    <td><input type='text' name='title' size='60' value='%%title%%' /></td>
  </tr>
  <tr>
    <td><b>theme: </b></td>
    <td>[[:theme::selecttemplates::selected=%%template%%:]]</td>
  </tr>
  <tr>
    <td><b>menu1: </b></td>
    <td><input type='text' name='menu1' value='%%menu1%%' /></td>
  </tr>
  <tr>
    <td><b>menu2: </b></td>
    <td><input type='text' name='menu2' value='%%menu2%%' /></td>
  </tr>
</table><br/>

<b>content (main content panel, not the whole page)</b><br/>
<textarea name='content' id='taContent' rows='10' cols='80' onkeyup="resizeTaToContent('taContent');"></textarea>
<br/><br/>
<div id='unbug'></div>


<b>nav1 (primary or leftmost navigation box/column)</b><br/>
<textarea name='nav1' id='taNav1' rows='5' cols='80' onkeyup="resizeTaToContent('taNav1')";>%%nav1%%</textarea>
<br/><br/>

<b>nav2 (secondary or rightmost navigation box/column)</b><br/>
<textarea name='nav2' id='taNav2' rows='5' cols='80'>%%nav2%%</textarea>
<br/><br/>

<b>breadcrumb (list of items showing the user where they are on the page)</b><br/>
<textarea name='breadcrumb' id='taBreadcrumb' rows='5' cols='80'>%%breadcrumb%%</textarea>
<br/><br/>

<b>script (inside javascript tags in page head)</b><br/>
<textarea name='script'  id='taScript' rows='5' cols='80'>%%script%%</textarea>
<br/><br/>

<b>jsinit (javascript to be run on page load)</b><br/>
<textarea name='jsinit'  id='taJsInit' rows='5' cols='80'>%%jsinit%%</textarea>
<br/><br/>

<script language='javascript'>

function resizeTaToContent(taId) {
	theTa = document.getElementById(taId);
	if (theTa.scrollHeight > theTa.clientHeight) {
		while ((theTa.scrollHeight > theTa.clientHeight) && (theTa.rows < 80)) { theTa.rows += 2; }
	}
}

%%contentJs64%%
base64_loadTextArea('taContent', contentJs64);
%%nav1Js64%%
base64_loadTextArea('taNav1', nav1Js64);
%%nav2Js64%%
base64_loadTextArea('taNav2', nav2Js64);
%%breadcrumbJs64%%
base64_loadTextArea('taBreadcrumb', breadcrumbJs64);
%%scriptJs64%%
base64_loadTextArea('taScript', scriptJs64);
%%jsinitJs64%%
base64_loadTextArea('taJsInit', jsinitJs64);

resizeTaToContent('taContent');
resizeTaToContent('taNav1');
resizeTaToContent('taNav2');
resizeTaToContent('taBreadcrumb');
resizeTaToContent('taScript');
resizeTaToContent('taJsInit');

</script>

<table noborder>
  <tr>
    <td valign='top'>
      <input type='submit' value='save' />
      </form>
    </td>
    <td valign='top'>
      <form name='cancelSave' method='/pages/list/'>
      <input type='submit' value='cancel' />
      </form>
    </td>
  </tr>
</table>
*/ ?>
