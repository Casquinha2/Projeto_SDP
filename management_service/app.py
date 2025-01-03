from flask import Flask
from controller import *

app = Flask(__name__)
app.register_blueprint(management_blueprint, url_prefix = '/management')

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)