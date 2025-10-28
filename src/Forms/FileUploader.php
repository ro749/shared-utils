<?php

namespace Ro749\SharedUtils\Forms;
use Ro749\SharedUtils\Readers\DbUpdater;
use Ro749\SharedUtils\Tables\BaseTable;
use Closure;
class FileUploader extends FormField
{
    public string $accept = '';
    public DbUpdater $updater;
    public BaseTable $preview_table;
    public function __construct(string $accept = '',DbUpdater $updater = null,BaseTable $preview_table,bool $autosave = false)
    {
        parent::__construct(InputType::FILE,autosave: $autosave);
        $this->accept = $accept;
        $this->updater = $updater;
        $this->preview_table = $preview_table;
    }

    public static function getType(): string
    {
        return 'file';
    }

    public function preview($file)
    {
        $this->updater->read_cvs($file);
    }

    public function save(){
        $this->updater->save_changes();
    }

    public function render(string $name,string $push = "",string $data)
    {
        return view('shared-utils::components.forms.file-uploader',[
            "field"=>$this,
            "name"=>$name,
            "push_init"=>$push,
        ]);
    }
}