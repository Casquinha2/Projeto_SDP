from flask import Flask, request, jsonify
import psycopg2
from psycopg2.extras import RealDictCursor
import requests

app = Flask(__name__)

DB_CONFIG = {
    "dbname": "management_db",
    "user": "user",
    "password": "password",
    "host": "postgres_management",
    "port": 5432,
}

def initialize_database():
    create_table_query = """
    CREATE TABLE IF NOT EXISTS events (
        id SERIAL PRIMARY KEY,
        event VARCHAR(80) NOT NULL,
        local VARCHAR(120) NOT NULL,
        data VARCHAR(120) NOT NULL,
        start_time VARCHAR(60) NOT NULL,
        end_time VARCHAR(60) NOT NULL,
        info TEXT
    );
    """
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute(create_table_query)
        conn.commit()
        cursor.close()
        conn.close()
        print("Tabela 'events' inicializada com sucesso.")
    except Exception as e:
        print(f"Erro ao inicializar a tabela: {e}")

def get_db_connection():
    return psycopg2.connect(**DB_CONFIG)

@app.route('/management', methods=['POST'])
def create_event():
    data = request.json
    if not data or not data.get('event') or not data.get('local') or not data.get('data') or not data.get('start_time') or not data.get('end_time'):
        return jsonify({"error": "Invalid input"}), 400
    
    if data.get('info') == 'None':
        data['info'] = None

    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute(
            "INSERT INTO events (event, local, data, start_time, end_time, info) VALUES (%s, %s, %s, %s, %s, %s) RETURNING id;",
            (data['event'], data['local'], data['data'], data['start_time'], data['end_time'], data['info'])
        )
        event_id = cursor.fetchone()[0]
        conn.commit()

        
        ticket_data = { "event_id": event_id, "total_tickets": data['total_tickets'], "available_tickets": data['total_tickets'], "price": data['price'] } 
        response = requests.post("http://ticket_service/ticket", json=ticket_data)

        if response.status_code != 201: 
            return jsonify({"error": "Failed to create tickets"}), 500

        ticket_id = response.json().get('id')
        return jsonify({"id": event_id, "event": data['event'], "local":data['local'], "data": data['data'], "start_time": data['start_time'], "end_time": data['end_time'], "info":data['info'], "ticket_id": ticket_id}), 201
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()


@app.route('/management', methods=['GET'])
def get_events():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)


        cursor.execute("SELECT * FROM management;")
        events = cursor.fetchall()

        return jsonify(events), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

if __name__ == '__main__':

    initialize_database()
    app.run(host='0.0.0.0', port=5000)