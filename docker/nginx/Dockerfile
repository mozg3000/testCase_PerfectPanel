FROM nginx:1.22-alpine

RUN apk add --no-cache curl

COPY ./nginx/conf.d /etc/nginx/conf.d

WORKDIR /app
