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
  --teal:#0BA896; --teal-dark:#077D6E; --teal-light:#E6F7F5; --teal-mid:#B2E8E3;
  --slate-900:#0F1D2E; --slate-800:#1A2E42; --slate-700:#2C4258; --slate-500:#4E6D8C;
  --slate-400:#7090AA; --slate-300:#9DB8CC; --slate-200:#C8DDE8; --slate-100:#E8F1F7;
  --slate-50:#F5F8FA; --white:#FFFFFF;
  --danger:#E53E3E; --danger-bg:#FFF5F5;
  --shadow-sm:0 1px 3px rgba(15,29,46,0.08),0 1px 2px rgba(15,29,46,0.04);
  --shadow-md:0 4px 16px rgba(15,29,46,0.10),0 2px 4px rgba(15,29,46,0.06);
  --shadow-lg:0 20px 48px rgba(15,29,46,0.12),0 8px 16px rgba(15,29,46,0.06);
}
html, body { height: 100%; font-family: 'Plus Jakarta Sans', sans-serif; background: var(--slate-50); color: var(--slate-900); overflow: hidden; }

.login-page { display:flex; height: 100vh; }
.login-left {
  flex:1; background: var(--white); display:flex; align-items:center; justify-content:center;
  position:relative; overflow:hidden;
}
.login-left::before {
  content:''; position:absolute; inset:0;
  background: radial-gradient(ellipse 70% 60% at 20% 15%, rgba(11,168,150,0.07) 0%, transparent 70%),
              radial-gradient(ellipse 50% 40% at 80% 85%, rgba(11,168,150,0.05) 0%, transparent 70%);
  pointer-events:none;
}
.login-left::after {
  content:''; position:absolute; bottom:-120px; left:-120px; width:400px; height:400px;
  border-radius:50%; background:radial-gradient(circle, rgba(11,168,150,0.08) 0%, transparent 70%);
}
.login-left-inner { position:relative; z-index:1; text-align:center; padding:40px; animation:fadeUp 0.6s ease both; }
.login-logo { display:flex; align-items:center; justify-content:center; gap:14px; margin-bottom:20px; }
.login-logo-icon {
  width:56px; height:56px; background:linear-gradient(135deg, var(--teal), var(--teal-dark));
  border-radius:16px; display:flex; align-items:center; justify-content:center;
  box-shadow:0 6px 20px rgba(11,168,150,0.30);
}
.login-logo-icon svg { width:28px; height:28px; }
.login-logo-texts { text-align:left; }
.login-logo-name { font-family:'Sora',sans-serif; font-weight:700; font-size:1.5rem; letter-spacing:-0.3px; color:var(--slate-900); line-height:1.1; }
.login-logo-tag { font-size:0.65rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--teal); }
.login-hero { margin-bottom:32px; }
.login-hero h1 { font-family:'Sora',sans-serif; font-size:clamp(1.8rem,3vw,2.6rem); font-weight:700; letter-spacing:-0.8px; line-height:1.15; color:var(--slate-900); margin-bottom:12px; }
.login-hero h1 .teal { color:var(--teal); }
.login-hero p { font-size:0.95rem; color:var(--slate-500); line-height:1.7; max-width:380px; margin:0 auto; }
.login-stats { display:flex; justify-content:center; gap:32px; }
.login-stat { text-align:center; }
.login-stat-val { font-family:'Sora',sans-serif; font-size:1.6rem; font-weight:700; color:var(--slate-900); }
.login-stat-val em { font-style:normal; color:var(--teal); }
.login-stat-lbl { font-size:0.6rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--slate-400); margin-top:2px; }

.login-right { width:480px; background:var(--white); display:flex; align-items:center; justify-content:center; padding:32px; position:relative; }
.login-right::before { content:''; position:absolute; top:-60px; right:-60px; width:260px; height:260px; border-radius:50%; background:radial-gradient(circle, rgba(11,168,150,0.06) 0%, transparent 70%); pointer-events:none; }

