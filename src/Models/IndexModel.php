<?php

namespace Clive0417\DBDiagramRegularParse\Models;


class IndexModel
{
    // [normal,unique,pk(多對多的)]
    public $index_type;
    // 為空 array
    public $index_columns= [];
    // index_comment
    public $index_comment;

    public function toLine()
    {
    }
}
