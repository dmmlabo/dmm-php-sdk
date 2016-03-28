## 女優検索 API
[戻る](README.md)

[DMM Web API](https://affiliate.dmm.com/api/)を通して女優の情報を検索します。

#### 無条件で女優情報を取得
```php
$actress = $dmm->api('actress')->find();
```

#### キーワードと頭文字を条件に取得
```php
$actress = $dmm->api('actress')->find([
  "keyword" => "麻美",
  "initial" => "あ"
]);
```

#### 検索範囲を指定して取得
```php
$actress = $dmm->api('actress')->find([
  "hits" => 10,
  "offset" => 2
]);
```
