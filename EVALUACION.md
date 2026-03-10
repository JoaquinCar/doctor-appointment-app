# EVALUACIÓN - Módulo de Doctores


---

## 1. Migraciones

### Comando: `php artisan make:migration create_specialities_table`
**Por qué se usa:** Genera automáticamente un archivo de migración con timestamp ordenado en `database/migrations/`. Las migraciones permiten versionar el esquema de la base de datos de forma reproducible en cualquier entorno.
**Archivo generado:** `database/migrations/2026_02_24_100000_create_specialities_table.php`

---

### Archivo: `database/migrations/2026_02_24_100000_create_specialities_table.php`
**Tipo:** Creado

**Qué se hizo:** Define la estructura de la tabla `specialities`, que actúa como catálogo de especialidades médicas.

**Por qué:** Los doctores no deben escribir su especialidad manualmente. Un catálogo normalizado evita errores ortográficos, permite filtrar y reportar por especialidad de forma consistente, y garantiza integridad referencial mediante llave foránea.

```php
Schema::create('specialities', function (Blueprint $table) {
    $table->id();                         // BIGINT UNSIGNED AUTO_INCREMENT PK
    $table->string('name', 100)->unique(); // Nombre único: evita duplicados
    $table->timestamps();
});
```

**Decisiones técnicas:**
- `unique()` en `name`: garantiza que no existan dos especialidades con el mismo nombre.
- `string(100)`: longitud suficiente para cualquier especialidad médica real.

---

### Comando: `php artisan make:migration create_doctors_table`
**Por qué se usa:** Genera la migración para la tabla principal del módulo. Se crea DESPUÉS de `specialities` para que la llave foránea `speciality_id` pueda referenciar una tabla ya existente.
**Archivo generado:** `database/migrations/2026_02_24_100001_create_doctors_table.php`

---

### Archivo: `database/migrations/2026_02_24_100001_create_doctors_table.php`
**Tipo:** Creado

**Qué se hizo:** Define la estructura de la tabla `doctors` con las columnas requeridas: `id`, `user_id`, `speciality_id`, `medical_license_number`, `biography`, `created_at`, `updated_at`.

**Por qué:** Se separa la información profesional del doctor (tabla `doctors`) de la información de autenticación (tabla `users`). Esto sigue el mismo patrón que `patients`, manteniendo la coherencia arquitectónica del sistema.

```php
Schema::create('doctors', function (Blueprint $table) {
    $table->id();                                      // BIGINT(20) UNSIGNED AUTO_INCREMENT PK
    $table->foreignId('user_id')->unique()             // FK → users.id, UNIQUE (1 doctor por usuario)
          ->constrained()->cascadeOnDelete();          // CASCADE: al borrar usuario, se borra el doctor
    $table->foreignId('speciality_id')                 // FK → specialities.id
          ->constrained();
    $table->string('medical_license_number', 50)       // Cédula profesional, puede ser null
          ->nullable();
    $table->text('biography')->nullable();             // Biografía larga, puede ser null
    $table->timestamps();
});
```

**Decisiones técnicas:**
- `unique()` en `user_id`: un usuario solo puede ser doctor una vez (relación 1:1).
- `cascadeOnDelete()` en `user_id`: al eliminar el usuario, su registro de doctor se elimina automáticamente. Evita huérfanos en la BD.
- `nullable()` en `medical_license_number` y `biography`: campos opcionales al crear, se pueden completar después desde la vista de edición.
- `constrained()` en `speciality_id`: garantiza que solo se puedan asignar especialidades existentes en el catálogo.

---

## 2. Seeders

### Archivo: `database/seeders/SpecialitySeeder.php`
**Tipo:** Creado

**Qué se hizo:** Seeder que inserta 8 especialidades médicas reales en la tabla `specialities` al inicializar la base de datos.

**Por qué:** El sistema necesita datos en el catálogo de especialidades desde el primer despliegue para que el dropdown del formulario de doctores sea funcional. Se usa un Seeder (y no inserción manual en la migración) porque los seeders son la capa correcta para datos iniciales en Laravel: pueden re-ejecutarse, son independientes del esquema, y se versionan junto al código.

