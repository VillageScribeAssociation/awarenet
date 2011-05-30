<? /*
<module>
    <modulename>calendar</modulename>
    <version>1.0</version>
    <revision>0.</revision>
    <description>For creating, editing, displaying and summarising calendar entries.</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <dbschema></dbschema>
    <search>no</search>
    <dependencies>
		<module>images</module>
		<module>alias</module>
    </dependencies>
    <models>
        <model>
            <name>Entry</name>
            <description></description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
            </relationships>
        </model>
    </models>
    <defaultpermissions>
		student:p|calendar|calendar_entry|new
		student:p|calendar|calendar_entry|show
		student:p|calendar|calendar_entry|images-show
		student:c|calendar|calendar_entry|edit|(if)|creator
		student:c|calendar|calendar_entry|delete|(if)|creator
		student:c|calendar|calendar_entry|images-add|(if)|creator
		student:c|calendar|calendar_entry|images-edit|(if)|creator
		student:c|calendar|calendar_entry|images-remove|(if)|creator

		teacher:p|calendar|calendar_entry|new
		teacher:p|calendar|calendar_entry|show
		teacher:p|calendar|calendar_entry|images-show
		teacher:c|calendar|calendar_entry|edit|(if)|creator
		teacher:c|calendar|calendar_entry|delete|(if)|creator
		teacher:c|calendar|calendar_entry|images-add|(if)|creator
		teacher:c|calendar|calendar_entry|images-edit|(if)|creator
		teacher:c|calendar|calendar_entry|images-remove|(if)|creator
    </defaultpermissions>

</module>
*/ ?>
