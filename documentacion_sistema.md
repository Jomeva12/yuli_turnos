# 📋 GestionTurnos v1.0 — Documentación Completa del Sistema

> **Proyecto:** Sistema de Gestión de Turnos para almacén tipo centro comercial (≈30 empleados)
> **Stack:** Laravel 11.49 · PHP 8.2 · MySQL · Blade · Bootstrap CSS
> **Producción:** https://yuli.diferencialdx.com

---

## 1. 🏢 Contexto del Negocio

La aplicación resuelve el problema de una administradora que debe planear manualmente los turnos mensuales de ~30 empleados en un almacén tipo centro comercial.

### Restricciones operativas clave
| Regla | Detalle |
|---|---|
| Descansos | 4 por mes: 3 en días ordinarios, 1 obligatoriamente en domingo |
| Zona crítica | Entre días 28 y 2 de cada mes **no se asignan descansos** (época de cierre/inventario) |
| Sábados | Nunca hay descansos los sábados |
| Rotación | Semanas alternadas de mañana / tarde para equidad |
| Turnos partidos | Se asignan 2 veces por semana (si está disponible) |
| Vacaciones | Con fecha de inicio y fin; bloquean el turno en todos los días del rango |

---

## 2. 🗄️ Base de Datos

### 2.1 Diagrama de relaciones

```
users ──────────────────────── (autenticación)

employees ──┬── area_employee ── areas
            ├── shifts
            ├── absences
            └── generation_notes
```

### 2.2 Tabla: `employees`
| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint PK | Identificador único |
| `name` | varchar(255) | Nombre completo del empleado |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

### 2.3 Tabla: `areas`
| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint PK | Identificador único |
| `name` | varchar(255) | Nombre del área (General, Buffet, Cosmético, etc.) |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

**Áreas del sistema:**
- **General** — área principal con mayor cantidad de turnos
- **Buffet** — 1 persona por día (excepto domingo)
- **Cosmético** — 2 personas por día
- **Domicilios** — 2 personas por día
- **Electrodoméstico** — 2 personas (Lun/Mar/Jue/Vie) · 3 los Miércoles/Sábados
- **Marking** — 1 persona (solo Martes y Jueves)
- **Varely Camacho** — 1 persona (solo Miércoles)

### 2.4 Tabla pivot: `area_employee`
| Columna | Tipo | Descripción |
|---|---|---|
| `employee_id` | FK → employees | — |
| `area_id` | FK → areas | — |

Controla qué empleado está habilitado para trabajar en cada área.

### 2.5 Tabla: `shifts` (turnos asignados)
| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint PK | — |
| `employee_id` | FK → employees | Empleado del turno |
| `area_id` | FK → areas | Área asignada |
| `date` | date | Fecha del turno |
| `schedule` | varchar nullable | Horario ej: `"7:00-11:00\|11:30-14:30"` (partidos usan \|) |
| `type` | varchar default `'normal'` | `normal` o `partido` |
| `is_manual` | tinyint | `1` si fue asignado manualmente; `0` si fue auto-generado |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

**Formato del campo `schedule`:**
- Turno normal: `"7:00-13:00"` (una franja)
- Turno partido: `"7:00-11:00|11:30-14:30"` (dos franjas separadas por `|`)
- Descanso: se representa con `null` en schedule y type = `'rest'` (o mediante `absences`)

### 2.6 Tabla: `absences` (novedades)
| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint PK | — |
| `employee_id` | FK → employees | Empleado afectado |
| `type` | varchar | `vacation`, `incapacity`, `rest` |
| `start_date` | date | Inicio del rango |
| `end_date` | date | Fin del rango |
| `comments` | text nullable | Observaciones opcionales |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

### 2.7 Tabla: `shift_templates` (plantillas de turno por día)
| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint PK | — |
| `day_of_week` | int | `1`=Lun, `2`=Mar, `3`=Mié, `4`=Jue, `5`=Vie, `6`=Sáb, `7`=Dom, `0`=Default |
| `area_id` | FK → areas | Área a cubrir |
| `schedule` | varchar | Horario de la plantilla |
| `type` | enum `normal\|partido` | Tipo de jornada |
| `required_count` | int default `1` | Personas requeridas en ese slot |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

> Contiene los "moldes" que el algoritmo usa para generar turnos automáticamente. El `ShiftTemplateSeeder` los pobla basado en `datos_proyecto.md`.

