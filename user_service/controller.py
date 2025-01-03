from flask import Blueprint, request, jsonify
from model import User

user_blueprint = Blueprint('user', __name__)

utilizadores = {}  
@user_blueprint.route('/<int:user_id>', methods=['GET'])
def get_user(user_id):
    user = utilizadores.get(user_id)
    if user:
        return jsonify(user.to_json())
    return jsonify({"error": "User not found"}), 404

@user_blueprint.route('/', methods=['POST'])
def create_user():
    user_data = request.json

    if not user_data or not user_data.get('name') or not user_data.get('email'):
        return jsonify({"error": "Invalid input"}), 400
    
    #gerar id automaticamente
    new_id = max(utilizadores.keys(), default=0)+1

    user = User(user_id=new_id, name=user_data['name'], email=user_data['email'])
    utilizadores[user.user_id] = user
    return jsonify(user.to_json()), 201