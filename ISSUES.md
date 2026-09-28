# migears-image — Known Issues

> Summary of this module's issues. The items themselves are in [`issues/`](issues/README.md), one file
> per item: a front-matter header and a thread. This file is generated from them and can be rewritten at
> any time; edit an item, never this file.
>
> From the miGears Full-Module Code Review Report (5th round, 2026-09-28).

| | |
|---|---|
| Status | **P1 open** |
| Size | src 414 lines (net) · 99 tests · 5 src files |

Legend — **P0** functional or security · **P1** documentation that fails when copied · **P2** robustness · **P3** metadata and docs

## At a glance

| | |
|---|---|
| Unsettled | P0 0 · P1 0 · P2 0 · P3 1 · other 1 |
| Settled | 0 of 2 |
| Waiting on the owner | `P3-1` |
| Waiting on the reviewer | `G3` |
| Waiting on the coordinator | _nothing_ |
| Deferred, owing nobody | _nothing_ |

| id | level | status | title |
|---|---|---|---|
| [`P3-1`](issues/P3-1.md) | P3 | **open** | `imagecolorallocatealpha()` returns `int|false` and is unchecked; a … |
| [`G3`](issues/G3.md) | - | **fixed** | Skip guard: `tests/ImageTestCase.php` skips when GD is absent, and … |

## Unclosed

What is left to do here: every item whose `status` is not `verified` or `closed`,
highest severity first. `waiting on` is the party who acts next, read from that status.

| | |
|---|---|
| Unclosed | **2** of 2 |
| By status | `open` 1 · `fixed` 1 |
| Waiting on | owner 1 · reviewer 1 |

| level | item | status | waiting on | title |
|---|---|---|---|---|
| **P3** | [`P3-1`](issues/P3-1.md) | `open` | owner | `imagecolorallocatealpha()` returns `int\|false` and is unchecked; a … |
| **-** | [`G3`](issues/G3.md) | `fixed` | reviewer | Skip guard: `tests/ImageTestCase.php` skips when GD is absent, and … |

## Verdict

A well-documented GD-based image manipulation library with thorough test coverage; save() returns false on GD output failure instead of throwing ImageException — directly contradicting the README's "all failures throw" promise.

## Fixed since the last round

G3 CI gate confirmed working (GD+WebP + DejaVu + --fail-on-skipped); P3-1 palette-exhaustion path for textWatermark confirmed fixed.

## Test gaps

No test for save() returning false on actual write failure (disk full / permission denied); no test for rotate failure path (imagerotate returning false); no test for imagecreatefrom* failure after type detection passes.

## Verification protocol

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- Warning/notice/deprecation/risky flags in `phpunit.xml.dist`: all four on
- A PHP warning counts as a test failure only where those flags are on; otherwise run `./vendor/bin/phpunit --fail-on-warning` explicitly.


---

# migears-image — 已知问题

> 本模块问题的概览。条目本体在 [`issues/`](issues/README.md)，一条目一文件：前置字段加讨论串。
> 本文件由条目生成，随时可以整段重写；请改条目，不要改本文件。
>
> 出自 miGears 全模块代码评审报告（5th round，2026-09-28）。

| | |
|---|---|
| 状态 | **P1 待修** |
| 体量 | src 414 行（净）· 99 个用例 · 5 个源文件 |

级别说明 — **P0** 功能性或安全级 · **P1** 文档照抄即错 · **P2** 健壮性 · **P3** 元数据与文档

## 状态一览

| | |
|---|---|
| 未了结 | P0 0 · P1 0 · P2 0 · P3 1 · 其他 1 |
| 已了结 | 0 / 2 |
| 等负责人 | `P3-1` |
| 等评审方 | `G3` |
| 等协调人 | _无_ |
| 已暂缓，不欠谁 | _无_ |

| id | 级别 | 状态 | 标题 |
|---|---|---|---|
| [`P3-1`](issues/P3-1.md) | P3 | **open** | imagecolorallocatealpha() 返回 int|false 且未检查；strict_types 下 false 会在 … |
| [`G3`](issues/G3.md) | - | **fixed** | 跳过守卫：缺少 GD 时 `tests/ImageTestCase.php` 会跳过，`tests/GDImageTest.php` 还会因 … |

## 未关闭

本模块还剩什么要做：所有 `status` 不是 `verified` 或 `closed` 的条目，按严重度从高到低。
`waiting on` 是下一步该动手的一方，由其状态读出。

| | |
|---|---|
| 未关闭 | **2** / 2 |
| 按状态 | `open` 1 · `fixed` 1 |
| 等在谁 | 负责人 1 · 评审方 1 |

| 级别 | 条目 | 状态 | 等在谁 | 标题 |
|---|---|---|---|---|
| **P3** | [`P3-1`](issues/P3-1.md) | `open` | 负责人 | imagecolorallocatealpha() 返回 int\|false 且未检查；strict_types 下 false 会在 … |
| **-** | [`G3`](issues/G3.md) | `fixed` | 评审方 | 跳过守卫：缺少 GD 时 `tests/ImageTestCase.php` 会跳过，`tests/GDImageTest.php` 还会因 … |

## 结论

一个文档完善、基于 GD 的图片处理库，测试覆盖全面；save() 在 GD 输出失败时返回 false 而非抛出 ImageException——直接违背 README「所有失败均抛异常」的承诺。

## 本轮已修复确认

G3 CI gate confirmed working (GD+WebP + DejaVu + --fail-on-skipped); P3-1 palette-exhaustion path for textWatermark confirmed fixed.

## 测试盲区

无 save() 在实际写入失败（磁盘满/权限不足）时返回 false 的测试；无 rotate 失败路径测试（imagerotate 返回 false）；无类型检测通过后 createfrom 失败的测试。

## 验证方式

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- `phpunit.xml.dist` 中的 warning/notice/deprecation/risky 开关：四个全开
- 只有在上述开关打开时 PHP 警告才会导致套件失败；否则请显式加 `--fail-on-warning`。