### 2.8 Tabla: `generation_notes` (bitácora de generación)
| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint PK | — |
| `employee_id` | FK nullable | Empleado al que refiere la nota |
| `date` | date | Día al que pertenece la nota |
| `message` | text | Descripción del evento |
| `type` | enum | `info`, `warning`, `error` |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

### 2.9 Tabla: `users` (administradores del sistema)
| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint PK | — |
| `name` | varchar | Nombre del usuario |
| `email` | varchar unique | Email de acceso |
| `password` | varchar | Hash bcrypt |
| `email_verified_at` | timestamp nullable | — |
| `remember_token` | varchar nullable | — |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

**Admin por defecto (vía `UserSeeder`):**
- Email: `yuli@diferencialdx.com`
- Password: `3176890957a`

### 2.10 Tabla: `personal_access_tokens` (Sanctum)
Generada automáticamente por Laravel Sanctum para autenticación basada en tokens.

---

## 3. 🛣️ Rutas del Sistema

```
GET  /login                        → LoginController@showLoginForm
POST /login                        → LoginController@login
POST /logout                       → LoginController@logout

── Protegidas por middleware auth ─────────────────────────────────────
GET  /                             → ShiftController@index          (Planilla principal)
GET  /employees                    → EmployeeController@index       (Habilidades)
POST /employees/toggle-area        → EmployeeController@toggleArea
POST /absences                     → AbsenceController@store        (Registrar novedad)
POST /absences/clear-month         → AbsenceController@clearMonth
GET  /shifts/manual                → ShiftController@manualIndex    (Asignación manual)
GET  /shifts/timeline/{date?}      → ShiftController@timeline       (Vista cronograma)
GET  /shifts/export                → ShiftController@export         (Exportar Excel)
GET  /shifts/{employee}/manual-shifts → ShiftController@manualAssignment
POST /api/shifts/manual-assign     → ShiftController@manualAssign
POST /shifts/generate-day          → ShiftController@generateDay
POST /shifts/generate-range        → ShiftController@generateRange
POST /shifts/generate-month        → ShiftController@generateMonth
POST /shifts/clear-month           → ShiftController@clearMonth
```

---

## 4. 📄 Páginas y Funcionalidades

### 4.1 `/login` — Inicio de sesión
**Vista:** `resources/views/auth/login.blade.php`
**Funcionalidades:**
- Formulario de email + contraseña con diseño **Glassmorphism** (fondo oscuro con efecto de cristal)
- Spinner de carga en el botón al enviar el formulario
- Mensajes de error de autenticación (email/password incorrecto)
- Validación de campos requeridos
- Redirección automática a `/` si ya hay sesión activa
- Botón de "Cerrar Sesión" en navbar (visible solo con sesión activa)

---

### 4.2 `/` — Planilla Principal (Calendario de Turnos)
**Vista:** `resources/views/shifts/index.blade.php`
**Controlador:** `ShiftController@index`

**Funcionalidades:**
- Tabla cronograma mensual: filas = empleados, columnas = días del mes
- **Selector de mes** para navegar entre meses
- **Encabezado de días en español** (Lun, Mar, Mié...) con carbon locale='es'
- **Domingos resaltados** en rojo
- **Día actual** marcado visualmente
- **Filtros por área** (botones toggle para filtrar empleados por área habilitada)
- **Filtros de novedades** (VAC, INC, PER, CAL) para resaltar vacaciones, incapacidades, permisos y calamidades
- Cada celda de turno muestra:
  - Horario(s) del turno
  - Colores codificados según el tipo de novedad
  - Badge de área con color distintivo
  - Indicador de turno manual (✏️)
- **Botones de generación:**
  - `Generar Mes Completo` — genera todos los días del mes automáticamente
  - `Limpiar Turnos` — elimina todos los turnos del mes (excepto manuales)
  - `Limpiar Novedades` — elimina todas las ausencias del mes
  - `Excel` — exporta la planilla a archivo .xlsx
  - `Asignación Manual` — navega a la vista de edición manual
- **Badge de Notas** — muestra contador de novedades generadas; al hacer clic abre modal con bitácora
- **Panel lateral izquierdo (Sidebar de empleado):**
  - Se activa al hacer clic en un día del encabezado
  - Muestra resumen del día: total empleados trabajando, libres, en vacaciones
  - Desglose por tipo de turno (Normales, Partidos)
  - Desglose por área
  - Enlace al "Cronograma de Cobertura"
