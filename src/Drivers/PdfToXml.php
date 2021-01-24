<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-08-07
 * Time: 16:13
 */

namespace Colombo\Converters\Drivers;


class PdfToXml extends PdfToHtml {
	
	protected $process_options = [
		'-i' => true,
		'-xml' => true,
		'-stdout' => true,
		'-nodrm' => true,
		'-fontfullname' => true,
	];
	
}