<?php

namespace Ro749\SharedUtils\Forms;
use Log;
use Ro749\SharedUtils\Readers\DbUpdater;
use Ro749\SharedUtils\Tables\BaseTable;
use Closure;
class FileUploader extends Field
{
    public string $accept = '';
    public DbUpdater $updater;
    public BaseTable $preview_table;

    public $cancel;
    public function __construct(string $accept = '',DbUpdater $updater = null,$cancel = null,BaseTable $preview_table,bool $autosave = false)
    {
        parent::__construct(InputType::FILE,autosave: $autosave);
        $this->accept = $accept;
        $this->updater = $updater;
        $this->preview_table = $preview_table;
        $this->cancel = $cancel;
    }

    public static function getType(): string
    {
        return 'file';
    }

    public function preview($file)
    {
        $this->updater->read_cvs($file);
    }

    public function cancel()
    {
        ($this->cancel)();
    }

    public function save(){
        $this->updater->save_changes();
    }

    public function render(string $name,string $push = "",string $data="", string $class="")
    {
        return view('shared-utils::components.forms.file-uploader',[
            "field"=>$this,
            "name"=>$name,
            "form_id"=>$push,
            "push_init"=>$push,
            "preview_table_id"=>$this->preview_table->get_id(),
            "class"=>$class
        ]);
    }
}