<? /*
<module>
    <modulename>wiki</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>A general-purpose non-semantic wiki.</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>no</enabled>
    <search>no</search>
    <dependencies>
		<module>aliases</module>
		<module>images</module>
		<module>comments</module>
    </dependencies>
    <models>
        <model>
            <name>Article</name>
            <description>object representing the current state of wiki articles</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
                <relationship>contributor</relationship>
            </relationships>
        </model>
        <model>
            <name>Revision</name>
            <description>stores a previous version of a project</description>
            <permissions>
                <permission>show</permission>
                <permission>revert</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
            </relationships>
        </model>
    </models>
    <defaultpermissions>
		public:p|wiki|Wiki_Article|show
		public:p|wiki|Wiki_Revision|show

		student:p|wiki|Wiki_Article|new
		student:p|wiki|Wiki_Article|show
		student:p|wiki|Wiki_Article|images-add
		student:p|wiki|Wiki_Article|images-show
		student:p|wiki|Wiki_Article|images-edit
		student:p|wiki|Wiki_Article|edit

		student:p|wiki|Wiki_Revision|new
		student:p|wiki|Wiki_Revision|show

		teacher:p|wiki|Wiki_Article|new
		teacher:p|wiki|Wiki_Article|show
		teacher:p|wiki|Wiki_Article|comments-add
		teacher:p|wiki|Wiki_Article|comments-show
		teacher:p|wiki|Wiki_Article|images-add
		teacher:p|wiki|Wiki_Article|images-show
		teacher:p|wiki|Wiki_Article|images-edit
		teacher:p|wiki|Wiki_Article|images-remove
		teacher:p|wiki|Wiki_Article|administer
		teacher:p|wiki|Wiki_Article|edit

		teacher:p|wiki|Wiki_Revision|new
		teacher:p|wiki|Wiki_Revision|show
		teacher:p|wiki|Wiki_Revision|revert

    </defaultpermissions>
</module>
*/ ?>
