<style>
    .dhead {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .dhead h1 { margin: 0 0 3px; font-size: 22px; letter-spacing: -.5px; color: #0b1f1a; }
    .dhead p { margin: 0; font-size: 12.5px; color: #64748b; }
    .dhead-side { display: flex; align-items: center; gap: 8px; padding-top: 3px; font-size: 11px; color: #94a3b8; }
    .icon-btn {
        display: grid; place-items: center;
        width: 32px; height: 32px;
        border-radius: 9px;
        background: #fff;
        border: 1px solid #e3ece8;
        color: #64748b;
        transition: background .15s ease, color .15s ease, border-color .15s ease;
    }
    .icon-btn:hover { background: #f0fdfa; color: #0f766e; border-color: #b8e6d8; }
    .icon-btn svg { width: 14px; height: 14px; }

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .tabs { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .tab {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 13px;
        border-radius: 9px;
        border: 1px solid #e3ece8;
        background: #fff;
        color: #52616f;
        font-size: 12px;
        font-weight: 600;
        transition: background .15s ease, color .15s ease, border-color .15s ease;
    }
    .tab:hover { color: #0f766e; border-color: #b8e6d8; background: #f7fdfb; }
    .tab .n {
        min-width: 22px;
        padding: 1px 7px;
        border-radius: 999px;
        background: #f1f5f3;
        color: #64748b;
        font-size: 10.5px;
        font-weight: 800;
        text-align: center;
        font-variant-numeric: tabular-nums;
    }
    .tab.active { background: linear-gradient(135deg, #14b8a6, #0f766e); border-color: transparent; color: #fff; box-shadow: 0 4px 12px -6px rgba(15,118,110,.6); }
    .tab.active .n { background: rgba(255,255,255,.24); color: #fff; }

    .seg {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 3px;
        border: 1px solid #e3ece8;
        border-radius: 10px;
        background: #fff;
    }
    .seg a { padding: 5px 11px; border-radius: 7px; font-size: 11.5px; font-weight: 600; color: #52616f; }
    .seg a:hover { color: #0f766e; }
    .seg a.active { background: #e8f7f1; color: #0f766e; }

    .filters {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        background: #fff;
        border: 1px solid #e6eeeb;
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 12px;
    }
    .field { display: flex; flex-direction: column; gap: 4px; }
    .field label { font-size: 10px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; color: #94a3b8; }
    .field input {
        font: inherit;
        font-size: 12px;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid #dbe6e1;
        background: #fff;
        color: #1e293b;
        outline: none;
        min-height: 34px;
    }
    .field input:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.12); }
    .field.grow { flex: 1 1 240px; }
    .field.grow input { width: 100%; }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        background: linear-gradient(135deg, #14b8a6, #0f766e);
        color: #fff;
        min-height: 34px;
    }
    .btn:hover { background: linear-gradient(135deg, #0f766e, #115e59); }
    .btn svg { width: 13px; height: 13px; }
    .btn-reset {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        background: #f7faf9;
        border: 1px solid #e3ece8;
        min-height: 34px;
    }
    .btn-reset:hover { color: #0f766e; border-color: #b8e6d8; }

    .panel {
        background: #fff;
        border: 1px solid #e6eeeb;
        border-radius: 16px;
        box-shadow: 0 1px 2px rgba(15,23,42,.03), 0 16px 30px -30px rgba(15,23,42,.4);
        overflow: hidden;
    }
    .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 13px 16px;
        border-bottom: 1px solid #f1f6f4;
    }
    .panel-head h2 { margin: 0; font-size: 13px; letter-spacing: -.1px; color: #0f172a; }
    .panel-head small { display: block; font-size: 10.5px; color: #94a3b8; margin-top: 1px; }
    .count-pill {
        padding: 3px 10px;
        border-radius: 999px;
        background: #eaf7f2;
        color: #0f766e;
        font-size: 10.5px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    thead th {
        text-align: left;
        padding: 9px 12px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .45px;
        text-transform: uppercase;
        color: #94a3b8;
        background: #fafcfb;
        border-bottom: 1px solid #eef4f1;
        white-space: nowrap;
    }
    tbody td { padding: 10px 12px; border-bottom: 1px solid #f4f8f6; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: 0; }
    tbody tr:hover { background: #f8fbfa; }
    .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    th.num { text-align: right; }
    .cell-code { font-weight: 700; color: #0f172a; font-variant-numeric: tabular-nums; }
    .cell-name { display: block; color: #64748b; font-size: 11px; margin-top: 1px; }
    .cell-muted { color: #64748b; }
    .cell-date { white-space: nowrap; color: #52616f; font-variant-numeric: tabular-nums; }
    .muted-row td { color: #94a3b8; }
    .negative { color: #dc2626; font-weight: 700; }
    .zero { color: #94a3b8; }

    .badge-status {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .3px;
        white-space: nowrap;
    }
    .st-active  { background: #fef3c7; color: #92400e; }
    .st-qc      { background: #dbeafe; color: #1d4ed8; }
    .st-ready   { background: #ede9fe; color: #6d28d9; }
    .st-acc     { background: #dcfce7; color: #15803d; }
    .st-publish { background: #dbeafe; color: #1d4ed8; }
    .st-info    { background: #e0f2fe; color: #0369a1; }
    .st-neutral { background: #f1f5f9; color: #475569; }
    .tag {
        display: inline-block;
        margin-left: 5px;
        padding: 2px 7px;
        border-radius: 6px;
        font-size: 9.5px;
        font-weight: 700;
        background: #fef2f2;
        color: #b91c1c;
        white-space: nowrap;
    }

    .cell-actions { white-space: nowrap; }
    .mini-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        margin: 1px 2px;
        border-radius: 7px;
        border: 1px solid transparent;
        font: inherit;
        font-size: 10.5px;
        font-weight: 700;
        line-height: 1.2;
        cursor: pointer;
        white-space: nowrap;
    }
    .mini-btn.primary { background: linear-gradient(135deg, #14b8a6, #0f766e); color: #fff; }
    .mini-btn.primary:hover { background: linear-gradient(135deg, #0f766e, #115e59); color: #fff; }
    .mini-btn.ghost { background: #fff; border-color: #dbe6e1; color: #475569; }
    .mini-btn.ghost:hover { border-color: #b8e6d8; color: #0f766e; }
    .mini-btn:disabled { background: #f1f5f3; border-color: #e6eeeb; color: #94a3b8; cursor: not-allowed; }

    .empty {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 26px 16px;
        color: #64748b;
        font-size: 12px;
    }
    .empty svg { width: 20px; height: 20px; color: #94a3b8; }
    .empty b { display: block; color: #334155; }
    .empty small { color: #94a3b8; }

    .pager {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding: 11px 16px;
        border-top: 1px solid #f1f6f4;
        font-size: 11.5px;
        color: #64748b;
    }
    .pager .links { display: flex; gap: 6px; }
    .pager a, .pager span.disabled {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 7px;
        border: 1px solid #e3ece8;
        font-weight: 600;
        color: #0f766e;
        background: #fff;
    }
    .pager span.disabled { color: #cbd5e1; border-color: #eef4f1; }
    .pager a:hover { background: #f0fdfa; border-color: #b8e6d8; }

    .error-note {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 13px 15px;
        color: #92400e;
        font-size: 12px;
    }
    .error-note a {
        font-weight: 700;
        background: #92400e;
        color: #fff;
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 11px;
    }

    @media (max-width: 620px) {
        .dhead h1 { font-size: 19px; }
        .field, .field.grow { flex: 1 1 100%; }
        .field input, .btn, .btn-reset { width: 100%; justify-content: center; }
        .toolbar { align-items: stretch; }
        .tabs { width: 100%; overflow-x: auto; flex-wrap: nowrap; padding-bottom: 2px; }
        .tab { flex: none; }
    }
</style>
