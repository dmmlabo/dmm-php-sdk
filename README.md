# DMM SDK for PHP (v3)
[![License](http://img.shields.io/badge/license-mit-blue.svg?style=flat-square)](https://github.com/DMMcomLabo/dmm-php-sdk/blob/master/LICENSE)
[![Build Status](http://img.shields.io/travis/DMMcomLabo/dmm-php-sdk.svg?style=flat-square)](https://travis-ci.org/DMMcomLabo/dmm-php-sdk)
[![Coverage Status](https://img.shields.io/coveralls/DMMcomLabo/dmm-php-sdk.svg?style=flat-square)](https://coveralls.io/github/DMMcomLabo/dmm-php-sdk?branch=master)
[![Packagist](https://img.shields.io/packagist/v/dmmcomlabo/dmm-sdk-v3.svg?style=flat-square)](https://packagist.org/packages/dmmcomlabo/dmm-sdk-v3)

DMM Web API version.3 クライアント for PHP

参照: [DMM Affiliate](https://affiliate.dmm.com/)

## インストール

```sh
composer require dmmcomlabo/dmm-sdk-v3
```

## 使い方
詳細は[Docs](docs)を参照してください

```php
$dmm = new DMM([
  "affiliate_id" => "YOUR-AFFILIATE-ID",
  "api_id"       => "YOUR-API-ID",
]);

// 商品検索APIを使用する場合
$response = $dmm->api("product")->find("DMM.R18");
$result = $response->decodedBody();
print_r($result);
```

## テスト

1. 実行するには[Composer](https://getcomposer.org/) をインストールしておく必要があります。グローバルでインストールし、`composer install`で依存関係にあるライブラリ・ファイルをインストールしてください。
2. `tests/DmmTestCredentials.php` を `tests/DmmTestCredentials.php.dist` を元に作成し、編集してください。このデータは実際にAPIサーバと通信を行なうテストの際に利用されます。  
環境変数で設定することも可能です。DMM_TEST_AFFILIATE_ID、DMM_TEST_API_IDでアフィリエイトIDとAPI IDをしてください。
3. ライブラリのrootディレクトリで以下のコマンドを実行してください。

```bash
$ ./vendor/bin/phpunit
```

ネット接続できない場合や、アフィリエイト登録していない場合は以下のようにしてください。
integrationをテストから除外することで実際にAPIサーバとの通信を行なうテストを除外して実行することができます。

```bash
$ ./vendor/bin/phpunit --exclude-group integration
```


## License
[MIT](LICENSE)
