<? /*

%%dimensionsTable%%
<hr/>
%%addPresetForm%%

<h2>Reload Default Values</h2>
<p>This will set image sizes for a two 2:1 two column layout.  Any custom 
sizes you have created will be unaffected.</p>

<form name='reloadDefaults' method='POST' action='%%serverPath%%images/settings/'>
 <input type='hidden' name='action' value='loadDefaults' />
 <input type='submit' value='Reset' />
</form>
<hr/>

%%setUnavailableForm%%
<hr/>

<h2>File Associations</h2>
<p>This module is registered to handle attachments of the following files.</p>
[[:live::listfileassoc::module=images:]]
<form name='frmResetAssoc' method='POST' action='%%serverPath%%images/settings/'>
	<input type='hidden' name='action' value='resetFileAssoc' />
	<input type='submit' value='Reset to defaults (images module only)' />
</form>
<br/>
<a href='%%serverPath%%live/settings/%%'>[ edit file associations &gt;&gt; ]</a>
<hr/>

*/ ?>
