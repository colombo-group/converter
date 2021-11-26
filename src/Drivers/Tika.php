<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-09-18
 * Time: 00:38
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use HocVT\TikaSimple\TikaSimpleClient;
use Vaites\ApacheTika\Clients\CLIClient;
use Vaites\ApacheTika\Clients\WebClient;

class Tika implements ConverterInterface {
    
    protected $options = [
        'web_host' => 'http://localhost',
        'web_port' => 9998,
        'timeout' => 30,
    ];
    
    /** @var TikaSimpleClient */
    protected $client;
    
    public function __construct( $mode = 'web', $tmp = ''){

    }
    
    /**
     * @param $path
     * @param $outputFormat
     * @param $inputFormat
     *
     * @return ConvertedResult
     */
    public function convert( $path, $outputFormat, $inputFormat = '' ): ConvertedResult {
        
        $this->prepareClient();
        
        $result = new ConvertedResult();
        
        try{
            switch ($outputFormat){
                case 'html':
                    $result->setContent( $this->client->rmetaFile( $path, 'html', false ) );
                    break;
                case 'txt':
                case 'text':
                    $result->setContent( $this->client->rmetaFile( $path, 'text', false ) );
                    break;
                default:
                    throw new ConvertException("Don not support output format " . $outputFormat);
            }
        }catch (\Exception $ex){
            $result->addErrors( $ex->getMessage() );
        }
        return $result;
    }
    
    public function setMode($mode){
        $this->options('mode', $mode);
    }
    
    protected function prepareClient(){
        $host = $this->options['web_host'] . ":" . $this->options['web_port'];
        $this->client = new TikaSimpleClient($host);
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
        }elseif ($value !== null){
            $this->options[$key] = $value;
        }else{
            return $this->options[$key];
        }
    }
    
    public function startPage( int $page ) {
        // Not supported
    }
    
    public function endPage( int $page ) {
        // Not supported
    }
}