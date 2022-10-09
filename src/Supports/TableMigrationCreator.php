<?php
namespace Clive0417\DBDiagramRegularParse\Supports;

use Carbon\Carbon;
use Clive0417\DBDiagramRegularParse\Models\ColumnModel;
use Clive0417\DBDiagramRegularParse\Models\IndexModel;
use Illuminate\Support\Facades\File;

class TableMigrationCreator
{
    protected $stub_path = __DIR__ . '/../stubs/migration.stub';

    protected $migration_path = __DIR__ . '/../../../../../database/migrations/';

    protected $stub;

    protected $table_name;

    protected $table_comment;

    protected $Columns;

    protected $Indexes;



    public function __construct()
    {
        $this->stub = File::get($this->stub_path);
        $this->table_name = '';
        $this->table_comment = '';
        $this->Columns = collect();
        $this->Indexes = collect();

    }

    public function setTableName(string $table_name)
    {
        $this->table_name = $table_name;
    }
    public function getTableName()
    {
        return $this->table_name;
    }
    public function setTableComment(string $table_comment)
    {
        $this->table_comment = $table_comment;
    }

    public function addColumn(ColumnModel $ColumnModel)
    {
        $this->Columns->add($ColumnModel);
        return $this;
    }
    public function addIndex(IndexModel $IndexModel)
    {
        $this->Indexes->add($IndexModel);
        return $this;
    }

    public function replaceDummyWordsInStub()
    {
        // 替換 table name
        $this->stub = str_replace('{{table_name}}',$this->table_name,$this->stub);

        // 替換 table_column
        if ($this->Columns) {
            $column_text = '';
            foreach ($this->Columns as $ColumnModel) {
                /** @var ColumnModel $ColumnModel */
                if ($ColumnModel->toLine() === '') {
                    continue;
                }
                $column_text = $column_text.'    '.'    '.'    '.$ColumnModel->toLine().';'.PHP_EOL;
            }
            $this->stub =  str_replace('{{table_columns}}',$column_text,$this->stub);
        }
        // 替換 table_index
        if ($this->Indexes) {
            $index_text = '';
            foreach ($this->Indexes as $IndexModel) {
                /** @var IndexModel $IndexModel */
                if ($IndexModel->toLine() === '') {
                    continue;
                }
                $index_text = $index_text.'    '.'    '.'    '.$IndexModel->toLine().';'.PHP_EOL;
            }
            $this->stub =  str_replace('{{table_indexes}}',$index_text,$this->stub);
        }

        //替換 table_comment
        if ($this->table_comment !=='') {
            $this->stub =  str_replace('{{table_comment}}',$this->table_comment,$this->stub);
        }


        return $this;
    }

    public function outputMigration()
    {
        if (!File::exists($this->migration_path)) {
            File::makeDirectory($this->migration_path, $mode = 0777, true, true);
        }

        File::put($this->migration_path.Carbon::now()->format('Y_m_d_His').'_create_'.$this->table_name.'_table'.'.php',$this->stub);
    }
}
