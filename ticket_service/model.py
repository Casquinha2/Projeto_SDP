class Ticket:
    def __init__(self, ticket_id, event_id, user_id):
        self.ticket_id = ticket_id
        self.event_id = event_id
        self.user_id = user_id

    def to_dict(self):
        return {'ticket_id': self.ticket_id,'event_id': self.event_id,'user_id': self.user_id}
