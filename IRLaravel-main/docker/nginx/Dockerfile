FROM nginx:latest

CMD ["nginx"]

COPY ./sites/fullchain.pem /etc/ssl/certs/
COPY ./sites/privkey.pem /etc/ssl/private/

EXPOSE 80 443
