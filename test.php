<?php 

     //  test file
	 
	  require_once("./src/FtpClass.php");
	  $ftp = new Ftp();
	  $ftp->connect("127.0.0.1",21,90,false);
	  $ftp->login("user","123");

	 //$list = $ftp->putInfoFilesInList('/folder1');
	 
	 //echo $ftp->getCurrentDir().'<br/>';

	  // $ftp->changeCurrentFolder('folder');
	   //$list = $ftp->putInfoInListFiles(); var_dump($list);
	 	// echo $ftp->getCurrentDir().'<br/>';

	 /*$ftp->goTofolerHome();
      echo $ftp->getCurrentDir();*/
	  
	  $ftp->changeCurrentFolder('/folder1'); echo  $ftp->downloadFileByFTP('/folder1/f.jfif');
	 //echo  $ftp->uploadFileByFTP('Classeur1.xlsx', base64_encode(file_get_contents('Classeur1.xlsx')));
	 
	// echo $ftp->getFileSize('Classeur1.xlsx','');

?>