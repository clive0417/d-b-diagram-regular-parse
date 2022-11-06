<?php
namespace Clive0417\DBDiagramRegularParse\Commands\Migrations;

use Carbon\Carbon;
use Clive0417\DBDiagramRegularParse\Models\Relations\BelongsToManyModel;
use Clive0417\DBDiagramRegularParse\Models\Relations\BelongsToModel;
use Clive0417\DBDiagramRegularParse\Models\Migrations\ColumnModel;
use Clive0417\DBDiagramRegularParse\Models\Relations\HasManyModel;
use Clive0417\DBDiagramRegularParse\Models\Relations\HasOneModel;
use Clive0417\DBDiagramRegularParse\Models\Migrations\IndexModel;
use Clive0417\DBDiagramRegularParse\Creators\MigrationCreator;
use Clive0417\DBDiagramRegularParse\Formats\Migrations\MigrationCreatorFormat;
use Illuminate\Console\Command;

class MigrationGenerateCommand extends Command
{
    protected $signature = 'clive0417:migration_generate {--db_diagram_path=}';


    protected $description = '讀取DB資料，自動產生model file';

    public function handle()
    {
        //開始執行
        $this->comment(sprintf('start at %s',Carbon::now()->toDateTimeString()));

        //
        $db_diagram_path = $this->option('db_diagram_path');
        //1.讀取檔案
        $db_diagram           = file_get_contents(base_path() . $db_diagram_path, 'r');
        $db_diagram_txt_array = explode(PHP_EOL, $db_diagram);

        //2.建立輸出空白檔 relation file
        $table_relation_belongsToMany_file = fopen(base_path() . "/storage/app/tmp/table_relation_belongsToMany_file.csv", 'w+');
        $table_relation_hasOne_file = fopen(base_path() . "/storage/app/tmp/table_relation_hasOne_file.csv", 'w+');
        $table_relation_hasMany_file = fopen(base_path() . "/storage/app/tmp/table_relation_hasMany_file.csv", 'w+');
        $table_relation_belongsTo_file = fopen(base_path() . "/storage/app/tmp/table_relation_belongsTo_file.csv", 'w+');

        //loop  status ㄌ要在foreach 外，因為是控制下一層迴圈。
        $Table                 = (new MigrationCreator());
        $is_intermediary_table = true; // 這邊用有 pk id 來 judge ， 然後
        $table_loop            = false;
        $table_index_loop      = false;
        $BelongsToManyModelA = null;
        $BelongsToManyModelB = null;
        $BelongToModel =null;
        $HasOneModel = null;
        $HasManyModel = null;


        //2.CSV Row Data 跑 Foreach
        foreach ($db_diagram_txt_array as $line) {
            $TableColumnModel             = null;
            $index_row                    = null;

            //1. LoopEnd(跳脫迴圈時，建立)
            if (MigrationCreatorFormat::isTableOrIndexLoopEnd($line)) {
                // 產出該table 的migration
                if ($Table->getTableName() !== '') {

                    $Table->replaceDummyWordsInStub()->outputMigration();
                    $this->info(sprintf('migration class %s already make',$Table->getTableName()));
                }
                $Table = (new MigrationCreator());;
                $is_intermediary_table = true;
                $table_loop            = false;
                $table_index_loop      = false;
                $BelongsToManyModelA   = null;
                $BelongsToManyModelB   = null;
                continue;
            }

            //進入tableIndex loop 判斷區 (最多條件的要放前面)
            if ($table_loop === true && $table_index_loop === true) {
                $IndexModel = (new IndexModel());
                if (MigrationCreatorFormat::isMultipleIndexKey($line)) {
                    $IndexModel->index_type = MigrationCreatorFormat::parseIndexType($line);
                    $IndexModel->index_columns = MigrationCreatorFormat::parseMultipleIndexColumnName($line);
                    $IndexModel->index_comment = MigrationCreatorFormat::parseNote($line);
                } elseif (MigrationCreatorFormat::isSingularIndexKey($line)) {
                    $IndexModel->index_type = MigrationCreatorFormat::parseIndexType($line);
                    $IndexModel->index_columns = MigrationCreatorFormat::parseSingularIndexColumnName($line);
                    $IndexModel->index_comment = MigrationCreatorFormat::parseNote($line);
                }

                $Table->addIndex($IndexModel);
                //index 相關執行完畢 continue; 不執行下述column 區間
                continue;
            }

            //進入table loop 判斷區
            if ($table_loop === true) {
                //Table.3 判斷是不是Index
                if (MigrationCreatorFormat::isTableIndexStart($line)) {
                    //Table.3.Y 進入 Index 迴圈
                    $table_index_loop = true;
                    continue;
                }

                //Table.4 判斷Column Name
                $column_name = MigrationCreatorFormat::parseColumnName($line);
                if ($column_name === '') {
                    continue;
                }
                // 判斷是不是多對多關聯表，因為id 一定是第一個故可以直接
                if ($column_name === 'id') {
                    $is_intermediary_table = false;
                }
                $ColumnModel = (new ColumnModel());
                $ColumnModel->column_name             = $column_name;
                $ColumnModel->column_type             = MigrationCreatorFormat::parseColumnType($line);
                $ColumnModel->column_length_or_option = MigrationCreatorFormat::parseColumnLengthOrOption($line);
                $ColumnModel->column_comment          = MigrationCreatorFormat::parseNote($line);
                $ColumnModel->column_nullable         = MigrationCreatorFormat::parseColumnNullable($line);
                $ColumnModel->column_default          = MigrationCreatorFormat::parseColumnDefault($line);
                $ColumnModel->column_auto_increment   = MigrationCreatorFormat::parseAutoIncrement($line);
                $ColumnModel->column_is_unique        = MigrationCreatorFormat::parseUnique($line);
                $ColumnModel->column_allow_minus      = MigrationCreatorFormat::parseAllowMinus($line);
                $Table->addColumn($ColumnModel);

                // 判斷此 column 是否有 關聯紀錄 ref

                if (MigrationCreatorFormat::hasRelation($line)) {
                    // intermediary_table 多對多關係
                    if ($is_intermediary_table) {
                        // 建立兩個BelongsToMany
                        //多對多'main_table_name',
                        if ($BelongsToManyModelA !== null) {
                            // 設定 $BelongsToManyModelA的
                            // relation_entity_name
                            // intermediary_table_relation_id
                            $BelongsToManyModelA->relation_entity_name = MigrationCreatorFormat::getEntityName(MigrationCreatorFormat::getRefTableName($line));
                            $BelongsToManyModelA->intermediary_table_relation_id = $column_name;

                            $BelongsToManyModelB = (new BelongsToManyModel());
                            $BelongsToManyModelB->main_table_name = MigrationCreatorFormat::getRefTableName($line);
                            $BelongsToManyModelB->relation_method_name = MigrationCreatorFormat::getBelongToManyRelationMethodName($BelongsToManyModelB->main_table_name,$Table->getTableName());
                            $BelongsToManyModelB->intermediary_table = $Table->getTableName();
                            $BelongsToManyModelB->intermediary_table_main_id = $column_name;
                            $BelongsToManyModelB->relation_entity_name = MigrationCreatorFormat::getEntityName($BelongsToManyModelA->main_table_name);
                            $BelongsToManyModelB->intermediary_table_relation_id = $BelongsToManyModelA->intermediary_table_main_id;
                            // Save to csv
                            $belongs_to_many_text_A['main_table']= $BelongsToManyModelA->main_table_name;
                            $belongs_to_many_text_A['method']= $BelongsToManyModelA->toLine();
                            fputcsv($table_relation_belongsToMany_file,$belongs_to_many_text_A);
                            $belongs_to_many_text_B['main_table']= $BelongsToManyModelB->main_table_name;
                            $belongs_to_many_text_B['method']= $BelongsToManyModelB->toLine();
                            fputcsv($table_relation_belongsToMany_file,$belongs_to_many_text_B);

                            // reset $BelongsToManyModelA and $BelongsToManyModelB
                            $BelongsToManyModelA = null;
                            $BelongsToManyModelB = null;

                        } else {
                            $BelongsToManyModelA = (new BelongsToManyModel());
                            $BelongsToManyModelA->main_table_name = MigrationCreatorFormat::getRefTableName($line);
                            $BelongsToManyModelA->relation_method_name = MigrationCreatorFormat::getBelongToManyRelationMethodName($BelongsToManyModelA->main_table_name,$Table->getTableName());
                            $BelongsToManyModelA->intermediary_table = $Table->getTableName();
                            $BelongsToManyModelA->intermediary_table_main_id = $column_name;
                        }
                    } else {
                        // 判斷是一對多 還是一對一
                        if (MigrationCreatorFormat::isOneToOneRelation($line)) {
                            $ref_table = MigrationCreatorFormat::getRefTableName($line);
                            // 添加 BelongsTo relation
                            $BelongToModel = (new BelongsToModel());
                            $BelongToModel->main_table_name = $Table->getTableName();
                            $BelongToModel->relation_method_name = MigrationCreatorFormat::getBelongToRelationMethodName($ref_table);
                            $BelongToModel->relation_entity_name = MigrationCreatorFormat::getEntityName($ref_table);
                            $BelongToModel->foreign_key = $column_name;
                            $belongs_to_text['main_table']= $BelongToModel->main_table_name;
                            $belongs_to_text['method']= $BelongToModel->toLine();
                            fputcsv($table_relation_belongsTo_file,$belongs_to_text);
                            // 添加 HasOne relation
                            $HasOneModel = (new HasOneModel());
                            $HasOneModel->main_table_name = $ref_table;
                            $HasOneModel->relation_method_name = MigrationCreatorFormat::getBelongToRelationMethodName($Table->getTableName());
                            $HasOneModel->relation_entity_name = MigrationCreatorFormat::getEntityName($Table->getTableName());
                            $HasOneModel->foreign_key = $column_name;
                            $HasOneModel->local_key = MigrationCreatorFormat::getLocalKey($line);
                            $has_one_text['main_table']= $HasOneModel->main_table_name;
                            $has_one_text['method']= $HasOneModel->toLine();
                            fputcsv($table_relation_hasOne_file,$has_one_text);

                        } elseif (MigrationCreatorFormat::isManyToOneRelation($line)) {
                            $ref_table = MigrationCreatorFormat::getRefTableName($line);
                            // 添加 BelongsTo relation
                            $BelongToModel = (new BelongsToModel());
                            $BelongToModel->main_table_name = $Table->getTableName();
                            $BelongToModel->relation_method_name = MigrationCreatorFormat::getBelongToRelationMethodName($ref_table);
                            $BelongToModel->relation_entity_name = MigrationCreatorFormat::getEntityName($ref_table);
                            $BelongToModel->foreign_key = $column_name;
                            $belongs_to_text['main_table']= $BelongToModel->main_table_name;
                            $belongs_to_text['method']= $BelongToModel->toLine();
                            fputcsv($table_relation_belongsTo_file,$belongs_to_text);
                            // 添加 HasOne relation
                            $HasManyModel = (new HasManyModel());
                            $HasManyModel->main_table_name = $ref_table;
                            $HasManyModel->relation_method_name = MigrationCreatorFormat::getBelongToRelationMethodName($Table->getTableName());
                            $HasManyModel->relation_entity_name = MigrationCreatorFormat::getEntityName($Table->getTableName());
                            $HasManyModel->foreign_key = $column_name;
                            $HasManyModel->local_key = MigrationCreatorFormat::getLocalKey($line);
                            $has_many_text['main_table']= $HasManyModel->main_table_name;
                            $has_many_text['method']= $HasManyModel->toLine();
                            fputcsv($table_relation_hasMany_file,$has_many_text);
                        }
                    }
                }
            }

            //進入table_index loop 判斷區

            //IsCommentLine
            if (MigrationCreatorFormat::IsCommentLine($line)) {
                $Table->setTableComment(MigrationCreatorFormat::getTableNameComment($line));
                continue;
            }


            //判斷是不是Table 開頭，table loop 開關打開
            if (MigrationCreatorFormat::isTableStart($line)) {
                $table_loop = true;
                $Table->setTableName(MigrationCreatorFormat::parseTableName($line));
                continue;
            }
        }

        //結束執行
        $this->comment(sprintf('finish at %s',Carbon::now()->toDateTimeString()));

    }

}