```php
Speciality::insert([
    ['name' => 'Cardiología'],
    ['name' => 'Pediatría'],
    ['name' => 'Neurología'],
    ['name' => 'Dermatología'],
    ['name' => 'Ortopedia'],
    ['name' => 'Ginecología'],
    ['name' => 'Psiquiatría'],
    ['name' => 'Medicina General'],
]);
```

---

### Archivo: `database/seeders/DoctorSeeder.php`
**Tipo:** Creado

**Qué se hizo:** Seeder que crea 2 usuarios con rol Doctor y sus registros de doctor asociados como datos de prueba.

**Por qué:** Permite verificar el funcionamiento del módulo inmediatamente después de ejecutar `php artisan db:seed`, sin necesidad de crear doctores manualmente desde la interfaz.

---

### Modificación: `database/seeders/DatabaseSeeder.php`
**Tipo:** Modificado

**Qué se hizo:** Se agregaron `SpecialitySeeder::class` y `DoctorSeeder::class` al array de seeders, en el orden correcto.

**Por qué:** El orden importa: `SpecialitySeeder` debe ejecutarse antes que `DoctorSeeder` porque los doctores requieren que existan especialidades para asignar `speciality_id`. Si se invierte el orden, falla la llave foránea.

```php
$this->call([
    RoleSeeder::class,
    UserSeeder::class,
    BloodTypeSeeder::class,
    PatientSeeder::class,
    SpecialitySeeder::class,  // ← ANTES que DoctorSeeder
    DoctorSeeder::class,
]);
```

---

## 3. Modelos Eloquent

### Archivo: `app/Models/Speciality.php`
**Tipo:** Creado

**Qué se hizo:** Modelo Eloquent para la tabla `specialities` con la relación inversa `hasMany(Doctor::class)`.

**Por qué:** El modelo permite interactuar con la tabla mediante Eloquent ORM. La relación `hasMany` posibilita consultas como `$speciality->doctors` para ver todos los doctores de una especialidad.

```php
public function doctors(): HasMany
{
    return $this->hasMany(Doctor::class);
}
```

---

### Archivo: `app/Models/Doctor.php`
**Tipo:** Creado

**Qué se hizo:** Modelo Eloquent para la tabla `doctors` con relaciones `belongsTo(User::class)` y `belongsTo(Speciality::class)`.

**Por qué:**
- `belongsTo(User::class)`: permite acceder a los datos del usuario asociado con `$doctor->user->name`, `$doctor->user->email`, etc.
- `belongsTo(Speciality::class)`: permite acceder al nombre de la especialidad con `$doctor->speciality->name` en lugar de manejar el ID manualmente.

```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function speciality(): BelongsTo
{
    return $this->belongsTo(Speciality::class);
}
```

---

### Modificación: `app/Models/User.php`
**Tipo:** Modificado

**Qué se hizo:** Se agregó la relación `hasOne(Doctor::class)` al modelo User.

**Por qué:** Mantiene la coherencia con la relación ya existente `hasOne(Patient::class)`. Permite acceder al doctor de un usuario con `$user->doctor` desde cualquier parte del sistema.

```php
public function doctor(): HasOne
{
    return $this->hasOne(Doctor::class);
}
```

---

## 4. Controlador

### Archivo: `app/Http/Controllers/Admin/DoctorController.php`
**Tipo:** Creado

**Qué se hizo:** Controlador resourceful que maneja el CRUD completo de doctores. Sigue el mismo patrón que `PatientController`.

**Métodos y lógica:**

- **`create()`:** Carga la lista de especialidades para el dropdown (`Speciality::pluck('name', 'id')`).

- **`store()`:** Valida los datos, crea el `User`, le asigna el rol `'Doctor'` (Spatie Permission), y crea el registro `Doctor` vinculado.

- **`edit()`:** Carga el doctor con sus relaciones (`user`, `speciality`), y pasa las especialidades disponibles para el dropdown.

