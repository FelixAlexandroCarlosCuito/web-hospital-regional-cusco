# Sistema de Gestión de Citas Médicas - Hospital Regional del Cusco

Plataforma transaccional orientada a la web para la gestión de citas médicas presenciales. Diseñada como un MVP para el Hospital Regional del Cusco, su objetivo central es eliminar las barreras logísticas (colas físicas "interminables") y optimizar el flujo operativo entre los pacientes, el personal médico y el área de admisión.

## 🚀 Características Principales (Release 1.0.0)

- **Control de Acceso Basado en Roles (RBAC):** Vistas y lógicas de negocio separadas para `Paciente`, `Médico` y `Admisión`.
- **Wizard de Reservas:** Flujo de programación de citas con validación asíncrona de disponibilidad de horarios y médicos.
- **Generación de Tickets:** Emisión de constancias de citas con formato de impresión física adaptado (soporte SIS y Particular).
- **Gestión Operativa:** Panel de ventanilla para cancelación y auditoría de la agenda clínica en tiempo real.
- **Interfaz Institucional:** Diseño responsivo basado en Bootstrap 5, respetando la paleta de colores oficial del hospital.

## 🛠️ Stack Tecnológico

- **Backend:** Laravel (PHP)
- **Frontend:** Blade, Bootstrap 5, CSS3, JavaScript
- **Base de Datos:** MySQL / MariaDB
- **Control de Versiones:** Git / GitHub (Commits Convencionales)

## ⚙️ Requisitos Previos

Para desplegar este proyecto en un entorno local, asegúrese de tener instalado:
- PHP
- Composer
- Motor de base de datos MySQL (XAMPP o similar)
- Repositorio del proyecto

## 🖥️ Guía de Instalación Local

Siga estos pasos estructurados para compilar y ejecutar el proyecto desde cero:

### En laboratorios de la universidad
- Seguir los pasos adjuntos en el [documento de la semana 9](https://aulavirtualuc.continental.edu.pe/mod/resource/view.php?id=3220874).
- Continuar con las indicaciones para computadoras propias detalladas a continuación.

### En computadoras propias
> **Nota de contingencia:** Si ocurre un fallo en los siguientes pasos, intente seguir las indicaciones del documento de la semana 9.

1. Crear desde cero una base de datos local con el nombre `hospital_regional`.
2. Abrir el proyecto clonado en su editor de código (ej. Visual Studio Code).
3. Abrir la terminal integrada y ejecutar la instalación de dependencias principales:
   ```bash
   composer install
   ```
4. Generar el archivo de variables de entorno:
   ```bash
   cp .env.example .env
   ```
5. Editar el archivo `.env` y **cambiar el puerto de la base de datos** (`DB_PORT`) para que coincida con el mostrado en el panel de control de XAMPP (suele ser 3306, pero puede variar según la configuración).
6. Generar la clave criptográfica de la aplicación:
   ```bash
   php artisan key:generate
   ```
7. Ejecutar las migraciones y poblar la base de datos con los registros de prueba (Seeders):
   ```bash
   php artisan migrate:fresh --seed
   ```
8. Levantar el servidor local de desarrollo:
   ```bash
   php artisan serve
   ```

## 🔐 Accesos y Usuarios de Prueba

> **Reglas de negocio simuladas:** La creación de cuentas de pacientes y el acceso de personal interno están restringidos. La validación de pacientes simula una conexión con RENIEC, y las cuentas del personal médico/admisión son pre-creadas por la administración del hospital.

Para realizar pruebas operativas en el entorno local, utilice los siguientes datos precargados:

### 1. Flujo de Paciente (Nuevo Registro)
Para ingresar como paciente, debe registrar una nueva cuenta validando su identidad con estos datos de prueba (Simulación RENIEC):
- **Nro DNI:** `76543210`
- **Dígito verificador:** `4`
- **Fecha de emisión:** `2020-05-15`

### 2. Flujos Internos (Cuentas pre-creadas)
El sistema cuenta con personal de prueba ya registrado. Utilice las siguientes credenciales en el Login:

| Rol Asignado | Usuario (Documento) | Contraseña |
| :--- | :--- | :--- |
| **Médico** (Dr. Carlos) | `44556677` | `44556677` |
| **Médico** (Dr. Sumaq) | `77665544` | `77665544` |
| **Admisión / Ventanilla** | `11223344` | `11223344` |

---
*Desarrollado para el Proyecto Académico de Programación Web.*
