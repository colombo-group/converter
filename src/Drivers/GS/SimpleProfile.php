<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/28/18
 * Time: 15:45
 */

namespace Colombo\Converters\Drivers\GS;


class SimpleProfile implements ProfileInterface {
	
	protected $options = [
		'-dSAFER=true' => true,
		'-dNOPAUSE=true' => true,
		'-dBATCH=true' => true,
		'-sDEVICE=pdfwrite' => true,
		"-dPDFSETTINGS=/screen" => true,
		'-dDetectDuplicateImages=true' => true,
		'-dCompressFonts=true' => true,
		'-dDownScaleFactor=true' => true,
        '-dFastWebView' => true,
	];
	
	public function getOptions() {
		return $this->options;
	}
}