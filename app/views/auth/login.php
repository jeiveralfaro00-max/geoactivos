<?php
// app/views/auth/login.php
if (Auth::check()) redirect('index.php?route=dashboard');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = (string)($_POST['password'] ?? '');
  list($ok, $msg) = Auth::attempt($email, $pass);
  if ($ok) redirect('index.php?route=dashboard');
  $error = $msg ?: 'No se pudo iniciar sesión.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GeoActivos — Iniciar Sesión</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

:root {
  --teal:       #0BA896;
  --teal-dark:  #077D6E;
  --teal-light: #E6F7F5;
  --teal-mid:   #C2EDE9;
  --slate-900:  #0F1D2E;
  --slate-700:  #2C4258;
  --slate-500:  #4E6D8C;
  --slate-300:  #9DB8CC;
  --slate-100:  #EBF1F6;
  --slate-50:   #F5F8FA;
  --white:      #FFFFFF;
  --danger:     #E53E3E;
  --danger-bg:  #FFF5F5;
  --shadow-sm:  0 1px 3px rgba(15,29,46,0.08), 0 1px 2px rgba(15,29,46,0.04);
  --shadow-md:  0 4px 16px rgba(15,29,46,0.10), 0 2px 4px rgba(15,29,46,0.06);
  --shadow-lg:  0 20px 48px rgba(15,29,46,0.12), 0 8px 16px rgba(15,29,46,0.06);
}

html, body {
  height: 100%;
  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--slate-50);
  color: var(--slate-900);
  overflow: hidden;
}

/* ── LAYOUT ── */
.layout {
  display: flex;
  height: 100vh;
}

/* ── LEFT PANEL ── */
.left {
  flex: 1;
  background: var(--white);
  border-right: 1px solid var(--slate-100);
  display: flex;
  flex-direction: column;
  padding: 0;
  overflow: hidden;
  position: relative;
}

/* Subtle geometric background */
.left::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 70% 60% at 20% 15%, rgba(11,168,150,0.07) 0%, transparent 70%),
    radial-gradient(ellipse 50% 40% at 80% 85%, rgba(11,168,150,0.05) 0%, transparent 70%);
  pointer-events: none;
}

.left-inner {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 40px 56px;
}

/* TOP NAV */
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: auto;
  animation: fadeUp 0.6s ease both;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, var(--teal), var(--teal-dark));
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(11,168,150,0.30);
  flex-shrink: 0;
}

.logo-icon svg {
  width: 22px;
  height: 22px;
}

.logo-texts {
  display: flex;
  flex-direction: column;
}

.logo-name {
  font-family: 'Sora', sans-serif;
  font-weight: 700;
  font-size: 1.2rem;
  letter-spacing: -0.3px;
  color: var(--slate-900);
  line-height: 1.1;
}

.logo-tagline {
  font-size: 0.65rem;
  font-weight: 500;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--slate-300);
}

.badge {
  font-size: 0.68rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--teal);
  background: var(--teal-light);
  border: 1px solid var(--teal-mid);
  padding: 5px 13px;
  border-radius: 100px;
}

/* HERO CONTENT */
.hero {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 32px 0;
}

.eyebrow {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
  animation: fadeUp 0.6s 0.1s ease both;
}

.eyebrow-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--teal);
}

.eyebrow-text {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--teal);
}

.hero-heading {
  font-family: 'Sora', sans-serif;
  font-size: clamp(2.4rem, 3.5vw, 3.6rem);
  font-weight: 700;
  line-height: 1.08;
  letter-spacing: -1px;
  margin-bottom: 16px;
  animation: fadeUp 0.6s 0.15s ease both;
}

.hero-heading .line-teal {
  color: var(--teal);
}

.hero-heading .line-dark {
  color: var(--slate-900);
}

.hero-sub {
  font-size: 1rem;
  font-weight: 400;
  color: var(--slate-500);
  line-height: 1.7;
  max-width: 420px;
  margin-bottom: 36px;
  animation: fadeUp 0.6s 0.2s ease both;
}

/* FEATURE CARDS CAROUSEL */
.fshow-wrap {
  animation: fadeUp 0.6s 0.25s ease both;
  margin-bottom: 28px;
}

