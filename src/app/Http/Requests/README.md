# REQUEST

## Requestクラスの用途
RequestクラスはRequestクラスと同一階層かつ同名（末尾のRequestを除く）の、
Actionsディレクトリ以下のクラスの事前バリデーションとして使用する。

そのため、Controllerに紐づくわけではなくActionクラスに紐づく形になるので、
Actionクラスを実行する場合は必ず事前に対応するリクエストクラスを使用し、
リクエスト内容をバリデーションする必要がある。

そうしないと、Actionクラスも対応するRequestクラスでバリデーションを行なっている前提で実装されているため、
バグの原因になってしまう。
