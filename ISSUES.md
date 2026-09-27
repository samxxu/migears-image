# migears-image — Known Issues / 已知问题

> Generated from the miGears Full-Module Code Review Report (4th round, 2026-09-27).
> This file has two regions. Everything above **Owner feedback** is generated from the report — do
> not edit it there. The **Owner feedback** region belongs to the module maintainer: write into it,
> and it is preserved verbatim when the file is regenerated.
> A `fixed` reply is verified against the code by the reviewer before the finding is closed; a
> `rejected` reply is either accepted as a false positive or answered with counter-evidence.
>
> 本文件分两个区域。**「负责人反馈」之前的全部内容**由评审报告生成，请勿在该区修改；
> **「负责人反馈」区**归模块负责人所有，重新生成时会原样保留。
> 标注 `fixed`（已修复）的回复会被评审对照代码核实后才关闭；标注 `rejected`（不认同）的，
> 评审要么采纳为误报，要么给出反驳证据。
>
> 摘自 miGears 全模块代码评审报告（第四轮，2026-09-27）。

| | |
|---|---|
| Status / 状态 | **P0 cleared / P0 已清零** |
| Findings / 问题 | P0 0 · P1 0 · P2 0 · P3 1 |
| Size / 体量 | src 612 lines (412 net) · 98 tests · 5 src files |

Legend / 图例 — **P0** functional or security · **P1** documentation that fails when copied · **P2** robustness · **P3** metadata and docs
级别说明 — **P0** 功能性或安全级 · **P1** 文档照抄即错 · **P2** 健壮性 · **P3** 元数据与文档

## Verdict / 结论

The best-documented module in this batch: the README now matches the implementation on every claim I checked, and the failure paths uniformly throw ImageException. Only cold-path return values remain unchecked.

本批文档与实现最一致的模块：我核对的每一条 README 承诺都与实现相符，失败路径统一抛 ImageException。仅剩冷门路径的返回值未检查。

## Fixed since the last round / 本轮已修复确认

上一轮全部 4 项修复：GD 返回值系统性补检查（resample/copy/merge/ttftext/createTrueColor 均有守卫与用例）、output() 改为记录缓冲层级 + try/finally、destroy() 的 no-op 契约写进 README 与接口、行数声明改为「几百行以内」并新增 VERSION 与 composer/徽章的一致性测试。 

## Open findings / 未修问题


### P3

**P3-1** — `src/GDImage.php:206-212,155-156,315-316`

- EN: `imagecolorallocatealpha()` returns `int|false` and is unchecked; a false under strict_types becomes a TypeError in `imagettftext()` rather than the `ImageException` the README promises. `imagealphablending`/`imagesavealpha` are likewise unchecked but effectively cannot fail on a real \GdImage.
- 中文: imagecolorallocatealpha() 返回 int|false 且未检查；strict_types 下 false 会在 imagettftext() 里变成 TypeError，而不是 README 承诺的 ImageException。imagealphablending/imagesavealpha 同样未检查，但对真实 \GdImage 基本不可能失败。
- Verification / 验证: static / 仅静态推断

## Test gaps / 测试盲区

No failure-path test for `imagecolorallocatealpha` (hard to construct, hence accepted); the strict-flag set in this module is partial (see the cross-module chapter).

imagecolorallocatealpha 的失败路径无用例（难以构造，可接受）；本模块的严格开关不完整（见跨模块章）。

## Verification protocol / 验证方式

- `./vendor/bin/phpunit` · `composer analyse` · `composer validate`
- Warning/notice/deprecation/risky flags in `phpunit.xml.dist`: all four on
- A PHP warning counts as a test failure only where those flags are on; otherwise run `./vendor/bin/phpunit --fail-on-warning` explicitly.
- 只有在上述开关打开时 PHP 警告才会导致套件失败；否则请显式加 `--fail-on-warning`。

## Owner feedback / 负责人反馈

<!-- OWNER-FEEDBACK:BEGIN -->
<!-- 渠道说明 / channel notice — 跨模块协调人发布，长期有效 / issued by the cross-module coordinator, standing
     ISSUES.md 是本模块「完整」的问题讨论与修复渠道，不只是评审结论的存放处。
     ISSUES.md is this module's COMPLETE issue-discussion-and-fix channel, not merely where review verdicts land.

     1. 每位负责人只对自己模块负责。对别的模块有意见、疑问、反证或改动建议，写入「对方模块」的 ISSUES.md，
        不要写在自己模块里。
        Each owner is responsible for their own module only. Opinions, questions, counter-evidence and
        change requests about ANOTHER module go into THAT module's ISSUES.md, never into your own.
     2. 在对方模块的文件里注明你是谁：模块名 + 身份。署名是硬要求，不署名则无法追溯来源。
        Sign it in the other module's file: your module name and your role. Signing is mandatory; an
        unsigned entry cannot be traced back to its author.
     3. 署名格式 / signature forms, so the source is distinguishable:
          reviewer — migears-full-review   评审方
          coordinator — cross-module       跨模块协调人
          owner — migears-<module>         其他模块负责人
     4. 结论文本一律带状态词：accepted / fixed / rejected / deferred / question / new-evidence。
        无署名条目下一轮可能被按新发现重新评级。
        Sign conclusions with one status word: accepted / fixed / rejected / deferred / question /
        new-evidence. An unsigned entry may be re-graded as a new finding in the next round.
     5. 开工之前先通读本文件：把每条开启条目按证据评估（签名条目也算），再把你接受的条目与自己的工作一并执行，
        不要拆成两轮。每条都要有状态词。
        Read this file before starting work: evaluate every open item on its evidence, signed entries
        included, then execute the ones you accept together with your own work in one pass. Every item
        gets a status word. -->

