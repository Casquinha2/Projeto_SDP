class User:
    def __init__(self, user_id, name, password, email, admin):
        self.user_id = user_id
        self.name = name
        self.password = password
        self.email = email
        self.admin = admin
    
    def to_json(self):
        return{'user_id':self.user_id,'name':self.name,'password':self.password, 'email':self.email, 'admin':self.admin}