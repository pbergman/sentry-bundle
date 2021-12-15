## Sentry Bundle

because the monolog handler provided by [entry/sentry-symfony](https://github.com/getsentry/sentry-symfony) will push all logs individually instead of using breadcrumbs this handler is created.    

when you configure your handler after a fingercrossed, buffer or any other handler that calls the `Monolog\Formatter\FormatterInterface::handleBatch` this will then create one message with rest being breadcrumbs of the original message.

so you could install like:

```
    handlers:
        main:
            type: fingers_crossed
            action_level: error
            handler: grouped
            excluded_http_codes: [404, 405]
        grouped:
            type: whatfailuregroup
            members: [ streamed, sentry ]
        streamed:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.log"
            level: debug
        sentry:
            type: sentry
            level: !php/const Monolog\Logger::INFO
            hub_id: Sentry\State\HubInterface
```

and when an error message is dispatched to sentry with breadcrumbs from all messages that equal or higher than INFO.