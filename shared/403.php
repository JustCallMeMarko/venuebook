<?php
require_once __DIR__ . '/../includes/auth.php';

http_response_code(403);

if (is_logged_in()) {
    $dashboard = is_admin()
        ? BASE_URL . '/admin/index.php'
        : BASE_URL . '/client/index.php';
} else {
    $dashboard = BASE_URL . '/login.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied | VenueBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="<?= BASE_URL ?>/assets/css/global.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Inter:wght@300;400;500&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent: #A67C52; /* matches sidebar accent */
            --muted:  var(--secondary-color);
            --bg:     var(--primary-color);
            --surface:var(--background-color);
            --border: rgba(0,0,0,0.06);
        }

        html, body {
            height: 100%;
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            color: var(--surface);
        }

        /* ── Grid noise texture ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Radial glow behind the 403 ── */
        .glow-orb {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -52%);
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(166,124,82,0.12) 0%, rgba(166,124,82,0.04) 50%, transparent 70%);
            pointer-events: none;
            z-index: 0;
            animation: pulse-glow 4s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { transform: translate(-50%, -52%) scale(1);   opacity: 1; }
            50%       { transform: translate(-50%, -52%) scale(1.12); opacity: 0.7; }
        }

        /* ── Floating particles ── */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            border-radius: 50%;
            background: var(--accent);
            opacity: 0;
            animation: float-up var(--dur) var(--delay) ease-in infinite;
        }

        @keyframes float-up {
            0%   { transform: translateY(0) scale(1);   opacity: 0; }
            10%  { opacity: 0.6; }
            90%  { opacity: 0.2; }
            100% { transform: translateY(-100vh) scale(0); opacity: 0; }
        }

        /* ── Main layout ── */
        .page {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 2rem;
            text-align: center;
        }

        /* ── The big 403 ── */
        .err-number {
            font-family: 'Cinzel', serif;
            font-weight: 900;
            font-size: clamp(7rem, 22vw, 15rem);
            line-height: 1;
            letter-spacing: -0.03em;
            color: transparent;
            -webkit-text-stroke: 1px rgba(34,34,34,0.9);
            position: relative;
            user-select: none;
            animation: slide-in 0.7s cubic-bezier(0.16,1,0.3,1) both;
        }

        /* Glitching filled overlay */
        .err-number::before {
            content: '403';
            position: absolute;
            inset: 0;
            color: rgba(34,34,34,1);
            -webkit-text-stroke: 0;
            clip-path: polygon(0 0, 100% 0, 100% 45%, 0 45%);
            animation: glitch 3.5s ease-in-out infinite;
        }

        .err-number::after {
            content: '403';
            position: absolute;
            inset: 0;
            color: rgba(34,34,34,0.45);
            -webkit-text-stroke: 0;
            opacity: 0;
            animation: glitch-secondary 3.5s ease-in-out infinite 0.08s;
        }

        @keyframes glitch {
            0%, 82%, 100% { clip-path: polygon(0 0, 100% 0, 100% 45%, 0 45%); transform: translate(0); opacity: 1; }
            84%           { clip-path: polygon(0 30%, 100% 30%, 100% 55%, 0 55%); transform: translate(-3px, 2px); opacity: 0.9; }
            86%           { clip-path: polygon(0 60%, 100% 60%, 100% 80%, 0 80%); transform: translate(3px, -2px); }
            88%           { clip-path: polygon(0 0, 100% 0, 100% 45%, 0 45%);    transform: translate(0); }
        }

        @keyframes glitch-secondary {
            0%, 83%, 100% { opacity: 0; transform: translate(0); }
            84%            { opacity: 0.5; transform: translate(3px, -2px); clip-path: polygon(0 30%, 100% 30%, 100% 55%, 0 55%); }
            86%            { opacity: 0; }
        }

        @keyframes slide-in {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Scanline across the number ── */
        .scanline {
            position: absolute;
            left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(166,124,82,0.55), transparent);
            animation: scan 2.5s linear infinite;
            pointer-events: none;
        }

        @keyframes scan {
            from { top: 0%; }
            to   { top: 100%; }
        }

        /* ── Text content ── */
        .err-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(166,124,82,0.10);
            border: 1px solid rgba(166,124,82,0.24);
            border-radius: 100px;
            padding: 4px 14px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 1.25rem;
            animation: fade-up 0.6s 0.3s both;
        }

        .err-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            animation: blink 1.2s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.2; }
        }

        .err-title {
            font-family: 'Cinzel', serif;
            font-size: clamp(1.1rem, 3vw, 1.5rem);
            font-weight: 700;
            color: #222;
            margin-bottom: 0.75rem;
            animation: fade-up 0.6s 0.45s both;
        }

        .err-desc {
            font-size: 0.9rem;
            font-weight: 300;
            color: rgba(34,34,34,0.6);
            max-width: 360px;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            animation: fade-up 0.6s 0.55s both;
        }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Button ── */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--accent);
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            padding: 12px 28px;
            border-radius: 6px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            animation: fade-up 0.6s 0.65s both;
        }

        .btn-back::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(166,124,82,0.35);
            color: #fff;
        }

        .btn-back:hover::before { opacity: 1; }
        .btn-back:active { transform: translateY(0); }

        .btn-arrow {
            width: 16px;
            height: 16px;
            transition: transform 0.2s;
        }

        .btn-back:hover .btn-arrow { transform: translateX(3px); }

        /* ── Corner decorations ── */
        .corner {
            position: fixed;
            width: 60px;
            height: 60px;
            pointer-events: none;
            z-index: 1;
        }

        .corner-tl { top: 24px; left: 24px; border-top: 1px solid rgba(232,52,26,0.3); border-left: 1px solid rgba(232,52,26,0.3); }
        .corner-tr { top: 24px; right: 24px; border-top: 1px solid rgba(232,52,26,0.3); border-right: 1px solid rgba(232,52,26,0.3); }
        .corner-bl { bottom: 24px; left: 24px; border-bottom: 1px solid rgba(232,52,26,0.3); border-left: 1px solid rgba(232,52,26,0.3); }
        .corner-br { bottom: 24px; right: 24px; border-bottom: 1px solid rgba(232,52,26,0.3); border-right: 1px solid rgba(232,52,26,0.3); }

        /* ── Logo watermark ── */
        .logo-mark {
            position: fixed;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Cinzel', serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.2em;
            color: rgba(255,255,255,0.15);
            z-index: 1;
            text-transform: uppercase;
            text-decoration: none;
        }
    </style>
