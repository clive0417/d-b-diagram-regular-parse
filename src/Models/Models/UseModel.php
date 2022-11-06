<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;


use Clive0417\DBDiagramRegularParse\Formats\Models\ModelCreatorFormat;

class UseModel
{
    protected $use_name;


    public function __construct($use_name)
    {
        $this->use_name  = $use_name;
    }

    public function toLine()
    {
        return sprintf('use %s\%s;',ModelCreatorFormat::getUsePath($this->use_name),$this->use_name);
    }
}
