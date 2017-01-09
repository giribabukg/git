<?php
$errmsg = '';

//Form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{	
	$requiredField = 'xmlfile';
	
	//Check if file was submitted
	if (!isset($_FILES[$requiredField]))
	{
		$error = true;
		$errmsg = $requiredField.' not supplied';
	}
	//Check for file upload errors
	elseif ($_FILES[$requiredField]['error'] > 0)
	{
		$error = true;
		switch ($_FILES[$requiredField]['error']) 
		{ 
			case UPLOAD_ERR_INI_SIZE: 
				$errmsg = 'The '.$requiredField.' file exceeds the upload_max_filesize directive in php.ini'; 
				break; 
			case UPLOAD_ERR_FORM_SIZE: 
				$errmsg = 'The '.$requiredField.' file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'; 
				break; 
			case UPLOAD_ERR_PARTIAL: 
				$errmsg = 'The '.$requiredField.' file was only partially uploaded'; 
				break; 
			case UPLOAD_ERR_NO_FILE: 
				$errmsg = 'No '.$requiredField.' file was uploaded'; 
				break; 
			case UPLOAD_ERR_NO_TMP_DIR: 
				$errmsg = $requiredField.' Missing a temporary folder'; 
				break; 
			case UPLOAD_ERR_CANT_WRITE: 
				$errmsg = 'Failed to write '.$requiredField.' to disk'; 
				break; 
			case UPLOAD_ERR_EXTENSION: 
				$errmsg = $requiredField.' File upload stopped by extension'; 
				break; 

			default: 
				$errmsg = 'Unknown upload error on '.$requiredField; 
				break; 
		} 
	}
	//Check file type
	elseif ($_FILES[$requiredField]['type'] != 'text/xml')
	{
		$error = true;
		$errmsg = $requiredField.' is not an XML file';
	}
	else
	{		
		$filename = strtolower(basename($_FILES[$requiredField]['name']));
			
		//All checks ok, move the file
		if (move_uploaded_file($_FILES[$requiredField]['tmp_name'], WWW_DIR.'xmlfiles/'.$filename)) {
			$pathinfo = pathinfo(WWW_DIR.'xmlfiles/'.$filename);
			header("Location: ?id=".basename($pathinfo['filename']));
		} else{
			$errmsg = 'Could not move uploaded file to final destination';
		}
	}
	
}

$page->smarty->assign('errorCode', 19);
$page->smarty->assign('errorMsg', $errmsg);

$page->content = $page->smarty->fetch('upload.tpl');
$page->render();