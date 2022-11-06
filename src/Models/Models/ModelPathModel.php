<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;


class ModelPathModel
{
    protected $entity_path;

    public function __construct(string $table_name)
    {
        $this->entity_path =  sprintf('%s%s/Entities/',config('model-generator.entity_root_path'),ModelCreatorFormat::getTableGroupName($table_name),) ;
    }

    public function toLine()
    {
        return $this->entity_path;
    }

}
