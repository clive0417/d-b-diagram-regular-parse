<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;


use Clive0417\DBDiagramRegularParse\Formats\Models\ModelCreatorFormat;

class ModelPathModel
{
    protected $model_path = __DIR__ . '/../../../../../../app/Models/';


    public function __construct(string $table_name)
    {
        $this->entity_path =  sprintf('%s/%s/Entities/',$this->model_path,ModelCreatorFormat::getTableGroupName($table_name));
    }

    public function toLine()
    {
        return $this->entity_path;
    }

}
