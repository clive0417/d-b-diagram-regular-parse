<?php
namespace Clive0417\DBDiagramRegularParse\Commands\Models;

use Carbon\Carbon;
use Clive0417\DBDiagramRegularParse\Models\Models\ClassNameModel;
use Clive0417\DBDiagramRegularParse\Models\Models\DatesModel;
use Clive0417\DBDiagramRegularParse\Models\Models\ModelNameModel;
use Clive0417\DBDiagramRegularParse\Models\Models\ModelPathModel;
use Clive0417\DBDiagramRegularParse\Models\Models\FillableModel;
use Clive0417\DBDiagramRegularParse\Models\Models\HiddenModel;
use Clive0417\DBDiagramRegularParse\Models\Models\NameSpaceModel;
use Clive0417\DBDiagramRegularParse\Models\Models\SetterGetterModel;
use Clive0417\DBDiagramRegularParse\Models\Models\TableModel;
use Clive0417\DBDiagramRegularParse\Models\Models\TraitModel;
use Clive0417\DBDiagramRegularParse\Models\Models\UseModel;
use Clive0417\ModelGenerator\Supports\ModelCreator;
use Clive0417\ModelGenerator\Supports\ModelCreatorSupport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ModelGenerateCommand extends Command
{
    protected $signature = 'clive0417:model_generate';


    protected $description = '讀取DB資料，自動產生model file';

    public function handle()
    {
        $this->comment(sprintf('start at %s',Carbon::now()->toDateTimeString()));

        //對DB  新增 type 格式 enum
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        //讀取DB ,取得所有table name
        $tables = DB::connection()->getDoctrineSchemaManager()->listTables();

        //tables 跑foreach
        foreach ($tables as $table) {
            //此張表有沒有Carbon 欄位
            $has_carbon_column = false;
            //判斷有無包含 'id' ，沒有則代表為中介表 skip
            $table_name = $table->getName();
            if (array_key_exists('id', $table->getColumns()) === false) {
                continue;
            }
            //設定table , EntityPath ClassName... table 級別資料
            $ModelCreator = (new ModelCreator());
            $ModelCreator->setEntityPath(new ModelPathModel($table_name));
            $ModelCreator->setEntityName(new ModelNameModel($table_name));
            $ModelCreator->setNamespace(new NameSpaceModel($table_name));
            $ModelCreator->setClassName(new ClassNameModel($table_name));
            $ModelCreator->addUse(new UseModel($ModelCreator->getClassName()->getExtendFrom()));
            $ModelCreator->setTable(new TableModel($table_name));
            //初始化 fillable/hidden/dates Model 輸出為單一一個array 。
            $FillableModel = (new FillableModel());
            $DatesModel = (new DatesModel());
            $HiddenModel = (new HiddenModel());

            //對column 跑 foreach。
            foreach ($table->getColumns() as $column_name => $Column) {

                //特殊欄位判斷 switch()
                // TODO step 4 設定各欄位 需添加的 trait/use...
                switch ($Column->getName()) {
                    case 'deleted_at':
                        $ModelCreator->addTrait(new TraitModel('SoftDeletes'));
                        $ModelCreator->addUse(new UseModel('SoftDeletes'));
                        $HiddenModel->addHidden($Column->getName());
                        break;
                    default:
                        $FillableModel->addFillable($Column->getName());
                        //判斷欄位 type dates /timestamp 就加進入此
                        if (in_array($Column->getName(), ModelCreatorSupport::getDateTimeTypeList())) {
                            $DatesModel->addDates($Column->getName());
                            $has_carbon_column = true;
                        }
                        // setter & getter
                        $ModelCreator->addSetterGetter(new SetterGetterModel($Column));
                        break;
                }
            }
            if ($has_carbon_column = true) {
                $ModelCreator->addUse(new UseModel('Carbon'));
            }
            //設定 fillable/hidden/dates Model to Creator
            $ModelCreator->setFillables($FillableModel);
            $ModelCreator->setDates($DatesModel);
            $ModelCreator->setHidden($HiddenModel);
            //匯出Entity 檔案
            $ModelCreator->replaceDummyWordsInStub()->outputEntity();
            $this->info(sprintf('%s 匯出完成',$ModelCreator->getEntityName()->toline()));

        }

        $this->comment('finish at %s',Carbon::now()->toDateTimeString());

    }

}
