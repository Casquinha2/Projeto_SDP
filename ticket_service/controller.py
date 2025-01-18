from flask import Blueprint, request, jsonify
from model import Ticket

ticket_blueprint = Blueprint('ticket', __name__)

tickets = {}

@ticket_blueprint.route('/<int:ticket_id>', methods=['GET'])
def get_ticket(ticket_id):
    ticket = tickets.get(ticket_id)
    if ticket:
        return jsonify(ticket.to_dict())
    return jsonify({'error': 'Order not found'}), 404

@ticket_blueprint.route('/', methods=['POST'])
def create_ticket():
    ticket_data = request.json
    ticket = Ticket(ticket_id=ticket_data['ticket_id'],event_id=ticket_data['event_id'],user_id=ticket_data['user_id'])
    tickets[ticket.ticket_id] = ticket
    return jsonify(ticket.to_dict()), 201
