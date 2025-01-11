import requests

def register_user():
    print("*** Registar Utilizador ***")
    nome = input("Digite o nome: ")
    password = input("Digite password: ")
    email = input("Digite o email: ")
    admin = input("Digite admin? (s ou n): ")
    if admin == "s":
        admin = True
    else:
        admin= False
    data = {"name": nome,"password":password, "email": email, "admin":admin}
    print(f"JSON: {data}")  
    response = requests.post("http://localhost:6000/users", json=data)
    print(f"Status Code: {response.status_code}")
    print(f"Response Text: {response.text}")
    return admin


def login_user():
    print("\n*** Iniciar Sessão ***")
    name = input("Digite o name: ")
    password = input("Digite a password: ")
    response = requests.post("http://localhost:6000/login", json={"name": name, "password": password})
    if response.status_code == 200:
        print("Login realizado com sucesso! Dados:", response.json())
    else:
        print("Falha no login:", response.json())

def make_event():
    print("\n*** Criar Evento ***")
    event = input("Digite o evento: ")
    local = input("Digite o local do evento: ")
    data = input("Digite a data do evento: ")
    start_time = input("Indique as horas de começo: ")
    end_time = input("Indique as horas de fim: ")
    info = input("Digite informações extras (opcional): ")
    event_data = {'event': event, 'local':local, 'data':data ,'start_time':start_time, 'end_time':end_time, 'info':info}

    response = requests.post("http://localhost:5000/management", json=event_data)
    print(response.json())


if __name__ == "__main__":
    while True:
        opcao = int(input("""
        **** MENU ****
        1- Registar utilizador
        2- Login
        3- Sair
        Qual a opção?
        """))
        match opcao:
            case 1:
                register_user()
            case 2:
                admin = login_user()
                while admin:
                    opcao = int(input("""
        **** MENU ****
        1- Adicionar evento
        2- Voltar
        Qual a opção?
        """))
                    match opcao:
                        case 1:
                            make_event()
                        case 2:
                            admin = False
                        case _:
                            print("Opção inválida. Tente novamente.")
            case 3:
                print("A sair do programa..")
                break
            case _:
                print("Opção inválida. Tente novamente.")


