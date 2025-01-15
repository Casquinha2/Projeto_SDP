from flask import Blueprint, request, jsonify
from model import Event

management_blueprint = Blueprint('management', __name__)

management = {}

@management_blueprint.route('/<int:event_id>', methods=['GET'])
def get_event(event_id):
    event = management.get(event_id)
    if event:
        return jsonify(event.to_dict())
    return jsonify({'error': 'Event not found'}), 404

@management_blueprint.route('/', methods=['POST'])
def create_event():
    event_data = request.json

    if not event_data.get('event') or not event_data.get('local') or not event_data.get('event_data') or not event_data.get('start_time') or not event_data.get('end_time') or not event_data.get('ticket_total') or not event_data.get('ticket_available') or not event_data.get('ticket_price'):
        return jsonify({"error": "Invalid input"}), 400
    
    #gerar id automaticamente
    new_id = max(management.keys(), default=0)+1

    event_object = Event(event_id=new_id, event=event_data['event'], local=event_data['local'], event_data=event_data['event_data'], start_time=event_data['start_time'], end_time=event_data['end_time'], ticket_total=event_data['ticket_total'], ticket_available=event_data['ticket_available'], ticket_price=event_data['ticket_price'] , info = event_data['info'])
    management[event_object.event_id] = event_object
    return jsonify(event_object.to_dict()), 201