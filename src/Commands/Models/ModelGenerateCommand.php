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
use Clive0417\DBDiagramRegularParse\Creators\ModelCreator;
use Clive0417\DBDiagramRegularParse\Supports\ModelCreatorSupport;
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

            //設定relations HasOne
            $hasOne_file_stream = fopen(base_path() . "/storage/app/tmp/table_relation_hasOne_file.csv", 'r');
            while(! feof($hasOne_file_stream))
            {
                $has_one = fgetcsv($hasOne_file_stream);
                if ($has_one == false) {
                    continue;
                }
                if ($has_one[0] == $table_name) {
                    $ModelCreator->addHasOne($has_one);
                }
            }

            if ($ModelCreator->getHasOne() !== []) {
                $ModelCreator->addUse(new UseModel('HasOne'));
            }

            //設定relations HasMany
            $hasMany_file_stream = fopen(base_path() . "/storage/app/tmp/table_relation_hasMany_file.csv", 'r');
            while (!feof($hasMany_file_stream)) {
                $has_many = fgetcsv($hasMany_file_stream);
                if ($has_many == false) {
                    continue;
                }
                if ($has_many[0] == $table_name) {
                    $ModelCreator->addHasMany($has_many);
                }
            }

            if ($ModelCreator->getHasMany() !== []) {
                $ModelCreator->addUse(new UseModel('HasMany'));
            }

            //設定relations belongsTo
            $belongsTo_file_stream = fopen(base_path() . "/storage/app/tmp/table_relation_belongsTo_file.csv", 'r');
            while (!feof($belongsTo_file_stream)) {
                $belongs_to = fgetcsv($belongsTo_file_stream);
                if ($belongs_to == false) {
                    continue;
                }
                if ($belongs_to[0] == $table_name) {
                    $ModelCreator->addBelongsTo($belongs_to);
                }
            }

            if ($ModelCreator->getBelongsTo() !== []) {
                $ModelCreator->addUse(new UseModel('BelongsTo'));
            }

            //設定relations belongsToMany
            $belongsToMany_file_stream = fopen(base_path() . "/storage/app/tmp/table_relation_belongsToMany_file.csv", 'r');
            while (!feof($belongsToMany_file_stream)) {
                $belongs_to_many = fgetcsv($belongsToMany_file_stream);
                if ($belongs_to_many == false) {
                    continue;
                }
                if ($belongs_to_many[0] == $table_name) {
                    $ModelCreator->addBelongsToMany($belongs_to_many);
                }
            }

            if ($ModelCreator->getBelongsToMany() !== []) {
                $ModelCreator->addUse(new UseModel('BelongsToMany'));
            }

            //匯出Entity 檔案
            $ModelCreator->replaceDummyWordsInStub()->outputEntity();
            $this->info(sprintf('%s 匯出完成',$ModelCreator->getEntityName()->toline()));

        }

        $this->comment('finish at %s',Carbon::now()->toDateTimeString());

    }

}
