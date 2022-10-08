<?php

namespace Clive0417\DBDiagramRegularParse\Models;


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
    // column_comment
    public $column_comment;
    // column_nullable
    public $column_nullable;
    // column_default
    public $column_default;
    // column_auto_increment
    public $column_auto_increment;
    // column_allow_minus
    public $column_allow_minus;
    //
    public $is_pk;


    public function toLine()
    {
        //TODO SpecialColumn 處理，收集在 Config 做設定
        // 1.'id'  $table->bigIncrements('id')->comment('流水號');
        // 2. 'uuid' $table->uuid('uuid')->comment('uuid');
        // 3. 'created_at' $table->timestamps();
        // 4. 'updated_at' 跳過
        // 5. 'deleted_at' $table->softDeletes();

        //TODO Switch By ColumnType Inside Judge Increments
        // $table->enum('receipt_type', ['B2B', 'B2C'])->comment('發票類型，B2B OR B2C');


        //TODO append nullable
        //TODO append default
        //TODO append unsigned
        //TODO append comment

    }
}
