<?php
/*
ssoread.php 

Author: C. Delfs 
Based on concepts by Mike Duncan

URL Parameters:

	file_uuid 		-	a UniqueID for a file name eg: 2345hnb52g454h5

Functionality explanation:
Called before fm_read.php from within FileMaker. This writes a temp file named with a UUID set by FileMaker. 
This same UUID will be passed to fm_read.php to pull the contents of the file later.
The temp file contains the session variable contents written from fm_link.php.
as well as any other needed environmental data


READ mode:
This File may need to be in a directory that does NOT require Authentication

*/

session_start();

	// check for required parameter, should match the UUID passed in fm write file.
	if(isset($_GET['file_uuid']) && strlen($_GET['file_uuid'])>0 ){
		
		$fn = "../sso/tmp/" .$_GET['file_uuid'].'.txt';
		$fr = fopen($fn, "r") or die("Unable to open file!");
		echo fread($fr, filesize($fn));
		fclose($fr);
		unlink($fn);  // remove the file once read

	} else {
		exit('Error: No file.');  
	}


?>
