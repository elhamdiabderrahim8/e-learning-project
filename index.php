<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enjah Project Hub</title>
    <style>
        :root {
            --bg: #0f172a;
            --bg-soft: #111c3a;
            --panel: rgba(15, 23, 42, 0.72);
            --panel-border: rgba(148, 163, 184, 0.18);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --accent-2: #22c55e;
            --warn: #f59e0b;
            --shadow: 0 20px 60px rgba(15, 23, 42, 0.35);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--text);
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.22), transparent 32%),
                radial-gradient(circle at top right, rgba(34, 197, 94, 0.16), transparent 28%),
                linear-gradient(135deg, #081024 0%, var(--bg) 55%, #020617 100%);
        }

        .wrap {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 40px 0 56px;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
            align-items: stretch;
            margin-bottom: 24px;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
        }

        .hero-copy {
            padding: 32px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(56, 189, 248, 0.12);
            color: #dbeafe;
            font-size: 0.85rem;
            letter-spacing: 0.02em;
        }

        .eyebrow span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 6px rgba(56, 189, 248, 0.12);
        }

        h1 {
            margin: 18px 0 14px;
            font-size: clamp(2rem, 5vw, 4.2rem);
            line-height: 0.98;
            letter-spacing: -0.04em;
        }

        .lead {
            max-width: 62ch;
            color: var(--muted);
            font-size: 1.02rem;
            line-height: 1.75;
            margin: 0 0 24px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 16px;
            border-radius: 14px;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-primary {
            background: linear-gradient(135deg, #38bdf8, #2563eb);
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.28);
        }

        .btn-secondary {
            border: 1px solid rgba(148, 163, 184, 0.3);
            background: rgba(15, 23, 42, 0.34);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 26px;
        }

        .stat {
            padding: 16px;
            border-radius: 18px;
            background: rgba(15, 23, 42, 0.42);
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        .stat strong {
            display: block;
            font-size: 1.35rem;
            margin-bottom: 6px;
        }

        .stat span {
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.45;
        }

        .side {
            padding: 24px;
            display: grid;
            gap: 14px;
            align-content: start;
        }

        .side h2,
        .section h2 {
            margin: 0 0 8px;
            font-size: 1.1rem;
            letter-spacing: 0.01em;
        }

        .route-list {
            display: grid;
            gap: 12px;
        }

        .route {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(2, 6, 23, 0.38);
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .route strong { display: block; margin-bottom: 4px; }
        .route span { color: var(--muted); font-size: 0.92rem; line-height: 1.4; }

        .route a {
            color: #dbeafe;
            text-decoration: none;
            white-space: nowrap;
            align-self: center;
            font-weight: 600;
        }

        .section {
            margin-top: 18px;
            padding: 24px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 16px;
        }

        .card {
            padding: 18px;
            border-radius: 20px;
            background: rgba(2, 6, 23, 0.34);
            border: 1px solid rgba(148, 163, 184, 0.14);
            display: grid;
            gap: 12px;
        }

        .tag {
            display: inline-flex;
            width: fit-content;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(56, 189, 248, 0.12);
            color: #dbeafe;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .tag.green { background: rgba(34, 197, 94, 0.12); color: #dcfce7; }
        .tag.amber { background: rgba(245, 158, 11, 0.12); color: #fef3c7; }

        .card p { margin: 0; color: var(--muted); line-height: 1.6; }

        .card a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
        }

        .footer {
            margin-top: 18px;
            padding: 20px 24px;
            color: var(--muted);
            font-size: 0.92rem;
        }

        @media (max-width: 900px) {
            .hero,
            .grid,
            .stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .wrap { width: min(100% - 20px, 1180px); padding-top: 18px; }
            .hero-copy,
            .side,
            .section,
            .footer { padding: 18px; }
            .route { flex-direction: column; }
        }
    </style>
</head>
<body>
    <main class="wrap">
        <section class="hero">
            <div class="panel hero-copy">
                <div class="eyebrow"><span></span> Enjah project hub</div>
                <h1>One clean start point for the whole project.</h1>
                <p class="lead">
                    This root page groups the main areas of the codebase so the project is easier to understand,
                    easier to open, and easier to navigate between the admin, professor, and student flows.
                </p>
                <div class="actions">
                    <a class="btn btn-primary" href="kmr/student/home.php">Open student site</a>
                    <a class="btn btn-secondary" href="professeur/index.php">Open professor site</a>
                    <a class="btn btn-secondary" href="admin/login.php">Open admin login</a>
                </div>

                <div class="stats">
                    <div class="stat">
                        <strong>3</strong>
                        <span>Main areas: admin, professor, and student</span>
                    </div>
                    <div class="stat">
                        <strong>1</strong>
                        <span>Shared entry point from the project root</span>
                    </div>
                    <div class="stat">
                        <strong>Clear</strong>
                        <span>One place to find the important folders and routes</span>
                    </div>
                </div>
            </div>

            <aside class="panel side">
                <div>
                    <h2>Quick routes</h2>
                    <p class="lead" style="margin:0; max-width:none; font-size:0.94rem;">Open the main app areas directly from here.</p>
                </div>
                <div class="route-list">
                    <div class="route">
                        <div>
                            <strong>Student portal</strong>
                            <span>Public landing and learner workflow.</span>
                        </div>
                        <a href="kmr/student/home.php">Go</a>
                    </div>
                    <div class="route">
                        <div>
                            <strong>Professor portal</strong>
                            <span>Teacher dashboard, lessons, and profile tools.</span>
                        </div>
                        <a href="professeur/index.php">Go</a>
                    </div>
                    <div class="route">
                        <div>
                            <strong>Admin panel</strong>
                            <span>Back office, students, professors, chat, and payments.</span>
                        </div>
                        <a href="admin/login.php">Go</a>
                    </div>
                </div>
            </aside>
        </section>

        <section class="panel section">
            <h2>Project map</h2>
            <p class="lead" style="margin-bottom:0; max-width:none;">
                The workspace currently contains more than one copy of the project. These are the main folders to focus on.
            </p>
            <div class="grid">
                <article class="card">
                    <div class="tag">Admin</div>
                    <h3 style="margin:0;">admin/</h3>
                    <p>Admin login, dashboard, students, professors, payments, and support chat.</p>
                    <a href="admin/README.md">Read admin notes</a>
                </article>
                <article class="card">
                    <div class="tag green">Professor</div>
                    <h3 style="margin:0;">professeur/</h3>
                    <p>Teacher landing page plus course, lesson, profile, certificate, and auth flows.</p>
                    <a href="professeur/index.php">Open professor hub</a>
                </article>
                <article class="card">
                    <div class="tag amber">Student</div>
                    <h3 style="margin:0;">kmr/student/</h3>
                    <p>Student landing page, pages folder, and backend actions with a shared bootstrap layer.</p>
                    <a href="kmr/student/index.php">Open student app</a>
                </article>
            </div>
        </section>

        <div class="panel footer">
            Suggested next step: keep this root hub as the default entry point, then gradually move shared components into a common folder.
        </div>
    </main>
</body>
</html>