.login-card { width:100%; max-width:380px; animation:slideIn 0.65s cubic-bezier(0.22,1,0.36,1) both; }
@keyframes slideIn { from{opacity:0;transform:translateY(20px) scale(0.98)} to{opacity:1;transform:translateY(0) scale(1)} }

.card-accent { height:3px; background:linear-gradient(90deg, var(--teal), rgba(11,168,150,0.3)); border-radius:12px 12px 0 0; margin:-1px -1px 0; }

.card-header { margin-bottom:24px; }
.card-logo { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
.card-logo-icon { width:44px; height:44px; background:linear-gradient(135deg, var(--teal), var(--teal-dark)); border-radius:12px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(11,168,150,0.25); }
.card-logo-icon svg { width:22px; height:22px; }
.card-logo-texts .name { font-family:'Sora',sans-serif; font-weight:700; font-size:1.05rem; color:var(--slate-900); }
.card-logo-texts .sub { font-size:0.62rem; font-weight:500; color:var(--slate-300); letter-spacing:0.06em; text-transform:uppercase; }
.card-title { font-family:'Sora',sans-serif; font-size:1.5rem; font-weight:700; letter-spacing:-0.4px; color:var(--slate-900); margin-bottom:6px; }
.card-subtitle { font-size:0.85rem; color:var(--slate-500); }

.status-row { display:flex; align-items:center; gap:7px; font-size:0.72rem; font-weight:600; color:var(--teal); margin-bottom:20px; padding:10px 14px; background:var(--teal-light); border-radius:8px; border:1px solid var(--teal-mid); }
.status-dot { width:7px; height:7px; border-radius:50%; background:var(--teal); animation:pulse 2.5s infinite; flex-shrink:0; }
@keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(11,168,150,0.4)} 50%{box-shadow:0 0 0 5px rgba(11,168,150,0)} }

.error-alert { display:flex; align-items:center; gap:10px; padding:12px 14px; background:var(--danger-bg); border:1px solid rgba(229,62,62,0.2); border-radius:10px; color:var(--danger); font-size:0.84rem; margin-bottom:18px; animation:fadeUp 0.3s ease both; }
.error-alert svg { width:16px; height:16px; flex-shrink:0; }

.f-group { margin-bottom:16px; }
.f-label { display:block; font-size:0.75rem; font-weight:600; letter-spacing:0.04em; color:var(--slate-700); margin-bottom:7px; }
.f-wrap { position:relative; }
.f-wrap input { width:100%; background:var(--slate-50); border:1.5px solid #D6E4EF; border-radius:10px; padding:12px 42px 12px 14px; font-family:'Plus Jakarta Sans',sans-serif; font-size:0.9rem; color:var(--slate-900); outline:none; transition:border-color 0.2s, box-shadow 0.2s, background 0.2s; }
.f-wrap input::placeholder { color:var(--slate-300); }
.f-wrap input:focus { border-color:var(--teal); background:var(--white); box-shadow:0 0 0 3px rgba(11,168,150,0.12); }
.f-ico { position:absolute; right:13px; top:50%; transform:translateY(-50%); color:var(--slate-300); display:flex; align-items:center; pointer-events:none; }

.btn-login { width:100%; margin-top:8px; background:linear-gradient(135deg,var(--teal),var(--teal-dark)); border:none; border-radius:10px; padding:14px 20px; color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:0.92rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:transform 0.18s, box-shadow 0.18s, filter 0.18s; box-shadow:0 4px 16px rgba(11,168,150,0.35); position:relative; overflow:hidden; }
.btn-login::after { content:''; position:absolute; top:0; left:-100%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent); transform:skewX(-20deg); animation:shimmer 3s ease infinite; }
@keyframes shimmer { 0%{left:-100%} 100%{left:160%} }
.btn-login:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(11,168,150,0.45); filter:brightness(1.05); }
.btn-login:active { transform:translateY(0); }

