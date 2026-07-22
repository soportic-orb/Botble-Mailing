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
