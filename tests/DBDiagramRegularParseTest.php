<?php

namespace Clive0417\DBDiagramRegularParse\Tests;

use Clive0417\DBDiagramRegularParse\Facades\DBDiagramRegularParse;
use Clive0417\DBDiagramRegularParse\ServiceProvider;
use Orchestra\Testbench\TestCase;

class DBDiagramRegularParseTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'd-b-diagram-regular-parse' => DBDiagramRegularParse::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
