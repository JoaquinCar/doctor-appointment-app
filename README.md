<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

# Panel Administrativo con Laravel y Flowbite

Este proyecto implementa una plantilla base para un panel administrativo en **Laravel**, utilizando componentes **Blade** y la librería de componentes de UI **Flowbite** sobre Tailwind CSS. El objetivo es crear una estructura de layout reutilizable y modular para las vistas protegidas de la aplicación.

---

## Pasos de la Implementación 🚀

### 1. Creación del Layout (`admin.blade.php`)

Para crear una plantilla base para las vistas del panel administrativo, se siguieron estos pasos:

* **Generación del Componente**: Se utilizó Artisan para crear un nuevo componente de Blade llamado `AdminLayout` con el siguiente comando:
    ```bash
    php artisan make:component AdminLayout
    ```
* **Reubicación del Layout**: El archivo de la vista del componente, `admin-layout.blade.php`, fue movido desde `resources/views/components/` a la carpeta `resources/views/layouts/` para una mejor organización de las plantillas. Posteriormente, fue renombrado a `admin.blade.php`.

* **Actualización de la Clase del Componente**: Se modificó la clase del componente en `app/View/Components/AdminLayout.php` para que el método `render()` apunte a la nueva ubicación del archivo Blade:
    ```php
    return view('layouts.admin');
    ```

---

### 2. Integración de Flowbite

Para construir la interfaz del panel, se integró la librería Flowbite:

* **Instalación**: Se añadió Flowbite al proyecto como una dependencia de NPM a través del comando:
    ```bash
    npm install flowbite --save
    ```
* **Separación de Componentes**: El código HTML del **navbar** (barra de navegación superior) y del **sidebar** (barra lateral) se obtuvo de la documentación oficial de Flowbite. Para mantener el layout principal limpio, estos bloques de código se separaron en archivos individuales:
    * `resources/views/layouts/includes/admin/navigation.blade.php`
    * `resources/views/layouts/includes/admin/sidebar.blade.php`
* **Inclusión en el Layout**: Finalmente, estos componentes se incluyeron en la plantilla `admin.blade.php` utilizando las directivas de Blade, asegurando que aparezcan en todas las páginas que hereden de este layout:
    ```blade
    @include('layouts.includes.admin.navigation')
    @include('layouts.includes.admin.sidebar')
    ```

---

### 3. Prueba de `slots` e `includes`

Se verificó que la estructura modular funcionara correctamente de la siguiente manera:

* **Uso del Layout**: La vista principal del dashboard, ubicada en `resources/views/admin/dashboard.blade.php`, se envolvió con la etiqueta del componente para heredar la plantilla:
    ```blade
    <x-admin-layout>
        Hola desde Admin
    </x-admin-layout>
    ```
* **Inyección de Contenido con `slot`**: El contenido específico de cada página (como el texto "Hola desde Admin") se inyecta dinámicamente en la plantilla `admin.blade.php` a través de la variable `{{ $slot }}`, permitiendo que el contenido cambie sin duplicar el código del layout.

* **Integración con Vistas Existentes**: Para unificar la apariencia del panel, la vista de perfil de usuario en `resources/views/profile/show.blade.php` fue modificada para usar el nuevo layout, cambiando la etiqueta `<x-app-layout>` por `<x-admin-layout>`.
