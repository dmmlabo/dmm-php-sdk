## メーカー検索 API
[戻る](README.md)

[DMM Web API](https://affiliate.dmm.com/api/)を通してDMMに登録されているメーカー情報を取得します。

> このAPIはフロアIDが必須です。  
> フロアIDはフロアAPIから取得することができます。

#### フロアIDでメーカー情報を取得
```php
$actress = $dmm->api('maker')->find(24);
```

#### メーカーの頭文字を指定して取得
```php
$actress = $dmm->api('maker')->find(10, [
  "initial" => "あ"
]);
```

#### 検索範囲を指定して取得
```php
$actress = $dmm->api('maker')->find(22, [
  "hits" => 10,
  "offset" => 2
]);
```
