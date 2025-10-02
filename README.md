# Cloud Group Test - Instrucciones para ejecutar el proyecto

Resumen corto: proyecto Laravel + Inertia (Vue 3). Estas instrucciones cubren instalación y ejecución tanto del backend (Laravel/PHP) como del frontend (assets con Vite).

Requisitos
- PHP >= 8.0 (o la versión que tu proyecto necesite)
- Composer
- Node.js >= 16 y npm
- Base de datos (MySQL)

1) Clonar el repositorio

2) Backend (Laravel)
- Instalar dependencias PHP:
  - composer install
- Copiar fichero de entorno y configurarlo:
  - Windows (PowerShell / CMD):
    - copy .env.example .env
  - Edita .env y ajusta DB_*, APP_URL, etc.
- Generar key:
  - php artisan key:generate
- Migraciones / seed:
  - php artisan migrate
  - php artisan db:seed
- Levantar servidor PHP (desarrollo):
  - php artisan serve
  - Por defecto se sirve en http://127.0.0.1:8000

3) Frontend (Node / Vite)
- Instalar dependencias JS:
  - npm install
  - Si falta axios/bootstrap: npm install axios bootstrap@5
- Ejecutar compilador Vite en modo desarrollo (hot-reload):
  - npm run dev
- Compilar assets para producción:
  - npm run build

Notas:
- Ejecuta php artisan serve y npm run dev en terminales separados. O ejecuta composer run dev para levantar ambos al mismo tiempo
- La instancia axios está en `resources/js/api.ts` (baseURL `/api` por defecto). Ajusta si tu backend está en otro prefijo.

Uso rápido
- Entradas principales:
  - resources/js/app.ts — punto de entrada JS (bootstrap + Inertia app).
  - resources/js/pages/TasksList.vue — vista de tareas (listado / crear / keywords).
  - resources/js/api.ts — instancia axios central.

Problemas comunes & soluciones
- "Bootstrap JS no funciona": asegúrate de `import 'bootstrap/dist/js/bootstrap.bundle.min.js'` en app.ts y que `npm run dev` esté activo.
- "CORS / 401": verifica .env CORS y configuración de Sanctum, y que frontend use la misma URL base (o ajusta axios baseURL).
- Hot-reload no actualiza: reinicia `npm run dev` y borra cache del navegador.

Comandos resumidos (Windows)
- Composer: composer install
- Copiar env: copy .env.example .env
- Key: php artisan key:generate
- Migrar: php artisan migrate
- Seed: php artisan db:seed
- NPM install: npm install
- NPM dev: npm run dev
- NPM build: npm run build
- Levantar Laravel: php artisan serve
