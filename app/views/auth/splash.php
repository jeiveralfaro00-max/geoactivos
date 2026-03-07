<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GeoActivos — Plataforma de Gestión de Activos</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
:root {
  --teal:#0BA896; --teal-dark:#077D6E; --teal-light:#E6F7F5; --teal-mid:#B2E8E3;
  --slate-900:#0F1D2E; --slate-800:#1A2E42; --slate-700:#2C4258; --slate-500:#4E6D8C;
  --slate-400:#7090AA; --slate-300:#9DB8CC; --slate-200:#C8DDE8; --slate-100:#E8F1F7;
  --slate-50:#F5F8FA; --white:#FFFFFF;
  --amber:#F59E0B; --blue:#3B82F6; --purple:#8B5CF6; --rose:#F43F5E; --green:#10B981;
}
html { scroll-behavior: smooth; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--white); color: var(--slate-900); overflow-x: hidden; }

/* NAV */
.nav {
  position: fixed; top:0; left:0; right:0; z-index:100;
  display:flex; align-items:center; justify-content:space-between;
  padding: 0 56px; height:64px;
  background: rgba(255,255,255,0.92); backdrop-filter:blur(16px);
  border-bottom: 1px solid var(--slate-100);
  animation: fadeDown 0.5s ease both;
}
@keyframes fadeDown { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:translateY(0)} }

.nav-logo { display:flex; align-items:center; gap:11px; text-decoration:none; }
.nav-logo-icon {
  width:36px; height:36px;
  background: linear-gradient(135deg, var(--teal), var(--teal-dark));
  border-radius:10px; display:flex; align-items:center; justify-content:center;
  box-shadow: 0 3px 10px rgba(11,168,150,0.28); flex-shrink:0;
}
.nav-logo-name { font-family:'Sora',sans-serif; font-weight:700; font-size:1.05rem; color:var(--slate-900); letter-spacing:-0.2px; }
.nav-links { display:flex; align-items:center; gap:28px; }
.nav-link { font-size:0.82rem; font-weight:600; color:var(--slate-500); text-decoration:none; transition:color 0.2s; }
.nav-link:hover { color:var(--teal); }
.nav-badge { font-size:0.65rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--teal); background:var(--teal-light); border:1px solid var(--teal-mid); padding:4px 12px; border-radius:100px; }
.btn-nav {
  padding:9px 22px; background:linear-gradient(135deg,var(--teal),var(--teal-dark)); color:#fff;
  border:none; border-radius:8px; font-family:'Plus Jakarta Sans',sans-serif; font-size:0.82rem;
  font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px;
  box-shadow:0 3px 12px rgba(11,168,150,0.30); transition:transform 0.18s, box-shadow 0.18s;
}
.btn-nav:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(11,168,150,0.40); }

/* HERO */
.hero {
  padding: 140px 56px 80px;
  display:flex; align-items:center; gap:64px;
  max-width:1280px; margin:0 auto;
}
.hero-content { flex:1; max-width:520px; }
.hero-eyebrow {
  display:inline-flex; align-items:center; gap:8px;
  background:var(--teal-light); border:1px solid var(--teal-mid);
  border-radius:100px; padding:5px 14px; margin-bottom:24px;
  animation: fadeUp 0.6s 0.1s ease both;
}
.hero-eyebrow-dot { width:7px; height:7px; border-radius:50%; background:var(--teal); animation:pulse 2.5s infinite; }
@keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(11,168,150,0.4)} 50%{box-shadow:0 0 0 5px rgba(11,168,150,0)} }
.hero-eyebrow-text { font-size:0.72rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--teal); }
.hero-h1 { font-family:'Sora',sans-serif; font-size:clamp(2.6rem,4vw,3.8rem); font-weight:800; line-height:1.06; letter-spacing:-1.5px; margin-bottom:20px; animation:fadeUp 0.6s 0.15s ease both; }
.hero-h1 .teal { color:var(--teal); }
.hero-sub { font-size:1rem; color:var(--slate-500); line-height:1.75; margin-bottom:36px; animation:fadeUp 0.6s 0.2s ease both; }
.hero-actions { display:flex; align-items:center; gap:14px; margin-bottom:44px; animation:fadeUp 0.6s 0.25s ease both; }
.btn-primary {
  padding:14px 28px; background:linear-gradient(135deg,var(--teal),var(--teal-dark)); color:#fff;
  border:none; border-radius:10px; font-family:'Plus Jakarta Sans',sans-serif; font-size:0.95rem;
  font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px;
  box-shadow:0 6px 20px rgba(11,168,150,0.35); transition:transform 0.18s, box-shadow 0.18s;
  position:relative; overflow:hidden;
}
.btn-primary::after { content:''; position:absolute; top:0; left:-100%; width:55%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,0.18),transparent); transform:skewX(-20deg); animation:shimmer 3s ease infinite; }
@keyframes shimmer { 0%{left:-100%} 100%{left:160%} }
.btn-primary:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(11,168,150,0.45); }
.btn-secondary {
  padding:14px 24px; background:var(--white); color:var(--slate-700);
  border:1.5px solid var(--slate-200); border-radius:10px; font-family:'Plus Jakarta Sans',sans-serif;
  font-size:0.9rem; font-weight:600; cursor:pointer; text-decoration:none;
  display:inline-flex; align-items:center; gap:7px; transition:border-color 0.2s, color 0.2s, transform 0.18s;
}
.btn-secondary:hover { border-color:var(--teal); color:var(--teal); transform:translateY(-1px); }
.social-proof { display:flex; align-items:center; gap:16px; animation:fadeUp 0.6s 0.3s ease both; }
.sp-avatars { display:flex; }
.sp-avatar { width:32px; height:32px; border-radius:50%; border:2px solid var(--white); display:flex; align-items:center; justify-content:center; font-size:0.65rem; font-weight:700; color:white; margin-left:-8px; flex-shrink:0; }
.sp-avatar:first-child { margin-left:0; }
.sp-text { font-size:0.78rem; color:var(--slate-500); line-height:1.4; }
.sp-text strong { color:var(--slate-900); font-weight:700; }

