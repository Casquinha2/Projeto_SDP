class Event:
    def __init__(self, event_id, event, local, date, start_time, end_time,ticket_total,  ticket_available, ticket_price, info):
        self.event_id = event_id
        self.event = event
        self.local = local
        self.date = date
        self.start_time = start_time
        self.end_time = end_time
        if info:
            self.info = info
        else:
            self.info = ""
        self.ticket_total = ticket_total
        self.ticket_available = ticket_available
        self.ticket_price = ticket_price


    def to_dict(self):
        return {'event_id': self.event_id ,'event': self.event ,'local': self.local ,'date': self.date ,'start_time': self.start_time ,'end_time': self.end_time ,'info': self.info, 'ticket_total': self.ticket_total ,'ticket_available': self.ticket_available, 'ticket_price': self.ticket_price}
