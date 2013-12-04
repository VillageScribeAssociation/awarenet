<? /*
<module>
    <modulename>files</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>Module for uploading, managing and displaying files.</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>no</enabled>
    <search>no</search>
    <models>
      <model>
        <name>Files_File</name>
		<description></description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
          <export>files-add</export>
          <export>files-show</export>
          <export>files-deleteall</export>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
        </relationships>
      </model>
	</models>
    <dependencies>
        <module>aliases</module>
    </dependencies>
</module>
*/ ?>
