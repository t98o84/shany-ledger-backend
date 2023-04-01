# shany-ledger-backend

## 各種情報
### API
URL: http://localhost:8000/

### MAIL
URL: http://localhost:8025/

## 構築手順
```shell
cp src/.env.example src/.env
docker composer exec api composer install
docker composer exec api php artisan migrate
docker composer exec api php artisan ide-helper:generate
docker composer exec api php artisan ide-helper:models -N
docker composer exec api php artisan ide-helper:meta
```

TODO:
- メールのレイアウト変更
- メール送信にキューを使用するかしないか検討
- そもそもキューを利用するかしないかを検討（色々大変そうなため）
- aggregation ledger の詳細設定を追加
  - 入力値の上限下限
  - 小数点の有無
  - 合計の上限、下限
  - アラート設定
  - 等
