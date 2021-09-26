<?php
/**
 * Created by PhpStorm.
 * User: hocvt
 * Date: 2019-09-25
 * Time: 15:39
 */

namespace Colombo\Converters\Drivers;


use Colombo\Converters\ConvertedResult;
use Colombo\Converters\Exceptions\ConvertException;
use Colombo\Converters\Process\CanRunCommand;

class Qpdf extends CanRunCommand implements ConverterInterface {
    
    protected $bin = 'qpdf';
    public static $gs_bin = '';
    
    protected $process_options = [
        '--linearize' => true,
        '--min-version=1.5' => true,
        '--object-streams=generate' => true,
    ];
    
    protected $start_page = 0;
    protected $end_page = 0;
    
    /**
     * qpdf --linearize --collate --empty --min-version=1.5 --pages qpdf.pdf 1-10 -- qpdf.o.pdf
     * @param $path
     * @param $outputFormat
     * @param $inputFormat
     *
     * @return ConvertedResult
     */
    public function convert( $path, $outputFormat, $inputFormat = '' ): ConvertedResult {
        $result = new ConvertedResult();
    
        $command = $this->buildCommand([$path]);
        $repaired_path = $path . ".repaired";
        $try_repair = false;
        start_convert:
        try{
            if($try_repair){
                $repaire_command = [
                   self::$gs_bin,
                   '-o',$repaired_path,
                    '-sDEVICE=pdfwrite',
                    '-dPDFSETTINGS=/prepress',
                    $path
                ];
                $this->run($repaire_command);
                $command = $this->buildCommand([$repaired_path]);
            }
            $this->run( $command);
            $result->setContent( $this->output() );
        }catch (\RuntimeException $ex){
            if ($try_repair == false && self::$gs_bin){
                $try_repair = true;
                goto start_convert;
            }
            $result->addErrors( $ex->getMessage(), $ex->getCode());
        } finally {
            @unlink($repaired_path);
        }
        return $result;
    }
    
    protected function buildCommand(array $append = [], array $prepend = [], $use_equal_symbol = false) : array{
        $command = parent::buildCommand([], $prepend);
        
        if($this->start_page || $this->end_page){
            $this->start_page = $this->start_page ? $this->start_page : 1;
            $this->end_page = $this->end_page ? $this->end_page : 'z';
            $command[] = '--collate';
            $command[] = '--empty';
            $command[] = '--pages';
            $command[] = $append[0];
            $command[] = $this->start_page . "-" . $this->end_page;
            $command[] = "--";
            $command[] = "-";
        }else{
            $command = array_merge($command, $append, ["-"]);
        }

//        dd($command);
        
        return $command;
    }
    
    public function startPage( int $page ) {
        $this->start_page = $page;
    }
    
    public function endPage( int $page ) {
        $this->end_page = $page;
    }
}