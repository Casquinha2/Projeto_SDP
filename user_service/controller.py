from flask import Blueprint, request, jsonify
from model import User

user_blueprint = Blueprint('user', __name__)

utilizadores = {}  # Assuming this is a dictionary with user data
admin = User(1, 'Admin', 'Admin123', 'admin@email.com', True)
@user_blueprint.route('/', methods=['GET'])
def verificar_utilizador():
    user_data = request.json
    if not user_data or not user_data.get('name') or not user_data.get('password'):
        return jsonify({"error": "Invalid login input"}), 400
    
    for user in utilizadores.values():
        if user.name == user_data['name'] and user.password == user_data['password']:
            return jsonify({"message": "Login successful!", "user_id": user.id}), 200 

    return jsonify({"error": "User not found"}), 404
    
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
    new_id = max(utilizadores.keys(), default=1)+1

    user = User(user_id=new_id, name=user_data['name'], password=user_data['password'], email=user_data['email'], admin=user_data['admin'])
    utilizadores[user.user_id] = user
    return jsonify(user.to_json()), 201

def teste():
    pass