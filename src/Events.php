<?php
declare(strict_types=1);

namespace PBergman\Bundle\SentryBundle;

class Events
{
    const EVENT_BEFORE_SEND       = 'pbergman.sentry.before_send';
    const EVENT_BEFORE_BREADCRUMB = 'pbergman.sentry.before_breadcrumb';
}