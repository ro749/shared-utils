<?php

namespace Ro749\SharedUtils\Forms;
use Log;
use Ro749\SharedUtils\Readers\DbReader;
use Ro749\SharedUtils\Tables\BaseTable;
use Closure;
class FileUploader extends Field
{
    public string $component = 'file-uploader';
    public string $name;
    public string $push;
    public string $data;

    public string $accept = '';
    public DbReader $reader;
    public BaseTable $preview_table;
    public Closure $cancel;
    public Closure $save;
    public function __construct(
        string $accept = '',
        DbReader $reader = null,
        Closure $cancel = null,
        Closure $save = null,
        BaseTable $preview_table,
        bool $autosave = false,
        string $name = "",
        string $push = "",
        string $data = "",
    )
    {
        parent::__construct(InputType::FILE,autosave: $autosave);
        $this->name = $name;
        $this->push = $push;
        $this->data = $data;
        $this->accept = $accept;
        $this->reader = $reader;
        $this->preview_table = $preview_table;
        $this->cancel = $cancel;
        $this->save = $save;
    }

    public static function getType(): string
    {
        return 'file';
    }

    public function preview($file)
    {
        $this->reader->read_csv($file);
    }

    public function cancel()
    {
        if(!empty($this->cancel)){
            ($this->cancel)();
        }
        
    }

    public function save(){
        if(!empty($this->save)){
            ($this->save)();
        }
    }

    public function render($name="", $form_id="")
    {
        return view('shared-utils::components.forms.file-uploader',[
            'element' => $this,
            'name' => $name,
            'form_id' => $form_id,
        ]);
    }
}