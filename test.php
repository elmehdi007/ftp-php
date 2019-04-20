<?php 

     //  test file
	 
	  require_once("./src/FtpClass.php");
	  $ftp = new Ftp('eng');
	  $ftp->connect("127.0.0.1",21,90,false);
	  $ftp->login("user","123");

	 /* //$list = $ftp->putInfoInListFiles();
	  echo $ftp->getCurrentDir().'<br/>';

	   $ftp->changeCurrentFolder('folder');
	   //$list = $ftp->putInfoInListFiles(); var_dump($list);
	 	 echo $ftp->getCurrentDir().'<br/>';

	 $ftp->goTofolerHome();
      echo $ftp->getCurrentDir();**/
	  
	 //echo  $ftp->downloadFileByFTP('Classeur1.xlsx');
	 //echo  $ftp->uploadFileByFTP('Classeur1.xlsx', base64_encode(file_get_contents('Classeur1.xlsx')));
	 
	// echo $ftp->getFileSize('Classeur1.xlsx','');

?>