# Sistema de Citas Médicas - Doctor Appointment App

## 📋 Descripción General

Aplicación web para la gestión integral de citas médicas construida con **Laravel 12**, **Livewire 3**, **Laravel Jetstream** y **Flowbite**. El sistema permite administrar citas entre pacientes y doctores a través de un panel administrativo centralizado.

## 🎯 Objetivo

Facilitar la gestión de citas médicas mediante una plataforma web que conecte administradores, doctores y pacientes, permitiendo la programación eficiente de consultas, evitando conflictos de horarios y optimizando la atención médica.

## 👥 Tipos de Usuarios

### 1. **Administrador**
- Gestión completa del sistema
- CRUD de doctores y especialidades
- CRUD de pacientes
- Gestión de horarios médicos
- Configuración de consultorios
- Asignación de citas
- Reportes y estadísticas
- Gestión de cancelaciones y reprogramaciones

### 2. **Doctores**
- Visualización de su agenda personal
- Consulta de citas asignadas
- Gestión de disponibilidad horaria
- Actualización de información de perfil
- Registro de observaciones en citas
- Marcado de asistencia/inasistencia

### 3. **Pacientes (Clientes)**
- Solicitud de citas médicas
- Visualización de citas programadas
- Cancelación de citas (con restricciones de tiempo)
- Actualización de datos personales
- Historial de citas
- Notificaciones de recordatorio

## 🏗️ Arquitectura Actual

### Layouts
- **AdminLayout** (`layouts/admin.blade.php`): Panel administrativo con sidebar y navegación
- **AppLayout** (`layouts/app.blade.php`): Layout para usuarios autenticados
- **GuestLayout** (`layouts/guest.blade.php`): Layout para visitantes

### Características Implementadas
- Sistema de autenticación completo con 2FA
- Panel administrativo con Flowbite Sidebar
- Navegación multi-nivel con secciones
- Componentes modulares reutilizables
- Gestión de perfiles de usuario
- API Tokens con Sanctum

## 📊 Estructura de Base de Datos (Diseño Propuesto)

### 1. **users** (Tabla Principal de Usuarios)
Sistema base de usuarios que se extiende con roles específicos.

**Campos:**
- `id` - PK
- `name` - VARCHAR(255)
- `email` - VARCHAR(255) UNIQUE
- `password` - VARCHAR(255)
- `role` - ENUM('admin', 'doctor', 'patient')
- `phone` - VARCHAR(20)
- `email_verified_at` - TIMESTAMP NULL
- `profile_photo_path` - VARCHAR(2048) NULL
- `two_factor_secret` - TEXT NULL
- `two_factor_recovery_codes` - TEXT NULL
- `is_active` - BOOLEAN DEFAULT TRUE
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP

**Relaciones:**
- Uno a Uno con `doctors` (cuando role='doctor')
- Uno a Uno con `patients` (cuando role='patient')

---

### 2. **doctors** (Información Específica de Doctores)
Datos profesionales y especialización de los médicos.

**Campos:**
- `id` - PK
- `user_id` - FK → users.id (UNIQUE)
- `specialty_id` - FK → specialties.id
- `license_number` - VARCHAR(50) UNIQUE
- `years_experience` - INT
- `education` - TEXT NULL
- `biography` - TEXT NULL
- `consultation_fee` - DECIMAL(10,2)
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP

**Relaciones:**
- Pertenece a `users`
- Pertenece a `specialties`
- Tiene muchos `doctor_schedules`
- Tiene muchos `appointments`

---

### 3. **patients** (Información de Pacientes)
Datos médicos y personales de los pacientes.

**Campos:**
- `id` - PK
- `user_id` - FK → users.id (UNIQUE)
- `date_of_birth` - DATE
- `gender` - ENUM('male', 'female', 'other')
- `blood_type` - VARCHAR(5) NULL
- `allergies` - TEXT NULL
- `medical_history` - TEXT NULL
- `emergency_contact_name` - VARCHAR(255)
- `emergency_contact_phone` - VARCHAR(20)
- `address` - TEXT
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP

**Relaciones:**
- Pertenece a `users`
- Tiene muchos `appointments`

---

### 4. **specialties** (Especialidades Médicas)
Catálogo de especialidades médicas disponibles.

**Campos:**
- `id` - PK
- `name` - VARCHAR(100) UNIQUE
- `description` - TEXT NULL
- `icon` - VARCHAR(50) NULL
- `is_active` - BOOLEAN DEFAULT TRUE
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP

**Relaciones:**
- Tiene muchos `doctors`

**Ejemplos:**
- Cardiología, Pediatría, Neurología, Dermatología, etc.

---

### 5. **offices** (Consultorios)
Espacios físicos donde se realizan las consultas.

**Campos:**
- `id` - PK
- `name` - VARCHAR(100)
- `floor` - VARCHAR(10) NULL
- `room_number` - VARCHAR(20)
- `capacity` - INT DEFAULT 1
- `equipment` - TEXT NULL (JSON con equipamiento disponible)
- `is_active` - BOOLEAN DEFAULT TRUE
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP

**Relaciones:**
- Tiene muchos `doctor_schedules`
- Tiene muchos `appointments`

---

### 6. **doctor_schedules** (Horarios de Doctores)
Define los horarios de disponibilidad de cada doctor.

