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

if(in_array( "-h", $arguments) || in_array( "--help", $arguments)){
	echo "converter [options] input_file output_file
Hỗ trợ chuyển đổi các định dạng file
Options :
\t -h : Show this help
\t -i : Path to input fil
	";
	exit();
}

$converter = new \Colombo\Converters\Helpers\Converter();

$converter->setInput($input);

// force custom converter
$ocrConverter = new \Colombo\Converters\Drivers\GS();
$converter->setForceConverter( $ocrConverter );
$converter->setOutputFormat( 'pdf');


$result = $converter->run();

if($result->isSuccess()){
	$result->saveTo( 'tmp/pdf.pdf');
	$result->saveAsZip('tmp/pdf.zip','xxx.pdf');
	print_r( $result->getMessages());
}else{
	print_r($result->getErrors());
}