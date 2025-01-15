class Ticket:
    def __init__(self, ticket_id, event_id, client_id):
        self.ticket_id = ticket_id
        self.event_id = event_id
        self.client_id = client_id

    def to_dict(self):
        return {'ticket_id': self.ticket_id,'event_id': self.event_id,'client_id': self.client_id}
