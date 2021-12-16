## Sentry Bundle

I have created this bundle because the [entry/sentry-symfony](https://github.com/getsentry/sentry-symfony) was not using the batch handler from monolog when connected with a buffer handler. This resulted in all messaged from a request being delivered individually instead of using breadcrumbs.   

So with this extension you could configure monolog like this:

```
    handlers:
        main:
            type: fingers_crossed
            action_level: error
            handler: grouped
            excluded_http_codes: [404, 405]
        grouped:
            type: whatfailuregroup
            members: [ streamed, sentry_deduplicated ]
        streamed:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.log"
            level: debug
        sentry_deduplicated:
            type:    deduplication
            handler: sentry                  
        sentry:
            type: sentry
            level: !php/const Monolog\Logger::INFO
            hub_id: Sentry\State\HubInterface
```

And when the fingercrossed handler get an error message, all messages in the buffer of INFO or higher will be grouped and send to sentry.  

This bundle has also created a bridge between the native hooks ([before_breadcrumb an before_send](https://docs.sentry.io/platforms/php/configuration/options/#hooks)) and symfony dispatcher. So now you can just create listener that listens to `PBergman\Bundle\SentryBundle\Events::EVENT_BEFORE_SEND` or `PBergman\Bundle\SentryBundle\Events::EVENT_BEFORE_BREADCRUMB`   

By default it will create a listener that will filter exception classes (see `bin/console config:dump-reference p_bergman_sentry`) to disable add the following config: 

```
p_bergman_sentry:
    excluded_exceptions: ~
```

Or set there the desired classes.