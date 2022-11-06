<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;



use Clive0417\DBDiagramRegularParse\Formats\Models\ModelCreatorFormat;

class FillableModel
{
    protected $fillable = [];

    public function addFillable($column_name)
    {
        $this->fillable[] = $column_name;
    }

    public function toLine()
    {
        $full_line = '';
        if (!empty($this->fillable)) {
            $full_line = ModelCreatorFormat::getIndent().'protected $fillable = ['.PHP_EOL;
            foreach ($this->fillable as $date_column_name) {
                $full_line = $full_line.ModelCreatorFormat::getIndent().ModelCreatorFormat::getIndent()."'".$date_column_name."'".",".PHP_EOL;
            }
            $full_line = $full_line.ModelCreatorFormat::getIndent().'];';
        }
        return $full_line;
    }
}
