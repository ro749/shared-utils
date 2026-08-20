<?php

namespace Ro749\SharedUtils\Readers;
use Illuminate\Support\Facades\Log;
class Reader 
{
    public string $warning_text='';

    public string $error_text='';
    public array $ignore_lines = [];
    //reads a file and returns an array
    public function read_csv($file): array
    {
        $raw_lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $titles = explode(',', $raw_lines[0]);
        Log::info(json_encode(config('lang'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        foreach ($titles as $k => &$title) {
            $title = trim($title, " \t\n\r\0\x0B\xEF\xBB\xBF");
            $title = str_replace('.', '', $title);
            $title = mb_strtolower($title);
            $title = str_replace(' ', '_', $title);
            if(!empty(config('lang.'.$title))) $title = config('lang.'.$title);
        }
        $this->check_columns($titles);
        if ($this->error_text != '') {
            echo $this->error_text;
            return [];
        }
        $data = array_slice($raw_lines, 1);
        $lines = [];
        
        foreach ($data as $key => &$raw_line) {
            $line = [];
            $raw_row = explode(',', $raw_line);
            unset($title);
            unset($k);
            foreach ($titles as $k => $title) {
                if (in_array($title, $this->ignore_lines)) continue;
                $line[$title] = $raw_row[$k];
            }
            
            $lines[] = $line;
        }
        $titles = array_diff($titles, $this->ignore_lines);
        $this->process_data($titles,$lines);
        return $lines;
    }

    public function check_columns(array &$titles):void{}

    public function process_data(array &$titles,array &$data):void{}

    public function get_type(string $column,array &$data):array{
        $type = ['int',0,0];
        foreach($data as &$row){
            if($row[$column] === '') continue;
            if($type[0] == 'int'){
                if(!is_numeric($row[$column]) || preg_match('/^0[0-9]/', $row[$column])){
                    return ['string'];
                }
                else if (strpos($row[$column], '.')){
                    $type[0] = 'float';
                    $parts = explode('.', $row[$column]);
                    $type[1] = strlen($parts[0]);
                    $type[2] = strlen($parts[1]);
                }
            }
            else if($type[0] == 'float'){
                if(!is_numeric($row[$column]) || preg_match('/^0[0-9]/', $row[$column])){
                    return ['string'];
                }
                else if (strpos($row[$column], '.')){
                    $parts = explode('.', $row[$column]);
                    $type[1] = strlen($parts[0])>$type[1] ? strlen($parts[0]) : $type[1];
                    $type[2] = strlen($parts[1])>$type[2] ? strlen($parts[1]) : $type[2];
                }
            }
        }
        
        return $type;
    }
}
