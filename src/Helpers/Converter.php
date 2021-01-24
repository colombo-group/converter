<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 15:32
 */

namespace Colombo\Converters\Helpers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Drivers\ConverterInterface;
use Colombo\Converters\Drivers\JodConverter;
use Colombo\Converters\Drivers\PdfToHtml;
use Colombo\Converters\Drivers\PdfToText;
use Colombo\Converters\Exceptions\ConvertException;
use Illuminate\Support\Arr;
use Symfony\Component\Mime\MimeTypes;

class Converter {
	
	const MSG_TIME = 1000;
	const MSG_OUTPUT = 1001;
	
	protected $input;
	protected $inputFormat;
	protected $outputFormat;
	protected $startPage;
	protected $endPage;
	protected $converterMapping = [
		'doc' => [
			'docx' => '',
			'html' => JodConverter::class,
			'pdf' => JodConverter::class,
			'txt' => JodConverter::class,
		],
		'docx' => [
			'html' => JodConverter::class,
			'txt' => JodConverter::class,
			'xml' => JodConverter::class,
			'pdf' => JodConverter::class,
		],
		'pdf' => [
			'html' => PdfToHtml::class,
			'xml'  => PdfToHtml::class,
			'text' => PdfToText::class,
			'txt'  => PdfToText::class,
		]
	];
	
	/** @var  ConverterInterface */
	protected $converter;
	/** @var  ConverterInterface always use this converter if it was assigned */
	protected $force_converter;
	protected $mimeHelper;
	
	protected $result;
	
	/**
	 * Init class with path to file
	 *
	 * @param string $path
	 *
	 * @throws \Exception
	 */
	public function __construct( $path = '', $inputFormat = null) {
		$this->mimeHelper = new MimeTypes();
		
		if(!empty($path)){
			$this->setInput( $path, $inputFormat);
		}
	}
	
	/**
	 * @return string
	 */
	public function getInput(): string {
		return $this->input;
	}
    
    /**
     * @param string $input
     * @param null $inputFormat
     *
     * @throws \Exception
     */
	public function setInput( string $input, $inputFormat = null ) {
		$this->input = $input;
		if($inputFormat == 'url'){
		    $this->inputFormat = 'url';
        }else{
            $this->inputFormat = $inputFormat ?? $this->mimeHelper->getExtensions($this->mimeHelper->guessMimeType( $input ))[0];
            if(!file_exists( $this->input )){
                throw new \Exception( "File not found at " . $this->input );
            }
        }
	}
	
	/**
	 * @return string
	 */
	public function getInputFormat(): ?string {
		return $this->inputFormat;
	}
	
	/**
	 * @param string $inputFormat
	 */
	public function setInputFormat( string $inputFormat ) {
		$this->inputFormat = $inputFormat;
	}
	
	/**
	 * @return string
	 */
	public function getOutputFormat() {
		return $this->outputFormat;
	}
	
	/**
	 * @param string $outputFormat
	 */
	public function setOutputFormat( $outputFormat ) {
		$this->outputFormat = $outputFormat;
	}
	
	/**
	 * @return integer
	 */
	public function getStartPage(): int {
		return $this->startPage;
	}
	
	/**
	 * @param integer $startPage
	 */
	public function setStartPage( $startPage ) {
		$this->startPage = $startPage;
	}
	
	/**
	 * @return integer
	 */
	public function getEndPage(): int
	{
		return $this->endPage;
	}
	
	/**
	 * @param integer $endPage
	 */
	public function setEndPage( int $endPage )
	{
		$this->endPage = $endPage;
	}
	
	/**
	 * @return ConverterInterface
	 */
	public function getConverter() {
		return $this->converter;
	}
	
	/**
	 * @return ConverterInterface
	 */
	public function getForceConverter() {
		return $this->force_converter;
	}
	
	/**
	 * Set force converter to use it for any format
	 * @param ConverterInterface $converter
	 */
	public function setForceConverter( ConverterInterface $converter ) {
		$this->force_converter = $converter;
	}
	
	/**
	 * Reset force_converter to false
	 */
	public function resetForceConverter(  ) {
		$this->force_converter = false;
	}
	
	/**
	 * Change converter options
	 */
	public function options() {
	
	}
	
	public function getMappingConverter($options = []){
        if($this->force_converter){
            if(count( $options )){
                $this->force_converter->options($options);
            }
            return $this->force_converter;
        }
        $converter = Arr::get($this->converterMapping, $this->getInputFormat() . "." . $this->getOutputFormat());
        if(!$converter){
            throw new \Exception("No converter was assigned to convert from " . $this->getInputFormat() . " to " . $this->getOutputFormat());
        }elseif($converter instanceof ConverterInterface){
            if(count( $options )){
                $converter->options($options);
            }
            return $converter;
        }else{
            if(!is_array( $converter )){
                $converter = [
                    'class' => $converter,
                    'options' => $options,
                ];
            }else{
                $converter['options'] = isset($converter['options']) ?
                    array_merge( $converter['options'], $options) : $options;
            }
            try{
                $instance = new $converter['class'];
                $instance->options($converter['options']);
                if(isset($this->converterMapping[$this->getInputFormat()])){
                    $this->converterMapping[$this->getInputFormat()][$this->getOutputFormat()] = $instance;
                }else{
                    $this->converterMapping[$this->getInputFormat()] = [
                        $this->getOutputFormat() => $instance
                    ];
                }
                return $this->converterMapping[$this->getInputFormat()][$this->getOutputFormat()];
            }catch (\Exception $ex){
                throw new ConvertException("Can not make converter " . $converter['class']);
            }
        }
    }
    
    /**
     * @param string $input
     * @param string $output
     * @param array|ConverterInterface|string $converter
     */
    public function setMappingConverter($input, $output, $converter){
        if(isset($this->converterMapping[$input])){
            $this->converterMapping[$input][$output] = $converter;
        }else{
            $this->converterMapping[$input] = [
                $output => $converter
            ];
        }
    }
	
	/**
	 * Call convert process
	 *
	 * @param array $options
	 *
	 * @return ConvertedResult
	 * @throws ConvertException
	 */
	public function run($options = []): ConvertedResult{
	 
		$converter = $this->getMappingConverter($options);
		
		if($this->startPage){
			$converter->startPage( $this->startPage);
		}
		if($this->endPage){
			$converter->endPage( $this->endPage);
		}
		$start = microtime(true);
		$convertedResult = $converter->convert( $this->getInput(),
			$this->getOutputFormat(),
			$this->getInputFormat()
		);
		$runtime = microtime(true) - $start;
		$convertedResult->addMessages( $runtime , self::MSG_TIME);
		return $convertedResult;
	}
    
}