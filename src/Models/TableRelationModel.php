<?php

namespace Clive0417\DBDiagramRegularParse\Models;


class TableRelationModel
{
    // relation_type (belongsToMany/belongsTo/hasMany/HasOne)
    public $relation_type;
    // main_table_name = $table_name
    public $main_table_name;
    // main_table_key_column = column_name
    public $main_table_key_column;
    // relation_table_name
    public $relation_table_name;
    // relation_table_key_column
    public $relation_table_key_column;




    public function toLine()
    {
    }
}