.fshow {
  position: relative;
  height: 120px;
}

.fcard {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  gap: 20px;
  background: var(--white);
  border: 1px solid var(--slate-100);
  border-radius: 16px;
  padding: 20px 24px;
  box-shadow: var(--shadow-md);
  opacity: 0;
  transform: translateY(12px);
  transition: opacity 0.5s ease, transform 0.5s ease;
  pointer-events: none;
  overflow: hidden;
}

.fcard::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--teal), rgba(11,168,150,0.2));
  border-radius: 16px 16px 0 0;
}

.fcard.active {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.fcard.exit {
  opacity: 0;
  transform: translateY(-10px);
}

.fcard-icon {
  width: 52px;
  height: 52px;
  background: var(--teal-light);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.fcard-body {
  flex: 1;
  min-width: 0;
}

.fcard-module {
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--teal);
  margin-bottom: 4px;
}

.fcard-title {
  font-family: 'Sora', sans-serif;
  font-size: 1rem;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 4px;
}

.fcard-desc {
  font-size: 0.78rem;
  color: var(--slate-500);
  line-height: 1.5;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.fcard-kpis {
  display: flex;
  flex-direction: column;
  gap: 4px;
  text-align: right;
  flex-shrink: 0;
}

.kpi-val {
  font-family: 'Sora', sans-serif;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--slate-900);
  line-height: 1;
}

.kpi-lbl {
  font-size: 0.6rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--slate-300);
}

.fbar {
  position: absolute;
  bottom: 0; left: 0;
  height: 2px;
  background: var(--teal);
  border-radius: 0 0 16px 0;
}
.fbar.run { animation: barFill 4.5s linear forwards; }
@keyframes barFill { from { width: 0% } to { width: 100% } }

/* DOTS */
.fdots {
  display: flex;
  gap: 5px;
  margin-top: 10px;
}

.fdot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--slate-100);
  border: none;
  cursor: pointer;
  transition: all 0.25s;
}

.fdot.on {
  width: 20px;
  border-radius: 3px;
  background: var(--teal);
}

/* STATS BAR */
.stats {
  display: flex;
  gap: 0;
  padding: 20px 24px;
  background: var(--slate-50);
  border: 1px solid var(--slate-100);
  border-radius: 14px;
  animation: fadeUp 0.6s 0.3s ease both;
}

.sitem {
  flex: 1;
  text-align: center;
  padding: 0 16px;
  border-right: 1px solid var(--slate-100);
}
.sitem:last-child { border-right: none; }

.sv {
  font-family: 'Sora', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--slate-900);
  line-height: 1;
  margin-bottom: 4px;
}

.sv em {
  font-style: normal;
  color: var(--teal);
}

.sl {
  font-size: 0.62rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--slate-300);
}

/* FOOTER LEFT */
.left-footer {
  padding-top: 24px;
  display: flex;
  align-items: center;
  gap: 8px;
  animation: fadeUp 0.6s 0.35s ease both;
}

.cert-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--slate-300);
  background: var(--white);
  border: 1px solid var(--slate-100);
  padding: 5px 11px;
  border-radius: 100px;
}

.cert-badge span { font-size: 0.8rem; }

/* ── RIGHT PANEL ── */
.right {
  width: 460px;
  flex-shrink: 0;
  background: var(--slate-50);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px;
  position: relative;
}

/* Soft bg decoration */
.right::before {
  content: '';
  position: absolute;
  bottom: -80px;
  right: -80px;
  width: 360px;
  height: 360px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(11,168,150,0.08) 0%, transparent 70%);
  pointer-events: none;
}

.right::after {
  content: '';
  position: absolute;
  top: -60px;
  left: -60px;
  width: 260px;
  height: 260px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(11,168,150,0.06) 0%, transparent 70%);
  pointer-events: none;
}

