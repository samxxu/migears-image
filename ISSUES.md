# migears-image — Known Issues / 已知问题

> Summary of this module's issues. The items themselves are in [`issues/`](issues/README.md), one file
> per item: a front-matter header and a thread. This file is generated from them and can be rewritten at
> any time; edit an item, never this file.
>
> 本模块问题的概览。条目本体在 [`issues/`](issues/README.md)，一条目一文件：前置字段加讨论串。
> 本文件由条目生成，随时可以整段重写；请改条目，不要改本文件。
>
> From the miGears Full-Module Code Review Report (4th round, 2026-09-27).

| | |
|---|---|
| Status / 状态 | **P0 cleared / P0 已清零** |
| Size / 体量 | src 612 lines (412 net) · 98 tests · 5 src files |

Legend / 图例 — **P0** functional or security · **P1** documentation that fails when copied · **P2** robustness · **P3** metadata and docs
级别说明 — **P0** 功能性或安全级 · **P1** 文档照抄即错 · **P2** 健壮性 · **P3** 元数据与文档

## At a glance / 状态一览

| | |
|---|---|
| Items / 条目 | P0 0 · P1 0 · P2 0 · P3 1 · other 1 |
| Answered / 已回复 | 1 of 2 |
| Waiting / 等待回复 | `P3-1` |

| id | level | status | title |
|---|---|---|---|
| [`P3-1`](issues/P3-1.md) | P3 | **open** | `imagecolorallocatealpha()` returns `int|false` and is unchecked; a … |
| [`G3`](issues/G3.md) | - | **fixed** | Skip guard: `tests/ImageTestCase.php` skips when GD is absent, and … |

## Verdict / 结论

The best-documented module in this batch: the README now matches the implementation on every claim I checked, and the failure paths uniformly throw ImageException. Only cold-path return values remain unchecked.

本批文档与实现最一致的模块：我核对的每一条 README 承诺都与实现相符，失败路径统一抛 ImageException。仅剩冷门路径的返回值未检查。

## Fixed since the last round / 本轮已修复确认

上一轮全部 4 项修复：GD 返回值系统性补检查（resample/copy/merge/ttftext/createTrueColor 均有守卫与用例）、output() 改为记录缓冲层级 + try/finally、destroy() 的 no-op 契约写进 README 与接口、行数声明改为「几百行以内」并新增 VERSION 与 composer/徽章的一致性测试。 

## Test gaps / 测试盲区

No failure-path test for `imagecolorallocatealpha` (hard to construct, hence accepted); the strict-flag set in this module is partial (see the cross-module chapter).

imagecolorallocatealpha 的失败路径无用例（难以构造，可接受）；本模块的严格开关不完整（见跨模块章）。

## Verification protocol / 验证方式

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- Warning/notice/deprecation/risky flags in `phpunit.xml.dist`: all four on
- A PHP warning counts as a test failure only where those flags are on; otherwise run `./vendor/bin/phpunit --fail-on-warning` explicitly.
- 只有在上述开关打开时 PHP 警告才会导致套件失败；否则请显式加 `--fail-on-warning`。
