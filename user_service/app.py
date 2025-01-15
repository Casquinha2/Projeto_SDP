from flask import Flask, request, jsonify
import psycopg2
from psycopg2.extras import RealDictCursor

app = Flask(__name__)

DB_CONFIG = {
    "dbname": "user_db",
    "user": "user",
    "password": "password",
    "host": "postgres_users",
    "port": 5432,
}

def initialize_database():
    create_table_query = """
    CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(80) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        admin BOOL NOT NULL
    );
    """
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute(create_table_query)
        conn.commit()
        
        # Create admin user if not exists
        cursor.execute(
            "INSERT INTO users (name, password, email, admin) VALUES (%s, %s, %s, %s) RETURNING id;",
            ('Admin', 'Admin123', 'admin@email.com', True)
        )
        conn.commit()

        cursor.close()
        conn.close()
        print("Tabela 'users' inicializada com sucesso, e administrador adicionado.")
    except psycopg2.errors.UniqueViolation:
        print("Admin já existe.")
    except Exception as e:
        print(f"Erro ao inicializar a tabela: {e}")

def get_db_connection():
    return psycopg2.connect(**DB_CONFIG)

@app.route('/users', methods=['POST'])
def create_user():
    data = request.json
    if not data or not data.get('name') or not data.get('password') or not data.get('email'):
        return jsonify({"error": "Entrada inválida"}), 400

    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute(
            "INSERT INTO users (name, password, email, admin) VALUES (%s, %s, %s, %s) RETURNING id;",
            (data['name'], data['password'], data['email'], False)
        )
        user_id = cursor.fetchone()[0]
        conn.commit()

        return jsonify({"id": user_id, "name": data['name'], "password":data['password'], "email": data['email'], "admin":False}), 201
    except psycopg2.errors.UniqueViolation:
        return jsonify({"error": "Email já cadastrado"}), 400
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

@app.route('/login', methods=['POST'])
def verificar_utilizador():
    user_data = request.json

    if not user_data or not user_data.get('name') or not user_data.get('password'):
        return jsonify({"error": "Entrada inválida"}), 400

    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)

        cursor.execute("SELECT * FROM users WHERE name = %s AND password = %s", (user_data['name'], user_data['password']))
        user = cursor.fetchone()

        if user:
            return jsonify({"user_id": user['id'], "admin": user['admin']}), 200
        else:
            return jsonify({"error": "Usuário não encontrado"}), 404
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

@app.route('/users', methods=['GET'])
def get_users():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)
        cursor.execute("SELECT * FROM users")
        users = cursor.fetchall()

        return jsonify(users), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

if __name__ == "__main__":
    initialize_database()
    app.run(host="0.0.0.0", port=6000)