- **`update()`:** Solo actualiza los 3 campos editables del doctor: `speciality_id`, `medical_license_number`, `biography`. Los datos del usuario (nombre, email) se editan por separado desde el módulo de usuarios.

- **`destroy()`:** Elimina el `User` asociado. El `CASCADE` en la BD elimina automáticamente el registro `Doctor`.

**Lógica de validación en `update()`:**
```php
$validated = $request->validate([
    'speciality_id'          => ['required', 'exists:specialities,id'],
    'medical_license_number' => ['nullable', 'string', 'max:50'],
    'biography'              => ['nullable', 'string', 'min:10', 'max:1000'],
]);
```

**Por qué `nullable` en cédula y biografía:** Son campos opcionales que el doctor puede no tener capturados aún. El sistema los muestra como "N/A" cuando están vacíos.

---

## 5. Componente Livewire (Datatable)

### Archivo: `app/Livewire/Admin/DataTables/DoctorTable.php`
**Tipo:** Creado

**Qué se hizo:** Componente Livewire que extiende `DataTableComponent` de rappasoft/laravel-livewire-tables. Muestra la tabla de doctores con búsqueda, ordenación y columna de acciones.

**Por qué Livewire Table:** Toda la gestión de paginación, búsqueda y ordenación es reactiva y sin recargar la página completa. Mismo patrón que `PatientTable` y `UserTable`.

**Lógica N/A en la tabla:**
```php
Column::make("Cédula Profesional", "medical_license_number")
    ->sortable()
    ->format(fn($value) => $value ?? 'N/A'),
```

**Por qué `?? 'N/A'`:** El operador null-coalescing retorna `'N/A'` cuando `medical_license_number` es `null` o no está definido, cumpliendo el requerimiento visual explícitamente.

**Eager loading de relaciones:**
```php
return Doctor::query()->with(['user', 'speciality']);
```
El `with()` previene el problema N+1 (una consulta por fila), cargando todas las relaciones en una sola consulta adicional.

---

## 6. Vistas (Blade)

### Archivo: `resources/views/admin/doctors/index.blade.php`
**Tipo:** Creado

**Qué se hizo:** Vista principal del módulo. Incluye el componente Livewire `doctor-table` y un botón "Nuevo" para crear doctores.

**Por qué:** Sigue el mismo patrón minimalista que `patients/index.blade.php`. La lógica de la tabla (incluyendo N/A) vive en `DoctorTable.php`, no en esta vista.

---

### Archivo: `resources/views/admin/doctors/create.blade.php`
**Tipo:** Creado

**Qué se hizo:** Formulario de creación que registra simultáneamente un `User` y un `Doctor`. Usa exclusivamente componentes WireUI.

**WireUI Components usados:**
- `x-wire-card`: contenedor principal con estilo de tarjeta.
- `x-wire-input`: campos de texto (nombre, email, teléfono, cédula).
- `x-wire-native-select`: dropdown para selección de especialidad.
- `x-wire-textarea`: campo de texto largo para biografía.
- `x-wire-button`: botones de acción.

**Por qué WireUI:** Proporciona componentes con validación visual integrada (muestra errores de Laravel directamente bajo el campo), estilos consistentes con Tailwind/Flowbite, y es la librería estándar del proyecto.

---

### Archivo: `resources/views/admin/doctors/edit.blade.php`
**Tipo:** Creado

**Qué se hizo:** Vista de edición SIN TABS (a diferencia del módulo de pacientes). Contiene:
1. **Header con lógica N/A**: muestra nombre, email, cédula y fragmento de biografía. Si estos están vacíos, muestra "N/A" explícitamente.
2. **Formulario con 3 campos**: Especialidad (dropdown), Cédula Profesional (input), Biografía (textarea).

**Lógica N/A en el header:**
```blade
<span class="font-medium">Cédula:</span>
{{ $doctor->medical_license_number ?? 'N/A' }}

<span class="font-medium">Biografía:</span>
{{ $doctor->biography ? Str::limit($doctor->biography, 60) : 'N/A' }}
```

