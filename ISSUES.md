# migears-image — Known Issues

> Summary of this module's issues. The items themselves are in [`issues/`](issues/README.md), one file
> per item: a front-matter header and a thread. This file is generated from them and can be rewritten at
> any time; edit an item, never this file.
>
> From the miGears Full-Module Code Review Report (6th round, 2026-10-01).

| | |
|---|---|
| Status | **P2 open** |
| Size | src 418 lines (net) · 105 tests · 5 src files |

Legend — **P0** functional or security · **P1** documentation that fails when copied · **P2** robustness · **P3** metadata and docs

## At a glance

| | |
|---|---|
| Unsettled | P0 0 · P1 0 · P2 1 · P3 0 · other 0 |
| Settled | 5 of 6 |
| Waiting on the owner | _nothing_ |
| Waiting on the coordinator | _nothing_ |
| Waiting on the reviewer | `P2-2` |
| Deferred, owing nobody | _nothing_ |

| id | level | status | title |
|---|---|---|---|
| [`P1-1`](issues/P1-1.md) | P1 | **verified** | `save()` returns `false` on a write failure and leaks a raw PHP … |
| [`P2-1`](issues/P2-1.md) | P2 | **verified** | A transparent PNG loses its alpha channel on a plain load-then-save, … |
| [`P2-2`](issues/P2-2.md) | P2 | **fixed** | save() suppresses the raw warning on the write but not on the mkdir … |
| [`P3-1`](issues/P3-1.md) | P3 | **verified** | `imagecolorallocatealpha()` returns `int|false` and is unchecked; a … |
| [`P3-2`](issues/P3-2.md) | P3 | **verified** | info() appears twice in the README API overview table — once under … |
| [`G3`](issues/G3.md) | - | **verified** | Skip guard: `tests/ImageTestCase.php` skips when GD is absent, and … |

## Unclosed

What is left to do here: every item whose `status` is not `verified` or `closed`,
highest severity first. `waiting on` is the party who acts next, read from that status.

| | |
|---|---|
| Unclosed | **1** of 6 |
| By status | `fixed` 1 |
| Waiting on | reviewer 1 |

| level | item | status | waiting on | title |
|---|---|---|---|---|
| **P2** | [`P2-2`](issues/P2-2.md) | `fixed` | reviewer | save() suppresses the raw warning on the write but not on the mkdir … |

## Verdict

The ruling that save() keeps returning a bool is carried out honestly, and the alpha-channel work from the audit is in place; the same warning discipline was not extended to the directory creation.

## Fixed since the last round

P1-1 verified (the save() bool is now an explicit exception in three places per README half, with the raw warning suppressed) and P3-2 verified (info() is listed once per half). Removing the @ or duplicating the row turns the module’s own test red.

## Test gaps

The write-failure path and the README line-count claim both have dedicated tests, but directory-creation failure has none — which is why the warning below escaped.

## Verification protocol

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- Warning/notice/deprecation/risky flags in `phpunit.xml.dist`: all four on
- A PHP warning counts as a test failure only where those flags are on; otherwise run `./vendor/bin/phpunit --fail-on-warning` explicitly.


---

# migears-image — 已知问题

> 本模块问题的概览。条目本体在 [`issues/`](issues/README.md)，一条目一文件：前置字段加讨论串。
> 本文件由条目生成，随时可以整段重写；请改条目，不要改本文件。
>
> 出自 miGears 全模块代码评审报告（6th round，2026-10-01）。

| | |
|---|---|
| 状态 | **P2 待修** |
| 体量 | src 418 行（净）· 105 个用例 · 5 个源文件 |

级别说明 — **P0** 功能性或安全级 · **P1** 文档照抄即错 · **P2** 健壮性 · **P3** 元数据与文档

## 状态一览

| | |
|---|---|
| 未了结 | P0 0 · P1 0 · P2 1 · P3 0 · 其他 0 |
| 已了结 | 5 / 6 |
| 等模块主 | _无_ |
| 等协调人 | _无_ |
| 等评审方 | `P2-2` |
| 已暂缓，不欠谁 | _无_ |

| id | 级别 | 状态 | 标题 |
|---|---|---|---|
| [`P1-1`](issues/P1-1.md) | P1 | **verified** | `save()` 在写入失败时返回 `false` 并漏出原始 PHP 警告，而 `README.md` 承诺所有失败都抛 … |
| [`P2-1`](issues/P2-1.md) | P2 | **verified** | 透明 PNG 在「载入再保存」时会丢掉 alpha 通道，两种水印下也一样，因为 `imagecreatefrompng()` 不打开 … |
| [`P2-2`](issues/P2-2.md) | P2 | **fixed** | save() 对写入抑制了原始警告，但对之前的 mkdir 没有：父路径无法创建时，调用方先看到原始 PHP 警告、之后才拿到文档承诺的 … |
| [`P3-1`](issues/P3-1.md) | P3 | **verified** | imagecolorallocatealpha() 返回 int|false 且未检查；strict_types 下 false 会在 … |
| [`P3-2`](issues/P3-2.md) | P3 | **verified** | README API 概览表格中 info() 出现两次——一次在「信息」类，一次在「其他」类——文档冗余。 |
| [`G3`](issues/G3.md) | - | **verified** | 跳过守卫：缺少 GD 时 `tests/ImageTestCase.php` 会跳过，`tests/GDImageTest.php` 还会因 … |

## 未关闭

本模块还剩什么要做：所有 `status` 不是 `verified` 或 `closed` 的条目，按严重度从高到低。
`waiting on` 是下一步该动手的一方，由其状态读出。

| | |
|---|---|
| 未关闭 | **1** / 6 |
| 按状态 | `fixed` 1 |
| 等在谁 | 评审方 1 |

| 级别 | 条目 | 状态 | 等在谁 | 标题 |
|---|---|---|---|---|
| **P2** | [`P2-2`](issues/P2-2.md) | `fixed` | 评审方 | save() 对写入抑制了原始警告，但对之前的 mkdir 没有：父路径无法创建时，调用方先看到原始 PHP 警告、之后才拿到文档承诺的 … |

## 结论

「save() 保留返回 bool」的裁定被如实执行，审计带来的 alpha 通道修复也在位；但同一套警告抑制纪律没有延伸到目录创建。

## 本轮已修复确认

P1-1 verified (the save() bool is now an explicit exception in three places per README half, with the raw warning suppressed) and P3-2 verified (info() is listed once per half). Removing the @ or duplicating the row turns the module’s own test red.

## 测试盲区

写入失败路径与 README 行数声明都有专门用例，但目录创建失败没有——下面那条警告正是因此逃逸。

## 验证方式

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- `phpunit.xml.dist` 中的 warning/notice/deprecation/risky 开关：四个全开
- 只有在上述开关打开时 PHP 警告才会导致套件失败；否则请显式加 `--fail-on-warning`。
