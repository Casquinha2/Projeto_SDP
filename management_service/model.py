class Event:
    def __init__(self, event_id, event, local, data, start_time, end_time, info):
        self.event_id = event_id
        self.event = event
        self.local = local
        self.data = data
        self.start_time = start_time
        self.end_time = end_time
        if info:
            self.info = info
        else:
            self.info = ""

    def to_dict(self):
        return {'event_id': self.event_id ,'event': self.event ,'local': self.local ,'data': self.data ,'start_time': self.start_time ,'end_time': self.end_time ,'info': self.info}
