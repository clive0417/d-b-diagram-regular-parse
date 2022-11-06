<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;

use Clive0417\DBDiagramRegularParse\Formats\Models\ModelCreatorFormat;

class ModelNameModel
{
    protected $entity_name;

    public function __construct(string $table_name)
    {
        $this->entity_name =  ModelCreatorFormat::getEntityName($table_name);
    }

    public function toLine()
    {
        return $this->entity_name;
    }

}
