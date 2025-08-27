<?php

namespace Ro749\SharedUtils\Readers;

class Reader 
{
    public string $warning_text='';

    public string $error_text='';
    public function read_cvs($file)
    {
        $raw_lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $titles = explode(',', $raw_lines[0]);
        $titles[0] = mb_substr($titles[0], 1, mb_strlen($titles[0]) - 1);
        foreach ($titles as $k => &$title) {
            $title = trim($title);
            $title = str_replace('.', '', $title);
            $title = mb_strtolower($title);
            $title = str_replace(' ', '_', $title);
        }
        $this->check_columns($titles);
        if ($this->error_text != '') {
            echo $this->error_text;
            return;
        }
        $data = array_slice($raw_lines, 1);
        $lines = [];
        
        foreach ($data as $key => &$raw_line) {
            $line = [];
            $raw_row = explode(',', $raw_line);
            unset($title);
            unset($k);
            foreach ($titles as $k => $title) {
                $line[$title] = $raw_row[$k];
            }
            
            $lines[] = $line;
        }
        $this->process_data($titles,$lines);
        return $lines;
    }

    public function check_columns(array &$titles):void{}

    public function process_data(array &$titles,array &$data):void{}
}
