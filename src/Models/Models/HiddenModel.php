<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;

class HiddenModel
{
    protected $hidden = [];

    public function addHidden($column_name)
    {
        $this->hidden[] = $column_name;
    }

    public function toLine()
    {
        $full_line = '';
        if (!empty($this->hidden)) {
            $full_line = "\t".'protected $hidden = [';
            foreach ($this->hidden as $date_column_name) {
                $full_line = $full_line."'".$date_column_name."'".",";
            }
            $full_line = $full_line.'];';
        }
        return $full_line;
    }
}