/* LOGIN CARD */
.lcard {
  width: 100%;
  max-width: 380px;
  background: var(--white);
  border: 1px solid #E0EBF2;
  border-radius: 24px;
  padding: 40px 36px;
  box-shadow: var(--shadow-lg);
  position: relative;
  z-index: 1;
  animation: slideIn 0.65s cubic-bezier(0.22, 1, 0.36, 1) both;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(20px) scale(0.98); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* CARD TOP ACCENT */
.card-accent {
  position: absolute;
  top: 0; left: 24px; right: 24px;
  height: 3px;
  background: linear-gradient(90deg, var(--teal), rgba(11,168,150,0.3));
  border-radius: 0 0 4px 4px;
}

/* LOGO INSIDE CARD */
.card-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 28px;
}

.card-logo-icon {
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, var(--teal), var(--teal-dark));
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(11,168,150,0.25);
}

.card-logo-icon svg { width: 22px; height: 22px; }

.card-logo-texts .name {
  font-family: 'Sora', sans-serif;
  font-weight: 700;
  font-size: 1.05rem;
  color: var(--slate-900);
  line-height: 1.1;
}

.card-logo-texts .sub {
  font-size: 0.65rem;
  font-weight: 500;
  color: var(--slate-300);
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

.card-title {
  font-family: 'Sora', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: -0.4px;
  color: var(--slate-900);
  margin-bottom: 6px;
}

.card-subtitle {
  font-size: 0.85rem;
  color: var(--slate-500);
  margin-bottom: 28px;
  line-height: 1.5;
}

/* STATUS */
.status-row {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--teal);
  margin-bottom: 24px;
  padding: 8px 14px;
  background: var(--teal-light);
  border-radius: 8px;
  border: 1px solid var(--teal-mid);
}

.status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--teal);
  animation: pulse 2.5s infinite;
  flex-shrink: 0;
}

@keyframes pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(11,168,150,0.4); }
  50%       { box-shadow: 0 0 0 5px rgba(11,168,150,0); }
}

/* ERROR */
.error-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  background: var(--danger-bg);
  border: 1px solid rgba(229,62,62,0.2);
  border-radius: 10px;
  color: var(--danger);
  font-size: 0.84rem;
  margin-bottom: 18px;
  animation: fadeUp 0.3s ease both;
}

.error-alert svg { width: 16px; height: 16px; flex-shrink: 0; }

/* FORM */
.f-group { margin-bottom: 16px; }

.f-label {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  color: var(--slate-700);
  margin-bottom: 7px;
}

.f-wrap { position: relative; }

.f-wrap input {
  width: 100%;
  background: var(--slate-50);
  border: 1.5px solid #D6E4EF;
  border-radius: 10px;
  padding: 12px 42px 12px 14px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.9rem;
  color: var(--slate-900);
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}

.f-wrap input::placeholder { color: var(--slate-300); }

.f-wrap input:focus {
  border-color: var(--teal);
  background: var(--white);
  box-shadow: 0 0 0 3px rgba(11,168,150,0.12);
}

.f-ico {
  position: absolute;
  right: 13px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--slate-300);
  display: flex;
  align-items: center;
  pointer-events: none;
}

/* SUBMIT BUTTON */
.btn-login {
  width: 100%;
  margin-top: 4px;
  background: linear-gradient(135deg, var(--teal), var(--teal-dark));
  border: none;
  border-radius: 10px;
  padding: 13px 20px;
  color: #fff;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.92rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: transform 0.18s, box-shadow 0.18s, filter 0.18s;
  box-shadow: 0 4px 16px rgba(11,168,150,0.35);
  position: relative;
  overflow: hidden;
}

.btn-login::after {
  content: '';
  position: absolute;
  top: 0; left: -100%;
  width: 60%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
  transform: skewX(-20deg);
  animation: shimmer 3s ease infinite;
}
@keyframes shimmer { 0%{left:-100%} 100%{left:160%} }

.btn-login:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(11,168,150,0.45);
  filter: brightness(1.05);
}

.btn-login:active { transform: translateY(0); }

/* DEMO BOX */
.demo-box {
  margin-top: 18px;
  padding: 11px 14px;
  background: #FFFBF0;
  border: 1px solid #F5E6B2;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 9px;
}

.demo-icon { font-size: 0.9rem; flex-shrink: 0; }

.demo-txt {
  font-size: 0.75rem;
  color: #8A6D00;
  line-height: 1.4;
}

