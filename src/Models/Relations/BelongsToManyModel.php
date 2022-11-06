<?php

namespace Clive0417\DBDiagramRegularParse\Models\Relations;

use Illuminate\Support\Str;

class BelongsToManyModel
{
    public $main_table_name;
    public $relation_method_name;
    public $relation_entity_name;
    public $intermediary_table;
    public $intermediary_table_main_id;
    public $intermediary_table_relation_id;


    public function toLine()
    {
        /*這邊就是先放上轉換結果 ，將要取代的東西做替換*/
        $line = "public function {{relation_method_name}}(): BelongsToMany
            {
                return \$this->belongsToMany({{relation_entity_name}}::class, {{intermediary_table}}, {{intermediary_table_main_id}}, {{intermediary_table_relation_id}})
                    ->withTimestamps();
            };";
        $line = Str::replace('{{relation_method_name}}',$this->relation_method_name,$line);
        $line = Str::replace('{{relation_entity_name}}',$this->relation_entity_name,$line);
        $line = Str::replace('{{intermediary_table}}',"'".$this->intermediary_table."'",$line);
        $line = Str::replace('{{intermediary_table_main_id}}',"'".$this->intermediary_table_main_id."'",$line);
        $line = Str::replace('{{intermediary_table_relation_id}}',"'".$this->intermediary_table_relation_id."'",$line);

        return $line;
    }
}
