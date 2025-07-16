<?php

namespace Ro749\SharedUtils\Tables;

enum ColumnModifier: string
{
    case METERS = 'meters';
    case FOOT = 'foot';
    case MONEY = 'money';
    case DOLARS = 'dolars';
    case PERCENT = 'percent';
    case DATE = 'date';
    case NUMBER = 'number';
}

abstract class LogicModifier
{
    abstract public function type(): string;

    abstract public function get_value($key):string;
}

class Enum extends LogicModifier
{
    public string $options;

    public function __construct(string $options)
    {
        $this->options = $options;
    }

    public function type(): string
    {
        return 'enum';
    }

    public  public function get_value($key):string{
        return $key;
    }
}

class ForeignKey extends LogicModifier
{
    public string $table;
    public string $column;

    public function __construct(string $table, string $column)
    {
        $this->table = $table;
        $this->column = $column;
    }
    public function type(): string
    {
        return 'foreign_key';
    }

    public  public function get_value($key):string{
        return $this->table . '.' . $this->column;
    }
}

class MultiForeignKey extends LogicModifier
{
    public string $key_column;
    public string $table;
    public array $columns;

    public function __construct(string $key_column, string $table, array $columns)
    {
        $this->key_column = $key_column;
        $this->table = $table;
        $this->columns = $columns;
    }
    public function type(): string
    {
        return 'multi_foreign_key';
    }

    public  public function get_value($key):string{
        $ans = 'CASE ';
        foreach ($this->columns as $column_key => $column_value) {
            $ans .= 'WHEN '.$this->table.'.'.$key.' = '.$column_key.' THEN '.$this->table.'.'.$column_value.' ';
        }
        $ans .= 'END ';
        return $ans;
    }
}
?>