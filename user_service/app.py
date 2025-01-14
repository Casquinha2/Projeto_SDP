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
        cursor.close()
        conn.close()
        admin_user()
        print("Tabela 'users' inicializada com sucesso.")
    except Exception as e:
        print(f"Erro ao inicializar a tabela: {e}")

def get_db_connection():
    return psycopg2.connect(**DB_CONFIG)

@app.route('/users', methods=['POST'])
def create_user():
    data = request.json
    if not data or not data.get('name') or not data.get('password') or not data.get('email') or not data.get('admin'):
        return jsonify({"error": "Invalid testing input"}), 400

    if data.get('admin') == 1:
        data['admin'] = True
    elif data.get('admin') == 2:
        data['admin'] = False
    else:
        return jsonify({"error": "Invalid admin value"}), 400
    
    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute(
            "INSERT INTO users (name, password, email, admin) VALUES (%s, %s, %s, %s) RETURNING id;",
            (data['name'], data['password'], data['email'], data['admin'])
        )
        user_id = cursor.fetchone()[0]
        conn.commit()

        return jsonify({"id": user_id, "name": data['name'], "password":data['password'], "email": data['email'], "admin":data['admin']}), 201
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

@app.route('/login', methods=['POST'])
def verificar_utilizador():
    user_data = request.json

    if not user_data or not user_data.get('name') or not user_data.get('password'):
        return jsonify({"error": "Invalid login input"}), 400

    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)

        # Fetch all users
        cursor.execute("SELECT * FROM users;")
        users = cursor.fetchall()

        for user in users:
            if user['name'] == user_data['name'] and user['password'] == user_data['password']:
                return jsonify({"user_id": user['id'], "admin":user['admin']}), 200

        return jsonify({"error": "User not found"}), 404
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()


def get_users():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)


        cursor.execute("SELECT * FROM users;")
        users = cursor.fetchall()

        return jsonify(users), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

def admin_user():
    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute(
            "INSERT INTO users (name, password, email, admin) VALUES ('Admin', 'Admin123', 'admin@email.com', True) RETURNING id;")
        conn.commit()
        conn.close()
    except Exception as e:
        print(f"Admin já existe")

if __name__ == "__main__":
   
    initialize_database()
    app.run(host="0.0.0.0", port=6000)

# docker exec -it postgres_users psql -U user -d user_db
