<?php

namespace Ro749\SharedUtils\Readers;

class MigrationHelper 
{
    //creates a new table
    public static function generate_migration_for_table($table_name,$columns_data,$table_data) {
        $migration_text = "Schema::create('".$table_name."', function (Blueprint \$table) {\n";

        $migration_text .= "\$table->id();\n";
        // Assuming $table_data is an associative array with column names as keys and data types as values
        foreach ($columns_data as $column_name => $data_type) {
            switch ($data_type) {
                case 'int':
                    $migration_text .= "\$table->integer('".$column_name."');\n";
                    break;
                case 'float':
                    $migration_text .= "\$table->float('".$column_name."');\n";
                    break;
                case 'string':
                default:
                    $migration_text .= "\$table->string('".$column_name."');\n";
                    break;
            }
        }
        $migration_text .= "});\n";

        foreach ($table_data as $row) {
            $migration_text .= "DB::table('".$table_name."')->insert([\n";
            foreach ($row as $column => $value) {
                $migration_text .= "'$column' => '$value',\n";
            }
            $migration_text .= "]);\n";
        }

        return $migration_text;
        
    }   
    //modifies data of a table of normalized values
    public static function generate_migration_for_alter_data($table_name,$table_data) {
        $migration_text = "";

        foreach ($table_data as $row) {
            $migration_text .= "DB::table('".$table_name."')->where('".$row[0]."', '".$row[1]."')->update(['".$row[0]."' => '".$row[2]."']);\n";
        }

        $migration_text .= "\n";

        return $migration_text;
        
    }
    //changes a column of a table
    public static function generate_migration_for_alter_table($table_name,$columns_data) {
        $migration_text = "Schema::table('".$table_name."', function (Blueprint \$table) {\n";

        // Assuming $columns_data is an associative array with column names as keys and data types as values
        foreach ($columns_data as $column_name => $data_type) {
            switch ($data_type) {
                case 'int':
                    $migration_text .= "\$table->integer('".$column_name."')->change();\n";
                    break;
                case 'float':
                    $migration_text .= "\$table->float('".$column_name."')->change();\n";
                    break;
                case 'string':
                default:
                    $migration_text .= "\$table->string('".$column_name."')->change();\n";
                    break;
            }
        }
        $migration_text .= "});\n";

        return $migration_text;
        
    }

    public static function generate_migration_for_add_rows($table_name,$columns_data) {
        $migration_text = "Schema::table('".$table_name."', function (Blueprint \$table) {\n";

        // Assuming $columns_data is an associative array with column names as keys and data types as values
        foreach ($columns_data as $column_name => $data_type) {
            switch ($data_type) {
                case 'int':
                    $migration_text .= "\$table->integer('".$column_name."');\n";
                    break;
                case 'float':
                    $migration_text .= "\$table->float('".$column_name."');\n";
                    break;
                case 'string':
                default:
                    $migration_text .= "\$table->string('".$column_name."');\n";
                    break;
            }
        }
        $migration_text .= "});\n";

        return $migration_text;
    }

    public static function generate_migration_for_remove_rows($table_name,$columns_data) {
        $migration_text = "Schema::table('".$table_name."', function (Blueprint \$table) {\n";

        // Assuming $columns_data is an associative array with column names as keys and data types as values
        foreach ($columns_data as $column_name) {
            $migration_text .= "\$table->dropColumn('".$column_name."');\n";
        }
        $migration_text .= "});\n";

        return $migration_text;
    }

    public static function create_migration_file($migration_name,$migration_text) {
        $ans = '<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        '.$migration_text.'
    }
};';

        $file = date('Y_m_d_His').'_'.$migration_name.'.php';
        file_put_contents(database_path('migrations/'.$file),$ans );
        echo "Migration created: ".database_path('migrations/'.$file);
    }


} 