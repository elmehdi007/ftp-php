<?php
class Ftp
{

	private $conxFTP = null;
	private static $langError = array(
		'eng' => array(
			"we can't found FTP extension" => "we can't found FTP extension",
			"we can't found this folder" => "we can't found this folder",
			"we can't found this file" => "we can't found this file",
			"we can't found this folder" => "we can't found this folder",
		),
	);

	private static $folderTmpFile = 'tmp-file/';

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct()
	{
		if (extension_loaded('ftp') === false) {
			throw new Exception(Ftp::$langError['eng']["we can't found FTP extension"]);
		}
	}

	/**
	 * connect to ftp server
	 *
	 * @param  mixed $host
	 * @param  mixed $port
	 * @param  mixed $timeout
	 * @param  mixed $useSSL
	 *
	 * @return void
	 */
	public function connect(string $host, int $port = 21, int $timeout = 90, bool $useSSL)
	{
		$this->conxFTP = ($useSSL === false) ? ftp_connect($host, $port, $timeout) : ftp_ssl_connect($host, $port, $timeout);
	}

	/**
	 * login to ftp server
	 *
	 * @param  mixed $userName
	 * @param  mixed $password
	 *
	 * @return void
	 */
	public function login(string $userName, string $password)
	{
		ftp_login($this->conxFTP, $userName, $password);
	}

	/**
	 * get current folder
	 *
	 * @return string
	 */
	public function  getCurrentDir()
	{
		return $this->pwd();
	}

	/**
	 * change current folder
	 *
	 * @param  mixed $pathFolder
	 *
	 * @return void
	 */
	public function changeCurrentFolder(string $pathFolder)
	{
		if ($this->chdir($pathFolder) === false) {
			throw new Exception(Ftp::$langError['eng']["we can't found this folder"]);
		}
	}

	/**
	 * goTofolerHome
	 *
	 * @return void
	 */
	public function goTofolerHome()
	{
		if ($this->getCurrentDir() == "/") {
			return false;
		}

		return $this->changeCurrentFolder('/');
	}

	/**
	 * removeRessource
	 *
	 * @param  mixed $fullPath
	 *
	 * @return void
	 */
	public function removeRessource(string $fullPath)
	{
		if (is_file($fullPath)) {

			return $this->removeFile($fullPath);
		} else {

			return $this->removeFolder($fullPath);
		}
	}

	/**
	 * removeFile
	 *
	 * @param  mixed $fullPathFile
	 *
	 * @return void
	 */
	public function removeFile(string $fullPathFile)
	{
		if ($this->checkRessourceExiste($fullPathFile)) {
			throw new Exception(Ftp::$langError['eng']["we can't found this file"]);
		}

		return $this->delete($fullPathFile);
	}

	/**
	 *  remove folder
	 *
	 * @param  mixed $fullPathDirectory
	 *
	 * @return void
	 */
	public function removeFolder(string $fullPathDirectory)
	{
		if ($this->checkRessourceExiste($fullPathDirectory)) {
			throw new Exception(Ftp::$langError['eng']["we can't found this folder"]);
		}

		return $this->rmdir($fullPathDirectory);
	}


	/**
	 * renameSource
	 *
	 * @param  mixed $oldname
	 * @param  mixed $newname
	 *
	 * @return void
	 */
	public function renameSource(string $oldname, string $newname)
	{

		return $this->rename($oldname, $newname);
	}

	/**
	 * getFullPathFile
	 *
	 * @param  mixed $fileName
	 *
	 * @return void
	 */
	public function getFullPathFile(string $fileName)
	{

		return $this->getCurrentDir() . "/" . $fileName;
	}

	/**
	 * last modifier of a file
	 *
	 * @param  mixed $pathFile
	 * @param  mixed $format
	 *
	 * @return void
	 */
	public function getlastModify(string $pathFile, string $format = "d-m-y")
	{
		$time = $this->mdtm($pathFile);
		$time = date($format, $time);
		return $time;
	}

