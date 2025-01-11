from flask import Flask
from controller import *

app = Flask(__name__)
app.register_blueprint(ticket_blueprint, url_prefix = '/ticket')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=3000)



'''
     environment:
      DB_NAME: ticket_db
      DB_USER: admin
      DB_PASSWORD: password
      DB_HOST: postgres_ticket
      DB_PORT: 5432
    depends_on:
      - postgres_ticket



    postgres_ticket:
    image: postgres:15
    container_name: postgres_ticket
    restart: always
    environment:
      POSTGRES_USER: user
      POSTGRES_PASSWORD: password
      POSTGRES_DB: ticket_db
    ports:
      - "5434:5432"
    networks:
      - microservices-net
'''