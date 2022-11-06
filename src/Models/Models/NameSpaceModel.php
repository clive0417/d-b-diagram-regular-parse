<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;


use Clive0417\DBDiagramRegularParse\Formats\Models\ModelCreatorFormat;

class NameSpaceModel
{

    protected $name_space_path;

    public function __construct($table_name)
    {
        $this->name_space_path = 'namespace '.str_replace('/','\\',sprintf('%s%s/Entities;','App\\Models\\',ModelCreatorFormat::getTableGroupName($table_name)));
    }

    public function toLine()
    {
        return $this->name_space_path;
    }
}
