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
<title>GeoActivos — Sistema de Gestión de Activos</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Bebas+Neue&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --blue:#1a6fff;
  --cyan:#00e5ff;
  --teal:#00bfa5;
  --dark:#060c1a;
  --darker:#03060f;
  --glass:rgba(6,12,26,0.82);
  --text:#f0f6ff;
  --muted:#4e6d8c;
  --green:#00e676;
  --gold:#ffb300;
  --danger:#ef4444;
}
html,body{height:100vh;overflow:hidden;font-family:'Space Grotesk',sans-serif;background:var(--darker);color:var(--text);}
#bgCanvas{position:fixed;inset:0;z-index:0;}
.overlay-grid{position:fixed;inset:0;z-index:1;background-image:linear-gradient(rgba(26,111,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(26,111,255,0.04) 1px,transparent 1px);background-size:56px 56px;pointer-events:none;}
.overlay-fade{position:fixed;inset:0;z-index:1;background:linear-gradient(110deg,rgba(3,6,15,0.97) 0%,rgba(3,6,15,0.75) 50%,rgba(3,6,15,0.15) 100%),linear-gradient(to top,rgba(3,6,15,0.95) 0%,transparent 40%),linear-gradient(to bottom,rgba(3,6,15,0.7) 0%,transparent 20%);pointer-events:none;}
.vignette{position:fixed;inset:0;z-index:1;background:radial-gradient(ellipse at center,transparent 35%,rgba(3,6,15,0.75) 100%);pointer-events:none;}
.scan-line{position:fixed;left:0;width:100%;height:1px;z-index:4;background:linear-gradient(90deg,transparent,rgba(0,229,255,0.4),rgba(0,229,255,0.8),rgba(0,229,255,0.4),transparent);animation:scanMove 12s linear infinite;pointer-events:none;opacity:.5;}
@keyframes scanMove{0%{top:-2px;opacity:0}5%{opacity:.5}95%{opacity:.4}100%{top:100vh;opacity:0}}
.glow-blob{position:fixed;border-radius:50%;filter:blur(120px);pointer-events:none;z-index:1;animation:blobFloat 10s ease-in-out infinite alternate;}
.glow-blob.b1{width:600px;height:400px;background:rgba(26,111,255,0.07);left:-150px;top:20%;}
.glow-blob.b2{width:500px;height:500px;background:rgba(0,229,255,0.05);right:-100px;top:30%;animation-delay:-4s;}
.glow-blob.b3{width:400px;height:300px;background:rgba(0,191,165,0.06);left:30%;bottom:0;animation-delay:-7s;}
@keyframes blobFloat{from{transform:translateY(0) scale(1)}to{transform:translateY(-40px) scale(1.05)}}
.layout{position:relative;z-index:10;display:flex;width:100%;height:100vh;}

/* LEFT */
.left{flex:1;display:flex;flex-direction:column;padding:44px 64px;justify-content:space-between;overflow:hidden;}
.topbar{display:flex;align-items:center;justify-content:space-between;animation:riseUp .9s ease both;}
.logo{display:flex;align-items:center;gap:14px;}
.logo-svg-wrap{position:relative;width:48px;height:48px;flex-shrink:0;}
.logo-svg-wrap svg{width:48px;height:48px;display:block;}
.logo-ring{position:absolute;inset:-3px;border-radius:14px;background:conic-gradient(from 0deg,var(--blue),var(--cyan),var(--teal),var(--blue));animation:spinRing 5s linear infinite;z-index:-1;filter:blur(1px);opacity:.8;}
@keyframes spinRing{to{transform:rotate(360deg)}}
.logo-text-wrap{display:flex;flex-direction:column;gap:1px;}
.logo-name{font-family:'Bebas Neue',cursive;font-size:1.9rem;letter-spacing:2px;line-height:1;background:linear-gradient(90deg,#fff 0%,var(--cyan) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.logo-tagline{font-size:.58rem;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--muted);}
.badge-ent{font-size:.6rem;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:var(--cyan);border:1px solid rgba(0,229,255,0.25);background:rgba(0,229,255,0.06);padding:5px 13px;border-radius:100px;}

/* HERO */
.hero{flex:1;display:flex;flex-direction:column;justify-content:center;padding-bottom:10px;}
.eyebrow{display:flex;align-items:center;gap:12px;margin-bottom:20px;animation:riseUp .9s .1s ease both;}
.eyebrow-bar{width:40px;height:2px;background:linear-gradient(90deg,var(--blue),var(--cyan));border-radius:2px;}
.eyebrow-txt{font-size:.7rem;font-weight:700;letter-spacing:3.5px;text-transform:uppercase;color:var(--cyan);}
.hero-h1{font-family:'Bebas Neue',cursive;font-size:clamp(3.5rem,5.5vw,5.8rem);line-height:.95;letter-spacing:2px;margin-bottom:18px;animation:riseUp .9s .15s ease both;}
.hero-h1 .line-grad{display:block;background:linear-gradient(95deg,#60a5fa 0%,var(--cyan) 50%,#a5f3fc 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.hero-h1 .line-white{display:block;color:#fff;}
.hero-p{font-size:1rem;font-weight:300;color:rgba(180,210,240,.7);line-height:1.75;max-width:430px;margin-bottom:36px;animation:riseUp .9s .2s ease both;}

/* PILLS */
.pills{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:44px;animation:riseUp .9s .25s ease both;}
.pill{display:flex;align-items:center;gap:7px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:100px;padding:6px 14px;font-size:.78rem;font-weight:500;color:rgba(200,225,255,.8);transition:all .3s;backdrop-filter:blur(8px);cursor:default;}
.pill:hover{background:rgba(26,111,255,0.12);border-color:rgba(26,111,255,.4);color:#fff;transform:translateY(-2px);box-shadow:0 4px 16px rgba(26,111,255,.15);}
.pill-i{font-size:.9rem;}

/* FEATURE CAROUSEL */
.fshow{position:relative;animation:riseUp .9s .3s ease both;}
.fcard{position:absolute;inset:0;display:flex;background:rgba(6,12,26,0.75);border:1px solid rgba(26,111,255,0.15);border-radius:18px;overflow:hidden;backdrop-filter:blur(24px);box-shadow:0 24px 60px rgba(0,0,0,.45),inset 0 1px 0 rgba(255,255,255,0.04);opacity:0;transform:translateY(16px);transition:opacity .7s ease,transform .7s ease;pointer-events:none;min-height:130px;}
.fcard::before{content:'';position:absolute;top:0;left:10%;right:10%;height:1px;background:linear-gradient(90deg,transparent,var(--blue),var(--cyan),transparent);opacity:.7;}
.fcard.active{opacity:1;transform:translateY(0);pointer-events:auto;}
.fcard.exit{opacity:0;transform:translateY(-16px);}
.fcard-icon-panel{width:130px;flex-shrink:0;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
.fcard-icon-panel::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent 50%,rgba(6,12,26,.9) 100%);}
.fcard-big-icon{font-size:3.5rem;filter:drop-shadow(0 0 24px rgba(0,229,255,.5));animation:iconFloat 4s ease-in-out infinite;position:relative;z-index:1;}
@keyframes iconFloat{0%,100%{transform:translateY(0) scale(1);}50%{transform:translateY(-6px) scale(1.05);}}
.fcard-body{flex:1;padding:22px 26px;display:flex;flex-direction:column;justify-content:center;}
.fcard-module{font-size:.62rem;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:var(--cyan);margin-bottom:7px;}
.fcard-title{font-family:'Bebas Neue',cursive;font-size:1.5rem;letter-spacing:1px;margin-bottom:7px;line-height:1.1;}
.fcard-desc{font-size:.82rem;color:rgba(170,200,230,.65);line-height:1.55;margin-bottom:12px;}
.fcard-kpis{display:flex;gap:20px;}
.kpi-v{font-family:'Bebas Neue',cursive;font-size:1.25rem;letter-spacing:1px;color:var(--text);}
.kpi-v em{font-style:normal;color:var(--cyan);}
.kpi-l{font-size:.63rem;text-transform:uppercase;letter-spacing:1.2px;color:var(--muted);}
.fbar{position:absolute;bottom:0;left:0;height:2px;background:linear-gradient(90deg,var(--blue),var(--cyan));}
.fbar.run{animation:barFill 4.5s linear forwards;}
@keyframes barFill{from{width:0%}to{width:100%}}
.fdots{display:flex;gap:6px;margin-top:12px;}
.fdot{width:5px;height:5px;border-radius:50%;background:rgba(74,106,138,.5);cursor:pointer;border:none;transition:all .3s;}
.fdot.on{width:20px;border-radius:3px;background:var(--blue);}

/* STATS */
.stats{display:flex;align-items:center;animation:riseUp .9s .35s ease both;}
.sitem{padding-right:28px;margin-right:28px;border-right:1px solid rgba(26,111,255,.12);}
.sitem:last-child{border-right:none;margin-right:0;padding-right:0;}
.sv{font-family:'Bebas Neue',cursive;font-size:1.8rem;letter-spacing:1px;line-height:1;margin-bottom:3px;}
.sv em{font-style:normal;color:var(--cyan);}
.sl{font-size:.65rem;text-transform:uppercase;letter-spacing:1.5px;color:var(--muted);}

/* RIGHT LOGIN */
.right{width:420px;display:flex;align-items:center;justify-content:center;padding:32px;position:relative;}
.right::before{content:'';position:absolute;left:0;top:12%;bottom:12%;width:1px;background:linear-gradient(to bottom,transparent,rgba(26,111,255,.5),rgba(0,229,255,.8),rgba(26,111,255,.5),transparent);animation:sepGlow 4s ease-in-out infinite;}
@keyframes sepGlow{0%,100%{opacity:.4}50%{opacity:1}}
.lcard{width:100%;background:rgba(4,8,22,0.85);border:1px solid rgba(26,111,255,.18);border-radius:24px;padding:42px 36px;backdrop-filter:blur(36px);box-shadow:0 50px 100px rgba(0,0,0,.6),0 0 0 1px rgba(26,111,255,.07),inset 0 1px 0 rgba(255,255,255,.05);position:relative;animation:slideIn .9s .2s cubic-bezier(.22,1,.36,1) both;}
@keyframes slideIn{from{opacity:0;transform:translateX(32px) scale(.97)}to{opacity:1;transform:translateX(0) scale(1)}}
.lcard::before{content:'';position:absolute;top:0;left:12%;right:12%;height:1px;background:linear-gradient(90deg,transparent,var(--blue),var(--cyan),transparent);opacity:.9;}

/* LOGO IN CARD */
.lc-logo-wrap{display:flex;align-items:center;justify-content:center;margin-bottom:22px;}
.lc-logo-inner{position:relative;width:64px;height:64px;}
.lc-spin-ring{position:absolute;inset:-4px;border-radius:20px;background:conic-gradient(from 0deg,var(--blue),var(--cyan),var(--teal),transparent,var(--blue));animation:spinRing 4s linear infinite;opacity:.8;}
.lc-logo-box{width:64px;height:64px;background:linear-gradient(135deg,#0a52cc,#5b21b6);border-radius:18px;display:flex;align-items:center;justify-content:center;box-shadow:0 0 40px rgba(26,111,255,.45),0 0 80px rgba(26,111,255,.15);position:relative;z-index:1;}
.lc-logo-box svg{width:32px;height:32px;}
.lc-title{font-family:'Bebas Neue',cursive;font-size:1.6rem;letter-spacing:2px;text-align:center;margin-bottom:4px;}
.lc-sub{font-size:.8rem;color:var(--muted);text-align:center;margin-bottom:6px;}
.lc-status{display:flex;align-items:center;justify-content:center;gap:7px;margin-bottom:28px;font-size:.7rem;font-weight:600;color:var(--green);letter-spacing:.5px;}
.status-dot{width:7px;height:7px;border-radius:50%;background:var(--green);box-shadow:0 0 8px var(--green);animation:statusPulse 2s infinite;}
@keyframes statusPulse{0%,100%{box-shadow:0 0 6px var(--green)}50%{box-shadow:0 0 16px var(--green),0 0 28px rgba(0,230,118,.3)}}

/* ERROR */
.error-alert{margin-bottom:20px;padding:13px 15px;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.3);border-radius:12px;color:#fca5a5;font-size:.85rem;display:flex;align-items:center;gap:10px;animation:slideDown .3s ease;}
.error-alert svg{width:17px;height:17px;flex-shrink:0;}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}

/* FORM */
.f-group{margin-bottom:16px;}
.f-label{display:block;margin-bottom:7px;font-size:.68rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);}
.f-wrap{position:relative;}
.f-wrap input{width:100%;background:rgba(255,255,255,0.04);border:1.5px solid rgba(26,111,255,.18);border-radius:12px;padding:13px 44px 13px 15px;color:var(--text);font-family:'Space Grotesk',sans-serif;font-size:.92rem;outline:none;transition:all .3s;}
.f-wrap input:focus{border-color:var(--blue);background:rgba(26,111,255,.07);box-shadow:0 0 0 4px rgba(26,111,255,.1);}
.f-wrap input::placeholder{color:rgba(78,109,140,.7);}
.f-ico{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;display:flex;align-items:center;}

/* BUTTON */
.btn-login{width:100%;margin-top:6px;background:linear-gradient(135deg,var(--blue),#0050cc);border:none;border-radius:12px;padding:15px;color:#fff;font-family:'Space Grotesk',sans-serif;font-size:.95rem;font-weight:700;letter-spacing:.5px;cursor:pointer;position:relative;overflow:hidden;transition:all .3s;box-shadow:0 8px 28px rgba(26,111,255,.35);display:flex;align-items:center;justify-content:center;gap:9px;}
.btn-login:hover{transform:translateY(-2px);box-shadow:0 14px 36px rgba(26,111,255,.5);}
.btn-login:active{transform:translateY(0);}
.btn-login::after{content:'';position:absolute;top:-50%;left:-80%;width:50%;height:200%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.18),transparent);transform:skewX(-20deg);animation:btnShimmer 3s infinite;}
@keyframes btnShimmer{0%{left:-80%}100%{left:130%}}

/* DEMO */
.demo-box{margin-top:20px;padding:11px 15px;background:rgba(255,179,0,.05);border:1px solid rgba(255,179,0,.2);border-radius:10px;display:flex;align-items:center;gap:9px;}
.demo-pulse{width:8px;height:8px;border-radius:50%;background:var(--gold);flex-shrink:0;animation:statusPulse 2s infinite;box-shadow:0 0 8px var(--gold);}
.demo-txt{font-size:.76rem;color:rgba(255,179,0,.9);}
.demo-txt strong{font-weight:700;}
.f-div{display:flex;align-items:center;gap:12px;margin:18px 0;}
.f-div-line{flex:1;height:1px;background:rgba(26,111,255,.12);}
.f-div-txt{font-size:.7rem;color:var(--muted);}
.lc-foot{text-align:center;margin-top:20px;font-size:.68rem;color:var(--muted);}
.lc-foot span{color:rgba(0,229,255,.6);}

@keyframes riseUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
.pt{position:fixed;border-radius:50%;pointer-events:none;z-index:2;animation:ptRise linear infinite;}
@keyframes ptRise{0%{transform:translateY(105vh) rotate(0);opacity:0}8%{opacity:.9}92%{opacity:.5}100%{transform:translateY(-80px) rotate(540deg);opacity:0}}
@media(max-width:900px){.left{display:none}.right{width:100%}}
</style>
</head>
<body>

<canvas id="bgCanvas"></canvas>
<div class="overlay-grid"></div>
<div class="overlay-fade"></div>
<div class="vignette"></div>
<div class="glow-blob b1"></div>
<div class="glow-blob b2"></div>
<div class="glow-blob b3"></div>
<div class="scan-line"></div>

<div class="layout">

  <!-- LEFT -->
  <div class="left">
    <div class="topbar">
      <div class="logo">
        <div class="logo-svg-wrap">
          <div class="logo-ring"></div>
          <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="lg1" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#1a6fff"/><stop offset="100%" stop-color="#00e5ff"/></linearGradient>
              <linearGradient id="lg2" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#00bfa5"/><stop offset="100%" stop-color="#00e5ff"/></linearGradient>
            </defs>
            <polygon points="24,2 42,12 42,36 24,46 6,36 6,12" stroke="url(#lg1)" stroke-width="2" fill="rgba(26,111,255,0.1)"/>
            <polygon points="24,9 36,16 36,32 24,39 12,32 12,16" stroke="url(#lg2)" stroke-width="1.2" fill="rgba(0,229,255,0.05)"/>
            <circle cx="24" cy="24" r="5" fill="url(#lg1)" opacity=".9"/>
            <line x1="24" y1="14" x2="24" y2="9" stroke="url(#lg1)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="24" y1="34" x2="24" y2="39" stroke="url(#lg1)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="14" y1="19" x2="10" y2="16.5" stroke="url(#lg2)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="34" y1="29" x2="38" y2="31.5" stroke="url(#lg2)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="34" y1="19" x2="38" y2="16.5" stroke="url(#lg2)" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="14" y1="29" x2="10" y2="31.5" stroke="url(#lg2)" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="24" cy="9" r="2" fill="#00e5ff" opacity=".8"/>
            <circle cx="36" cy="16" r="1.5" fill="#1a6fff" opacity=".7"/>
            <circle cx="36" cy="32" r="1.5" fill="#1a6fff" opacity=".7"/>
            <circle cx="24" cy="39" r="2" fill="#00e5ff" opacity=".8"/>
            <circle cx="12" cy="32" r="1.5" fill="#1a6fff" opacity=".7"/>
            <circle cx="12" cy="16" r="1.5" fill="#1a6fff" opacity=".7"/>
          </svg>
        </div>
        <div class="logo-text-wrap">
          <span class="logo-name">GeoActivos</span>
          <span class="logo-tagline">Asset Management Platform</span>
        </div>
      </div>
      <span class="badge-ent">GeSaProv Project Design</span>
    </div>

    <div class="hero">
      <div class="eyebrow">
        <div class="eyebrow-bar"></div>
        <span class="eyebrow-txt">Plataforma Industrial · Biomédica · Clínica</span>
      </div>
      <div class="hero-h1">
        <span class="line-white">Gestión Total</span>
        <span class="line-grad">de Activos</span>
      </div>
      <p class="hero-p">Controla, monitorea y optimiza cada equipo de tu empresa — ventiladores, monitores, computadores, maquinaria — en una sola plataforma inteligente.</p>

      <div class="pills">
        <span class="pill"><span class="pill-i">🖥️</span> Equipos Biomédicos</span>
        <span class="pill"><span class="pill-i">🔧</span> Mantenimiento</span>
        <span class="pill"><span class="pill-i">📋</span> Calibraciones ISO</span>
        <span class="pill"><span class="pill-i">📦</span> Inventario</span>
        <span class="pill"><span class="pill-i">🏢</span> Multi-Empresa</span>
        <span class="pill"><span class="pill-i">🔍</span> Auditoría Completa</span>
        <span class="pill"><span class="pill-i">📊</span> Reportes</span>
        <span class="pill"><span class="pill-i">📱</span> Código QR</span>
      </div>

      <div class="fshow" style="height:138px;margin-bottom:12px;">
        <div class="fcard active" id="fc0">
          <div class="fcard-icon-panel" style="background:linear-gradient(135deg,rgba(26,111,255,0.12),rgba(0,229,255,0.06));"><span class="fcard-big-icon">🖥️</span></div>
          <div class="fcard-body">
            <div class="fcard-module">Módulo · Inventario</div>
            <div class="fcard-title">Activos Biomédicos</div>
            <div class="fcard-desc">Registra ventiladores, monitores ECG, desfibriladores, computadores clínicos y más con fotos, serial y hoja de vida.</div>
            <div class="fcard-kpis"><div><div class="kpi-v">100<em>%</em></div><div class="kpi-l">Trazable</div></div><div><div class="kpi-v">QR<em>·</em>PDF</div><div class="kpi-l">Etiquetas</div></div></div>
          </div>
          <div class="fbar run" id="fb0"></div>
        </div>
        <div class="fcard" id="fc1">
          <div class="fcard-icon-panel" style="background:linear-gradient(135deg,rgba(0,191,165,0.12),rgba(0,229,255,0.06));"><span class="fcard-big-icon">🔧</span></div>
          <div class="fcard-body">
            <div class="fcard-module">Módulo · Mantenimiento</div>
            <div class="fcard-title">Preventivo & Correctivo</div>
            <div class="fcard-desc">Programa, ejecuta y cierra órdenes de trabajo con costos, firmas digitales y adjuntos técnicos.</div>
            <div class="fcard-kpis"><div><div class="kpi-v">3<em>+</em></div><div class="kpi-l">Tipos</div></div><div><div class="kpi-v">✍️</div><div class="kpi-l">Firma Digital</div></div></div>
          </div>
          <div class="fbar" id="fb1"></div>
        </div>
        <div class="fcard" id="fc2">
          <div class="fcard-icon-panel" style="background:linear-gradient(135deg,rgba(124,58,237,0.15),rgba(26,111,255,0.06));"><span class="fcard-big-icon">🎯</span></div>
          <div class="fcard-body">
            <div class="fcard-module">Módulo · Calibración</div>
            <div class="fcard-title">Certificados ISO</div>
            <div class="fcard-desc">Genera certificados de calibración con puntos de medida, patrones trazables y verificación pública por QR.</div>
            <div class="fcard-kpis"><div><div class="kpi-v">ISO<em>·</em></div><div class="kpi-l">Certificado</div></div><div><div class="kpi-v">🔗</div><div class="kpi-l">Verificación Pública</div></div></div>
          </div>
          <div class="fbar" id="fb2"></div>
        </div>
        <div class="fcard" id="fc3">
          <div class="fcard-icon-panel" style="background:linear-gradient(135deg,rgba(255,179,0,0.1),rgba(255,68,68,0.06));"><span class="fcard-big-icon">📊</span></div>
          <div class="fcard-body">
            <div class="fcard-module">Módulo · Auditoría</div>
            <div class="fcard-title">Trazabilidad Total</div>
            <div class="fcard-desc">Cada cambio registrado: quién, cuándo y qué modificó. Timeline completo por activo, usuario y módulo.</div>
            <div class="fcard-kpis"><div><div class="kpi-v">25<em>+</em></div><div class="kpi-l">APIs</div></div><div><div class="kpi-v">∞</div><div class="kpi-l">Historial</div></div></div>
          </div>
          <div class="fbar" id="fb3"></div>
        </div>
        <div class="fcard" id="fc4">
          <div class="fcard-icon-panel" style="background:linear-gradient(135deg,rgba(0,230,118,0.1),rgba(0,191,165,0.06));"><span class="fcard-big-icon">🏢</span></div>
          <div class="fcard-body">
            <div class="fcard-module">Módulo · Multi-Tenant</div>
            <div class="fcard-title">Varias Empresas</div>
            <div class="fcard-desc">Gestiona múltiples clientes en una sola instancia. Datos 100% aislados, roles granulares y permisos por módulo.</div>
            <div class="fcard-kpis"><div><div class="kpi-v">∞</div><div class="kpi-l">Empresas</div></div><div><div class="kpi-v">RBAC</div><div class="kpi-l">Roles & Permisos</div></div></div>
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

    <div class="stats">
      <div class="sitem"><div class="sv">10<em>+</em></div><div class="sl">Módulos</div></div>
      <div class="sitem"><div class="sv">20<em>+</em></div><div class="sl">Tablas BD</div></div>
      <div class="sitem"><div class="sv">25<em>+</em></div><div class="sl">APIs AJAX</div></div>
      <div class="sitem"><div class="sv">∞</div><div class="sl">Empresas</div></div>
    </div>
  </div>

  <!-- RIGHT LOGIN -->
  <div class="right">
    <div class="lcard">

      <div class="lc-logo-wrap">
        <div class="lc-logo-inner">
          <div class="lc-spin-ring"></div>
          <div class="lc-logo-box">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="llg1" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#60a5fa"/><stop offset="100%" stop-color="#00e5ff"/></linearGradient>
              </defs>
              <polygon points="24,2 42,12 42,36 24,46 6,36 6,12" stroke="url(#llg1)" stroke-width="2.5" fill="rgba(26,111,255,0.15)"/>
              <polygon points="24,10 35,16.5 35,31.5 24,38 13,31.5 13,16.5" stroke="url(#llg1)" stroke-width="1.5" fill="rgba(0,229,255,0.08)" opacity=".7"/>
              <circle cx="24" cy="24" r="5.5" fill="url(#llg1)"/>
              <circle cx="24" cy="24" r="2.5" fill="white" opacity=".9"/>
              <line x1="24" y1="10" x2="24" y2="18.5" stroke="#00e5ff" stroke-width="1.5" stroke-linecap="round" opacity=".8"/>
              <line x1="24" y1="29.5" x2="24" y2="38" stroke="#00e5ff" stroke-width="1.5" stroke-linecap="round" opacity=".8"/>
              <line x1="13.5" y1="17" x2="19" y2="20.5" stroke="#60a5fa" stroke-width="1.5" stroke-linecap="round" opacity=".8"/>
              <line x1="29" y1="27.5" x2="34.5" y2="31" stroke="#60a5fa" stroke-width="1.5" stroke-linecap="round" opacity=".8"/>
              <line x1="34.5" y1="17" x2="29" y2="20.5" stroke="#60a5fa" stroke-width="1.5" stroke-linecap="round" opacity=".8"/>
              <line x1="19" y1="27.5" x2="13.5" y2="31" stroke="#60a5fa" stroke-width="1.5" stroke-linecap="round" opacity=".8"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="lc-title">INICIAR SESIÓN</div>
      <div class="lc-sub">Accede a tu plataforma de activos</div>

      <div class="lc-status">
        <div class="status-dot"></div>
        Sistema Operativo · Todos los servicios activos
      </div>

      <?php if (!empty($error)): ?>
      <div class="error-alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span><?= e($error) ?></span>
      </div>
      <?php endif; ?>

      <form method="post" autocomplete="off">
        <div class="f-group">
          <label class="f-label">Correo Electrónico</label>
          <div class="f-wrap">
            <input type="email" name="email" placeholder="usuario@empresa.com" required autofocus value="<?= e($_POST['email'] ?? '') ?>">
            <span class="f-ico"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
          </div>
        </div>
        <div class="f-group">
          <label class="f-label">Contraseña</label>
          <div class="f-wrap">
            <input type="password" name="password" placeholder="••••••••••" required>
            <span class="f-ico"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
          </div>
        </div>
        <button type="submit" class="btn-login">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Entrar a GeoActivos
        </button>
      </form>

      <div class="demo-box">
        <div class="demo-pulse"></div>
        <div class="demo-txt">Demo: <strong>admin@demo.com</strong> · <strong>Admin123*</strong></div>
      </div>

      <div class="f-div"><div class="f-div-line"></div><span class="f-div-txt">GeoActivos Enterprise</span><div class="f-div-line"></div></div>

      <div class="lc-foot">
        Plataforma Multi-Tenant · RBAC · Auditoría · ISO<br>
        <span>© 2026 GeoActivos · Todos los derechos reservados</span>
      </div>

    </div>
  </div>
</div>

<script>
const canvas = document.getElementById('bgCanvas');
const ctx = canvas.getContext('2d');
let W, H, nodes = [];
function resize(){ W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
resize();
window.addEventListener('resize', ()=>{ resize(); initNodes(); });
function initNodes(){
  nodes = [];
  const n = Math.floor(W*H/9000);
  for(let i=0;i<n;i++){
    const types = [{hue:214,sat:90,lit:65},{hue:185,sat:100,lit:60},{hue:170,sat:80,lit:55}];
    const t = types[Math.floor(Math.random()*types.length)];
    nodes.push({x:Math.random()*W,y:Math.random()*H,vx:(Math.random()-.5)*.25,vy:(Math.random()-.5)*.25,r:Math.random()*1.8+.4,hue:t.hue,sat:t.sat,lit:t.lit,pulse:Math.random()*Math.PI*2});
  }
}
initNodes();
let mouse = {x:W/2,y:H/2};
document.addEventListener('mousemove', e=>{ mouse.x=e.clientX; mouse.y=e.clientY; });
let t = 0;
function draw(){
  ctx.clearRect(0,0,W,H);
  ctx.fillStyle='#03060f'; ctx.fillRect(0,0,W,H);
  const grad = ctx.createRadialGradient(W*.3,H*.5,0,W*.3,H*.5,W*.6);
  grad.addColorStop(0,'rgba(26,111,255,0.04)'); grad.addColorStop(1,'transparent');
  ctx.fillStyle=grad; ctx.fillRect(0,0,W,H);
  t+=.008;
  nodes.forEach(n=>{ n.x+=n.vx; n.y+=n.vy; n.pulse+=.015; if(n.x<0||n.x>W)n.vx*=-1; if(n.y<0||n.y>H)n.vy*=-1; });
  for(let i=0;i<nodes.length;i++){
    for(let j=i+1;j<nodes.length;j++){
      const dx=nodes[i].x-nodes[j].x, dy=nodes[i].y-nodes[j].y, d=Math.sqrt(dx*dx+dy*dy);
      if(d<130){ ctx.beginPath(); ctx.moveTo(nodes[i].x,nodes[i].y); ctx.lineTo(nodes[j].x,nodes[j].y); ctx.strokeStyle=`hsla(${nodes[i].hue},${nodes[i].sat}%,${nodes[i].lit}%,${(1-d/130)*.12})`; ctx.lineWidth=.6; ctx.stroke(); }
    }
  }
  nodes.forEach(n=>{
    const dx=n.x-mouse.x, dy=n.y-mouse.y, d=Math.sqrt(dx*dx+dy*dy);
    if(d<200){ ctx.beginPath(); ctx.moveTo(n.x,n.y); ctx.lineTo(mouse.x,mouse.y); ctx.strokeStyle=`rgba(0,229,255,${(1-d/200)*.3})`; ctx.lineWidth=.7; ctx.stroke(); }
    const pulse=.7+Math.sin(n.pulse)*.3;
    ctx.beginPath(); ctx.arc(n.x,n.y,n.r*pulse,0,Math.PI*2); ctx.fillStyle=`hsla(${n.hue},${n.sat}%,${n.lit}%,${pulse*.8})`; ctx.fill();
    if(n.r>1.4){ ctx.beginPath(); ctx.arc(n.x,n.y,n.r*3,0,Math.PI*2); ctx.fillStyle=`hsla(${n.hue},${n.sat}%,${n.lit}%,0.04)`; ctx.fill(); }
  });
  for(let i=0;i<4;i++){
    const x=(W*.15+i*(W*.22)+Math.sin(t+i)*30), y=(H*.5+Math.cos(t*.7+i)*60), s=18+i*8;
    ctx.beginPath();
    for(let k=0;k<6;k++){ const a=(Math.PI/3)*k-Math.PI/6; k===0?ctx.moveTo(x+s*Math.cos(a),y+s*Math.sin(a)):ctx.lineTo(x+s*Math.cos(a),y+s*Math.sin(a)); }
    ctx.closePath(); ctx.strokeStyle=`rgba(26,111,255,${.025+i*.005})`; ctx.lineWidth=1; ctx.stroke();
  }
  requestAnimationFrame(draw);
}
draw();

function spawnPt(){
  const p=document.createElement('div'); p.className='pt';
  const sz=Math.random()*3+1, h=[214,185,170][Math.floor(Math.random()*3)];
  p.style.cssText=`width:${sz}px;height:${sz}px;left:${Math.random()*100}%;background:hsla(${h},85%,65%,0.6);box-shadow:0 0 ${sz*3}px hsla(${h},85%,65%,0.4);animation-duration:${Math.random()*18+12}s;animation-delay:${Math.random()*4}s;`;
  document.body.appendChild(p); setTimeout(()=>p.remove(),24000);
}
setInterval(spawnPt,1200);

let cur=0;
const total=5;
function goTo(idx){
  const oc=document.getElementById('fc'+cur), ob=document.getElementById('fb'+cur), od=document.querySelectorAll('.fdot')[cur];
  oc.classList.remove('active'); oc.classList.add('exit'); ob.classList.remove('run'); od.classList.remove('on');
  const prev=cur;
  setTimeout(()=>{
    document.getElementById('fc'+prev).classList.remove('exit');
    cur=idx;
    const nc=document.getElementById('fc'+cur), nb=document.getElementById('fb'+cur), nd=document.querySelectorAll('.fdot')[cur];
    nc.classList.add('active'); nd.classList.add('on'); nb.classList.remove('run'); void nb.offsetWidth; nb.classList.add('run');
  },450);
}
let auto=setInterval(()=>goTo((cur+1)%total),4500);
document.querySelector('.fshow').addEventListener('mouseenter',()=>clearInterval(auto));
document.querySelector('.fshow').addEventListener('mouseleave',()=>{ auto=setInterval(()=>goTo((cur+1)%total),4500); });

const blobStyles=`.glow-blob{position:fixed;border-radius:50%;filter:blur(130px);pointer-events:none;z-index:1;animation:blobF 11s ease-in-out infinite alternate;}.b1{width:700px;height:450px;background:rgba(26,111,255,0.07);left:-200px;top:15%;}.b2{width:550px;height:550px;background:rgba(0,229,255,0.05);right:-150px;top:25%;animation-delay:-5s;}.b3{width:450px;height:300px;background:rgba(0,191,165,0.05);left:25%;bottom:-50px;animation-delay:-8s;}@keyframes blobF{from{transform:translateY(0) scale(1)}to{transform:translateY(-50px) scale(1.08)}}`;
const st=document.createElement('style'); st.textContent=blobStyles; document.head.appendChild(st);
['b1','b2','b3'].forEach(c=>{ const d=document.createElement('div'); d.className=`glow-blob ${c}`; document.body.appendChild(d); });
</script>
</body>
</html>