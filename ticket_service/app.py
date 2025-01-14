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
    CREATE TABLE IF NOT EXISTS ticket (
        id SERIAL PRIMARY KEY,
        event_id VARCHAR(5) UNIQUE NOT NULL,
        total_tickets VARCHAR(5) NOT NULL,
        avaiable_tickets VARCHAR(5) NOT NULL,
        price VARCHAR(5) NOT NULL
    );
    """
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute(create_table_query)
        conn.commit()
        cursor.close()
        conn.close()
        print("Tabela 'ticket' inicializada com sucesso.")
    except Exception as e:
        print(f"Erro ao inicializar a tabela: {e}")

def get_db_connection():
    return psycopg2.connect(**DB_CONFIG)


@app.route('/ticket', methods=['POST'])
def create_ticket():
    data = request.json
    if not data or not data.get('event_id') or not data.get('total_tickets') or not data.get('available_tickets') or not data.get('price'):
        return jsonify({"error": "Invalid input"}), 400

    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute(
            "INSERT INTO users (event_id, total_tickets, available_tickets, price, info) VALUES (%s, %s, %s, %s) RETURNING id;",
            (data['event_id'], data['total_tickets'], data['available_tickets'], data['price'])
        )
        ticket_id = cursor.fetchone()[0]
        conn.commit()

        return jsonify({"id": ticket_id, "event_id": data['event_id'], "total_tickets":data['total_tickets'], "available_tickets": data['available_tickets'], "price": data['price']}), 201
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

@app.route('/ticket', methods=['GET'])
def get_tickets():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)


        cursor.execute("SELECT * FROM management;")
        tickets = cursor.fetchall()

        return jsonify(tickets), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()


if __name__ == '__main__':
    
    initialize_database()
    app.run(host='0.0.0.0', port=3000)
