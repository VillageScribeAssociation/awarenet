<? /*
<module>
    <modulename>moblog</modulename>
    <version>1.0</version>
    <revision>For producing the moblog (user blogs agregated by school, group, etc).</revision>
    <description>For producing the moblog (user blogs agregated by school, group, etc).</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <dbschema></dbschema>
    <search>no</search>
    <models>
      <model>
        <name>Moblog_Post</name>
		<description>Individual blog posts.</description>
        <permissions>
          <permission>show</permission>
          <permission>list</permission>
          <permission>edit</permission>
          <permission>new</permission>
          <permission>delete</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		student:p|moblog|Moblog_Post|show
		student:p|moblog|Moblog_Post|list
		student:p|moblog|Moblog_Post|comments-add
		student:p|moblog|Moblog_Post|comments-show
		student:p|moblog|Moblog_Post|images-show
		student:c|moblog|Moblog_Post|edit|(if)|creator
		student:c|moblog|Moblog_Post|images-add|(if)|creator
		student:c|moblog|Moblog_Post|images-remove|(if)|creator
		student:c|moblog|Moblog_Post|images-edit|(if)|creator
		student:c|moblog|Moblog_Post|delete|(if)|creator

		teacher:p|moblog|Moblog_Post|show
		teacher:p|moblog|Moblog_Post|list
		teacher:p|moblog|Moblog_Post|comments-add
		teacher:p|moblog|Moblog_Post|comments-show
		teacher:p|moblog|Moblog_Post|images-show
		teacher:c|moblog|Moblog_Post|edit|(if)|creator
		teacher:c|moblog|Moblog_Post|images-add|(if)|creator
		teacher:c|moblog|Moblog_Post|images-remove|(if)|creator
		teacher:c|moblog|Moblog_Post|images-edit|(if)|creator
		teacher:c|moblog|Moblog_Post|delete|(if)|creator
    </defaultpermissions>
</module>
*/ ?>