.demo-txt strong { font-weight: 700; color: #6B5200; }

/* DIVIDER */
.f-div {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 20px 0 0;
}

.f-div-line {
  flex: 1;
  height: 1px;
  background: var(--slate-100);
}

.f-div-txt {
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--slate-300);
  white-space: nowrap;
}

/* FOOTER */
.lc-foot {
  text-align: center;
  font-size: 0.67rem;
  color: var(--slate-300);
  margin-top: 16px;
  line-height: 1.7;
}

/* ANIMATIONS */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .left { display: none; }
  .right { width: 100%; }
}
</style>
</head>
<body>

<div class="layout">

  <!-- ══ LEFT PANEL ══ -->
  <div class="left">
    <div class="left-inner">

      <!-- TOPBAR -->
      <div class="topbar">
        <div class="logo">
          <div class="logo-icon">
            <svg viewBox="0 0 48 48" fill="none">
              <defs>
                <linearGradient id="lg" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                  <stop offset="0%" stop-color="#fff" stop-opacity="0.95"/>
                  <stop offset="100%" stop-color="#C2EDE9"/>
                </linearGradient>
              </defs>
              <polygon points="24,3 43,13.5 43,34.5 24,45 5,34.5 5,13.5" stroke="url(#lg)" stroke-width="2.2" fill="none"/>
              <polygon points="24,11 36,18 36,30 24,37 12,30 12,18" stroke="url(#lg)" stroke-width="1.4" fill="rgba(255,255,255,0.1)"/>
              <circle cx="24" cy="24" r="5" fill="url(#lg)"/>
              <circle cx="24" cy="24" r="2" fill="rgba(11,168,150,0.8)"/>
              <line x1="24" y1="11" x2="24" y2="19" stroke="url(#lg)" stroke-width="1.5" stroke-linecap="round"/>
              <line x1="24" y1="29" x2="24" y2="37" stroke="url(#lg)" stroke-width="1.5" stroke-linecap="round"/>
              <line x1="12.5" y1="18.5" x2="19" y2="21.5" stroke="url(#lg)" stroke-width="1.4" stroke-linecap="round"/>
              <line x1="29" y1="26.5" x2="35.5" y2="29.5" stroke="url(#lg)" stroke-width="1.4" stroke-linecap="round"/>
              <line x1="35.5" y1="18.5" x2="29" y2="21.5" stroke="url(#lg)" stroke-width="1.4" stroke-linecap="round"/>
              <line x1="19" y1="26.5" x2="12.5" y2="29.5" stroke="url(#lg)" stroke-width="1.4" stroke-linecap="round"/>
            </svg>
          </div>
          <div class="logo-texts">
            <span class="logo-name">GeoActivos</span>
            <span class="logo-tagline">Asset Management Platform</span>
          </div>
        </div>
        <span class="badge">GeSaProv Project</span>
      </div>

      <!-- HERO -->
      <div class="hero">
        <div class="eyebrow">
          <div class="eyebrow-dot"></div>
          <span class="eyebrow-text">Plataforma Industrial · Biomédica · Clínica</span>
        </div>

        <h1 class="hero-heading">
          <span class="line-dark">Gestión Total</span><br>
          <span class="line-teal">de Activos</span>
        </h1>

        <p class="hero-sub">
          Controla, monitorea y optimiza cada equipo de tu empresa — ventiladores, monitores ECG, computadores, maquinaria — en una sola plataforma inteligente.
        </p>

        <!-- FEATURE CAROUSEL -->
        <div class="fshow-wrap">
          <div class="fshow">

            <div class="fcard active" id="fc0">
              <div class="fcard-icon">🖥️</div>
              <div class="fcard-body">
                <div class="fcard-module">Inventario</div>
                <div class="fcard-title">Activos Biomédicos</div>
                <div class="fcard-desc">Ventiladores, monitores ECG, desfibriladores y más con fotos, serial y hoja de vida.</div>
              </div>
              <div class="fcard-kpis">
                <div><div class="kpi-val">QR</div><div class="kpi-lbl">Etiquetas</div></div>
              </div>
              <div class="fbar run" id="fb0"></div>
            </div>

            <div class="fcard" id="fc1">
              <div class="fcard-icon">🔧</div>
              <div class="fcard-body">
                <div class="fcard-module">Mantenimiento</div>
                <div class="fcard-title">Preventivo & Correctivo</div>
                <div class="fcard-desc">Programa y cierra órdenes de trabajo con costos, firmas digitales y adjuntos técnicos.</div>
              </div>
              <div class="fcard-kpis">
                <div><div class="kpi-val">✍️</div><div class="kpi-lbl">Firmas</div></div>
              </div>
              <div class="fbar" id="fb1"></div>
            </div>

            <div class="fcard" id="fc2">
              <div class="fcard-icon">🎯</div>
              <div class="fcard-body">
                <div class="fcard-module">Calibración</div>
                <div class="fcard-title">Certificados ISO</div>
                <div class="fcard-desc">Genera certificados con patrones trazables y verificación pública por código QR.</div>
              </div>
              <div class="fcard-kpis">
                <div><div class="kpi-val">ISO</div><div class="kpi-lbl">Certificado</div></div>
              </div>
              <div class="fbar" id="fb2"></div>
            </div>

            <div class="fcard" id="fc3">
              <div class="fcard-icon">📊</div>
              <div class="fcard-body">
                <div class="fcard-module">Auditoría</div>
                <div class="fcard-title">Trazabilidad Total</div>
                <div class="fcard-desc">Cada cambio registrado: quién, cuándo y qué modificó. Timeline completo por activo.</div>
              </div>
              <div class="fcard-kpis">
                <div><div class="kpi-val">∞</div><div class="kpi-lbl">Historial</div></div>
              </div>
              <div class="fbar" id="fb3"></div>
            </div>

            <div class="fcard" id="fc4">
              <div class="fcard-icon">🏢</div>
              <div class="fcard-body">
                <div class="fcard-module">Multi-Tenant</div>
                <div class="fcard-title">Varias Empresas</div>
                <div class="fcard-desc">Gestiona múltiples clientes en una sola instancia con RBAC y datos 100% aislados.</div>
              </div>
              <div class="fcard-kpis">
                <div><div class="kpi-val">RBAC</div><div class="kpi-lbl">Roles</div></div>
              </div>
              <div class="fbar" id="fb4"></div>
            </div>

          </div>
          <div class="fdots">
            <button class="fdot on" onclick="goTo(0)"></button>
            <button class="fdot" onclick="goTo(1)"></button>
            <button class="fdot" onclick="goTo(2)"></button>
            <button class="fdot" onclick="goTo(3)"></button>
            <button class="fdot" onclick="goTo(4)"></button>
          </div>
        </div>

        <!-- STATS -->
        <div class="stats">
          <div class="sitem"><div class="sv">10<em>+</em></div><div class="sl">Módulos</div></div>
          <div class="sitem"><div class="sv">20<em>+</em></div><div class="sl">Tablas BD</div></div>
          <div class="sitem"><div class="sv">25<em>+</em></div><div class="sl">APIs AJAX</div></div>
          <div class="sitem"><div class="sv">∞</div><div class="sl">Empresas</div></div>
        </div>
      </div>

      <!-- BOTTOM BADGES -->
      <div class="left-footer">
        <div class="cert-badge"><span>🔒</span> Multi-Tenant</div>
        <div class="cert-badge"><span>✅</span> ISO Ready</div>
        <div class="cert-badge"><span>📱</span> QR Codes</div>
        <div class="cert-badge"><span>📋</span> Auditoría</div>
      </div>

    </div>
  </div>

  <!-- ══ RIGHT PANEL ══ -->
  <div class="right">
    <div class="lcard">
      <div class="card-accent"></div>

      <!-- LOGO -->
      <div class="card-logo">
        <div class="card-logo-icon">
          <svg viewBox="0 0 48 48" fill="none">
            <defs>
              <linearGradient id="clg" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#fff" stop-opacity="0.95"/>
                <stop offset="100%" stop-color="#C2EDE9"/>
              </linearGradient>
            </defs>
            <polygon points="24,3 43,13.5 43,34.5 24,45 5,34.5 5,13.5" stroke="url(#clg)" stroke-width="2.2" fill="none"/>
            <circle cx="24" cy="24" r="5" fill="url(#clg)"/>
            <circle cx="24" cy="24" r="2" fill="rgba(11,120,100,0.7)"/>
            <line x1="24" y1="12" x2="24" y2="19" stroke="url(#clg)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="24" y1="29" x2="24" y2="36" stroke="url(#clg)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="13" y1="19" x2="19" y2="22" stroke="url(#clg)" stroke-width="1.4" stroke-linecap="round"/>
            <line x1="29" y1="26" x2="35" y2="29" stroke="url(#clg)" stroke-width="1.4" stroke-linecap="round"/>
            <line x1="35" y1="19" x2="29" y2="22" stroke="url(#clg)" stroke-width="1.4" stroke-linecap="round"/>
            <line x1="19" y1="26" x2="13" y2="29" stroke="url(#clg)" stroke-width="1.4" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="card-logo-texts">
          <div class="name">GeoActivos</div>
          <div class="sub">Asset Management</div>
        </div>
      </div>

      <h2 class="card-title">Bienvenido de nuevo</h2>
      <p class="card-subtitle">Ingresa tus credenciales para acceder a la plataforma.</p>

      <!-- STATUS -->
      <div class="status-row">
        <div class="status-dot"></div>
        Sistema operativo · Todos los servicios activos
      </div>

      <!-- ERROR -->
      <?php if (!empty($error)): ?>
      <div class="error-alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span><?= e($error) ?></span>
      </div>
      <?php endif; ?>

      <!-- FORM -->
      <form method="post" autocomplete="off">
        <div class="f-group">
          <label class="f-label">Correo electrónico</label>
          <div class="f-wrap">
            <input
              type="email"
              name="email"
              placeholder="usuario@empresa.com"
              required
              autofocus
              value="<?= e($_POST['email'] ?? '') ?>"
            >
            <span class="f-ico">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
            </span>
          </div>
        </div>

        <div class="f-group">
          <label class="f-label">Contraseña</label>
          <div class="f-wrap">
            <input
              type="password"
              name="password"
              placeholder="••••••••••"
              required
            >
            <span class="f-ico">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </span>
          </div>
        </div>

        <button type="submit" class="btn-login">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <polyline points="10 17 15 12 10 7"/>
            <line x1="15" y1="12" x2="3" y2="12"/>
          </svg>
          Entrar a GeoActivos
        </button>
      </form>

      <!-- DEMO -->
      <div class="demo-box">
        <span class="demo-icon">🔑</span>
        <div class="demo-txt">
          Demo: <strong>admin@demo.com</strong> · <strong>Admin123*</strong>
        </div>
      </div>

      <div class="f-div">
        <div class="f-div-line"></div>
        <span class="f-div-txt">GeoActivos Enterprise</span>
        <div class="f-div-line"></div>
      </div>

      <div class="lc-foot">
        Plataforma Multi-Tenant · RBAC · Auditoría · ISO<br>
        © 2026 GeoActivos · Todos los derechos reservados
      </div>
    </div>
  </div>

</div>

<script>
// ── Feature Carousel ──
let cur = 0;
const total = 5;

function goTo(idx) {
  const oc = document.getElementById('fc' + cur);
  const ob = document.getElementById('fb' + cur);
  const od = document.querySelectorAll('.fdot')[cur];

  oc.classList.remove('active');
  oc.classList.add('exit');
  ob.classList.remove('run');
  od.classList.remove('on');

  const prev = cur;
  setTimeout(() => {
    document.getElementById('fc' + prev).classList.remove('exit');
    cur = idx;
    const nc = document.getElementById('fc' + cur);
    const nb = document.getElementById('fb' + cur);
    const nd = document.querySelectorAll('.fdot')[cur];
    nc.classList.add('active');
    nd.classList.add('on');
    nb.classList.remove('run');
    void nb.offsetWidth;
    nb.classList.add('run');
  }, 400);
}

let auto = setInterval(() => goTo((cur + 1) % total), 4500);
const fshow = document.querySelector('.fshow');
fshow.addEventListener('mouseenter', () => clearInterval(auto));
fshow.addEventListener('mouseleave', () => { auto = setInterval(() => goTo((cur + 1) % total), 4500); });
</script>
</body>
</html>
