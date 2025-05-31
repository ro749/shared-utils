<?php
namespace Ro749\SharedUtils\Tables;

use Illuminate\Support\Facades\DB;

class BaseTableDefinition
{
    //the id the table is going to have
    public string $id;

    //the table name
    public string $table;

    /** var array<string, Column> */
    public array $columns = [];

    public ?View $view = null;
    public ?Delete $delete = null;

    public function __construct(string $id, string $table, array $columns, View $view = null, Delete $delete = null)
    {
        $this->id = $id;
        $this->table = $table;
        $this->columns = $columns;
        $this->view = $view;
        $this->delete = $delete;
    }

    public function getColumn(string $key): ?Column
    {
        return $this->columns[$key] ?? null;
    }

    public function getColumnKeys(): array
    {
        return array_keys($this->columns);
    }

    public function needsButtons(): bool
    {
        return $this->view || $this->delete;
    }

    public function get(): mixed
    {
        return DB::table($this->table)
            ->select(array_merge(['id'], $this->getColumnKeys()))
            ->get();
    }

    public function delete(int $id): void
    {
        DB::table($this->table)->where('id', $id)->delete();
    }
}