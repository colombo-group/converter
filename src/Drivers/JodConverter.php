<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/22/18
 * Time: 02:06
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class JodConverter implements ConverterInterface {
	
	protected $options = [
		'base_uri' => 'http://localhost:8081',
		'timeout' => 300,
		'verify' => false,
	];
	protected $client;
    
    public function __construct( $host = '', $tmp = '' ) {
        if($host){
            $this->options('base_uri', $host);
        }
    }
	
	/**
	 * @param $path
	 * @param $outputFormat
	 * @param $inputFormat
	 *
	 * @return ConvertedResult
	 */
	public function convert( $path, $outputFormat, $inputFormat = '' ): ConvertedResult {
		$client = $this->getClient();
		$result = new ConvertedResult();
		try{
			$response = $client->post('/lool/convert-to/' . $outputFormat, [
				'multipart' => [
					[
						'name' => 'data',
						'contents' => fopen( $path, 'r'),
						'filename' => basename( $path) . "." . $inputFormat,
					],
				],
				'verify' => false,
			]);
			$result->setContent( $response->getBody()->getContents() );
		}catch (RequestException $ex){
			$msg = "Can not convert by jodconverter " . $this->options('base_uri') . " :: " . $ex->getMessage();
			$result->addErrors($msg, $ex->getCode());
		}
		return $result;
	}
	
	/**
	 * Custom options
	 *
	 * @param null $key
	 * @param null $value
	 *
	 * @return mixed
	 *
	 */
	public function options( $key = null, $value = null ) {
		if(is_array($key)){
			if($value === true){// overwrite all option
				$this->options = $key;
			}else{
				foreach ($key as $k => $v){
					$this->options($k, $v);
				}
			}
		}elseif ($key != null){
			if($value !== null){
				$this->options[$key] = $value;
			}
			return $this->options[$key];
		}
		return $this->options;
	}
	
	public function startPage( int $page ) {
		// Not supported
		$this->options('start_page', $page);
	}
	
	public function endPage( int $page ) {
		// Not supported
		$this->options('end_page', $page);
	}
	
	protected function getClient(){
		$client = new Client([
			'base_uri' => $this->options('base_uri'),
			'timeout' => $this->options('timeout'),
			'verify' => $this->options('verify'),
		]);
		try{
			$client->get( "v2/api-docs", [
				'timeout' => 5,
				'verify' => false,
			]);
		}catch (RequestException $ex){
			throw new ConvertException("JodConverter can not connect to " . $this->options('base_uri'));
		}
		return $client;
	}
}