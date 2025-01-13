from flask import Blueprint, request, jsonify
from model import Ticket

ticket_blueprint = Blueprint('ticket', __name__)

tickets = {}

'''def verificar_utilizador(user_id):
    
    try:
        response = requests.get(f'http://localhost:6000/users/{user_id}')
        if response.status_code == 200:
            return True
        else:
            return False
    except requests.exceptions.RequestException as e:
        print("Erro ao conectar ao User Service:", e)
        return False'''


@ticket_blueprint.route('/<int:ticket_id>', methods=['GET'])
def get_ticket(ticket_id):
    ticket = tickets.get(ticket_id)
    if ticket:
        return jsonify(ticket.to_dict())
    return jsonify({'error': 'Order not found'}), 404

@ticket_blueprint.route('/', methods=['POST'])
def create_ticket():
    ticket_data = request.json
    ticket = Ticket(ticket_id=ticket_data['ticket_id'],event_id=ticket_data['event_id'],total_tickets=ticket_data['total_tickets'], available_tickets=ticket_data['available_tickets'], price = ticket_data['price'], info=ticket_data['info'])
    tickets[ticket.ticket_id] = ticket
    return jsonify(ticket.to_dict()), 201
