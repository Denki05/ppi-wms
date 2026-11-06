<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 10px;
    }
    .back-link:hover { color: #0f766e; }
    .back-link svg { width: 12px; height: 12px; }

    .notice {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 12px 15px;
        border-radius: 12px;
        font-size: 12px;
        margin-bottom: 12px;
        border: 1px solid;
    }
    .notice.ok { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
    .notice.err { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
    .notice svg { width: 16px; height: 16px; flex: none; margin-top: 1px; }

    .hero {
        background: #fff;
        border: 1px solid #e6eeeb;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 1px 2px rgba(15,23,42,.03), 0 16px 30px -30px rgba(15,23,42,.4);
        margin-bottom: 12px;
    }
    .hero-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .hero-top h1 { margin: 0 0 2px; font-size: 20px; letter-spacing: -.4px; color: #0b1f1a; }
    .hero-top p { margin: 0; font-size: 12px; color: #64748b; }
    .hero-chips { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }
    .chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #f7faf9;
        border: 1px solid #e6eeeb;
        font-size: 10.5px;
        font-weight: 600;
        color: #52616f;
    }

    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px 20px;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid #f1f6f4;
    }
    .meta-item span {
        display: block;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: .45px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 3px;
    }
    .meta-item b { font-size: 12.5px; font-weight: 600; color: #1e293b; word-break: break-word; }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(148px, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }
    .stat {
        background: #fff;
        border: 1px solid #e6eeeb;
        border-left: 3px solid var(--accent, #14b8a6);
        border-radius: 12px;
        padding: 11px 14px;
    }
    .stat span {
        display: block;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 2px;
    }
    .stat b {
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
        letter-spacing: -.4px;
    }
    .stat small { font-size: 10.5px; color: #94a3b8; font-weight: 600; margin-left: 3px; }

    .action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        background: #fff;
        border: 1px solid #e6eeeb;
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 12px;
    }
    .action-bar .note { font-size: 12px; color: #52616f; max-width: 640px; }
    .action-bar .note b { color: #0f172a; }
    .action-bar .note.warn { color: #92400e; }
    .action-bar .step-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 800;
        line-height: 1;
        flex: none;
    }
    .btn-line .step-num { background: #0f172a; color: #fff; }
    .btn-acc .step-num { background: rgba(255,255,255,.92); color: #15803d; }
    .btn-acc {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 9px;
        border: 0;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff;
        box-shadow: 0 6px 14px -8px rgba(21,128,61,.8);
    }
    .btn-acc:hover { background: linear-gradient(135deg, #15803d, #166534); }
    .btn-acc:disabled { opacity: .6; cursor: default; }
    .btn-acc svg { width: 13px; height: 13px; }
    .action-buttons { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .action-buttons form { margin: 0; }
    .btn-line {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 14px;
        border-radius: 9px;
        border: 1px solid #d8e1dc;
        background: #fff;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }
    .btn-line:hover { border-color: #b45309; color: #b45309; background: #fffbeb; }
    .btn-line svg { width: 13px; height: 13px; }

    .detail-panel { margin-bottom: 12px; }
    .qc-ok { color: #15803d; font-weight: 700; }
    .qc-bad { color: #b91c1c; font-weight: 700; }
    .qc-hold { color: #b45309; font-weight: 700; }

    @media (max-width: 620px) {
        .hero { padding: 15px; }
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
