## 作者検索 API
[戻る](README.md)

[DMM Web API](https://affiliate.dmm.com/api/)を通してDMMに登録されている作者情報を取得します。

> このAPIはフロアIDが必須です。  
> フロアIDはフロアAPIから取得することができます。

#### フロアIDで作者情報を取得
```php
$actress = $dmm->api('author')->find(24);
```

#### 作者の頭文字を指定して取得
```php
$actress = $dmm->api('author')->find(10, [
  "initial" => "あ"
]);
```

#### 検索範囲を指定して取得
```php
$actress = $dmm->api('author')->find(22, [
  "hits" => 10,
  "offset" => 2
]);
```
