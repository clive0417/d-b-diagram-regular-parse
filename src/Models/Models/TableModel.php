<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;


class TableModel
{
    protected $table_name;


    public function __construct($table_name)
    {
        $this->table_name  = $table_name;
    }

    public function toLine()
    {
        return "\t".'protected $table = '."'".$this->table_name."';";
    }

    public function getTableName()
    {
        return $this->table_name;
    }
}
