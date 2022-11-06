<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;

use Clive0417\DBDiagramRegularParse\Formats\Models\ModelCreatorFormat;

class ClassNameModel
{
    protected $class_name;

    protected $extend_from;

    protected $implements =[];

    public function __construct($table_name)
    {
        $this->extend_from = ModelCreatorFormat::getExtendFrom($table_name);
        $this->class_name = ModelCreatorFormat::getEntityName($table_name);
    }

    public function addImplement(string $implement)
    {
        $this->implements[] = $implement;
    }

    /**
     * @return string
     */
    public function getExtendFrom(): string
    {
        return $this->extend_from;
    }

    public function toLine()
    {
        $full_line = sprintf('class %s ',$this->class_name);
        if ($this->extend_from !== null) {
            $full_line = $full_line.sprintf('extends %s ',$this->extend_from);
        }
        if (!empty($this->implements)) {
            $full_line = $full_line.'implements ';
            foreach ($this->implements as $implement) {
                $full_line = $full_line.$implement.',';
            }
            $full_line = substr($full_line, 0, -1);
        }

        return $full_line;
    }

}
