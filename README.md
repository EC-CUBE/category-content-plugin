# カテゴリコンテンツプラグイン

[![Build Status](https://travis-ci.org/EC-CUBE/category-content-plugin.svg?branch=master)](https://travis-ci.org/EC-CUBE/category-content-plugin)
[![Build status](https://ci.appveyor.com/api/projects/status/t2xabdv25eal0l3f?svg=true)](https://ci.appveyor.com/project/ECCUBE/category-content-plugin)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/70da7277-1540-46a5-9990-e86ca0aaa85d/mini.png)](https://insight.sensiolabs.com/projects/70da7277-1540-46a5-9990-e86ca0aaa85d)
[![Coverage Status](https://coveralls.io/repos/github/EC-CUBE/category-content-plugin/badge.svg?branch=master)](https://coveralls.io/github/EC-CUBE/category-content-plugin?branch=master)

## 概要
商品一覧のカテゴリごとに、コンテンツ（説明文や画像）を表示できるプラグインです。

## フロント
### 商品一覧のカテゴリごとに、コンテンツを表示できる。
- 表示中のカテゴリに登録されたコンテンツを表示する。
- 表示中のカテゴリにコンテンツが登録されていない場合は、何も表示しない。

## 管理画面
### カテゴリごとに、任意のコンテンツを登録することができる。
- カテゴリ登録ページで、コンテンツの登録・編集が可能。
- コンテンツにはHTMLタグが使用可能。

## オプション
### コンテンツの表示位置を変更することができる。
- 商品一覧ページのtwigファイルに`<!--# category-content-plugin-tag #-->`と入力すると、その位置に表示を行う。