**Campos:**
- `id` - PK
- `doctor_id` - FK → doctors.id
- `office_id` - FK → offices.id
- `day_of_week` - ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')
- `start_time` - TIME
- `end_time` - TIME
- `appointment_duration` - INT (minutos, ej: 30)
- `is_active` - BOOLEAN DEFAULT TRUE
- `valid_from` - DATE NULL (permite horarios temporales)
- `valid_until` - DATE NULL
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP

**Relaciones:**
- Pertenece a `doctors`
- Pertenece a `offices`

**Índices únicos:**
- `UNIQUE(doctor_id, day_of_week, start_time, end_time)` - Evita dobles horarios

**Validaciones:**
- No permitir solapamiento de horarios para el mismo doctor
- Validar que `end_time` > `start_time`

---

### 7. **appointments** (Citas Médicas)
Registro de todas las citas programadas.

**Campos:**
- `id` - PK
- `patient_id` - FK → patients.id
- `doctor_id` - FK → doctors.id
- `office_id` - FK → offices.id
- `appointment_date` - DATE
- `appointment_time` - TIME
- `duration` - INT (minutos)
- `status` - ENUM('scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show')
- `reason` - TEXT
- `notes` - TEXT NULL (notas del doctor)
- `cancellation_reason` - TEXT NULL
- `cancelled_at` - TIMESTAMP NULL
- `cancelled_by` - FK → users.id NULL
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP

**Relaciones:**
- Pertenece a `patients`
- Pertenece a `doctors`
- Pertenece a `offices`
- Tiene muchos `appointment_reminders`

**Índices únicos:**
- `UNIQUE(doctor_id, appointment_date, appointment_time)` - Evita dobles citas del mismo doctor
- `UNIQUE(office_id, appointment_date, appointment_time)` - Evita doble uso del consultorio

**Validaciones:**
- No permitir citas en horarios no disponibles del doctor
- No permitir citas fuera del horario del consultorio
- Restricciones de cancelación (ej: mínimo 24h antes)

---

### 8. **appointment_reminders** (Recordatorios de Citas)
Sistema de notificaciones programadas.

**Campos:**
- `id` - PK
- `appointment_id` - FK → appointments.id
- `reminder_type` - ENUM('email', 'sms', 'notification')
- `scheduled_for` - TIMESTAMP (cuándo enviar el recordatorio)
- `sent_at` - TIMESTAMP NULL
- `status` - ENUM('pending', 'sent', 'failed')
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP

**Relaciones:**
- Pertenece a `appointments`

**Ejemplo de uso:**
- Recordatorio 24h antes: email
- Recordatorio 2h antes: SMS
- Recordatorio 30min antes: notificación push

---



---

## 🚀 Casos de Uso Principales

### Escenario 1: Programación de Cita
1. **Paciente** selecciona especialidad
2. Sistema muestra doctores disponibles
3. Paciente elige doctor y fecha
4. Sistema valida:
   - Horario del doctor (`doctor_schedules`)
   - Disponibilidad del consultorio (`offices`)
   - No hay excepciones (`schedule_exceptions`)
   - No hay conflictos (`appointments` con UNIQUE)
5. Se crea la cita y se programan recordatorios

### Escenario 2: Doble Horario (Prevención)
**Problema:** Doctor asignado en dos consultorios al mismo tiempo.

**Solución:**
- Constraint UNIQUE en `doctor_schedules`: `(doctor_id, day_of_week, start_time, end_time)`
- Validación de solapamiento en el modelo antes de guardar

### Escenario 3: Cancelación con Restricciones
1. Paciente solicita cancelación
2. Sistema valida tiempo mínimo (ej: 24h antes)
3. Si es válido:
   - Actualiza `status` a 'cancelled'
   - Registra `cancelled_at` y `cancelled_by`
   - Guarda razón en `cancellation_reason`
   - Crea log en `audit_logs`
4. Se libera el horario para otras citas

### Escenario 4: Gestión de Vacaciones
1. Admin registra vacaciones en `schedule_exceptions`
2. Sistema automáticamente:
   - Bloquea nuevas citas en ese período
   - Notifica a pacientes con citas existentes
   - Ofrece reprogramación

---
## 📝 Notas Técnicas

### Índices Recomendados
```sql
-- Appointments
INDEX idx_appt_patient (patient_id, appointment_date)
INDEX idx_appt_doctor (doctor_id, appointment_date)
INDEX idx_appt_status (status, appointment_date)

-- Doctor Schedules
INDEX idx_schedule_doctor (doctor_id, day_of_week)

-- Schedule Exceptions
INDEX idx_exception_date (exception_date)
INDEX idx_exception_doctor (doctor_id, exception_date)
```

### Políticas de Caché
- Horarios de doctores: Cache 1 hora
- Especialidades: Cache 24 horas
- Disponibilidad de consultorios: Cache 30 minutos

### Queues (Colas)
- Envío de recordatorios: Queue con delay
- Generación de reportes: Queue en background
- Notificaciones: Queue prioritaria

---

## 👨‍💻 Desarrollo

```bash
# Instalación
composer install
npm install

# Configuración
cp .env.example .env
php artisan key:generate

# Migraciones
php artisan migrate --seed

# Ejecución
npm run dev        # Vite para assets
php artisan serve  # Servidor de desarrollo
```

---

**Versión:** 1.0.0
**Última actualización:** Octubre 2025
**Framework:** Laravel 12.x
