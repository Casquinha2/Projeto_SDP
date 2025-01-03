from flask import Flask
from controller import *

app = Flask(__name__)
app.register_blueprint(ticket_blueprint, url_prefix = '/ticket')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=3000)