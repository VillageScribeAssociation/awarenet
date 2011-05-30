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
		public:p|wiki|wiki_article|show
		public:p|wiki|wiki_revision|show

		student:p|wiki|wiki_article|new
		student:p|wiki|wiki_article|show
		student:p|wiki|wiki_article|images-add
		student:p|wiki|wiki_article|images-show
		student:p|wiki|wiki_article|images-edit
		student:p|wiki|wiki_article|edit

		student:p|wiki|wiki_revision|new
		student:p|wiki|wiki_revision|show

		teacher:p|wiki|wiki_article|new
		teacher:p|wiki|wiki_article|show
		teacher:p|wiki|wiki_article|comments-add
		teacher:p|wiki|wiki_article|comments-show
		teacher:p|wiki|wiki_article|images-add
		teacher:p|wiki|wiki_article|images-show
		teacher:p|wiki|wiki_article|images-edit
		teacher:p|wiki|wiki_article|images-remove
		teacher:p|wiki|wiki_article|administer
		teacher:p|wiki|wiki_article|edit

		teacher:p|wiki|wiki_revision|new
		teacher:p|wiki|wiki_revision|show
		teacher:p|wiki|wiki_revision|revert

    </defaultpermissions>
</module>
*/ ?>
