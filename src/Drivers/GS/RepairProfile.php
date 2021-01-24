<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/28/18
 * Time: 17:24
 */

namespace Colombo\Converters\Drivers\GS;


class RepairProfile implements ProfileInterface {
	
	protected $options = [
		'-dSAFER=true' => true,
		'-dNOPAUSE=true' => true,
		'-dBATCH=true' => true,
		'-sDEVICE=pdfwrite' => true,
		"-dPDFSETTINGS=/prepress" => true,
		'-dDetectDuplicateImages=true' => true,
		'-dCompressFonts=true' => true,
		'-dDownScaleFactor=true' => true,
	];
	
	public function getOptions() {
		// TODO: Implement getOptions() method.
	}
}