	/**
	 * getFileSize
	 *
	 * @param  mixed $file
	 * @param  mixed $unite
	 *
	 * @return void
	 */
	public function getFileSize(string $file, string $unite = null)
	{
		$size = $this->size($file);
		return $size;
	}

	// 
	/**
	 * get folder size
	 *
	 * @param  mixed $fullPath
	 *
	 * @return int
	 */
	public function getFolderSize(string $fullPath)
	{
		$size = 0;

		return $size;
	}

	/**
	 *  excuter commande ftp
	 *
	 * @param  mixed $command
	 *
	 * @return void
	 */
	public function execCommandeFtp(string $command)
	{
		$this->exec($command);
	}

	/**
	 * get binary file from ftp server
	 *
	 * @param  mixed $filePathFile
	 *
	 * @return void
	 */
	public function downloadFileByFTP(string $fullPathFile)
	{
		//copy file in tmp folder
		$fp = fopen($tmpPathName = self::$folderTmpFile.rand(1000,9999), 'w');
		$this->getfget($fp, $fullPathFile);
		fclose($fp);
		 
		$shortNameFile = substr(strrchr($fullPathFile, "/"), 1);
		$shortNameFile = (isset($shortNameFile) && $shortNameFile != '') ? $shortNameFile : $fullPathFile;
		var_dump($tmpPathName);
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . basename($shortNameFile) . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . $this->getFileSize($fullPathFile));
		readfile($tmpPathName); 
	}

	/**
	 * uploadFileByFTP
	 *
	 * @param  mixed $shortNameFile
	 * @param  mixed $dataFile64
	 *
	 * @return void
	 */
	public function uploadFileByFTP(string $shortNameFile, $dataFile64)
	{
		$extentionFile = '.' . substr(strrchr($shortNameFile, '.'), 1);
		$contentFile = base64_decode($dataFile64);

		// save the file in web server after we can uplaod it to ftp server
		$pathLocalFile = Ftp::$folderTmpFile . $shortNameFile;
		file_put_contents($pathLocalFile, $contentFile);

		// open the file for send it to the ftp server
		$handle = fopen($pathLocalFile, 'r');
		$this->fput($shortNameFile, $handle);

		// free file
		fclose($handle);
		$handle = null;
	}

	/**
	 * 	 get liste of files of directory
	 *
	 * @param  mixed $folder
	 *
	 * @return void
	 */
	public function putInfoFilesInList($folder = null)
	{
		(!$folder || empty($folder) || trim($folder) == "") ? $this->getCurrentDir() : $this->changeCurrentFolder($folder);
		$listFileDetaill = $this->mlsd($folder);
		$listFile = $this->nlist($folder);
		$permissionFile = $this->rawlist($folder);

		for ($i = 0; $i < count($listFileDetaill); $i++) {
			$fullPathFile = $this->getFullPathFile($listFileDetaill[$i]['name']);
			$listFileDetaill[$i]['modify'] = $this->getlastModify($listFile[$i]);
			$listFileDetaill[$i]['fullPath'] =  $listFile[$i];
			$listFileDetaill[$i]['permession'] = trim(explode(' ', $permissionFile[$i])[0]);

			if ($listFileDetaill[$i]['type'] === "file") {

				$listFileDetaill[$i]['size'] = $this->size($fullPathFile);
			} else {

				$listFileDetaill[$i]['size'] = $this->getFolderSize($fullPathFile);
			}
		}

		return $listFileDetaill;
	}

	public function __destruct()
	{
		$this->close();
		$this->conxFTP = null;
	}

	/**
	 * checkRessourceExiste
	 *
	 * @param  mixed $fullPathRessource
	 *
	 * @return void
	 */
	public function checkRessourceExiste($fullPathRessource)
	{
		return ($this->size($fullPathRessource) > 0) ? true : false;
	}

	public function __call($function, array $arguments)
	{
		$function = "ftp_" . $function;
		if (function_exists($function)) {
			array_unshift($arguments, $this->conxFTP);
			return call_user_func_array($function, $arguments);
		}
	}
}
