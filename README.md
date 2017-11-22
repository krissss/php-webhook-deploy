# php 通过 webhook 部署更新项目

> 当前针对 coding 和 linux 系统，可以自己扩展其他平台和系统

## 使用

1. 将 `deploy.php` 复制到项目下可通过外网访问的目录下。

1. 修改其中的需要修改的地方，比如 `define` 的配置参数，和 `git pull` 完成后的后续脚本

1. 提交代码

1. clone 代码到服务器。请使用 `git clone https://username:password@git.coding.net/xxx/xxx.git` 的方式免密码 clone 代码，防止之后的更新需要密码的问题，不想使用此方法的可以使用 SSH-KEY 部署的方式免密码（此处略）

1. 配置 Coding （或其他平台）的 `webhook`

1. 修改本地代码后提交，检查是否有产生日志和日志信息。
