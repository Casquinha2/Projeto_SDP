from flask import Flask, request, jsonify
import psycopg2
from psycopg2.extras import RealDictCursor

app = Flask(__name__)

DB_CONFIG = {
    "dbname": "ticket_db",
    "user": "user",
    "password": "password",
    "host": "postgres_ticket",
    "port": 5432,
}

def initialize_database():
    create_table_query = """
    CREATE TABLE IF NOT EXISTS tickets (
        id SERIAL PRIMARY KEY,
        event_id VARCHAR(5) UNIQUE NOT NULL,
        client_id VARCHAR(5) UNIQUE NOT NULL
    );
    """
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute(create_table_query)
        conn.commit()
        cursor.close()
        conn.close()
        print("Tabela 'tickets' inicializada com sucesso.")
    except Exception as e:
        print(f"Erro ao inicializar a tabela: {e}")

def get_db_connection():
    return psycopg2.connect(**DB_CONFIG)


@app.route('/ticket', methods=['POST'])
def create_ticket():
    data = request.json
    if not data or not data.get('event_id') or not data.get('client_id'):
        return jsonify({"error": "Invalid input"}), 400

    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute(
            "INSERT INTO tickets (event_id, client_id) VALUES (%s, %s) RETURNING id;",
            (data['event_id'], data['client_id'])
        )
        ticket_id = cursor.fetchone()[0]
        conn.commit()

        return jsonify({"id": ticket_id, "event_id": data['event_id'], "client_id": data['client_id']}), 201
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

@app.route('/ticket', methods=['GET'])
def get_tickets():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)


        cursor.execute("SELECT * FROM tickets;")
        tickets = cursor.fetchall()
        cursor.close()
        conn.close()

        return jsonify(tickets), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()


if __name__ == '__main__':
    
    initialize_database()
    app.run(host='0.0.0.0', port=3000)
