# 🐾 DocuHuella - Sistema de Gestión Veterinaria

**DocuHuella** es una solución tecnológica integral diseñada para optimizar la gestión de expedientes clínicos veterinarios. El sistema permite centralizar la información médica de las mascotas, facilitando el trabajo de los profesionales y mejorando el seguimiento para los cuidadores.

--- LOCAL --- XAMPP

LINK: http://localhost:8080/DocuHuella/

LINK: http://localhost/DocuHuella/

--- Servidor ---
LINK: http://docuhuella.infinityfreeapp.com
---

## 🏗️ Arquitectura del Proyecto
Este sistema ha sido desarrollado bajo el patrón de diseño **MVC (Modelo-Vista-Controlador)**, garantizando un código escalable, organizado y fácil de mantener.

- **Modelo:** Gestión de la lógica de datos y conexión a MySQL mediante PDO.
- **Vista:** Interfaces de usuario dinámicas y responsivas.
- **Controlador:** Intermediario que procesa las peticiones y gestiona el flujo de información.

---

## 🚀 Características Principales

* **🔐 Control de Acceso Seguro:** Sistema de autenticación con roles diferenciados (Administrador, Veterinario, Cuidador).
* **📋 Expediente Clínico Digital:** Registro detallado de consultas, diagnósticos presuntivos y tratamientos.
* **🐶 Gestión de Pacientes:** Base de datos completa de mascotas con seguimiento de peso, rasgos físicos y estado de esterilización.
* **👨‍⚕️ Perfiles Profesionales:** Validación de credenciales para veterinarios (Exequatur y Colegiatura COLVET).
* **📊 Panel Administrativo:** Herramientas para la gestión de usuarios y generación de reportes estadísticos mensuales.

---

## 🛠️ Stack Tecnológico

| Componente | Tecnología |
| :--- | :--- |
| **Lenguaje de Servidor** | PHP |
| **Base de Datos** | MySQL |
| **Frontend** | HTML5, CSS3 (Bootstrap) y JavaScript |
| **Entorno de Desarrollo** | XAMPP / Visual Studio Code |
| **Despliegue** | InfinityFree (Hosting gratuito) |

---

## 📂 Estructura de Directorios

```text
DocuHuella/
├── config/      # Configuración de la conexión a la base de datos (PDO)
├── controllers/ # Lógica intermedia que procesa las solicitudes
├── models/      # Clases PHP que interactúan con la base de datos
├── views/       # Archivos de interfaz, formularios y reportes
├── public/      # Recursos estáticos (CSS, JS, Logo oficial)
├── setup.php    # Script temporal de inicialización
└── DocuhuellaDB.sql # Estructura exportada de la base de datos

```

---

## ⚙️ Configuración del Entorno Local

Para que el proyecto funcione en tu computadora local, sigue estos pasos al pie de la letra:

### 1. Base de Datos

1. Abre **XAMPP** y activa los módulos de **Apache** y **MySQL**.
2. Presiona admin en xampp o ve a tu navegador e ingresa a [http://localhost:8080/phpmyadmin/](https://www.google.com/search?q=http://localhost:8080/phpmyadmin/) *(Ajusta el puerto si usas uno diferente al 8080)*.
3. Crea una base de datos nueva con el nombre exacto: `Docuhuella`.
4. Ve a la pestaña "Importar" y sube el archivo `DocuhuellaDB.sql` que se encuentra en la raíz de este proyecto.

### 2. Inicialización de Datos (Roles y Administrador)

Una vez importada la base de datos, debes ejecutar el script de inicialización para que el sistema cree los roles predeterminados (Admin, Veterinario, Cuidador) y la cuenta maestra.

1. Entra a la siguiente ruta en tu navegador:
`http://localhost:8080/DocuHuella/setup.php`
`http://localhost/DocuHuella/setup.php`
3. Si ves los mensajes de confirmación en verde, el sistema ya está configurado.
4. **Credenciales de acceso iniciales:**
* **Correo:** `master@docuhuella.com`
* **Contraseña:** `master123`



> ⚠️ **Nota importante sobre el puerto:** Si tu panel de XAMPP da un error con Apache en el puerto 80, debes cambiarlo al **8080** en el archivo `httpd.conf` de Apache. Asegúrate de usar `localhost:8080` en todas tus URLs locales.

---

## 👥 Equipo de Desarrollo (Grupo 2)

* **Ricardo Peña García (Ricky)**
* **Eddual Rafael Corniel**
* **Lerinson Samuel Volquez**

---

*© 2026 DocuHuella - Universidad Católica Tecnológica de Barahona (UCATEBA).*

```
