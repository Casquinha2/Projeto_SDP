from flask import Blueprint, request, jsonify
from model import User
import requests

user_blueprint = Blueprint('user', __name__)

utilizadores = {}  

def verificar_utilizador(user_id):
    try:
        response = requests.get(f'http://localhost:6000/users/{user_id}')
        if response.status_code == 200:
            return True
        else:
            return False
    except requests.exceptions.RequestException as e:
        print("Erro ao conectar ao User Service:", e)
        return False
    
@user_blueprint.route('/<int:user_id>', methods=['GET'])
def get_user(user_id):
    user = utilizadores.get(user_id)
    if user:
        return jsonify(user.to_json())
    return jsonify({"error": "User not found"}), 404

@user_blueprint.route('/', methods=['POST'])
def create_user():
    user_data = request.json

    if not user_data or not user_data.get('name') or not user_data.get('password') or not user_data.get('email') or not user_data.get('admin'):
        return jsonify({"error": "Invalid input"}), 400
    
    #gerar id automaticamente
    new_id = max(utilizadores.keys(), default=0)+1

    user = User(user_id=new_id, name=user_data['name'], password=user_data['password'], email=user_data['email'], admin=user_data['admin'])
    utilizadores[user.user_id] = user
    return jsonify(user.to_json()), 201