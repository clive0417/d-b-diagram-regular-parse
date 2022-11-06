<?php

namespace Clive0417\DBDiagramRegularParse\Formats\Migrations;

use Illuminate\Support\Str;
use Mpociot\HumanRegex\HumanRegex;

class MigrationCreatorFormat
{
    public static function isTableStart(string $line): bool
    {
        $regex = HumanRegex::create()->find('table')->or('Table')->whitespaces();
        return $regex->matches($line);
    }
    public static function isCommentLine(string $line): bool
    {
        $regex = HumanRegex::create()->find('//');
        return $regex->matches($line);
    }
    public static function getTableNameComment(string $line): string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->find('//');
            })
            ->capture(function () {
                return HumanRegex::create()->anything();
            });

        return $regex->findMatches($line)[2];

    }

    public static function isTableIndexStart(string $line): bool
    {
        $regex = HumanRegex::create()->find('indexes');
        return $regex->matches($line);
    }

    public static function parseTableName(string $line): string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->find('table')->or('Table');
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('[a-zA-Z0-9_\\-]')->multipleTimes();
            });
        return  $regex->findMatches($line)[3];
    }

    public static function parseColumnName(string $line): string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->startOfString()->whitespace();
            })
            ->capture(function () {
                return HumanRegex::create()->add('[a-zA-Z0-9_\\-]')->multipleTimes();
            })
            ->capture(function () {
                return HumanRegex::create()->startOfString()->whitespaces();
            });

        return empty($regex->findMatches($line)) ? '' : $regex->findMatches($line)[2];
    }

    public static function parseColumnType(string $line): string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->startOfString()->whitespace();
            })
            ->capture(function () {
                return HumanRegex::create()->add('[a-zA-Z0-9_\\-]')->multipleTimes();
            })
            ->capture(function () {
                return HumanRegex::create()->startOfString()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->startOfString()->alphanumerics();
            });

        return empty($regex->findMatches($line)) ? '' : $regex->findMatches($line)[4];
    }

    public static function parseColumnLengthOrOption(string $line): string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->startOfString()->whitespace();
            })
            ->capture(function () {
                return HumanRegex::create()->add('[a-zA-Z0-9_\\-]')->multipleTimes();
            })
            ->capture(function () {
                return HumanRegex::create()->startOfString()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->startOfString()->alphanumerics();
            })
            ->capture(function () {
                return HumanRegex::create()->startOfString()->add('\\(');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->multipleTimes();
            })
            ->capture(function () {
                return HumanRegex::create()->startOfString()->add('\\)');
            })
            ->capture(function () {
                return HumanRegex::create()->startOfString()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->startOfString()->add('\\[');
            });
        return empty($regex->findMatches($line)) ? '' : $regex->findMatches($line)[6];
    }

    public static function parseNote(string $line): ?string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('note:');
            })
            ->capture(function () {
                return HumanRegex::create()->add(' ')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\'');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\'');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return empty($regex->findMatches($line)) ? null : $regex->findMatches($line)[6];
    }

    public static function parseColumnNullable(string $line): bool
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('null');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return $regex->matches($line);
    }

    public static function parseColumnDefault(string $line): ?string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('default:');
            })
            ->capture(function () {
                return HumanRegex::create()->add(' ')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\'')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->alphanumerics();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\'')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add(' ')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add(',');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });
        return empty($regex->findMatches($line)) ? null : $regex->findMatches($line)[6];
    }


    public static function parseAutoIncrement(string $line): bool
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('increment');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return $regex->matches($line);
    }

    public static function parseUnique(string $line): bool
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('unique');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return $regex->matches($line);
    }

    public static function parseAllowMinus(string $line): bool
    {
        // 此line 文字包含負數
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('負');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return $regex->matches($line);
    }

    public static function isMultipleIndexKey(string $line): bool
    {
        //(allowance_status, status, deleted_at) [note: '狀態索引']
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\\(');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\\)');
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return $regex->matches($line);
    }

    public static function isSingularIndexKey(string $line): bool
    {

        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('[a-zA-Z0-9_\\-\'\"]')->multipleTimes();
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return $regex->matches($line);
    }

    public static function parseIndexType(string $line): string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->anything();
            })
            ->capture(function () {
                return HumanRegex::create()->add('pk')->or('unique');
            })
            ->capture(function () {
                return HumanRegex::create()->anything();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return empty($regex->findMatches($line)) ? 'index' : $regex->findMatches($line)[3];
    }

    public static function parseMultipleIndexColumnName(string $line): array
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\(');
            })
            ->capture(function () {
                return HumanRegex::create()->anything();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\)');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            });
        $multiple_index_column_name = explode(',',$regex->findMatches($line)[3]);
        $multiple_index_column_name =array_map(function ($column_name){
            return trim($column_name);
        },$multiple_index_column_name);
        return $multiple_index_column_name;

    }

    public static function parseSingularIndexColumnName(string $line): array
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('[a-zA-Z0-9_\\-\'\"]')->multipleTimes();
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });
        return array($regex->findMatches($line)[2]);

    }



    public static function hasRelation(string $line): bool
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('ref:');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return $regex->matches($line);
    }

    public static function isOneToOneRelation(string $line): bool
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('ref:');
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->then('-');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return $regex->matches($line);
    }

    public static function isManyToOneRelation(string $line): bool
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('ref:');
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->then('>');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });

        return $regex->matches($line);
    }


    public static function isTableOrIndexLoopEnd(string $line): bool
    {
        $regex = HumanRegex::create()->find('}');
        return $regex->matches($line);
    }

    public static function isRelationLine(string $line): bool
    {
        $regex = HumanRegex::create()->startOfString()->then('Ref:');
        return $regex->matches($line);
    }

    public static function getRefTableName($line): string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('ref:');
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->then('>')->or('-');
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\.');
            });

        return $regex->findMatches($line)[7];
    }

    public static function getBelongToManyRelationMethodName($main_table_name,$intermediary_table_name): string
    {
        return Str::camel(rtrim(ltrim(Str::replace(Str::singular($main_table_name),'',$intermediary_table_name),'_'),'_'));
    }

    public static function getBelongToRelationMethodName($table_name): string
    {
        return Str::camel(Str::singular($table_name));
    }

    public static function getEntityName($table_name)
    {
        return ucfirst(Str::camel(Str::singular($table_name))) . 'Entity';
    }

    public static function getLocalKey($line): string
    {
        $regex = HumanRegex::create()
            ->capture(function () {
                return HumanRegex::create()->add('\[');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->then('ref:');
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('>')->or('-');
            })
            ->capture(function () {
                return HumanRegex::create()->whitespaces();
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\.');
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add(' ')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('.')->zeroOrMore();
            })
            ->capture(function () {
                return HumanRegex::create()->add('\]');
            });
        return $regex->findMatches($line)[9];
    }
}
