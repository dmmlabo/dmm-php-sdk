## ジャンル検索 API
[戻る](README.md)

[DMM Web API](https://affiliate.dmm.com/api/)を通してDMMの商品ジャンル情報を取得します。

> このAPIはフロアIDが必須です。  
> フロアIDはフロアAPIから取得することができます。

#### フロアIDでジャンルを取得
```php
$actress = $dmm->api('genre')->find(24);
```

#### ジャンルの頭文字を指定して取得
```php
$actress = $dmm->api('genre')->find(10, [
  "initial" => "あ"
]);
```

#### 検索範囲を指定して取得
```php
$actress = $dmm->api('genre')->find(22, [
  "hits" => 10,
  "offset" => 2
]);
```
