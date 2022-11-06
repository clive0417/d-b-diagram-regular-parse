<?php

namespace Clive0417\DBDiagramRegularParse\Models\Models;

use Clive0417\DBDiagramRegularParse\Formats\Models\ModelCreatorFormat;
use Doctrine\DBAL\Schema\Column;

class SetterGetterModel
{
    protected $Column;

    public function __construct(Column $Column)
    {
        $this->Column = $Column;
    }

    public function toLine()
    {
        $setter = '';
        $getter = '';

        $setter = ModelCreatorFormat::generateSetFunction($this->Column);
        $getter = ModelCreatorFormat::generateGetFunction($this->Column);

        return $setter.PHP_EOL.$getter.PHP_EOL;
    }
}
