<?php

namespace Clive0417\DBDiagramRegularParse\Models\Migrations;

use Mpociot\HumanRegex\HumanRegex;

class ColumnModel
{
    // unique
    public $column_is_unique;
    // column_name
    public $column_name;
    // column_type
    public $column_type;
    // column_length
    public $column_length_or_option;

    // column_nullable
    public $column_nullable;
    // column_default
    public $column_default;
    // column_auto_increment
    public $column_auto_increment;
    // column_allow_minus
    public $column_allow_minus;
    // column_comment
    public $column_comment;


    public function toLine()
    {
        $line = '$table';
        $migration_column_type = $this->generateMigrationColumnType();
        if ($migration_column_type === '') {
            return '';
        } else {
            $line = $line.'->'.$this->generateMigrationColumnType();
        }
        // 特殊欄位判斷 timestamp // softDelete 直接回傳 ，不進行後續判斷。
        if (in_array($migration_column_type,['softDeletes()','timestamps()'])) {
            return $line;
        }
        //append nullable
        if ($this->column_nullable) {
            $line = $line.'->'.'nullable()';
        }
        //append unsigned
        // TODO 待建立 Support 或者是常數 替換 array hard cord
        if ($this->column_allow_minus === false && in_array($this->column_type,['float','decimal','tinyint','smallint','mediumint','bigint']) ) {
            $line = $line.'->'.'unsigned()';
        }
        //append default
        if ($this->column_default !== null) {
            $line = $line.'->'.sprintf('default(%s)',"'".$this->column_default."'");
        }
        //append comment
        if ($this->column_comment !== null) {
            $line = $line.'->'.sprintf('comment(%s)',"'".$this->column_comment."'");
        }
        return $line;
    }

    /**
     * @return string
     * @Author  : Shou
     * @DateTime:2022/10/9 3:20 下午
     */
    protected function generateMigrationColumnType()
    {
        //TODO SpecialColumn 處理，收集在 Config 做設定
        // 2. 'uuid' $table->uuid('uuid')->comment('uuid');
        // 3. 'created_at' $table->timestamps();
        // 4. 'updated_at' 跳過
        // 5. 'deleted_at' $table->softDeletes();
        $migration_column_type = '';
        //先判斷有 auto_increment 的情形
        if ($this->column_auto_increment) {
            switch ($this->column_type) {
                case 'tinyint':
                    $migration_column_type = sprintf("tinyIncrements('%s')",$this->column_name);
                    break;
                case 'smallint':
                    $migration_column_type = sprintf("smallIncrements('%s')",$this->column_name);
                    break;
                case 'mediumint':
                    $migration_column_type = sprintf("mediumIncrements('%s')",$this->column_name);
                    break;
                case 'bigint':
                    $migration_column_type = sprintf("bigIncrements('%s')",$this->column_name);
                    break;
            }
        } else {
            switch ($this->column_type ) {
                case 'tinyint':
                    $migration_column_type = sprintf("tinyInteger('%s')",$this->column_name);
                    break;
                case 'smallint':
                    $migration_column_type = sprintf("smallInteger('%s')",$this->column_name);
                    break;
                case 'mediumint':
                    $migration_column_type = sprintf("mediumInteger('%s')",$this->column_name);
                    break;
                case 'bigint':
                    $migration_column_type = sprintf("bigInteger('%s')",$this->column_name);
                    break;
                case 'char':
                    //特殊欄位判斷
                    if ($this->contentUuid($this->column_name)) {
                        $migration_column_type = sprintf("uuid('%s')",$this->column_name);
                        break;
                    } else {
                        $migration_column_type = sprintf("char('%s', %s)",$this->column_name,$this->column_length_or_option);
                        break;
                    }
                case 'varchar':
                    $migration_column_type = sprintf("string('%s', %s)",$this->column_name,$this->column_length_or_option);
                    break;
                case 'timestamp':
                    //特殊欄位判斷
                    switch ($this->column_name) {
                        case 'created_at':
                            $migration_column_type = 'timestamps()';
                            break;
                        case 'updated_at':
                            // created_at 跟 updated_at 共用 timestamps()
                            $migration_column_type = '';
                            break;
                        case 'deleted_at':
                            $migration_column_type = 'softDeletes()';
                            break;
                        default:
                            $migration_column_type = sprintf("timestamp('%s')",$this->column_name);
                            break;
                    }
                    break;
                case 'text':
                    $migration_column_type = sprintf("text('%s')",$this->column_name);
                    break;
                case 'enum'://enum('receipt_type', ['B2B', 'B2C'])
                    $migration_column_type = sprintf("enum('%s',[%s])",$this->column_name,$this->column_length_or_option);
                    break;
                case 'float':
                    $migration_column_type = sprintf("float('%s')",$this->column_name);
                    break;
                case 'decimal':
                    $migration_column_type = sprintf("decimal('%s',10,2)",$this->column_name);
                    break;
            }
        }
        return $migration_column_type;
    }

    public function contentUuid($column_name):  bool
    {
        $regex = HumanRegex::create()->find('uuid');
        return $regex->matches($column_name);
    }
}
