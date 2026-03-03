    </div><!-- /.container-fluid -->
  </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<footer class="main-footer text-sm" style="
  background:rgba(3,6,15,.95);
  border-top:1px solid rgba(26,111,255,.15);
  color:rgba(140,180,220,.8);
  padding:18px 20px;
  transition: none;
  margin-left: 0;
  width: 100%;
  margin-top: 56px;
">
  <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:16px;">

    <!-- IZQUIERDA -->
    <div class="d-flex align-items-center" style="gap:12px;">
      <div style="width:4px;height:24px;background:linear-gradient(180deg,#1a6fff,#00e5ff);border-radius:2px;"></div>
      <div>
        <div style="font-family:'Bebas Neue',cursive;font-size:.95rem;letter-spacing:1px;color:#fff;">
          <i class="fas fa-cube mr-1" style="color:#00e5ff;"></i>GeoActivos
        </div>
        <div style="font-size:.7rem;color:rgba(78,109,140,.8);margin-top:1px;">Gestión profesional de activos</div>
      </div>
      <span style="background:rgba(0,229,255,.1);border:1px solid rgba(0,229,255,.2);border-radius:6px;padding:4px 9px;font-size:.6rem;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#00e5ff;margin-left:10px;white-space:nowrap;">MULTI-TENANT</span>
    </div>

    <!-- CENTRO (hidden en mobile) -->
    <div class="d-none d-md-flex align-items-center" style="gap:10px;color:rgba(100,140,180,.7);font-size:.75rem;">
      <span style="width:4px;height:4px;border-radius:50%;background:#00e676;display:inline-block;"></span>
      <span>Sistema estable</span>
      <span style="width:4px;height:4px;border-radius:50%;background:#00e676;display:inline-block;"></span>
      <span>Auditoría activa</span>
      <span style="width:4px;height:4px;border-radius:50%;background:#00e676;display:inline-block;"></span>
      <span>Eliminación segura</span>
    </div>

    <!-- DERECHA -->
    <div class="text-right" style="white-space:nowrap;">
      <div style="font-family:'Bebas Neue',cursive;font-size:.95rem;letter-spacing:1px;color:#fff;">GeSaProv</div>
      <div style="font-size:.7rem;color:rgba(78,109,140,.8);margin-top:2px;">
        <span>&copy;</span> 2025 &nbsp;
        <span style="background:rgba(255,179,0,.1);border:1px solid rgba(255,179,0,.2);border-radius:4px;padding:2px 7px;font-weight:700;color:#ffb300;display:inline-block;margin-left:6px;">PRO</span>
        <span style="color:rgba(100,140,180,.6);margin-left:8px;">v1.0</span>
      </div>
    </div>

  </div>

</footer>

<!-- Sidebar Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

</div><!-- /.wrapper -->

<!-- =======================
     JS OBLIGATORIO
======================= -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script src="<?= e(base_url()) ?>/assets/js/app.js"></script>

</body>
</html>
