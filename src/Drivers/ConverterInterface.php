<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 17:18
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;

interface ConverterInterface {
    
    public function __construct( $bin = '', $tmp = '');
    
	/**
	 * @param $path
	 * @param $outputFormat
	 * @param $inputFormat
	 *
	 * @return ConvertedResult
	 */
	public function convert($path, $outputFormat, $inputFormat = ''): ConvertedResult;
	
	/**
	 * Custom options
	 *
	 * @param null $key
	 * @param null $value
	 *
	 * @return mixed
	 *
	 */
	public function options($key = null, $value = null);
	
	public function startPage(int $page);
	public function endPage(int $page);
	
}