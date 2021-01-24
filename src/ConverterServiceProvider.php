<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 12/21/18
 * Time: 15:44
 */

namespace Colombo\Converters;


use Colombo\Converters\Commands\ConvertCommand;
use Illuminate\Support\ServiceProvider;

class ConverterServiceProvider extends ServiceProvider {
	
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->commands( ConvertCommand::class );
	}
	
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
	
}