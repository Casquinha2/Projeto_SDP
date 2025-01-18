from flask import Blueprint, request, jsonify
from model import Payment

payment_blueprint = Blueprint('payment', __name__)

payments = {}  


@payment_blueprint.route('/<int:payment_id>', methods=['GET'])
def get_payment(payment_id):
    payment = payments.get(payment_id)
    if payment:
        return jsonify(payment.to_dict())
    return jsonify({'error': 'Payment not found'}), 404

@payment_blueprint.route('/', methods=['POST'])
def create_payment():
    payment_data = request.json
    payment = Payment(payment_id=payment_data['payment_id'], ticket_id=payment_data['ticket_id'], user_id=payment_data['user_id'], payment_method=payment_data['payment_method'], name=payment_data['name'], card_number=payment_data['card_number'], validation_date=payment_data['validation_date'], cvv=payment_data['cvv'], paypal_email=payment_data['paypal_email'])
    payments[payment.payment_id] = payment
    return jsonify(payment.to_dict()), 201
