<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-08-19
 * Time: 13:36
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\Helpers\TemporaryDirectory;

trait HasTmp {
	
	/** @var  TemporaryDirectory */
	protected $tmpFolder;
	
	public function setTmp( $location ) {
		if($this->tmpFolder){
			$this->tmpFolder->empty();
		}
		if($location instanceof TemporaryDirectory){
			$this->tmpFolder = $location;
		}else{
			$this->tmpFolder = new TemporaryDirectory( $location );
			$this->tmpFolder->create();
			return $this->tmpFolder->path();
		}
	}
	
	public function options( $key = null, $value = null ) {
		if ( $key == 'tmp' ) {
			$this->setTmp( $value );
		} else {
			return parent::options( $key, $value );
		}
	}
	
}