<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;


class DatesModel
{
    protected $dates = [];

    public function addDates($column_name)
    {
        $this->dates[] = $column_name;
    }

    public function toLine()
    {
        $full_line = '';
        if (!empty($this->dates)) {
            $full_line = "\t".'protected $dates = [';
            foreach ($this->dates as $date_column_name) {
                $full_line = $full_line."'".$date_column_name."'".",";
            }
            $full_line = $full_line.'];';
        }
        return $full_line;
    }
}
