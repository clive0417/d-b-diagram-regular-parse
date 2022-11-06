# D B Diagram Regular Parse

[![GitHub Workflow Status](https://github.com/clive0417/d-b-diagram-regular-parse/workflows/Run%20tests/badge.svg)](https://github.com/clive0417/d-b-diagram-regular-parse/actions)
[![styleci](https://styleci.io/repos/CHANGEME/shield)](https://styleci.io/repos/CHANGEME)

[![Packagist](https://img.shields.io/packagist/v/clive0417/d-b-diagram-regular-parse.svg)](https://packagist.org/packages/clive0417/d-b-diagram-regular-parse)
[![Packagist](https://poser.pugx.org/clive0417/d-b-diagram-regular-parse/d/total.svg)](https://packagist.org/packages/clive0417/d-b-diagram-regular-parse)
[![Packagist](https://img.shields.io/packagist/l/clive0417/d-b-diagram-regular-parse.svg)](https://packagist.org/packages/clive0417/d-b-diagram-regular-parse)

Package description: CHANGE ME

## Installation

Install via composer
```bash
composer require clive0417/d-b-diagram-regular-parse
```

### Publish package assets

```bash
php artisan vendor:publish --provider="Clive0417\DBDiagramRegularParse\ServiceProvider"
```

## Usage

### 使用方法請參考影片
https://youtu.be/4fopuupr1Mk


### step1 安裝套件
```bash
composer require clive0417/d-b-diagram-regular-parse
```

### step2 複製 DB Diagram 並存檔
```text
//商家
table merchants [headercolor: #16a085]
{
  id                            bigint(20)          [pk, increment, note: '流水號']
  uuid                          char(36)            [unique, note: 'uuid']
  name                          varchar(250)        [null, note:'名稱']
  company_tax_id_number         varchar(10)         [note:'統編']
  status                        smallint(5)         [default: 201, note: '狀態']
  deleted_at                    timestamp
  created_at                    timestamp
  updated_at                    timestamp

  indexes{
    uuid                                [unique, note: 'uuid索引']
    company_tax_id_number               [note: '統編索引']
    (status, deleted_at)                [note: '狀態索引']
  }
}
....
```

### step3 執行 解析 DB Diagram 產生 migration 檔指令
```bash
php artisan clive0417:migration_generate --db_diagram_path="{{DB Diagram 檔案路徑}}"
```

### step4 執行 laravel migrate 指令，建立DB table
```bash
php artisan migrate
```
### step5 執行讀取DB 產生 model 檔指令
```bash
php artisan clive0417:model_generate
```




## Security

If you discover any security related issues, please email
instead of using the issue tracker.

## Credits

- [](https://github.com/clive0417/d-b-diagram-regular-parse)
- [All contributors](https://github.com/clive0417/d-b-diagram-regular-parse/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
