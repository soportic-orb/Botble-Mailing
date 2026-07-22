<?php

return [
    'name' => 'Mailing',

    'campaigns' => [
        'name' => 'Campañas',
        'create' => 'Nueva campaña',
        'edit' => 'Editar campaña',
        'type' => 'Tipo',
        'stats' => 'Estadísticas',
        'send_now' => 'Enviar ahora',
        'send_confirm' => 'La campaña se enviará a todos los contactos suscritos. ¿Continuar?',
        'cancel_sending' => 'Cancelar envío',
        'sending_started' => 'El envío ha comenzado. Los correos saldrán por lotes según las reglas de envío.',
        'sending_cancelled' => 'El envío se ha cancelado.',
        'already_sending' => 'Esta campaña ya se está enviando o ya se ha enviado.',
        'cannot_cancel' => 'Solo se pueden cancelar campañas programadas o en curso.',
        'no_contacts' => 'No hay contactos suscritos a los que enviar.',
        'not_editable' => 'Esta campaña ya no se puede editar porque el envío ya ha comenzado.',
        'form' => [
            'subject' => 'Asunto del correo',
            'subject_placeholder' => 'Asunto del correo saliente',
            'content' => 'Contenido del correo',
            'scheduled_at' => 'Programar envío para',
            'scheduled_at_helper' => 'Opcional. Formato: AAAA-MM-DD HH:MM. Déjalo vacío para mantener la campaña como borrador y enviarla manualmente con el botón "Enviar ahora".',
        ],
    ],

    'contacts' => [
        'name' => 'Contactos',
        'create' => 'Nuevo contacto',
        'edit' => 'Editar contacto',
    ],

    'contact_statuses' => [
        'subscribed' => 'Suscrito',
        'unsubscribed' => 'Dado de baja',
    ],

    'statuses' => [
        'draft' => 'Borrador',
        'scheduled' => 'Programada',
        'sending' => 'Enviando',
        'sent' => 'Enviada',
        'cancelled' => 'Cancelada',
    ],

    'types' => [
        'manual' => 'Manual',
        'post_published' => 'Nueva entrada',
        'monthly_digest' => 'Resumen mensual',
    ],

    'log_statuses' => [
        'pending' => 'Pendiente',
        'sent' => 'Entregado',
        'failed' => 'Rebotado',
    ],

    'auto_post' => [
        'campaign_name' => 'Nueva entrada: :title',
    ],

    'digest' => [
        'campaign_name' => 'Resumen mensual — :date',
        'subject' => 'Últimas publicaciones — :month',
    ],

    'settings' => [
        'menu' => 'Configuración',
        'title' => 'Configuración de Mailing',
        'description' => 'Configura el envío automático, el resumen mensual, las reglas de envío anti-spam y las actualizaciones OTA.',
        'auto_post_enabled' => 'Enviar correo al publicar una entrada',
        'auto_post_enabled_helper' => 'Si está activado, cada vez que se publique una entrada del blog se enviará automáticamente un correo a todos los contactos suscritos con el título de la entrada como asunto y su contenido como cuerpo.',
        'monthly_enabled' => 'Enviar resumen mensual',
        'monthly_enabled_helper' => 'Si está activado, se enviará un correo mensual con el listado de publicaciones publicadas desde el último mailing mensual (título y primeras líneas de cada publicación).',
        'monthly_day' => 'Día del mes para el resumen',
        'monthly_day_helper' => 'Día del mes (1-31) en el que se envía el resumen mensual. En meses más cortos se enviará el último día.',
        'monthly_time' => 'Hora de envío del resumen',
        'monthly_time_helper' => 'Hora del día (HH:MM, 24h) a partir de la cual se envía el resumen el día configurado.',
        'batch_size' => 'Correos por lote',
        'batch_size_helper' => 'Regla de envío anti-spam: número máximo de correos enviados por lote. Por defecto: 50.',
        'batch_interval' => 'Minutos entre lotes',
        'batch_interval_helper' => 'Regla de envío anti-spam: tiempo mínimo de espera (en minutos) entre dos lotes consecutivos. Por defecto: 5.',
        'github_repository' => 'Repositorio de GitHub (actualizaciones OTA)',
        'github_repository_helper' => 'Repositorio en formato propietario/nombre usado para comprobar y descargar nuevas versiones del plugin.',
        'github_token' => 'Token de GitHub (opcional)',
        'github_token_helper' => 'Solo es necesario para repositorios privados o para aumentar el límite de peticiones a la API de GitHub.',
    ],

    'update' => [
        'menu' => 'Actualización',
        'title' => 'Actualización del plugin (OTA)',
        'current_version' => 'Versión instalada',
        'latest_version' => 'Última versión disponible en GitHub',
        'up_to_date' => 'El plugin está actualizado.',
        'update_available' => 'La versión :version está disponible. Puedes actualizar automáticamente.',
        'update_now' => 'Actualizar ahora',
        'update_confirm' => 'Se descargará el plugin desde GitHub y se actualizará. Se recomienda hacer una copia de seguridad antes. ¿Continuar?',
        'changelog' => 'Notas de la versión',
        'check_error' => 'No se pudo comprobar la última versión en GitHub.',
        'no_release' => 'No se encontró ninguna release o etiqueta en el repositorio de GitHub configurado.',
        'already_latest' => 'Ya tienes instalada la última versión.',
        'download_failed' => 'La descarga desde GitHub ha fallado (HTTP :status).',
        'zip_failed' => 'No se pudo abrir el paquete descargado.',
        'invalid_package' => 'El paquete descargado no es un plugin válido (falta plugin.json).',
        'updated_success' => 'Plugin actualizado correctamente a la versión :version.',
        'source' => 'Origen de las actualizaciones',
    ],

    'stats' => [
        'title' => 'Estadísticas: :name',
        'total' => 'Destinatarios',
        'processed' => 'Procesados',
        'delivered' => 'Entregados',
        'bounced' => 'Rebotados',
        'opened' => 'Abiertos',
        'pending' => 'Pendientes',
        'progress' => 'Progreso del envío',
        'recent_logs' => 'Últimos envíos',
        'log_email' => 'Correo',
        'log_status' => 'Estado',
        'log_sent_at' => 'Enviado el',
        'log_opened_at' => 'Abierto el',
        'log_error' => 'Error',
        'no_logs' => 'Todavía no se ha puesto en cola ningún correo.',
        'scheduled_for' => 'Programada para el :date',
        'completed_at' => 'Completada el :date',
        'back_to_list' => 'Volver a campañas',
    ],

    'unsubscribe' => [
        'title' => 'Cancelar suscripción',
        'success' => 'Tu suscripción se ha cancelado. Ya no recibirás nuestros correos.',
        'invalid' => 'El enlace no es válido o la suscripción ya no existe.',
        'back_home' => 'Ir al sitio web',
    ],

    'email' => [
        'name' => 'Mailing',
        'description' => 'Correos enviados por el plugin Mailing (campañas, nuevas entradas y resumen mensual)',
        'templates' => [
            'campaign' => [
                'title' => 'Campaña manual',
                'description' => 'Correo enviado a todos los contactos cuando el administrador envía una campaña manual',
            ],
            'post_published' => [
                'title' => 'Nueva entrada publicada',
                'description' => 'Correo enviado automáticamente a todos los contactos cuando se publica una entrada del blog',
            ],
            'monthly_digest' => [
                'title' => 'Resumen mensual',
                'description' => 'Correo mensual con el listado de publicaciones publicadas desde el último mailing',
            ],
        ],
        'variables' => [
            'mailing_subject' => 'Asunto del correo',
            'mailing_content' => 'Contenido principal del correo (HTML)',
            'unsubscribe_url' => 'URL de baja del contacto',
            'tracking_pixel' => 'Píxel de seguimiento de aperturas',
        ],
    ],
];
