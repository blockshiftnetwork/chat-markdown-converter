# Project Status: Q2 2026

## Overview

We just shipped the **payments rewrite** and *cut p99 latency* by ==58%==. ***Huge win!*** Backend codebase down ~~12,500~~ to 9,800 LOC.

## Highlights

- Migrated to event-sourced ledger
- Replaced `legacy/billing.php` with stateless service
- 99.97% uptime over the rolling 30 days

## Tasks for next sprint

- [x] Land observability dashboards
- [x] Cut over EU region
- [ ] Backfill Q1 reconciliations
- [ ] Author postmortem for the March incident

## Performance

| Metric | Before | After |
| --- | --- | --- |
| p50 | 180ms | 42ms |
| p99 | 2400ms | 1010ms |

## Code sample

```php
function settle(Invoice $invoice): Receipt
{
    $ledger->record($invoice);
    return Receipt::for($invoice);
}
```

> Reliability is not a feature — it's a *prerequisite*.

---

Read the full report at [the wiki](https://wiki.example.com/q2-2026) and see the dashboard ![dashboard](https://img.example.com/dash.png).
