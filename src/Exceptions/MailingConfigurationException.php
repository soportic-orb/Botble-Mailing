<?php

namespace Botble\Mailing\Exceptions;

use Exception;

/**
 * Thrown when the selected mail transport is unusable (missing settings,
 * expired/revoked OAuth connection...). Batches are paused without marking
 * recipient logs as bounced, unlike per-message delivery failures.
 */
class MailingConfigurationException extends Exception
{
}
