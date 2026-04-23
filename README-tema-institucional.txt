PAQUETE ACTUALIZADO

1. app.blade.php
   Reemplazar en:
   resources/views/layouts/theme/app.blade.php

2. theme-institutional.css
   Copiar en:
   public/assets/css/theme-institutional.css

3. theme-institutional-toggle.js
   Copiar en:
   public/assets/js/theme-institutional-toggle.js

4. topnavbar-theme-toggle-snippet.blade.php
   Es un fragmento opcional.
   Pégalo dentro de tu partial:
   resources/views/layouts/theme/partials/topnavbar.blade.php

Notas:
- app.blade.php ya carga el CSS y el JS del tema.
- Se eliminó la duplicidad de plugins.css.
- Se corrigieron rutas directas para usar asset().
- El modo claro/oscuro queda persistente con localStorage.
