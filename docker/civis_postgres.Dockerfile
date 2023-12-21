FROM postgres:15.2-alpine
RUN apk add --no-cache supervisor
ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]