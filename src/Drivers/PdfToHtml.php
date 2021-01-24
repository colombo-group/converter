<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 18:51
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Process\CanRunCommand;

class PdfToHtml extends CanRunCommand implements ConverterInterface {
 
    protected $bin = 'pdftohtml';
    
	protected $process_options = [
		'-i' => true,
		'-stdout' => true,
		'-nodrm' => true,
		'-fontfullname' => true,
	];
	
	
	/**
	 * @param $path
	 * @param string $outputFormat
	 * @param string $inputFormat
	 *
	 * @return ConvertedResult
	 * @throws ConvertException
	 */
	public function convert( $path, $outputFormat, $inputFormat = '' ): ConvertedResult {
		$result = new ConvertedResult();
		
		switch ($outputFormat){
			case 'html':
				
				break;
			case 'xml':
				$this->options('-xml', true);
				break;
			default:
			throw new ConvertException($outputFormat . " was not supported by pdftohtml converter");
		}
		
		$command = $this->buildCommand([],[$path]);
		try{
			$this->run( $command);
			$result->setContent( $this->output() );
		}catch (\RuntimeException $ex){
			$result->addErrors( $ex->getMessage(), $ex->getCode());
		}
		return $result;
	}
	
	public function startPage( int $page ) {
		$this->options('-f', $page);
	}
	
	public function endPage( int $page ) {
		$this->options('-l', $page);
	}
}