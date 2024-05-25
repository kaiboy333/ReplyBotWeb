# ReplyBot API
アクセストークンをCRAD操作するためのAPIです。
## slack.getAllTokens
### URL
```
<URL>/slack.getAllTokens
```
### 説明
DBに登録したすべてのアクセストークンを表示します
### パラメーター
- password
    - 個人的にしか使わないので秘密です

## slack.addToken
### URL
```
<URL>/slack.addToken
```
### 説明
DBにアクセストークンを追加します
### パラメーター
- password
    - 個人的にしか使わないので秘密です
- user_id
    - SlackのユーザーID
- access_token
    - アクセストークン

## slack.getToken
### URL
```
<URL>/slack.getToken
```
### 説明
DBからアクセストークンを取得します
### パラメーター
- password
    - 個人的にしか使わないので秘密です
- user_id
    - SlackのユーザーID

## slack.updateToken
### URL
```
<URL>/slack.updateToken
```
### 説明
DBのアクセストークンを更新します
### パラメーター
- password
    - 個人的にしか使わないので秘密です
- user_id
    - SlackのユーザーID
- access_token
    - 上書きするアクセストークン

## slack.removeToken
### URL
```
<URL>/slack.removeToken
```
### 説明
DBのアクセストークンを削除します
### パラメーター
- password
    - 個人的にしか使わないので秘密です
- user_id
    - SlackのユーザーID