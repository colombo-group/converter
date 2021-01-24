<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 16:39
 */

namespace Colombo\Converters;


use Colombo\Converters\Exceptions\Result\CanNotWriteResultException;
use Colombo\Converters\Helpers\TemporaryDirectory;
use Illuminate\Filesystem\Filesystem;
use PhpZip\ZipFile;

class ConvertedResult {
	protected $files = [
//		'path_file_1' => 'content_file_1'
	];
	protected $isMultiFile = false;
	protected $content;
	protected $filesystem;
	protected $isSuccess = false;
	protected $messages = [
//		'code' => 'message',
	];
	protected $errors = [
//		'code' => 'message',
	];
	
	/**
	 * ConvertedResult constructor.
	 *
	 * @param $filesystem
	 */
	public function __construct( $filesystem = null ) {
	    if($filesystem){
	        $this->filesystem = $filesystem;
        }else{
            $this->filesystem = new Filesystem();
        }
	}
	
	public function getContent(){
		if($this->isMultiFile){
			return array_keys( $this->files );
		}else{
			return $this->content;
		}
	}
	
	public function setContent($content){
		if(!$this->isSuccess){
			$this->isSuccess = true;
		}
		$this->content = $content;
	}
	
	public function addContent($content, $path){
		if(!$this->isSuccess){
			$this->isSuccess = true;
		}
		if(!$this->isMultiFile){
			$this->isMultiFile = true;
		}
		$this->files[$path] = $content;
	}
	
	public function clearContent(){
		$this->files = [];
		$this->content = '';
		$this->isSuccess = false;
		$this->isMultiFile = false;
	}
    
    /**
     * @param $path
     * @param bool $force
     * @param int $mode
     *
     * @return int file size
     * @throws CanNotWriteResultException
     */
	public function saveTo($path, $force = false, $mode = 0755){
		if($this->isMultiFile){
			// check dir
			if($this->filesystem->exists( $path )){
				if(!$this->filesystem->isDirectory( $path )){
					throw new CanNotWriteResultException("Path should be a directory, got " . $path);
				}
				if(!$this->filesystem->isWritable( $path)){
					throw new CanNotWriteResultException("Can not write to " . $path);
				}
			}else{
				$isMade = $this->filesystem->makeDirectory( $path, $mode, true, $force);
				if(!$isMade){
					throw new CanNotWriteResultException("Can not create " . $path);
				}
			}
			$path = rtrim( $path, "/");
			// write to target dir
			foreach ($this->files as $p => $content){
				$this->filesystem->put($path . "/" . $p, $content);
			}
			return count($this->files);
		}else{
			if(file_exists( $path ) && !$force){
				throw new CanNotWriteResultException("file existed at " . $path);
			}
			$this->filesystem->put( $path, $this->content );
			return filesize( $path );
		}
	}
	
	/**
	 * @return bool
	 */
	public function isMultiFile(): bool {
		return $this->isMultiFile;
	}
	
	/**
	 * @param bool $isMultiFile
	 */
	public function setIsMultiFile( bool $isMultiFile ) {
		$this->isMultiFile = $isMultiFile;
	}
	
	/**
	 * @return Filesystem
	 */
	public function getFilesystem(): Filesystem {
		return $this->filesystem;
	}
	
	/**
	 * @param Filesystem $filesystem
	 */
	public function setFilesystem( Filesystem $filesystem ) {
		$this->filesystem = $filesystem;
	}
	
	/**
	 * @return bool
	 */
	public function isSuccess(): bool {
		return $this->isSuccess;
	}
	
	/**
	 * @param bool $isSuccess
	 */
	public function setIsSuccess( bool $isSuccess ) {
		$this->isSuccess = $isSuccess;
	}
	
	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}
	
	/**
	 * @param $message
	 * @param null $code
	 *
	 * @return bool
	 * @internal param array $messages
	 */
	public function addMessages( $message, $code = null ) {
		if(is_array( $message)){
			foreach ($message as $m){
				$this->addMessages( $m);
			}
			return true;
		}
		if($code){
			$this->messages[$code] = $message;
		}else{
			$this->messages["__" . count($this->errors)] = $message;
		}
	}
	
	/**
	 * @return array
	 */
	public function getErrors(): array {
		return $this->errors;
	}
	
	/**
	 * @param $message
	 * @param null $code
	 *
	 * @return bool
	 * @internal param array $errors
	 */
	public function addErrors( $message, $code = null) {
		if(is_array( $message)){
			foreach ($message as $m){
				$this->addErrors( $m);
			}
			return true;
		}
		if($code){
			$this->errors[$code] = $message;
		}else{
			$this->errors["__" . count($this->errors)] = $message;
		}
	}
    
    /**
     * @param string $zip_path full path with file name
     * @param string $file_name file name for content when result is single file
     *
     * @return \PhpZip\ZipFileInterface
     * @throws CanNotWriteResultException
     * @throws \PhpZip\Exception\ZipException
     */
	public function saveAsZip($zip_path, $file_name = ''){
		$zip = $this->makeZip($file_name);
		return $zip->saveAsFile( $zip_path );
	}
	
	/**
	 * @param string $file_name
	 *
	 * @return string
	 * @throws CanNotWriteResultException
	 * @throws \PhpZip\Exception\ZipException
	 */
	public function readAsZip($file_name = ''){
		$zip = $this->makeZip($file_name);
		return $zip->outputAsString();
	}
    
    /**
     * @param string $file_name
     *
     * @return ZipFile
     * @throws CanNotWriteResultException
     * @throws \PhpZip\Exception\ZipException
     */
	private function makeZip($file_name = ''){
		if(!$this->isMultiFile && empty($file_name)){
			throw new CanNotWriteResultException("You must supply file name when output is single");
		}
		$zip = new ZipFile();
		if($this->isMultiFile){
			foreach ($this->files as $path => $content){
				$zip->addFromString( $path, $content);
			}
		}else{
			$zip->addFromString( $file_name, $this->content);
		}
		return $zip;
	}
	
}