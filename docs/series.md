## シリーズ検索 API
[戻る](README.md)

[DMM Web API](https://affiliate.dmm.com/api/)を通してDMMの商品シリーズ情報を取得します。

> このAPIはフロアIDが必須です。  
> フロアIDはフロアAPIから取得することができます。

#### フロアIDでシリーズを取得
```php
$actress = $dmm->api('series')->find(24);
```

#### シリーズの頭文字を指定して取得
```php
$actress = $dmm->api('series')->find(10, [
  "initial" => "あ"
]);
```

#### 検索範囲を指定して取得
```php
$actress = $dmm->api('series')->find(22, [
  "hits" => 10,
  "offset" => 2
]);
```