/* MOCKUP */
.hero-visual { flex:1.1; position:relative; animation:fadeUp 0.7s 0.2s ease both; }
.mockup-wrap {
  position:relative; border-radius:20px; overflow:hidden;
  box-shadow: 0 40px 80px rgba(15,29,46,0.18), 0 16px 32px rgba(15,29,46,0.10), 0 0 0 1px rgba(15,29,46,0.06);
}
.browser-chrome { background:#F0F2F5; padding:12px 16px 10px; display:flex; align-items:center; gap:10px; border-bottom:1px solid #E0E4E8; }
.chrome-dots { display:flex; gap:5px; }
.chrome-dot { width:10px; height:10px; border-radius:50%; }
.cd-r{background:#FF5F57} .cd-a{background:#FEBC2E} .cd-g{background:#28C840}
.chrome-bar { flex:1; background:#E2E6EA; border-radius:5px; padding:4px 12px; font-size:0.68rem; color:#8A9BAD; font-weight:500; display:flex; align-items:center; gap:6px; }

/* DASH */
.dash { background:#F5F8FA; display:flex; height:440px; overflow:hidden; }
.dash-sidebar { width:52px; background:var(--slate-900); display:flex; flex-direction:column; align-items:center; padding:12px 0; gap:4px; flex-shrink:0; }
.sb-logo { width:30px; height:30px; background:linear-gradient(135deg,var(--teal),var(--teal-dark)); border-radius:9px; display:flex; align-items:center; justify-content:center; margin-bottom:8px; }
.sb-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:0.82rem; color:rgba(255,255,255,0.4); }
.sb-icon.on { background:rgba(11,168,150,0.2); color:var(--teal); }
.sb-div { width:26px; height:1px; background:rgba(255,255,255,0.08); margin:3px 0; }
.dash-main { flex:1; display:flex; flex-direction:column; overflow:hidden; }
.dash-topbar { background:var(--white); border-bottom:1px solid var(--slate-100); padding:9px 18px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
.dash-title { font-family:'Sora',sans-serif; font-weight:700; font-size:0.82rem; color:var(--slate-900); }
.dash-tr { display:flex; align-items:center; gap:8px; }
.dsearch { background:var(--slate-50); border:1px solid var(--slate-100); border-radius:7px; padding:4px 10px; font-size:0.65rem; color:var(--slate-300); width:120px; display:flex; align-items:center; gap:4px; }
.dav { width:26px; height:26px; background:linear-gradient(135deg,var(--teal),var(--blue)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.58rem; font-weight:700; color:white; }
.dash-body { flex:1; overflow:hidden; padding:14px 18px; display:flex; flex-direction:column; gap:12px; }

/* KPIs */
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
.kpi-card { background:var(--white); border:1px solid var(--slate-100); border-radius:11px; padding:12px 13px; box-shadow:0 1px 4px rgba(15,29,46,0.05); }
.kpi-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:7px; }
.kpi-lbl { font-size:0.58rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:var(--slate-400); }
.kpi-ico { width:22px; height:22px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:0.68rem; }
.kpi-num { font-family:'Sora',sans-serif; font-size:1.25rem; font-weight:700; color:var(--slate-900); line-height:1; margin-bottom:4px; }
.kpi-d { font-size:0.58rem; font-weight:600; }
.dup{color:#10B981} .ddn{color:#F43F5E}

/* Chart + Table */
.content-row { display:grid; grid-template-columns:1.5fr 1fr; gap:10px; flex:1; min-height:0; }
.chart-card, .table-card { background:var(--white); border:1px solid var(--slate-100); border-radius:11px; padding:13px 15px; box-shadow:0 1px 4px rgba(15,29,46,0.05); display:flex; flex-direction:column; overflow:hidden; }
.card-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
.card-ttl { font-family:'Sora',sans-serif; font-size:0.72rem; font-weight:700; color:var(--slate-900); }
.legend { display:flex; gap:8px; }
.leg { display:flex; align-items:center; gap:3px; font-size:0.56rem; color:var(--slate-400); font-weight:500; }
.legdot { width:6px; height:6px; border-radius:50%; }
.chart-svg { flex:1; width:100%; }
.mtable { width:100%; border-collapse:collapse; }
.mtable th { font-size:0.56rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--slate-300); text-align:left; padding:0 0 7px; border-bottom:1px solid var(--slate-100); }
.mtable td { font-size:0.63rem; color:var(--slate-700); padding:5px 0; border-bottom:1px solid rgba(232,241,247,0.5); }
.mtable td:last-child { text-align:right; }
.sp { display:inline-flex; align-items:center; gap:2px; padding:2px 7px; border-radius:100px; font-size:0.56rem; font-weight:600; }
.sok{background:#E6F9F0;color:#0D9B5F} .swrn{background:#FFF7E6;color:#C07A00} .salt{background:#FFF0F2;color:#C81E3A}

/* Bottom row */
.bot-row { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
.mini-card { background:var(--white); border:1px solid var(--slate-100); border-radius:11px; padding:11px 13px; box-shadow:0 1px 4px rgba(15,29,46,0.05); }
.mc-hdr { display:flex; align-items:center; gap:6px; margin-bottom:7px; }
.mc-ico { width:20px; height:20px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:0.65rem; }
.mc-ttl { font-size:0.62rem; font-weight:700; color:var(--slate-800); }
.bar-row { display:flex; align-items:center; gap:5px; margin-bottom:3px; }
.bar-lbl { font-size:0.55rem; color:var(--slate-400); width:52px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.bar-track { flex:1; height:5px; background:var(--slate-100); border-radius:3px; overflow:hidden; }
.bar-fill { height:100%; border-radius:3px; background:linear-gradient(90deg,var(--teal),var(--teal-dark)); }
.bar-val { font-size:0.56rem; font-weight:700; color:var(--slate-700); min-width:20px; text-align:right; }

/* Float badges */
.float-badge {
  position:absolute; background:var(--white); border-radius:12px; padding:10px 14px;
  box-shadow:0 8px 28px rgba(15,29,46,0.15), 0 2px 8px rgba(15,29,46,0.08);
  display:flex; align-items:center; gap:9px; white-space:nowrap;
}
.fb1 { bottom:-16px; left:-28px; animation:bob 4s ease-in-out infinite; }
.fb2 { top:56px; right:-22px; animation:bob 4s ease-in-out infinite; animation-delay:-2s; }
@keyframes bob { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-7px)} }
.fb-ico { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:0.9rem; flex-shrink:0; }
.fb-v { font-family:'Sora',sans-serif; font-size:0.9rem; font-weight:700; color:var(--slate-900); line-height:1; }
.fb-l { font-size:0.6rem; color:var(--slate-400); font-weight:500; }

/* MODULES */
.modules-section { padding:80px 56px; background:var(--slate-50); border-top:1px solid var(--slate-100); border-bottom:1px solid var(--slate-100); }
.sec-hdr { text-align:center; margin-bottom:48px; }
.sec-eyebrow { display:inline-flex; align-items:center; gap:7px; font-size:0.68rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--teal); margin-bottom:12px; }
.sec-eyebrow::before,.sec-eyebrow::after { content:''; width:24px; height:1.5px; background:var(--teal); border-radius:2px; opacity:0.5; }
.sec-title { font-family:'Sora',sans-serif; font-size:2.1rem; font-weight:800; letter-spacing:-0.8px; color:var(--slate-900); margin-bottom:10px; }
.sec-sub { font-size:0.93rem; color:var(--slate-500); max-width:460px; margin:0 auto; line-height:1.7; }
.mod-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; max-width:1160px; margin:0 auto; }
.mod-card {
  background:var(--white); border:1px solid var(--slate-100); border-radius:18px; padding:24px 20px;
  transition:transform 0.22s, box-shadow 0.22s, border-color 0.22s; position:relative; overflow:hidden;
}
.mod-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:18px 18px 0 0; opacity:0; transition:opacity 0.22s; }
.mod-card:hover { transform:translateY(-4px); box-shadow:0 16px 40px rgba(15,29,46,0.10), 0 4px 12px rgba(15,29,46,0.06); border-color:var(--teal-mid); }
.mod-card:hover::before { opacity:1; }
.c-teal::before   { background:linear-gradient(90deg,var(--teal),var(--teal-dark)); }
.c-blue::before   { background:linear-gradient(90deg,var(--blue),#2563EB); }
.c-purple::before { background:linear-gradient(90deg,var(--purple),#7C3AED); }
.c-amber::before  { background:linear-gradient(90deg,var(--amber),#D97706); }
.c-green::before  { background:linear-gradient(90deg,var(--green),#059669); }
.c-rose::before   { background:linear-gradient(90deg,var(--rose),#E11D48); }
.c-slate::before  { background:linear-gradient(90deg,var(--slate-500),var(--slate-700)); }
.c-cyan::before   { background:linear-gradient(90deg,#06B6D4,#0891B2); }
.mod-ico { width:46px; height:46px; border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:1.25rem; margin-bottom:13px; }
.it{background:#E6F7F5} .ib{background:#EFF6FF} .ip{background:#F5F3FF} .ia{background:#FFFBEB} .ig{background:#ECFDF5} .ir{background:#FFF1F2} .is{background:var(--slate-100)} .ic{background:#ECFEFF}
.mod-name { font-family:'Sora',sans-serif; font-size:0.9rem; font-weight:700; color:var(--slate-900); margin-bottom:6px; }
.mod-desc { font-size:0.74rem; color:var(--slate-500); line-height:1.6; margin-bottom:12px; }
.mod-tags { display:flex; flex-wrap:wrap; gap:4px; }
.mod-tag { font-size:0.57rem; font-weight:600; letter-spacing:0.05em; padding:2px 8px; border-radius:100px; background:var(--slate-50); color:var(--slate-500); border:1px solid var(--slate-100); }

/* STATS */
.stats-strip { background:var(--slate-900); padding:48px 56px; display:flex; align-items:center; justify-content:center; }
.stat-item { flex:1; max-width:200px; text-align:center; padding:0 24px; border-right:1px solid rgba(255,255,255,0.08); }
.stat-item:last-child { border-right:none; }
.stat-val { font-family:'Sora',sans-serif; font-size:2.2rem; font-weight:800; color:var(--white); letter-spacing:-1px; line-height:1; margin-bottom:6px; }
.stat-val em { font-style:normal; color:var(--teal); }
.stat-lbl { font-size:0.65rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:rgba(157,184,204,0.7); }

/* CTA */
.cta-section { padding:88px 56px; text-align:center; background:var(--white); position:relative; overflow:hidden; }
.cta-section::before { content:''; position:absolute; top:-140px; left:50%; transform:translateX(-50%); width:560px; height:380px; border-radius:50%; background:radial-gradient(circle,rgba(11,168,150,0.07) 0%,transparent 70%); pointer-events:none; }
.cta-badge { display:inline-flex; align-items:center; gap:7px; background:var(--teal-light); border:1px solid var(--teal-mid); border-radius:100px; padding:6px 16px; margin-bottom:22px; font-size:0.72rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--teal); }
.cta-title { font-family:'Sora',sans-serif; font-size:clamp(2rem,3.5vw,2.8rem); font-weight:800; letter-spacing:-1px; color:var(--slate-900); margin-bottom:14px; line-height:1.1; }
.cta-title .teal { color:var(--teal); }
.cta-sub { font-size:0.95rem; color:var(--slate-500); max-width:440px; margin:0 auto 32px; line-height:1.75; }
.btn-cta {
  padding:16px 36px; background:linear-gradient(135deg,var(--teal),var(--teal-dark)); color:#fff;
  border:none; border-radius:12px; font-family:'Plus Jakarta Sans',sans-serif; font-size:1rem;
  font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:9px;
  box-shadow:0 8px 24px rgba(11,168,150,0.40); transition:transform 0.18s, box-shadow 0.18s;
  position:relative; overflow:hidden;
}
.btn-cta::after { content:''; position:absolute; top:0; left:-100%; width:55%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,0.18),transparent); transform:skewX(-20deg); animation:shimmer 3s ease infinite; }
.btn-cta:hover { transform:translateY(-2px); box-shadow:0 12px 32px rgba(11,168,150,0.50); }
.cta-note { font-size:0.72rem; color:var(--slate-300); display:flex; align-items:center; justify-content:center; gap:6px; margin-top:18px; }

/* FOOTER */
.footer { background:var(--slate-900); padding:32px 56px; display:flex; align-items:center; justify-content:space-between; border-top:1px solid rgba(255,255,255,0.06); }
.foot-logo { display:flex; align-items:center; gap:10px; }
.foot-logo-ico { width:30px; height:30px; background:linear-gradient(135deg,var(--teal),var(--teal-dark)); border-radius:8px; display:flex; align-items:center; justify-content:center; }
.foot-logo-name { font-family:'Sora',sans-serif; font-weight:700; font-size:0.9rem; color:var(--white); }
.foot-copy { font-size:0.68rem; color:rgba(157,184,204,0.6); }
.foot-badges { display:flex; gap:8px; }
.foot-badge { font-size:0.62rem; font-weight:600; color:rgba(157,184,204,0.7); background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); padding:4px 10px; border-radius:100px; }

@keyframes fadeUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
.reveal { opacity:0; transform:translateY(22px); transition:opacity 0.6s ease, transform 0.6s ease; }
.reveal.visible { opacity:1; transform:translateY(0); }

@media (max-width:1024px) {
  .hero { flex-direction:column; padding:100px 32px 60px; }
  .hero-visual { width:100%; }
  .mod-grid { grid-template-columns:repeat(2,1fr); }
  .nav { padding:0 24px; }
  .nav-links { display:none; }
}
@media (max-width:640px) {
  .mod-grid { grid-template-columns:1fr; }
  .footer { flex-direction:column; gap:12px; text-align:center; }
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <a href="#" class="nav-logo">
    <div class="nav-logo-icon">
      <svg viewBox="0 0 48 48" fill="none" width="18" height="18">
        <defs><linearGradient id="nlg" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse"><stop stop-color="#fff" stop-opacity=".95"/><stop offset="100%" stop-color="#C2EDE9"/></linearGradient></defs>
        <polygon points="24,3 43,13.5 43,34.5 24,45 5,34.5 5,13.5" stroke="url(#nlg)" stroke-width="2.5" fill="none"/>
        <circle cx="24" cy="24" r="5" fill="url(#nlg)"/>
        <circle cx="24" cy="24" r="2" fill="rgba(11,120,100,.7)"/>
        <line x1="24" y1="12" x2="24" y2="19" stroke="url(#nlg)" stroke-width="1.6" stroke-linecap="round"/>
        <line x1="24" y1="29" x2="24" y2="36" stroke="url(#nlg)" stroke-width="1.6" stroke-linecap="round"/>
        <line x1="13" y1="19" x2="19" y2="22" stroke="url(#nlg)" stroke-width="1.5" stroke-linecap="round"/>
        <line x1="29" y1="26" x2="35" y2="29" stroke="url(#nlg)" stroke-width="1.5" stroke-linecap="round"/>
        <line x1="35" y1="19" x2="29" y2="22" stroke="url(#nlg)" stroke-width="1.5" stroke-linecap="round"/>
        <line x1="19" y1="26" x2="13" y2="29" stroke="url(#nlg)" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
    </div>
    <span class="nav-logo-name">GeoActivos</span>
  </a>
  <div class="nav-links">
    <a href="#modulos" class="nav-link">Módulos</a>
    <a href="#plataforma" class="nav-link">Plataforma</a>
    <span class="nav-badge">GeSaProv</span>
  </div>
  <a href="index.php?route=login" class="btn-nav">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
    Ingresar
  </a>
</nav>

<!-- HERO -->
<section class="hero" id="plataforma">
  <div class="hero-content">
    <div class="hero-eyebrow">
      <div class="hero-eyebrow-dot"></div>
      <span class="hero-eyebrow-text">Industrial · Biomédico · Clínico</span>
    </div>
    <h1 class="hero-h1">Gestión Total<br>de <span class="teal">Activos</span><br>Empresariales</h1>
    <p class="hero-sub">Controla, monitorea y optimiza cada equipo — ventiladores, monitores ECG, computadores, maquinaria — en una sola plataforma inteligente con trazabilidad total.</p>
    <div class="hero-actions">
      <a href="index.php?route=login" class="btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Ingresar a la Plataforma
      </a>
      <a href="#modulos" class="btn-secondary">
        Ver módulos
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
      </a>
    </div>
    <div class="social-proof">
      <div class="sp-avatars">
        <div class="sp-avatar" style="background:linear-gradient(135deg,#0BA896,#077D6E)">JM</div>
        <div class="sp-avatar" style="background:linear-gradient(135deg,#3B82F6,#2563EB)">CA</div>
        <div class="sp-avatar" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED)">LP</div>
        <div class="sp-avatar" style="background:linear-gradient(135deg,#F59E0B,#D97706)">RG</div>
      </div>
      <div class="sp-text"><strong>Multi-tenant · RBAC · ISO</strong><br>Plataforma 100% trazable y auditable</div>
    </div>
  </div>

  <!-- MOCKUP -->
  <div class="hero-visual">
    <div class="float-badge fb2">
      <div class="fb-ico" style="background:#E6F7F5">📊</div>
      <div><div class="fb-v">98.4%</div><div class="fb-l">Disponibilidad equipos</div></div>
    </div>

    <div class="mockup-wrap">
      <div class="browser-chrome">
        <div class="chrome-dots"><div class="chrome-dot cd-r"></div><div class="chrome-dot cd-a"></div><div class="chrome-dot cd-g"></div></div>
        <div class="chrome-bar">
          <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          geoactivos.app/dashboard
        </div>
      </div>

      <div class="dash">
        <div class="dash-sidebar">
          <div class="sb-logo">
            <svg viewBox="0 0 48 48" fill="none" width="15" height="15">
              <polygon points="24,3 43,13.5 43,34.5 24,45 5,34.5 5,13.5" stroke="rgba(255,255,255,.9)" stroke-width="2.5" fill="none"/>
              <circle cx="24" cy="24" r="5" fill="rgba(255,255,255,.9)"/>
            </svg>
          </div>
          <div class="sb-icon on">🏠</div>
          <div class="sb-icon">🖥️</div>
          <div class="sb-icon">🔧</div>
          <div class="sb-icon">🎯</div>
          <div class="sb-div"></div>
          <div class="sb-icon">📊</div>
          <div class="sb-icon">📋</div>
          <div class="sb-icon">🏢</div>
          <div class="sb-div"></div>
          <div class="sb-icon">📱</div>
          <div class="sb-icon">⚙️</div>
        </div>

        <div class="dash-main">
          <div class="dash-topbar">
            <span class="dash-title">Dashboard · GeoActivos</span>
            <div class="dash-tr">
              <div class="dsearch">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Buscar activos...
              </div>
              <div class="dav">GA</div>
            </div>
          </div>

          <div class="dash-body">
            <!-- KPIs -->
            <div class="kpi-row">
              <div class="kpi-card">
                <div class="kpi-top"><span class="kpi-lbl">Activos Totales</span><div class="kpi-ico" style="background:#E6F7F5">🖥️</div></div>
                <div class="kpi-num">1,284</div>
                <div class="kpi-d dup">▲ 12 este mes</div>
              </div>
              <div class="kpi-card">
                <div class="kpi-top"><span class="kpi-lbl">En Mantenimiento</span><div class="kpi-ico" style="background:#FFFBEB">🔧</div></div>
                <div class="kpi-num">47</div>
                <div class="kpi-d ddn">▼ 3 pendientes</div>
              </div>
              <div class="kpi-card">
                <div class="kpi-top"><span class="kpi-lbl">Calibraciones</span><div class="kpi-ico" style="background:#F5F3FF">🎯</div></div>
                <div class="kpi-num">23</div>
                <div class="kpi-d dup">▲ ISO vigentes</div>
              </div>
              <div class="kpi-card">
                <div class="kpi-top"><span class="kpi-lbl">Disponibilidad</span><div class="kpi-ico" style="background:#ECFDF5">✅</div></div>
                <div class="kpi-num">98<span style="font-size:.65rem;color:#4E6D8C">%</span></div>
                <div class="kpi-d dup">▲ Óptimo</div>
              </div>
            </div>

            <!-- Chart + Table -->
            <div class="content-row">
              <div class="chart-card">
                <div class="card-hdr">
                  <span class="card-ttl">Mantenimientos por Mes</span>
                  <div class="legend">
                    <div class="leg"><div class="legdot" style="background:#0BA896"></div>Preventivo</div>
                    <div class="leg"><div class="legdot" style="background:#3B82F6"></div>Correctivo</div>
                  </div>
                </div>
                <svg class="chart-svg" viewBox="0 0 320 100" preserveAspectRatio="none">
                  <line x1="0" y1="20" x2="320" y2="20" stroke="#E8F1F7" stroke-width="1"/>
                  <line x1="0" y1="40" x2="320" y2="40" stroke="#E8F1F7" stroke-width="1"/>
                  <line x1="0" y1="60" x2="320" y2="60" stroke="#E8F1F7" stroke-width="1"/>
                  <line x1="0" y1="80" x2="320" y2="80" stroke="#E8F1F7" stroke-width="1"/>
                  <defs>
                    <linearGradient id="g1" x1="0" y1="0" x2="0" y2="1"><stop stop-color="#0BA896" stop-opacity=".20"/><stop offset="100%" stop-color="#0BA896" stop-opacity=".02"/></linearGradient>
                    <linearGradient id="g2" x1="0" y1="0" x2="0" y2="1"><stop stop-color="#3B82F6" stop-opacity=".15"/><stop offset="100%" stop-color="#3B82F6" stop-opacity=".02"/></linearGradient>
                  </defs>
                  <path d="M0,74 C26,68 52,52 80,47 C106,42 132,56 160,44 C186,32 212,24 240,18 C266,12 292,26 320,20 L320,100 L0,100Z" fill="url(#g1)"/>
                  <path d="M0,74 C26,68 52,52 80,47 C106,42 132,56 160,44 C186,32 212,24 240,18 C266,12 292,26 320,20" fill="none" stroke="#0BA896" stroke-width="2.2" stroke-linecap="round"/>
                  <path d="M0,88 C26,84 52,78 80,74 C106,70 132,80 160,72 C186,64 212,58 240,54 C266,50 292,62 320,56 L320,100 L0,100Z" fill="url(#g2)"/>
                  <path d="M0,88 C26,84 52,78 80,74 C106,70 132,80 160,72 C186,64 212,58 240,54 C266,50 292,62 320,56" fill="none" stroke="#3B82F6" stroke-width="1.8" stroke-linecap="round" stroke-dasharray="5,3"/>
                  <circle cx="80" cy="47" r="3" fill="#0BA896"/>
                  <circle cx="160" cy="44" r="3" fill="#0BA896"/>
                  <circle cx="240" cy="18" r="3.5" fill="#0BA896" stroke="white" stroke-width="1.5"/>
                  <text x="5" y="98" font-size="7" fill="#9DB8CC" font-family="sans-serif">Ene</text>
                  <text x="44" y="98" font-size="7" fill="#9DB8CC" font-family="sans-serif">Feb</text>
                  <text x="88" y="98" font-size="7" fill="#9DB8CC" font-family="sans-serif">Mar</text>
                  <text x="130" y="98" font-size="7" fill="#9DB8CC" font-family="sans-serif">Abr</text>
                  <text x="174" y="98" font-size="7" fill="#9DB8CC" font-family="sans-serif">May</text>
                  <text x="218" y="98" font-size="7" fill="#9DB8CC" font-family="sans-serif">Jun</text>
                  <text x="262" y="98" font-size="7" fill="#9DB8CC" font-family="sans-serif">Jul</text>
                </svg>
              </div>
              <div class="table-card">
                <div class="card-hdr"><span class="card-ttl">Últimas OT</span></div>
                <table class="mtable">
                  <thead><tr><th>Activo</th><th>Tipo</th><th>Estado</th></tr></thead>
                  <tbody>
                    <tr><td>Ventilador UCI-04</td><td>Prev.</td><td><span class="sp sok">✓ OK</span></td></tr>
                    <tr><td>Monitor ECG-12</td><td>Corr.</td><td><span class="sp swrn">⚠ Proceso</span></td></tr>
                    <tr><td>Compresor A-07</td><td>Prev.</td><td><span class="sp sok">✓ OK</span></td></tr>
                    <tr><td>Desfibrilador-03</td><td>Cal.</td><td><span class="sp salt">! Vence</span></td></tr>
                    <tr><td>PC Clínica-19</td><td>Prev.</td><td><span class="sp sok">✓ OK</span></td></tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Bottom cards -->
            <div class="bot-row">
              <div class="mini-card">
                <div class="mc-hdr"><div class="mc-ico it">🏥</div><span class="mc-ttl">Activos por Sede</span></div>
                <div class="bar-row"><span class="bar-lbl">Sede Central</span><div class="bar-track"><div class="bar-fill" style="width:78%"></div></div><span class="bar-val">420</span></div>
                <div class="bar-row"><span class="bar-lbl">Clínica Norte</span><div class="bar-track"><div class="bar-fill" style="width:52%"></div></div><span class="bar-val">281</span></div>
                <div class="bar-row"><span class="bar-lbl">UCI Sur</span><div class="bar-track"><div class="bar-fill" style="width:34%"></div></div><span class="bar-val">184</span></div>
              </div>
              <div class="mini-card">
                <div class="mc-hdr"><div class="mc-ico ip">📋</div><span class="mc-ttl">Calibraciones ISO</span></div>
                <div class="bar-row"><span class="bar-lbl">Vigentes</span><div class="bar-track"><div class="bar-fill" style="width:82%;background:linear-gradient(90deg,#10B981,#059669)"></div></div><span class="bar-val">41</span></div>
                <div class="bar-row"><span class="bar-lbl">Por vencer</span><div class="bar-track"><div class="bar-fill" style="width:22%;background:linear-gradient(90deg,#F59E0B,#D97706)"></div></div><span class="bar-val">11</span></div>
                <div class="bar-row"><span class="bar-lbl">Vencidos</span><div class="bar-track"><div class="bar-fill" style="width:8%;background:linear-gradient(90deg,#F43F5E,#E11D48)"></div></div><span class="bar-val">4</span></div>
              </div>
              <div class="mini-card">
                <div class="mc-hdr"><div class="mc-ico ia">⚡</div><span class="mc-ttl">OT Abiertas</span></div>
                <div class="bar-row"><span class="bar-lbl">Biomédico</span><div class="bar-track"><div class="bar-fill" style="width:65%"></div></div><span class="bar-val">18</span></div>
                <div class="bar-row"><span class="bar-lbl">Industrial</span><div class="bar-track"><div class="bar-fill" style="width:42%"></div></div><span class="bar-val">12</span></div>
                <div class="bar-row"><span class="bar-lbl">IT / Sistemas</span><div class="bar-track"><div class="bar-fill" style="width:28%"></div></div><span class="bar-val">8</span></div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /dash -->
    </div><!-- /mockup-wrap -->

    <div class="float-badge fb1">
      <div class="fb-ico" style="background:#E6F7F5">🔧</div>
      <div><div class="fb-v">47 OT Activas</div><div class="fb-l">Órdenes de trabajo</div></div>
    </div>
  </div><!-- /hero-visual -->
</section>

<!-- MODULES -->
<section class="modules-section" id="modulos">
  <div class="sec-hdr reveal">
    <div class="sec-eyebrow">Módulos del Sistema</div>
    <h2 class="sec-title">Todo lo que necesitas en un solo lugar</h2>
    <p class="sec-sub">Cada módulo diseñado para gestión profesional de activos industriales, biomédicos y clínicos.</p>
  </div>
  <div class="mod-grid">
    <div class="mod-card c-teal reveal"><div class="mod-ico it">🖥️</div><div class="mod-name">Inventario & Activos</div><div class="mod-desc">Registra cada equipo con fotos, ficha técnica, serial, hoja de vida y código QR. Clasificación por tipo, sede y categoría.</div><div class="mod-tags"><span class="mod-tag">QR</span><span class="mod-tag">Foto</span><span class="mod-tag">Ficha técnica</span></div></div>
    <div class="mod-card c-blue reveal"><div class="mod-ico ib">🔧</div><div class="mod-name">Mantenimiento</div><div class="mod-desc">Programa y ejecuta órdenes de trabajo preventivas y correctivas. Costos, adjuntos, firma digital y cierre técnico.</div><div class="mod-tags"><span class="mod-tag">Preventivo</span><span class="mod-tag">Correctivo</span><span class="mod-tag">OT</span></div></div>
    <div class="mod-card c-purple reveal"><div class="mod-ico ip">🎯</div><div class="mod-name">Calibración ISO</div><div class="mod-desc">Genera certificados con puntos de medida, patrones trazables y verificación pública por QR.</div><div class="mod-tags"><span class="mod-tag">ISO</span><span class="mod-tag">Certificado</span><span class="mod-tag">Verificación</span></div></div>
    <div class="mod-card c-amber reveal"><div class="mod-ico ia">📊</div><div class="mod-name">Auditoría & Trazabilidad</div><div class="mod-desc">Cada cambio registrado automáticamente: quién, cuándo y qué modificó. Timeline completo por activo.</div><div class="mod-tags"><span class="mod-tag">Historial</span><span class="mod-tag">Timeline</span><span class="mod-tag">Logs</span></div></div>
    <div class="mod-card c-green reveal"><div class="mod-ico ig">🏢</div><div class="mod-name">Multi-Empresa</div><div class="mod-desc">Gestiona múltiples clientes en una sola instancia. Datos 100% aislados, roles granulares por módulo.</div><div class="mod-tags"><span class="mod-tag">Multi-tenant</span><span class="mod-tag">RBAC</span><span class="mod-tag">Aislamiento</span></div></div>
    <div class="mod-card c-rose reveal"><div class="mod-ico ir">📱</div><div class="mod-name">Código QR</div><div class="mod-desc">Genera e imprime etiquetas QR para cada activo. Escanea desde cualquier móvil para ver su ficha completa.</div><div class="mod-tags"><span class="mod-tag">Etiquetas</span><span class="mod-tag">Móvil</span><span class="mod-tag">PDF</span></div></div>
    <div class="mod-card c-slate reveal"><div class="mod-ico is">📋</div><div class="mod-name">Reportes</div><div class="mod-desc">Genera reportes en PDF y Excel de activos, mantenimientos, calibraciones y disponibilidad por período.</div><div class="mod-tags"><span class="mod-tag">PDF</span><span class="mod-tag">Excel</span><span class="mod-tag">KPI</span></div></div>
    <div class="mod-card c-cyan reveal"><div class="mod-ico ic">⚙️</div><div class="mod-name">Roles & Permisos</div><div class="mod-desc">Control de acceso granular por módulo. Define qué puede ver, crear, editar o eliminar cada usuario.</div><div class="mod-tags"><span class="mod-tag">RBAC</span><span class="mod-tag">Permisos</span><span class="mod-tag">Usuarios</span></div></div>
  </div>
</section>

<!-- STATS -->
<div class="stats-strip">
  <div class="stat-item reveal"><div class="stat-val">10<em>+</em></div><div class="stat-lbl">Módulos</div></div>
  <div class="stat-item reveal"><div class="stat-val">20<em>+</em></div><div class="stat-lbl">Tablas BD</div></div>
  <div class="stat-item reveal"><div class="stat-val">25<em>+</em></div><div class="stat-lbl">APIs AJAX</div></div>
  <div class="stat-item reveal"><div class="stat-val">∞</div><div class="stat-lbl">Empresas</div></div>
  <div class="stat-item reveal"><div class="stat-val">100<em>%</em></div><div class="stat-lbl">Trazabilidad</div></div>
</div>

<!-- CTA -->
<section class="cta-section">
  <div class="cta-badge">🔒 Acceso Seguro · Multi-Tenant</div>
  <h2 class="cta-title">¿Listo para gestionar<br>tus <span class="teal">activos</span>?</h2>
  <p class="cta-sub">Ingresa a la plataforma con tus credenciales y accede a todas las herramientas de gestión empresarial.</p>
  <a href="index.php?route=login" class="btn-cta">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
    Ingresar a GeoActivos
  </a>
  <div class="cta-note">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    Acceso seguro · RBAC · Datos cifrados
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="foot-logo">
    <div class="foot-logo-ico">
      <svg viewBox="0 0 48 48" fill="none" width="15" height="15"><polygon points="24,3 43,13.5 43,34.5 24,45 5,34.5 5,13.5" stroke="rgba(255,255,255,.9)" stroke-width="2.5" fill="none"/><circle cx="24" cy="24" r="5" fill="rgba(255,255,255,.9)"/></svg>
    </div>
    <span class="foot-logo-name">GeoActivos</span>
  </div>
  <span class="foot-copy">© 2026 GeoActivos · GeSaProv Project Design · Todos los derechos reservados</span>
  <div class="foot-badges">
    <span class="foot-badge">Multi-Tenant</span>
    <span class="foot-badge">ISO Ready</span>
    <span class="foot-badge">RBAC</span>
  </div>
</footer>

<script>
const obs = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      setTimeout(() => entry.target.classList.add('visible'), entry.target.dataset.delay || 0);
    }
  });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal').forEach((el, i) => {
  el.dataset.delay = (i % 4) * 80;
  obs.observe(el);
});
</script>
</body>
</html>
