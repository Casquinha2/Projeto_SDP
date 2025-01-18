class Payment:
    def __init__(self, payment_id, event_id, user_id, payment_method, name, card_number, validation_date, cvv, paypal_email):
        self.payment_id = payment_id
        self.event_id = event_id
        self.user_id = user_id
        self.payment_method = payment_method
        self.name = name
        self.card_number = card_number
        self.validation_date = validation_date
        self.cvv = cvv
        self.paypal_email = paypal_email

    def to_dict(self):
        return {'payment_id': self.payment_id, 'event_id': self.event_id, 'user_id': self.user_id, 'payment_method': self.payment_method, 'name': self.name, 'card_number': self.card_number, 'validation_date': self.validation_date, 'cvv': self.cvv, 'paypal_email': self.paypal_email}
