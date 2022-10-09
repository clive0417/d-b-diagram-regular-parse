<?php

namespace Clive0417\DBDiagramRegularParse\Models;

use Illuminate\Support\Str;
use Mpociot\HumanRegex\HumanRegex;

class BelongsToModel
{
    public $main_table_name;
    public $relation_method_name;
    public $relation_entity_name;
    public $foreign_key;



    public function toLine()
    {
        /*這邊就是先放上轉換結果 ，將要取代的東西做替換*/
        /*
        public function members(): BelongsTo
        {
            return $this->belongsTo(MemberEntity::class, 'member_id');
        }
         * */
        $line = "public function {{relation_method_name}}(): BelongsTo
        {
            return \$this->belongsTo({{relation_entity_name}}::class, {{foreign_key}});
        }";
        $line = Str::replace('{{relation_method_name}}',$this->relation_method_name,$line);
        $line = Str::replace('{{relation_entity_name}}',$this->relation_entity_name,$line);
        $line = Str::replace('{{foreign_key}}',"'".$this->foreign_key."'",$line);

        return $line;
    }

}