- **Panel lateral derecho (Employee Sidebar):**
  - Se activa al hacer clic en el nombre de un empleado
  - Muestra nombre, botón para editar sus turnos manualmente
  - Perfil del mes

---

### 4.3 `/shifts/manual` — Asignación Manual de Turnos
**Vista:** `resources/views/shifts/manual_index.blade.php`
**Controlador:** `ShiftController@manualIndex`

**Funcionalidades:**
- Tabla cronograma editable: misma estructura que la planilla principal
- **Scroll vertical independiente** dentro del contenedor (altura fija = `100vh - 200px`)
- **Triple sticky:**
  - ↕️ Fila de días (header) sticky en la parte superior
  - ↔️ Columna de nombres (ASESOR) sticky a la izquierda
  - 🔲 Celda esquina "ASESOR" sticky en ambas direcciones (z-index 200)
- **Días en español** (locale='es')
- **Domingos resaltados** en rosa
- Al hacer clic en una celda de turno → abre modal deslizable lateral con:
  - Nombre del empleado y fecha seleccionada
  - Plantillas de turno agrupadas por tipo y horario
  - Opción de marcar novedades (VAC, INC, PER, CAL, DESCANSO)
  - Área habilitadas del empleado para ese día
  - Botón guardar con validación AJAX
- Guardado sin recarga de página (fetch API)
- Las celdas con turno manual muestran indicador visual especial

---

### 4.4 `/employees/{employee}/manual-shifts` — Editor Individual
**Vista:** `resources/views/shifts/manual_assignment.blade.php`
**Controlador:** `ShiftController@manualAssignment`

**Funcionalidades:**
- Vista de edición de turnos día por día para un empleado específico
- Muestra nombre y mes en español
- Grid de días del mes con estado actual de cada turno
- Permite seleccionar plantilla de horario para cada día
- Guarda via AJAX

---

### 4.5 `/employees` — Habilidades del Personal
**Vista:** `resources/views/employees/` (index)
**Controlador:** `EmployeeController@index`

**Funcionalidades:**
- Lista de todos los empleados
- Para cada empleado, checkboxes de áreas disponibles
- Toggle instantáneo via AJAX (`POST /employees/toggle-area`)
- Sin recargar la página; la tabla pivot `area_employee` se actualiza en tiempo real
- Visual con checkmarks verdes / grises por área

---

### 4.6 `/shifts/timeline/{date?}` — Cronograma de Cobertura
**Vista:** `resources/views/shifts/timeline.blade.php`
**Controlador:** `ShiftController@timeline`

**Funcionalidades:**
- Vista horizontal del día seleccionado (6:00 a 22:00) en intervalos de 15 minutos
- Filas verticales = Áreas fijas (Buffet, Cosmético, Electrodoméstico, Domicilios, Marking, Varely Camacho)
- Para el área **General**: una fila por cada trabajador asignado ese día
- Bloques coloreados representan el horario activo de cada empleado
- Título en español con fecha completa (ej: "Cronograma de Cobertura: lunes 23 de marzo 2026")

---

### 4.7 `/shifts/export` — Exportar a Excel
**Controlador:** `ShiftController@export`
**Clase:** `App\Exports\ShiftsExport`
**Vista parcial:** `resources/views/exports/shifts.blade.php`

**Funcionalidades:**
- Genera archivo `.xlsx` con la planilla del mes seleccionado
- Usa **Maatwebsite/Excel** para el renderizado
- Días en MAYÚSCULAS en español (`strtoupper + translatedFormat`)
- Formato tabular: empleados en filas, días en columnas, con horario en cada celda

---

## 5. ⚙️ Algoritmo de Generación Automática de Turnos

**Clase:** `App\Http\Controllers\ShiftController::privateGenerateDay()`
**Comando artisan:** `App\Console\Commands\GenerateShifts`

### Flujo de generación (por día):
1. **Leer plantillas** del día de la semana desde `shift_templates`
2. **Obtener empleados habilitados** para cada área/plantilla
3. **Filtrar empleados** con ausencias (vacation, incapacity) en ese día
4. **Verificar zona crítica** (días 28 al 2): no asignar descansos
5. **Asignar descanso dominical** si el empleado aún no tuvo 1 descanso en domingo ese mes
6. **Asignar descansos ordinarios** (máx 3 por mes) respetando que no se le "deba" descanso
7. **Asignar turnos** según `shift_templates.required_count` por área
8. **Rotación equitativa**: alterna empleados entre mañana y tarde semana a semana
9. **Registrar notas** en `generation_notes` con tipo info/warning/error
10. **Marcar turno manual** (`is_manual=0`) para que `clear-month` no los borre