**Por qué sin tabs:** El doctor solo tiene 3 campos editables (especialidad, cédula, biografía). Los tabs son para cuando hay muchos campos distribuidos en categorías (como pacientes con 15+ campos). Un formulario simple es más limpio y usable.

**Por qué `Str::limit()`:** La biografía puede ser muy larga. En el header solo se muestra un fragmento (60 caracteres) para no desbordar el diseño. El texto completo está disponible en el campo textarea del formulario.

---

### Archivo: `resources/views/admin/doctors/show.blade.php`
**Tipo:** Creado

**Qué se hizo:** Vista de detalle del doctor. Muestra información del usuario y datos médicos del doctor en una grid de 2 columnas usando `x-wire-card`.

**Lógica N/A:**
```blade
{{ $doctor->medical_license_number ?? 'N/A' }}
{{ $doctor->biography ?? 'Sin biografía registrada' }}
```

---

### Archivo: `resources/views/admin/doctors/actions.blade.php`
**Tipo:** Creado

**Qué se hizo:** Partial de Blade que renderiza los botones de acción (Ver, Editar, Eliminar) para cada fila de la tabla Livewire.

**Por qué un archivo separado:** El componente Livewire de rappasoft recibe una función que retorna una vista (`view('admin.doctors.actions', ...)`). Separar los botones en su propio archivo mantiene el código del componente limpio y los botones son reutilizables.

```blade
<form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="delete-form">
    @csrf
    @method('DELETE')
    <x-wire-button type="submit" red xs>
        <i class="fa-solid fa-trash"></i>
    </x-wire-button>
</form>
```

**Por qué `class="delete-form"`:** El layout admin tiene un listener de SweetAlert2 que intercepta todos los formularios con esa clase y muestra una confirmación antes de enviar el DELETE.

---

## 7. Rutas

### Modificación: `routes/admin.php`
**Tipo:** Modificado

**Qué se hizo:** Se agregó la ruta resource para doctores.

```php
Route::resource('doctors', \App\Http\Controllers\Admin\DoctorController::class);
```

**Por qué `resource()`:** Genera automáticamente las 7 rutas RESTful (index, create, store, show, edit, update, destroy) con sus nombres (`admin.doctors.*`). Es el estándar de Laravel para CRUDs.

---

## 8. Traducciones — `lang/es/validation.php`

### Modificación: `lang/es/validation.php`
**Tipo:** Modificado

**Qué se hizo:** Se agregaron los nombres en español de los campos del doctor en el array `attributes`.

```php
// Campos de doctores
'speciality_id'          => 'especialidad',
'medical_license_number' => 'cédula profesional',
'biography'              => 'biografía',
```

**Por qué:** Laravel usa este array para mostrar mensajes de validación con nombres legibles. Sin esto, el mensaje sería "El campo speciality_id es obligatorio" en vez de "El campo especialidad es obligatorio".

---

## 9. Ajustes posteriores a la implementación inicial

### Corrección: Columna "Biografía" faltante en la tabla del index
**Archivo:** `app/Livewire/Admin/DataTables/DoctorTable.php`
**Tipo:** Modificado

**Qué se hizo:** Se agregó la columna `Biografía` a la tabla Livewire del index con lógica N/A explícita. Sin esta columna, el requerimiento visual de mostrar N/A en el index no tenía efecto para la biografía.

```php
Column::make('Biografía', 'biography')
    ->format(fn($value) => $value
        ? \Illuminate\Support\Str::limit($value, 50)
        : 'N/A'
    ),
```

**Por qué `Str::limit($value, 50)`:** La biografía puede contener cientos de caracteres. En una celda de tabla solo se muestran los primeros 50 para no romper el diseño. Si el campo es `null` o vacío, el operador ternario retorna `'N/A'` directamente sin llamar a `Str::limit`.

**Por qué `\Illuminate\Support\Str::limit`:** En los callbacks de columnas de rappasoft se usa el FQCN (nombre completamente calificado) para evitar ambigüedad de namespace, ya que el callback es una closure PHP y no un contexto de Blade donde los facades son auto-resueltos.

