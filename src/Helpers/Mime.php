<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 16:01
 */

namespace Colombo\Converters\Helpers;


use Colombo\Converters\MimeType\BuiltInReader;
use Colombo\Converters\MimeType\MapperInterface;
use Colombo\Converters\MimeType\MimeyMapper;
use Colombo\Converters\MimeType\ReaderInterface;

class Mime {
	protected $mapper;
	protected $reader;
	
	/**
	 * Mime constructor.
	 *
	 * @param $mapper
	 * @param $reader
	 */
	public function __construct( MapperInterface $mapper = null, ReaderInterface $reader = null ) {
		$this->mapper = $mapper ?: new MimeyMapper();
		$this->reader = $reader ?: new BuiltInReader();
	}
	
	public function getExtension($path){
		$mime = $this->getMimeType( $path );
		$ext = $this->mapper->extension( $mime );
		return $ext;
	}
	
	public function getMimeType($path){
		$mime = $this->reader->fromFile( $path );
		return $mime;
	}
	
}