<?php

namespace Colombo\Converters\Commands;

use Colombo\Converter\Drivers\Pdftohtml\PdftotextConverter;
use Colombo\Converter\Exif\ExifTool;
use Colombo\Converters\Helpers\Converter;
use Colombo\FormulaDetector\Xml\XmlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;


class ConvertCommand extends Command
{
    protected $client;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'colombo_convert
                        {--i|input= : File input}
                        {--f|format= : Output format}
                        {--b|begin= : Begin page}
                        {--e|end= : end page}
                        {--d|dir=. : Output dir}
                        {--o|output= : Output dir}
                        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert file to another format';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    $input = realpath( $this->option('input') );
	    $format = $this->option('format');
	    $dir = realpath( $this->option('dir') );
	    $output = $this->option('output');
	    
	    $convert_helper = new Converter();
	    
	    $convert_helper->setInput( $input );
	    $convert_helper->setOutputFormat( $format );
	    $result = $convert_helper->run();
	    if($result->isSuccess()){
	    	$result->saveTo( $output );
	    }else{
	    	dd($result->getErrors());
	    }
	    
    }

}
