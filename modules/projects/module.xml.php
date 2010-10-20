<? /*
<module>
    <modulename>projects</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>Projects module, allow multiple students to collaborate on a document.</description>
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
            <name>Membership</name>
            <description>Relationship object associating a user with a project</description>
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
        <model>
            <name>Project</name>
            <description>object for representing user projects</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
                <relationship>projectmember</relationship>
                <relationship>projectadmin</relationship>
            </relationships>
        </model>
        <model>
            <name>Revision</name>
            <description>stores a previous version of a project</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
                <relationship>projectmember</relationship>
                <relationship>projectadmin</relationship>
            </relationships>
        </model>

    </models>
    <defaultpermissions>
		student:p|projects|Projects_Project|show

		student:p|projects|Projects_Project|new
		student:p|projects|Projects_Project|show
		student:p|projects|Projects_Project|comments-add
		student:p|projects|Projects_Project|comments-show
		student:p|projects|Projects_Project|images-show
		student:c|projects|Projects_Project|images-remove|(if)|projectadmin
		student:c|projects|Projects_Project|administer|(if)|projectadmin
		student:c|projects|Projects_Project|edit|(if)|projectmember
		student:c|projects|Projects_Project|edit|(if)|projectadmin
		student:c|projects|Projects_Project|tags-manage|(if)|projectmember
		student:c|projects|Projects_Project|images-add|(if)|projectmember
		student:c|projects|Projects_Project|images-add|(if)|projectadmin
		student:c|projects|Projects_Project|edit|(if)|projectadmin
		student:p|projects|Projects_Revision|show

		teacher:p|projects|Projects_Project|new
		teacher:p|projects|Projects_Project|show
		teacher:p|projects|Projects_Project|comments-add
		teacher:p|projects|Projects_Project|comments-show
		teacher:p|projects|Projects_Project|images-show
		teacher:c|projects|Projects_Project|images-remove|(if)|projectadmin
		teacher:c|projects|Projects_Project|administer|(if)|projectadmin
		teacher:c|projects|Projects_Project|edit|(if)|projectmember
		teacher:c|projects|Projects_Project|edit|(if)|projectadmin
		teacher:c|projects|Projects_Project|tags-manage|(if)|projectmember
		teacher:c|projects|Projects_Project|images-add|(if)|projectmember
		teacher:c|projects|Projects_Project|images-add|(if)|projectadmin
		teacher:c|projects|Projects_Project|edit|(if)|projectadmin
		teacher:p|projects|Projects_Revision|show
    </defaultpermissions>
</module>
*/ ?>
