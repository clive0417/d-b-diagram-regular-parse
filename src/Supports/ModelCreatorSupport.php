<?php
namespace Clive0417\DBDiagramRegularParse\Supports;

use Doctrine\DBAL\Types\Types;

class ModelCreatorSupport
{
    public static function getDateTimeTypeList()
    {
        return [
            Types::DATE_IMMUTABLE,
            Types::DATE_MUTABLE,
            Types::DATEINTERVAL,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIME_MUTABLE,
            Types::DATETIMETZ_IMMUTABLE,
            Types::DATETIMETZ_MUTABLE,
            Types::TIME_IMMUTABLE,
            Types::TIME_MUTABLE,
        ];
    }

    public static function getIntTypeList()
    {
        return [
            Types::BOOLEAN,
            Types::SMALLINT,
            Types::INTEGER,
            Types::BIGINT,
        ];
    }

    public static function getStringTypeList()
    {
        return [
            Types::ASCII_STRING,
            Types::BINARY,
            Types::BLOB,
            Types::STRING,
            Types::TEXT,
        ];
    }

}