**Estado del requerimiento tras la corrección:**

| Ubicación | N° Licencia | Biografía |
|---|---|---|
| `index.blade.php` (tabla Livewire) | ✅ N/A | ✅ N/A |
| `edit.blade.php` (header) | ✅ N/A | ✅ N/A |

---

### Modificación: `resources/views/admin/doctors/edit.blade.php`
**Tipo:** Modificado

**Qué se hizo:** Se eliminó el bloque `x-wire-alert` que mostraba el aviso "Edición de cuenta de usuario" dentro del formulario de edición.

**Por qué:** El módulo de doctores solo expone los 3 campos propios del doctor (especialidad, número de licencia, biografía). El aviso de redirección al módulo de usuarios no corresponde al flujo de este formulario y añadía ruido visual innecesario.

---

### Modificación: "Cédula Profesional" → "Número de licencia médica" (solo números)
**Archivos modificados:**
- `resources/views/admin/doctors/edit.blade.php`
- `resources/views/admin/doctors/create.blade.php`
- `resources/views/admin/doctors/show.blade.php`
- `app/Livewire/Admin/DataTables/DoctorTable.php`
- `app/Http/Controllers/Admin/DoctorController.php`
- `lang/es/validation.php`

**Qué se hizo:** Se renombró el campo de "Cédula Profesional" a "Número de licencia médica" en todas las vistas, la tabla y las traducciones. Adicionalmente se cambió la validación para aceptar únicamente dígitos.

**Por qué:** El término correcto en el contexto médico mexicano es "Número de cédula/licencia médica" y su valor es estrictamente numérico (emitido por la SEP). Aceptar letras sería un error de dominio.

**Cambio en validación (`DoctorController.php`):**
```php
// Antes
'medical_license_number' => ['nullable', 'string', 'max:50'],

// Después
'medical_license_number' => ['nullable', 'digits_between:1,20'],
```

**Por qué `digits_between:1,20`:** La regla `digits_between` de Laravel valida que el valor contenga únicamente dígitos (0-9) y que su longitud esté entre 1 y 20 caracteres. Rechaza cualquier letra o símbolo. Se combina con `nullable` para que el campo siga siendo opcional.

**Cambio en el input (vistas):**
```blade
<x-wire-input
    label="Número de licencia médica"
    name="medical_license_number"
    placeholder="Solo números (opcional)"
    inputmode="numeric"
    ...
/>
```

**Por qué `inputmode="numeric"`:** Atributo HTML5 que indica al navegador (especialmente en móviles) que debe mostrar el teclado numérico al enfocar este campo. No restringe el input a nivel de HTML, la restricción real la aplica la validación del servidor.

---

## 10. Reto de Lógica: Catálogo de Especialidades (`speciality_id`)

**El problema:** La tabla `doctors` tiene un campo `speciality_id` (llave foránea). Esto significa que un doctor no escribe su especialidad como texto libre, sino que selecciona un valor de un catálogo preexistente. Si ese catálogo está vacío, el dropdown del formulario aparece vacío y no se puede registrar ningún doctor.

**La pregunta:** ¿Cómo se crea y puebla ese catálogo para que el sistema sea funcional desde el primer despliegue?

### Solución: Migración + Seeder + Relación Eloquent + Dropdown WireUI

**Paso 1 — Migración (`specialities`):**
Se crea la tabla `specialities` con `id` y `name` (único). Esta tabla es independiente y no depende de ninguna otra, por lo que su migración va primero en el timestamp.

```php
// database/migrations/2026_02_24_100000_create_specialities_table.php
$table->id();
$table->string('name', 100)->unique();
$table->timestamps();
```

**Paso 2 — Llave foránea en `doctors`:**
La migración de `doctors` (timestamp posterior) referencia `specialities` mediante `foreignId`:

```php
// database/migrations/2026_02_24_100001_create_doctors_table.php
$table->foreignId('speciality_id')->constrained();
```

