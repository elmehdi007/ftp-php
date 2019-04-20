<?php 
class Ftp{
	
	  private $conxFTP=NULL;
	  private $langError = array( 'eng' => array(
												  "we can't found FTP extension" => "we can't found FTP extension",
												  "we can't found this folder" => "we can't found this folder",
												  
												  ) 
							    );
				
	 static private $folderTmpFile = 'tmp-file/'; 	
	  
	  private $listFonctionNeedExsitesSource = array();
	  
	  public function  __construct(){		  
			if (extension_loaded('ftp')===false) {
				 throw new Exception($langError['eng']["we can't found FTP extension"]);
			}
	  }
	  
	   // connect to ftp server
	  public function connect(string $host,int $port=21,int $timeout=90,bool $useSSL){
           if($useSSL===false){
			   $this->conxFTP = ftp_connect($host,$port,$timeout);
		   }		   
		  else{
			    $this->conxFTP = ftp_ssl_connect($host,$port,$timeout);  
		  }
		 return $this->conxFTP ;
	  }
	  
	  // login to ftp server
	  public function login(string $userName,string $password){
		  ftp_login ($this->conxFTP,$userName,$password);
	  }
	  
	   // get current folder
	  public function getCurrentDir(){

		  return $this->pwd();
	  }
	  	 
	   // change current folder
	  public function changeCurrentFolder(string $pathFolder){
		    $statChangeFolder = $this->chdir($pathFolder);
		  if($statChangeFolder  == false){
			    throw new Exception($langError['eng']["we can't found this folder"]);
		  }
		  return $statChangeFolder ;
	  }
	  
	 
	 public function goTofolerHome(){
		  if( $this->getCurrentDir() == "/"){
			  return false;
		  }
		  //cdup
		  return  $this->changeCurrentFolder('/');
	  }
	  
	    // remove file 
	  public function  removeFile (string $fullPathFILE ){
		  return $this->delete( $fullPathFILE) ;
		  
	  }
	  
	 // remove folder 
	  public function  removeFolder (string $fullPathDirectory ){
		  return  $this->rmdir ( $fullPathDirectory) ;
	  }
	  
     // renomer file or folder
	  public function renameSource (string $oldname , string $newname){
		  
		  return  $this->rename ($oldname , $newname ) ;
		  
	  }
	   // get full path of file 
	  public function getFullPathFILE(string $fileName){
		    return $this->getCurrentDir()."/".$fileName;
	  }
	  
	   // last modifier of a file
	  public function getlastModify(string $pathFile, string $format="d-m-y"){
		  $time = $this->mdtm($pathFile);
		  $time = date($format, $time);
		  return $time;
	  }
	  
	  //get file size
	  public function getFileSize(string $file,string $unite){
		  $size = $this->size($file);
		  return $size;
	  }
	  
	  // get folder size
	  public function getFolderSize(string $fullPath){
		  $size = 0;
		  
		  return $size; 
	  }
	  
	  // excuter commande
	  public function execCommandeFtp (string $command ){
			$this->exec( $command );
	  }
	  
	  // get binary file from ftp server 
	  public function downloadFileByFTP(string $filePathFile){
		  $extentionFile = '.'.substr(strrchr($filePathFile, '.'), 1);
		  $shortNameFile = substr(strrchr($filePathFile, "/"), 1);
		  $shortNameFile = (isset($shortNameFile) && $shortNameFile != '')?$shortNameFile:$filePathFile;
		 
		 $pathLocalFile = Ftp::$folderTmpFile.$shortNameFile ;
		  readfile( $pathLocalFile );
		    header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($pathLocalFile).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
		 //suprission tmp file
		 
		 return $fileContent;
	  }
	  
	 public function uploadFileByFTP(string $shortNameFile,$dataFile64){
		 $extentionFile = '.'.substr(strrchr($shortNameFile, '.'), 1);
		 $contentFile =  base64_decode ( $dataFile64 ); 

		// save the file in web server after we can uplaod it to ftp server
		 $pathLocalFile = Ftp::$folderTmpFile.$shortNameFile ;
		 file_put_contents($pathLocalFile, $contentFile);
		 
	      // open the file for send it to the ftp server
		 $handle = fopen($pathLocalFile, 'r');
		 $this->fput($shortNameFile, $handle);
		
		// free file
		 fclose($handle );
		 $handle = null;
	  }
	  
	  
	  // put and get liste of files of directory
	  public function putInfoInListFiles(){
		  $listFileDetaill = $this->mlsd($this->getCurrentDir());
		  $listFile = $this->nlist($this->getCurrentDir());
		  $permissionFile = $this->rawlist($this->getCurrentDir());
	
		  for($i=0;$i<count($listFileDetaill);$i++)
		  {   
			  $fullPathFile = $this->getFullPathFILE($listFileDetaill[$i]['name']);
			  $listFileDetaill[$i]['modify'] = $listFile[$i]; 
			  $listFileDetaill[$i]['fullPath'] = $listFile[$i]; 
			  $listFileDetaill[$i]['permession'] = trim( explode(' ', $permissionFile[$i])[0]); 
			  
			  if($listFileDetaill[$i]['type']==="file")
			  {
				  $listFileDetaill[$i]['size']= $this->getSize($fullPathFile,"");  
			  }
			  
			  else {
				   $listFileDetaill[$i]['size']= $this->getFolderSize($fullPathFile);
			  }
		  }
		  
		  return $listFileDetaill;
	  }
	  
	public function __destruct()
    {
		$this->close();
    }
	
	 public function __call($function, array $arguments){
		     $function= "ftp_".$function;
			if(function_exists($function)){
					array_unshift($arguments,$this->conxFTP);
					return call_user_func_array($function,$arguments);
			}
		   
	   }
}

?>