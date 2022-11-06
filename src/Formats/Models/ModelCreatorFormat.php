<?php

namespace Clive0417\DBDiagramRegularParse\Formats\Models;

use Clive0417\ModelGenerator\Supports\ModelCreatorSupport;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Support\Str;

class ModelCreatorFormat
{
    /**
     * @param \Doctrine\DBAL\Schema\Column $Column
     *
     * @return string
     * @Author  : Shou
     * @DateTime:2022/9/11 4:51 下午
     */
    public static function generateSetFunction(Column $Column)
    {
        //第二個佔位符是type
        $type = self::fieldTypeMapping($Column->getType()->getName());
        //第三個佔位符是正常的 field_name ~不用再寫
        //第四個佔位符是 nullable 的程式碼
        //第五個佔位符是 Contract Name
        //第六個佔位符是加 Single quote 的field_name
        //第七個佔位符是field_name
        $nullable = $nullable = ($Column->getNotnull()) ? '' : '=null';

        //整理function 程式碼
        return "\t".sprintf('public function set%s(%s $%s%s){ return $this->setAttribute(%s,$%s);}',
            ucfirst(Str::camel($Column->getName())), $type, $Column->getName(), $nullable,
            "'" . $Column->getName() . "'", $Column->getName());
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $Column
     *
     * @return string
     * @Author  : Shou
     * @DateTime:2022/9/11 4:52 下午
     */
    public static function generateGetFunction(Column $Column)
    {
        //第四個佔位符是type
        $type = self::fieldTypeMapping($Column->getType()->getName());
        //第二個佔位符是 type 的 型別 ":"
        $type_sign = ($type === '') ? '' : ':';
        //第三個佔位符是 nullable
        $nullable = (!$Column->getNotnull() && $type !=='' ) ? '?' : '';
        //第五個佔位符是加 Single quote 的field_name

        return "\t".sprintf('public function get%s()%s%s%s{ return $this->getAttribute(%s);}',
            ucfirst(Str::camel($Column->getName())), $type_sign, $nullable, $type, "'" . $Column->getName() . "'");
    }

    /**
     * @Author  : Shou
     * @DateTime:2022/7/26 6:24 下午
     */
    public static function fieldTypeMapping($column_type)
    {
        $php_data_type = '';
        // 先判斷 整數/字串/ Carbon
        if (in_array($column_type, ModelCreatorSupport::getIntTypeList())) {
            // 整數判斷type 包含 int
            $php_data_type = 'int';
        } elseif (in_array($column_type, ModelCreatorSupport::getStringTypeList())) {
            // 字串判 char/varchar/text/enum...
            $php_data_type = 'string';
        } elseif (in_array($column_type, ModelCreatorSupport::getDateTimeTypeList())) {
            // 時間類別
            $php_data_type = 'Carbon';
        }
        return $php_data_type;
    }

    /**
     * @param $table_name
     *
     * @return mixed|string
     * @Author  : Shou
     * @DateTime:2022/9/10 11:26 下午
     */
    public static function getTableGroupName($table_name)
    {
        return ucfirst(Str::plural(explode('_', $table_name)[0]));
    }

    public static function getEntityName($table_name)
    {
        return ucfirst(Str::camel(Str::singular($table_name))) . 'Entity';
    }

    public static function getExtendFrom($table_name)
    {
        /*TODO  step.2 請設定好自己專案規則 model 從哪一個 class 下繼承 */
        switch (true) {
            case in_array($table_name, ['members', 'managers', 'iams', 'admins']): // 會員,管理員
                return 'User';
            default:
                return 'Model';
        }
    }

    public static function getUsePath($use_name)
    {
        /*TODO step.3 請設定好所有會引入的相關檔案路徑*/
        switch ($use_name) {
            case 'Model':
            case 'SoftDeletes':
                return 'Illuminate\Database\Eloquent';
            case 'User':
                return 'Illuminate\Foundation\Auth\User';
            case 'Carbon':
                return 'Carbon';
            default:
                return '';
        }
    }
    public static function getIndent()
    {
        return '    ';
    }

}
