<?php

namespace Clive0417\DBDiagramRegularParse\Models\Migrations;


class IndexModel
{
    // [normal,unique,pk(多對多的)]
    public $index_type;
    // 為空 array
    public $index_columns = [];
    // index_comment
    public $index_comment;

    public function toLine()
    {
        $line                  = '$table';
        //append Index type
        $migration_index_type = $this->generateMigrationIndexType();
        if ($migration_index_type === '') {
          return '';
        }
        $line = $line = $line.'->'.$this->generateMigrationIndexType();

        //append Comment
        //append comment
        if ($this->index_comment !== '') {
            $line = $line.'->'.sprintf('comment(%s)',"'".$this->index_comment."'");
        }

        return $line;
    }

    protected function generateMigrationIndexType()
    {
        $index_type_method_name = '';
        switch ($this->index_type) {
            case 'index':
                $index_type_method_name = 'index';
                break;
            case 'unique':
                $index_type_method_name = 'unique';
                break;
            case 'pk':
                $index_type_method_name = 'primary';
                break;
        }

        $migration_index_type = sprintf("%s(['%s'], 'idx-%s')",$index_type_method_name, implode("','",$this->index_columns),implode('-',$this->index_columns));


        return $migration_index_type;
    }
}
