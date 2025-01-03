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
    user = User(user_id=user_data['user_id'], name=user_data['name'], email=user_data['email'])
    utilizadores[user.user_id] = user
    return jsonify(user.to_json()), 201