<!-- Maintainers: reply under each finding's `### <id>` heading and keep the headings, so the
     reviewer can map your reply to the finding. Status vocabulary, one word followed by your
     reasoning and any evidence:
       accepted      you agree; it will be fixed
       fixed         you believe it is already fixed in the code (the reviewer verifies this)
       rejected      you disagree — give the reason; the reviewer either accepts it as a false
                     positive or answers with counter-evidence
       deferred      deliberate, out of scope for now — give the reason
       question      you need a decision or clarification first
       new-evidence  you have additional facts bearing on the finding
     You may also add findings of your own under `### New — <short title>`.

     负责人：请在对应 `### <编号>` 标题下逐条回复，并保留标题以便评审对应。
     状态词（一个词 + 理由与证据）：
       accepted      认同，将会修复
       fixed         认为代码里已经修好（评审会对照代码核实）
       rejected      不认同——请给理由；评审要么采纳为误报，要么给出反驳证据
       deferred      有意暂缓或超出范围——请给理由
       question      需要先明确或决策
       new-evidence  补充与本次结论相关的新事实
     也欢迎在 `### New — <简短标题>` 下补充你发现的问题。 -->

### P3-1
<!-- 负责人反馈 / owner response here -->
<!-- 跨模块条目 / cross-module items — 由跨模块协调人提出，非本轮评审 finding。口径见工作区根目录 `migears-engineering-gates.md`。
      Filed by the cross-module coordinator, not by the round's review. Standard: `migears-engineering-gates.md` at the workspace root. -->

### G3

- EN: Skip guard: `tests/ImageTestCase.php` skips when GD is absent, and `tests/GDImageTest.php` also skips on WebP support and on "No usable font file found"; the workflow installs no `extensions:` and no fonts. A run in which those tests skip still exits 0, so the half can report green while running nothing — the failure mode `migears-cache`'s workflow names in a comment. Standard: put `--fail-on-skipped` on the CI command line rather than in `phpunit.xml.dist`, so a maintainer running the suite as root locally does not get spurious failures, or install the dependency so that nothing skips. The guarantee is what is missing, not necessarily the coverage: whether the runner happens to have gd, WebP and a font today is a property of the runner image, not of this repository. Either install what the tests need, or make the skip fail so a runner-image change shows up as a red build.
- 中文: 跳过守卫：缺少 GD 时 `tests/ImageTestCase.php` 会跳过，`tests/GDImageTest.php` 还会因 WebP 支持缺失和「找不到可用字体文件」跳过；而工作流没有装 `extensions:`，也没有装字体。这些用例被跳过时整次运行仍然退出 0，也就是那半个套件可以在什么都没跑的情况下报绿——正是 `migears-cache` 工作流注释里点名的失效模式。标准做法是把 `--fail-on-skipped` 放在 CI 命令行上而不是 `phpunit.xml.dist` 里，这样本地以 root 跑套件的人不会看到假失败；或者把依赖装上，让没有用例会跳过。缺的是「保证」，不一定是覆盖：runner 今天恰好有没有 gd、WebP 和字体，是 runner 镜像的属性，不是本仓库的属性。要么把用例需要的东西装上，要么让跳过直接失败，这样镜像一变就会以红灯暴露出来。
- Reply with one status word (`accepted` / `fixed` / `rejected` / `deferred` / `question`). / 请回复一个状态词（`accepted` / `fixed` / `rejected` / `deferred` / `question`）。
coordinator — cross-module

- **fixed** — I installed the dependencies AND put the flag on the CI command line, so both halves of
  the guarantee are present: the suite has nothing to skip, and a skip that still happens fails the
  build.
  - `.github/workflows/tests.yml`: setup-php now sets `extensions: gd` (GD with WebP; `ImageTestCase`
    skips without it), a new `Install fonts` step runs `apt-get install -y fonts-dejavu-core` so
    `findFontFile()` resolves `/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf`, and the `Tests` step
    runs `vendor/bin/phpunit --fail-on-skipped`. `phpunit.xml.dist` is untouched, so a maintainer
    running `phpunit` locally does not get spurious failures.
  - Exit codes verified both ways (PHP 8.5.10, GD + WebP + a TrueType font all present locally):
    - dependencies present: `./vendor/bin/phpunit --fail-on-skipped` → `OK (99 tests, 209 assertions)`,
      exit 0.
    - a dependency missing (WebP absent, simulated with `php -d disable_functions=imagewebp
      vendor/bin/phpunit --fail-on-skipped`, so `function_exists('imagewebp')` is false exactly as on a
      GD built without libwebp) → `Skipped: 3`, exit 1.
  - `./vendor/bin/phpstan analyse --no-progress` → `[OK] No errors`, exit 0.
  - Completes the promise with a second safety net: before, the three capabilities (GD, WebP, a font)
    were only assumed of the runner image; now a runner-image change that drops one shows up as a red
    build instead of a green build that ran nothing.

  owner — migears-image

<!-- OWNER-FEEDBACK:END -->
