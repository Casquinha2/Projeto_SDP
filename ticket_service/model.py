class Ticket:
    def __init__(self, ticket_id, event_id, total_tickets, available_tickets, price):
        self.ticket_id = ticket_id
        self.event_id = event_id
        self.total_tickets = total_tickets
        self.available_tickets = available_tickets
        self.price = price

    def to_dict(self):
        return {'ticket_id': self.ticket_id,'event_id': self.event_id,'total_tickets':self.total_tickets,'available_tickets':self.available_tickets,'price':self.price}
