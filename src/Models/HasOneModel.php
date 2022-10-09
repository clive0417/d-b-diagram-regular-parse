<?php

namespace Clive0417\DBDiagramRegularParse\Models;

use Illuminate\Support\Str;

class HasOneModel
{
    public $main_table_name;
    public $relation_method_name;
    public $relation_entity_name;
    public $foreign_key;
    public $local_key;


    public function toLine()
    {
        /*這邊就是先放上轉換結果 ，將要取代的東西做替換*/
        /*
         public function profiles(): HasOne
        {
            return $this->hasOne(MemberProfileEntity::class, 'member_id', 'id');
        }
         * */
        $line = "public function {{relation_method_name}}(): HasOne
        {
            return \$this->hasOne({{relation_entity_name}}::class, {{foreign_key}}, {{local_key}});
        }";
        $line = Str::replace('{{relation_method_name}}',$this->relation_method_name,$line);
        $line = Str::replace('{{relation_entity_name}}',$this->relation_entity_name,$line);
        $line = Str::replace('{{foreign_key}}',"'".$this->foreign_key."'",$line);
        $line = Str::replace('{{local_key}}',"'".$this->local_key."'",$line);

        return $line;
    }
}