`constrained()` sin argumentos busca automáticamente la tabla `specialities` (convención Laravel: nombre del campo sin `_id` en plural). La BD rechaza cualquier `speciality_id` que no exista en el catálogo.

**Paso 3 — SpecialitySeeder (poblar el catálogo):**
Se elige un Seeder en lugar de insertar datos directamente en la migración porque:
- Las migraciones son para definir estructura, no para insertar datos.
- Los seeders pueden re-ejecutarse de forma independiente (`php artisan db:seed --class=SpecialitySeeder`).
- Se pueden modificar o ampliar sin tocar el esquema de la BD.

```php
// database/seeders/SpecialitySeeder.php
$specialities = [
    'Cardiología', 'Pediatría', 'Neurología', 'Dermatología',
    'Ortopedia',   'Ginecología', 'Psiquiatría', 'Medicina General',
];

foreach ($specialities as $name) {
    Speciality::firstOrCreate(['name' => $name]);
}
```

`firstOrCreate` previene duplicados si el seeder se ejecuta más de una vez.

**Paso 4 — Orden en `DatabaseSeeder.php`:**
```php
SpecialitySeeder::class,  // ← primero: llena el catálogo
DoctorSeeder::class,      // ← después: puede usar speciality_id válidos
```

Si el orden se invierte, `DoctorSeeder` falla con error de llave foránea porque intenta asignar un `speciality_id` que aún no existe.

**Paso 5 — Relación Eloquent:**
```php
// app/Models/Doctor.php
public function speciality(): BelongsTo {
    return $this->belongsTo(Speciality::class);
}

// app/Models/Speciality.php
public function doctors(): HasMany {
    return $this->hasMany(Doctor::class);
}
```

Gracias a estas relaciones se accede al nombre de la especialidad con `$doctor->speciality->name` sin escribir JOINs manuales.

**Paso 6 — Dropdown en el controlador y la vista:**
El controlador carga las especialidades como array `[id => nombre]`:
```php
// DoctorController@create / edit
$specialities = Speciality::pluck('name', 'id')->toArray();
return view('admin.doctors.create', compact('specialities'));
```

La vista usa `x-wire-native-select` de WireUI que genera un `<select>` HTML nativo con los estilos del sistema:
```blade
<x-wire-native-select
    label="Especialidad"
    name="speciality_id"
    :options="$specialities"
    option-key-value
    placeholder="Seleccione una especialidad"
    :value="old('speciality_id')"
/>
```

`option-key-value` indica a WireUI que el array tiene formato `[id => label]`, usando el `id` como `value` del `<option>` y el nombre como texto visible.

**Resultado final:** Al hacer `php artisan migrate && php artisan db:seed`, el sistema queda listo con 8 especialidades en el catálogo y el dropdown funcional desde el primer despliegue, sin necesidad de inserción manual.

---

## 11. Comandos Finales de Despliegue

### Comando: `php artisan migrate`
**Por qué se usa:** Ejecuta todas las migraciones pendientes en orden cronológico. Crea las tablas `specialities` y `doctors` en la base de datos.
**Archivos que procesa:**
- `2026_02_24_100000_create_specialities_table.php`
- `2026_02_24_100001_create_doctors_table.php`

---

### Comando: `php artisan db:seed --class=SpecialitySeeder`
**Por qué se usa:** Ejecuta únicamente el seeder de especialidades para poblar el catálogo inicial. Los `--class` permite ejecutar un seeder específico sin re-ejecutar todos los demás.
**Archivo que ejecuta:** `database/seeders/SpecialitySeeder.php`
**Resultado:** 8 filas insertadas en `specialities`.

---

### Comando: `php artisan db:seed --class=DoctorSeeder`
**Por qué se usa:** Inserta datos de prueba de doctores para poder probar el módulo inmediatamente.
**Archivo que ejecuta:** `database/seeders/DoctorSeeder.php`
**Resultado:** 2 usuarios con rol Doctor y sus registros de doctor creados.

---

*Documento generado durante el desarrollo del Módulo de Doctores — MediMatch v1.0*
