# Mailing — Plugin de envío masivo de correos para Botble CMS

Sistema de envío de correos electrónicos masivos a los contactos suscritos, usando el sistema
nativo de SMTP de Botble y la plantilla de correo configurada por defecto en el CMS.

## Características

- **Contactos**: CRUD de contactos suscritos, con baja automática mediante enlace de
  cancelación en cada correo. Si el plugin *Newsletter* de Botble está activo, los nuevos
  suscriptores se sincronizan automáticamente como contactos.
- **Envío automático al publicar**: cuando se publica una entrada del blog se envía un correo a
  todos los contactos (asunto = título de la entrada, cuerpo = contenido de la entrada).
  Activable/desactivable desde la configuración.
- **Envío mensual automático**: correo mensual con el listado de publicaciones publicadas desde
  el último mailing mensual (título + primeras líneas). Día del mes y hora configurables, y
  activable/desactivable.
- **Envío manual**: creación de campañas con el editor visual por defecto de Botble, con envío
  inmediato ("Enviar ahora") o programado (fecha y hora).
- **Reglas de envío anti-spam**: los correos salen en lotes (tamaño de lote y minutos entre
  lotes configurables) para proteger la reputación del remitente.
- **Panel estadístico por envío**: destinatarios, entregados, rebotados (tasa de rebote),
  aperturas (píxel de seguimiento), pendientes y progreso, además del registro detallado de
  cada correo.
- **Plantillas de correo**: todos los correos usan el sistema de plantillas de email de Botble
  (cabecera/pie por defecto), editables en *Configuración → Correo electrónico → Mailing*.
- **Servidor de envío seleccionable**: los correos del plugin pueden salir por el SMTP por
  defecto de la instalación, por un **SMTP personalizado** propio del plugin, o desde una
  **cuenta corporativa de Microsoft 365** autenticada por OAuth (envío vía Microsoft Graph
  API). Incluye botón de **correo de prueba** para validar la configuración.
- **Actualización OTA**: muestra la versión instalada y la última versión publicada en GitHub,
  con botón "Actualizar ahora" que descarga e instala la nueva versión automáticamente.

## Requisitos

- Botble CMS **7.0** o superior.
- SMTP configurado en *Configuración → Correo electrónico*.
- Cron de Laravel activo en el servidor (necesario para los envíos por lotes, las campañas
  programadas y el resumen mensual):

  ```
  * * * * * php /ruta-a-tu-web/artisan schedule:run >> /dev/null 2>&1
  ```

- Extensión PHP `zip` (para las actualizaciones OTA).

## Instalación

1. Descarga la última release de este repositorio (o clónalo).
2. Copia el contenido en `platform/plugins/mailing` (la carpeta debe llamarse **mailing**).
3. En el panel de administración ve a **Plugins** y activa **Mailing**.
4. Configura el plugin en **Mailing → Configuración**.

## Uso

- **Mailing → Campañas**: crea campañas manuales con el editor visual. Si rellenas
  "Programar envío para", la campaña quedará programada; si no, podrás lanzarla con
  "Enviar ahora" desde la página de estadísticas o guardarla como borrador.
- **Mailing → Contactos**: gestiona los contactos suscritos.
- **Mailing → Configuración**: activa el envío automático al publicar, el resumen mensual
  (día y hora) y ajusta las reglas de envío (correos por lote y minutos entre lotes).
- **Mailing → Actualización**: comprueba la versión instalada frente a la última release de
  GitHub y actualiza con un clic.

## Servidor de envío alternativo

En **Mailing → Configuración → Servidor de envío** puedes elegir:

- **Por defecto de Botble**: usa el SMTP configurado en *Configuración → Correo electrónico*.
- **SMTP personalizado**: host, puerto, usuario, contraseña, cifrado y remitente propios, solo
  para los correos de este plugin (no toca la configuración global).
- **Cuenta de Microsoft (OAuth / Graph API)**: los correos se envían desde la cuenta
  Microsoft 365 autenticada, a través de la API de Microsoft Graph (funciona aunque el tenant
  tenga SMTP AUTH deshabilitado). Los tokens se guardan cifrados y se renuevan automáticamente.

Tras guardar la configuración, usa el botón **"Enviar prueba"** para verificar el servidor
seleccionado antes de lanzar una campaña.

### Conectar una cuenta de Microsoft (guía de Azure/Entra ID)

1. Entra en [portal.azure.com](https://portal.azure.com) (o entra.microsoft.com) con una
   cuenta administradora del tenant y ve a **Microsoft Entra ID → App registrations →
   New registration**.
2. Nombre: por ejemplo `Botble Mailing`. En **Supported account types** elige
   *"Accounts in this organizational directory only"* (solo tu empresa).
3. En **Redirect URI** selecciona tipo **Web** y pega la URL de callback que muestra el plugin
   en *Mailing → Configuración* (es del tipo
   `https://tu-dominio.com/admin/mailing/settings/microsoft/callback`). Pulsa **Register**.
4. En la página **Overview** de la aplicación copia el **Application (client) ID** y el
   **Directory (tenant) ID**.
5. Ve a **Certificates & secrets → New client secret**, crea un secreto y copia su **Value**
   (solo se muestra una vez).
6. Ve a **API permissions → Add a permission → Microsoft Graph → Delegated permissions** y
   añade `Mail.Send` y `User.Read` (`offline_access` se concede automáticamente al autorizar).
   Si tu tenant lo requiere, pulsa **Grant admin consent**.
7. En el panel de Botble, *Mailing → Configuración*: selecciona el servidor
   **Cuenta de Microsoft**, pega Tenant ID, Client ID y Client Secret, y **guarda**.
8. Pulsa **"Conectar con Microsoft"**, inicia sesión con la cuenta corporativa desde la que
   quieres enviar y acepta los permisos. Verás la cuenta conectada en la configuración.

Notas:
- Los correos salen siempre **desde la cuenta autenticada** (el remitente lo impone Microsoft).
- Si el secreto de cliente caduca o se revoca el acceso, los envíos quedan en pausa (no se
  pierden destinatarios) y la configuración mostrará un aviso para reconectar.
- Microsoft 365 aplica límites diarios de envío por buzón (~10.000 destinatarios/día); ajusta
  los lotes en consecuencia.

## Comandos

| Comando | Descripción |
| --- | --- |
| `php artisan mailing:process` | Promociona campañas programadas y envía el siguiente lote de cada campaña en curso. Se ejecuta cada minuto vía scheduler. |
| `php artisan mailing:digest` | Envía el resumen mensual si corresponde. Se ejecuta cada hora vía scheduler. Admite `--force` para enviarlo inmediatamente. |

## Publicar una nueva versión (OTA)

1. Sube el número de `version` en `plugin.json`.
2. Crea una release en GitHub con etiqueta `vX.Y.Z` (por ejemplo `v1.1.0`).
3. Las instalaciones existentes verán la nueva versión en **Mailing → Actualización** y podrán
   actualizar con el botón.

## Licencia

MIT