.demo-box { margin-top:18px; padding:11px 14px; background:#FFFBF0; border:1px solid #F5E6B2; border-radius:10px; display:flex; align-items:center; gap:9px; }
.demo-icon { font-size:0.9rem; flex-shrink:0; }
.demo-txt { font-size:0.75rem; color:#8A6D00; line-height:1.4; }
.demo-txt strong { font-weight:700; color:#6B5200; }

.f-div { display:flex; align-items:center; gap:12px; margin:20px 0 0; }
.f-div-line { flex:1; height:1px; background:var(--slate-100); }
.f-div-txt { font-size:0.65rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:var(--slate-300); white-space:nowrap; }

.lc-foot { text-align:center; font-size:0.67rem; color:var(--slate-300); margin-top:16px; line-height:1.7; }

@keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

@media(max-width:900px) { .login-left{display:none} .login-right{width:100%} }
</style>
</head>
<body>

<div class="login-page">
  <div class="login-left">
    <div class="login-left-inner">
      <div class="login-logo">
        <div class="login-logo-icon">
          <svg viewBox="0 0 48 48" fill="none">
            <defs><linearGradient id="lg" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#fff" stop-opacity=".95"/><stop offset="100%" stop-color="#C2EDE9"/></linearGradient></defs>
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
        <div class="login-logo-texts">
          <div class="login-logo-name">GeoActivos</div>
          <div class="login-logo-tag">Asset Management</div>
        </div>
      </div>
      <div class="login-hero">
        <h1>Gestión Total<br>de <span class="teal">Activos</span></h1>
        <p>Controla, monitorea y optimiza cada equipo de tu empresa en una sola plataforma inteligente.</p>
      </div>
      <div class="login-stats">
        <div class="login-stat"><div class="login-stat-val">10<em>+</em></div><div class="login-stat-lbl">Módulos</div></div>
        <div class="login-stat"><div class="login-stat-val">20<em>+</em></div><div class="login-stat-lbl">Tablas BD</div></div>
        <div class="login-stat"><div class="login-stat-val">100<em>%</em></div><div class="login-stat-lbl">Trazabilidad</div></div>
      </div>
    </div>
  </div>

  <div class="login-right">
    <div class="login-card">
      <div class="card-accent"></div>
      <div class="card-header">
        <div class="card-logo">
          <div class="card-logo-icon">
            <svg viewBox="0 0 48 48" fill="none">
              <defs><linearGradient id="clg" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#fff" stop-opacity=".95"/><stop offset="100%" stop-color="#C2EDE9"/></linearGradient></defs>
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
            <div class="sub">Enterprise</div>
          </div>
        </div>
        <h2 class="card-title">Bienvenido de nuevo</h2>
        <p class="card-subtitle">Ingresa tus credenciales para acceder a la plataforma.</p>
      </div>

      <div class="status-row">
        <div class="status-dot"></div>
        Sistema operativo · Todos los servicios activos
      </div>

      <?php if(!empty($error)): ?>
      <div class="error-alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span><?= e($error) ?></span>
      </div>
      <?php endif; ?>

      <form method="post" autocomplete="off">
        <div class="f-group">
          <label class="f-label">Correo electrónico</label>
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
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Entrar a GeoActivos
        </button>
      </form>

      <div class="demo-box">
        <span class="demo-icon">🔑</span>
        <div class="demo-txt">Demo: <strong>admin@demo.com</strong> · <strong>Admin123*</strong></div>
      </div>

      <div class="f-div"><div class="f-div-line"></div><span class="f-div-txt">GeoActivos Enterprise</span><div class="f-div-line"></div></div>
      <div class="lc-foot">Plataforma Multi-Tenant · RBAC · Auditoría · ISO<br>© 2026 GeoActivos · Todos los derechos reservados</div>
    </div>
  </div>
</div>

</body>
</html>
