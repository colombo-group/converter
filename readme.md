## Requirement

- Poppler tools kit
- Jod converter

## Usage

```php
$converter = new \Colombo\Converters\Helpers\Converter();
    
$converter->setInput($input);

// force custom converter


$converter->setOutputFormat( 'pdf');

$converter->setEndPage(1);
$converter->setEndPage(2);

$result = $converter->run();

if($result->isSuccess()){
    $result->saveTo('xxx.pdf');
}else{
    print_r($result->getErrors());
}
```


### With laravel

- Add `ConverterServiceProvider` to app.php and type command like this


    php artisan colombo_convert -i ~/Downloads/30325685_1545065160.doc -f pdf --output ./x.pdf 

### Stable

- poppler utils
- gs 
- pdf2htmlex    
    
### Todo

 -[ ] Improve phpunit test

