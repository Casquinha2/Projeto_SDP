#  Plataforma de Venda de Bilhetes
Projeto universitário para a Universidade Autónoma de Lisboa no âmbito da unidade curricular Sistemas Distribuídos e Paralelos e a unidade curricular Desenvolvimento Web.   

Este projeto é uma plataforma de Venda de bilhetes básica com funcionalidades de adicionar eventos, remover eventos, compra de bilhetes e criação de usuários.   

Os eventos têm nome, local, data, hora de inicio, hora de fim, bilhetes totais e preço dos bilhetes. Ainda podem conter uma informação adicional e quantidade de bilhetes disponiveis, sendo que estes são opcionais.
Na tela inicial da plataforma, os utilizadores podem criar uma nova conta ou fazer login com uma conta existente, As contas contém um nome de utilizador, uma password e um email, em que este tem que ser único. 
Ainda existe um campo no backend que é para saber se a conta é admin ou não.  
A adição e remoção dos eventos é feita no painel de administrador.
Na compra de um bilhete o utilizador tem vários métodos de pagamento que poderá escolher (como é um projeto universitário o utilizador não chega a comprar bilhetes).

## Linguagens usadas no projeto
- Python - usada para o backend da plataforma;
- HTML - usada para fazer o frontend;
- CSS - usada para fazer o design do frontend;
- PHP - usada para fazer a interligação entre o frontend e o backend.

Neste projeto ainda foi usado o Docker com os Kubernetes para simular vários serviços em diferentes máquinas, sendo que houve problemas com os comandos para a implementação dos Kubernetes.  

Para base de dados foi usado o Postgress que armazena quatro diferentes tabelas.  
A primeira tabela armazena tudo em relação aos eventos.  
A segunda tabela armazena os dados de login dos utilizadores.  
A terceira tabela armazena os dados dos bilhetes comprados.  
A quarta tabela armazena os dados do pagamento dos bilhetes.