</head>
<body>

<!-- Decorative -->
<div class="glow-orb"></div>
<div class="corner corner-tl"></div>
<div class="corner corner-tr"></div>
<div class="corner corner-bl"></div>
<div class="corner corner-br"></div>
<a class="logo-mark" href="<?= BASE_URL ?>">VenueBook</a>

<!-- Particles -->
<div class="particles" id="particles"></div>

<!-- Main -->
<div class="page">

    <!-- Big number -->
    <div class="err-number" style="position:relative;">
        403
        <div class="scanline"></div>
    </div>

    <!-- Text -->
    <div class="err-badge">Access Denied</div>
    <h1 class="err-title">You don't belong here.</h1>
    <p class="err-desc">
        <?php if (is_logged_in()): ?>
            Your account doesn't have permission to view this page.
            Head back to your dashboard.
        <?php else: ?>
            This area is restricted. Please log in with an authorised account to continue.
        <?php endif; ?>
    </p>

    <a href="<?= htmlspecialchars($dashboard) ?>" class="btn btn-dark">
        <?= is_logged_in() ? 'Back to Dashboard' : 'Go to Login' ?>
        <svg class="btn-arrow" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 8H13M13 8L8.5 3.5M13 8L8.5 12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

</div>

<script>
    // Spawn floating particles
    const container = document.getElementById('particles');
    const count = 28;
    for (let i = 0; i < count; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.cssText = `
            left: ${Math.random() * 100}%;
            bottom: ${-Math.random() * 20}%;
            --dur: ${4 + Math.random() * 8}s;
            --delay: ${Math.random() * 6}s;
            width: ${1 + Math.random() * 2.5}px;
            height: ${1 + Math.random() * 2.5}px;
            opacity: 0;
        `;
        container.appendChild(p);
    }

    // Subtle mouse parallax on the glow orb
    const orb = document.querySelector('.glow-orb');
    document.addEventListener('mousemove', (e) => {
        const dx = (e.clientX / window.innerWidth  - 0.5) * 30;
        const dy = (e.clientY / window.innerHeight - 0.5) * 20;
        orb.style.transform = `translate(calc(-50% + ${dx}px), calc(-52% + ${dy}px))`;
    });
</script>

</body>
</html>