FROM redis:latest

LABEL maintainer="Mahmoud Zalt <mahmoud@zalt.me>"

***REMOVED******REMOVED*** For security settings uncomment, make the dir, copy conf, and also start with the conf, to use it
***REMOVED***RUN mkdir -p /usr/local/etc/redis
***REMOVED***COPY redis.conf /usr/local/etc/redis/redis.conf

VOLUME /data

EXPOSE 6379

***REMOVED***CMD ["redis-server", "/usr/local/etc/redis/redis.conf"]
CMD ["redis-server"]
