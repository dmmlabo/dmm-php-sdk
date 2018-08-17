## 商品検索 API
[戻る](README.md)

[DMM Web API](https://affiliate.dmm.com/api/)を通して商品情報を取得します。

> このAPIはサイト種別（DMM.com or FANZA）が必須です。  

#### サイト種別で商品情報を取得
```php
$actress = $dmm->api('product')->find("DMM.com");
```

#### サービスとフロア名を条件に取得
```php
$actress = $dmm->api('product')->find("FANZA", [
  "service" => "digital",
  "floor" => "videoa"
]);
```

#### 検索範囲を指定して取得
```php
$actress = $dmm->api('product')->find("FANZA",[
  "hits" => 10,
  "offset" => 2
]);
```