### Reglas de descanso:
- Máximo **4 descansos por mes** (3 ordinarios + 1 domingo)
- Un descanso en domingo es obligatorio antes de fin de mes
- Si un empleado se queda sin días posibles para descanso, se fuerza en el siguiente día disponible
- Los días 28-2 de cada mes se bloquean para descansos
- Sábados: nunca descanso

---

## 6. 🔐 Autenticación y Seguridad

**Motor:** Laravel Sanctum + Guards estándar de Laravel
**Middleware:** `auth` protege todas las rutas operativas

- Sesiones gestionadas con driver `file`
- CSRF habilitado en todos los formularios
- Logout via `POST /logout` (protegido contra CSRF)
- Configuración: `config/sanctum.php`, `config/auth.php`
- El token de sesión se mantiene seguro sin exposición en URLs

---


## 7. 📦 Seeders

| Seeder | Descripción |
|---|---|
| `DatabaseSeeder` | Orquesta todos los seeders |
| `EmployeeSeeder` | Carga los ~30 empleados de la nómina |
| `AreaSeeder` | Carga las 7 áreas del almacén |
| `ShiftTemplateSeeder` | Carga las plantillas de horario por día de la semana (basado en `datos_proyecto.md`) |
| `UserSeeder` | Crea el usuario admin `yuli@diferencialdx.com` |
| `AbsenceSeeder` | Carga ausencias de prueba (dev) |
| `ShiftSeeder` | Carga turnos de prueba (dev) |

---

## 9. 🧩 Modelos y Relaciones

```
User
  - (sin relaciones con empleados, solo autenticación)

Employee
  - belongsToMany(Area) via area_employee
  - hasMany(Shift)
  - hasMany(Absence)
  - hasMany(GenerationNote)

Area
  - belongsToMany(Employee) via area_employee
  - hasMany(Shift)
  - hasMany(ShiftTemplate)

Shift
  - belongsTo(Employee)
  - belongsTo(Area)

Absence
  - belongsTo(Employee)

ShiftTemplate
  - belongsTo(Area)

GenerationNote
  - belongsTo(Employee) [nullable]
```

---

## 10. 🎨 UX y Diseño

- **Tema:** Dark mode con gradientes y colores neón sobre fondo oscuro
- **Login:** Glassmorphism (tarjeta con `backdrop-filter: blur`)
- **Navbar:** Logo "GestionTurnos v1.0", botones de navegación principales, botón "Cerrar Sesión"
- **Planilla:** Colores diferenciados por área, novedades resaltadas con colores semánticos
- **Responsive:** Scroll horizontal en planillas; scroll vertical en asignación manual
- **Sticky headers:** Encabezados de días y columna de nombres siempre visibles al desplazarse
- **Badges:** Contador visual de notas de generación con colores según severidad

---

## 11. 🔧 Problemas Resueltos y Soluciones

| Problema | Solución Aplicada |
|---|---|
| `BindingResolutionException: ShiftController does not exist` | Agregar `use App\Http\Controllers\ShiftController;` en `routes/web.php` |
| Error 419 Page Expired (CSRF) | Sincronizar `APP_URL` en `.env` y cambiar `SESSION_DRIVER=file` |
| Clase Sanctum no encontrada en producción | Usar `composer install --no-scripts` y subir `config/sanctum.php` explícitamente |
| Días en inglés en producción | Usar `$carbonDate->locale('es')->translatedFormat()` en lugar de `translatedFormat()` solo |
| Scroll vertical bloqueado en asignación manual | Añadir `overflow: auto; height: calc(100vh - 200px)` al contenedor `.timeline-container` |
| Header de días no sticky al hacer scroll vertical | `position: sticky; top: 0; z-index: 50` en `.day-head` |
| Celda "ASESOR" no fija en ambas dimensiones | `.day-head.sticky-col` con `top: 0; left: 0; z-index: 200` |

---

*Documentación generada el 23 de marzo de 2026 — GestionTurnos v1.0*
