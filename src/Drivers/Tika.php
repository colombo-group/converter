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
use Vaites\ApacheTika\Clients\CLIClient;
use Vaites\ApacheTika\Clients\WebClient;

class Tika implements ConverterInterface {
    
    protected $options = [
        'mode' => 'cli',// cli/web
        'web_host' => 'http://localhost',
        'web_port' => 9998,
        'cli_path' => null,
        'cli_java' => null,
        'check' => true,
        'timeout' => 15,
    ];
    
    /** @var WebClient */
    protected $client;
    
    public function __construct( $mode = 'web', $tmp = ''){
        $this->setMode( $mode );
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
                    $result->setContent( $this->client->getHTML( $path ) );
                    break;
                case 'txt':
                case 'text':
                    $result->setContent( $this->client->getText( $path ) );
                    break;
                case 'mime':
                    $result->setContent( $this->client->getMIME( $path ) );
                    break;
                case 'lang':
                case 'language':
                    $result->setContent( $this->client->getLanguage( $path ) );
                    break;
                case 'meta_html':
                    $result->setContent( $this->client->request('rmeta/html', $path ) );
                    break;
                case 'meta_text':
                    $result->setContent( $this->client->request('rmeta/text', $path ) );
                    break;
                default:
                    throw new ConvertException("Dont support output format " . $outputFormat);
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
        if($this->options('mode') == 'web'){
            $this->client = new WebClient(
                $this->options['web_host'],
                $this->options['web_port'],
                [
                    CURLOPT_TIMEOUT => $this->options('timeout')
                ],
                $this->options['check']
            );
        }elseif ($this->options('mode') == 'cli'){
            $this->client = new CLIClient($this->options['cli_path'], $this->options['cli_java'], $this->options['check']);
        }
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