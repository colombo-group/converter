<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 18:24
 */

ini_set( 'display_errors', 1);

require __DIR__ . "/../vendor/autoload.php";

$arguments = $argv;

if(count($argv) == 1){
	echo "converter input_file output_format output_file
Hỗ trợ chuyển đổi các định dạng file
	";
	exit();
}

$converter = new \Colombo\Converters\Helpers\Converter();

$converter->setInput($arguments[1]);

// force custom converter
if($arguments[4] ?? false){
    $converter_class = '\\Colombo\\Converters\\Drivers\\' . $arguments[4];
    $ocrConverter = new $converter_class;
    $converter->setForceConverter( $ocrConverter );
}
$converter->setOutputFormat( $arguments[2]);


$result = $converter->run();

if($result->isSuccess()){
	$result->saveTo( $arguments[3] );
	print_r( $result->getMessages());
}else{
	print_r($result->getErrors());
}