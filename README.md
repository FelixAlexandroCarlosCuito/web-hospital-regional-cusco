# Sistema de Gestión de Citas Médicas — Hospital Regional del Cusco

Plataforma web transaccional orientada a la gestión de citas médicas de consulta externa para el Hospital Regional del Cusco (HRC). Su objetivo central es reducir las colas presenciales de madrugada y articular el flujo operativo entre pacientes, personal médico, ventanilla de admisión y administración institucional.

## 🚀 Características Principales (Release 2.0.0)

- **Control de Acceso Basado en Roles (RBAC):** Flujos, paneles y permisos estrictamente separados para `Paciente`, `Médico`, `Admisión` y `Administrador`, con revocación inmediata de sesiones ante baja lógica de cuentas.
- **Asistente de Reservas con Asignación Automática:** Validación asíncrona de horarios hábiles (lunes a viernes) y asignación automática al especialista activo con menor carga diaria mediante transacciones y bloqueos pesimistas (`lockForUpdate`) para evitar sobreventa de cupos por concurrencia.
- **Constancia Oficial con Verificación QR:** Documento institucional adaptado a impresión monocromática en una sola página A4 (`@media print`), indicaciones diferenciadas para pacientes SIS y Particulares (pago presencial en Caja) y código QR dinámico con firma criptográfica (`URL::signedRoute`) que valida la vigencia en tiempo real y enmascara el DNI en consultas externas.
- **Panel Administrativo y Analítica:** Dashboard con KPIs diarios y mensuales, cuatro gráficos interactivos (`Chart.js`), tabla de carga por médico y gestión completa de Especialidades (con imágenes de referencia), Personal (baja lógica) y Novedades institucionales.
- **Portal Público Institucional:** Portada con indicadores reales, cartera multimedia de especialidades, portal de Novedades y Campañas filtrable por categoría y paginado, y Guía de Atención.
- **Optimización de Consultas (Eloquent y MySQL):** Índices compuestos en tablas críticas, consultas sargables, selección acotada de columnas, agregaciones condicionales, paginación, caché de lectura pública con invalidación automática y auditoría anti-N+1 (`preventLazyLoading`).
- **Seguridad Web (Enfoque OWASP):** Prevención de asignación masiva en modelos, consultas parametrizadas contra SQL Injection, escape automático contra XSS, protección CSRF, limitación de tasa (`throttle`) contra fuerza bruta, cabeceras de seguridad HTTP (`SecurityHeaders`), páginas institucionales de error (`403`, `404`, `419`, `429`, `500`) y auditoría de dependencias con `composer audit`.

## 🛠️ Stack Tecnológico

- **Backend:** Laravel (PHP), Eloquent ORM
- **Frontend:** Blade, Bootstrap 5, Bootstrap Icons, CSS3, JavaScript
- **Librerías Frontend:** Chart.js (analítica administrativa), `qrcode-generator` (QR vectorial)
- **Base de Datos:** MySQL / MariaDB (InnoDB con índices compuestos)
- **Control de Versiones:** Git / GitHub (Commits Convencionales)

## ⚙️ Requisitos Previos

Para desplegar este proyecto en un entorno local, asegúrese de tener instalado:
- PHP (8.2 o superior)
- Composer
- Motor de base de datos MySQL / MariaDB (XAMPP o similar)
- Git y repositorio del proyecto

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
5. Editar el archivo `.env` verificando que `DB_DATABASE=hospital_regional` y **ajustando el puerto de la base de datos** (`DB_PORT`) para que coincida con el mostrado en el panel de control de XAMPP (suele ser `3306`, pero puede variar según la configuración).
6. Generar la clave criptográfica de la aplicación:
   ```bash
   php artisan key:generate
   ```
7. Crear el enlace simbólico de almacenamiento público (necesario para mostrar las imágenes de Especialidades y Novedades):
   ```bash
   php artisan storage:link
   ```
8. Ejecutar las migraciones (incluidos los índices de rendimiento y seguridad) y poblar la base de datos con los registros de prueba:
   ```bash
   php artisan migrate:fresh --seed
   ```
9. Levantar el servidor local de desarrollo:
   ```bash
   php artisan serve
   ```

> **Prueba de escaneo QR desde celular (opcional):** Si desea escanear el código QR de la constancia desde un dispositivo móvil en la misma red Wi-Fi, configure `APP_URL=http://<IP_LOCAL_DE_TU_PC>:8000` en `.env`, ejecute `php artisan config:clear` y levante el servidor con `php artisan serve --host=0.0.0.0`.

## 🔐 Accesos y Usuarios de Prueba

> **Reglas de negocio simuladas:** El registro de pacientes valida la identidad contra un padrón simulado de RENIEC, mientras que las cuentas de personal interno (`Médico`, `Admisión` y `Administrador`) son gestionadas exclusivamente por Administración.

### 1. Flujo de Paciente (Nuevo Registro — Simulación RENIEC)
Utilice cualquiera de estos documentos ficticios para probar el registro en dos fases:

| DNI | Dígito Verificador | Fecha de Emisión | Ciudadano | Escenario |
| :--- | :---: | :---: | :--- | :--- |
| `76543210` | `4` | `2020-05-15` | Juan Carlos Pérez Gonzales | Libre para registro |
| `71234568` | `7` | `2021-03-10` | María Elena Condori Ccahuana | Libre para registro |
| `74569812` | `2` | `2019-11-22` | Diego Armando Huamán Soto | Libre para registro |
| `70112233` | `9` | `2022-01-18` | Luz Marina Quispe Mamani | Ya registrado (prueba detección duplicada) |

### 2. Cuentas Pre-creadas (Inicio de Sesión)
El seeder precarga usuarios para todos los roles y escenarios de seguridad:

| Rol / Escenario | Usuario (DNI) | Contraseña | Detalle de prueba |
| :--- | :--- | :--- | :--- |
| **Administrador** | `76869228` | `admin1` | Acceso a KPIs, gráficos, catálogo, personal y novedades |
| **Admisión / Ventanilla** | `11223344` | `11223344` | Consulta de agendas por médico y cancelación operativa |
| **Médico activo** (Dr. Sumaq Qori) | `77665544` | `77665544` | Medicina General (comparte especialidad para balanceo de carga) |
| **Médico activo** (Dr. Carlos Mendoza) | `44556677` | `44556677` | Cardiología — Consultorio 12 |
| **Paciente pre-registrado** (Luz Marina) | `70112233` | `123456` | 2/3 citas activas y 1 cita con candado de cancelación `< 12 h` |
| **Médico inactivo** (Dr. Héctor Salazar) | `45556677` | `45556677` | Prueba de bloqueo de acceso por baja lógica |

## 🛡️ Verificación Rápida de Seguridad y Caché

Para auditar dependencias o limpiar cachés tras modificar configuración o rutas:

```bash
composer audit
php artisan optimize:clear
```

---
*Proyecto académico desarrollado para la asignatura de Programación Web — Cusco, 2026.*
