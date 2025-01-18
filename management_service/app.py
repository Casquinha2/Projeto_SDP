from flask import Flask, request, jsonify
import psycopg2
from psycopg2.extras import RealDictCursor

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
        info TEXT,
        ticket_total INTEGER NOT NULL,
        ticket_available INTEGER NOT NULL,
        ticket_price FLOAT NOT NULL
    );
    """


    #receber id do evento

    #temp_total = total -> temp_available += available

    #if temp_total - 1 <= 0 : (erro) : temp_total -= 1 -> temp_available += 1

    #update BD (receber BD pelo id e mudar os valores acima)


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
    if not data or not data.get('event') or not data.get('local') or not data.get('data') or not data.get('start_time') or not data.get('end_time') or not data.get('ticket_total') or not data.get('ticket_available') or not data.get('ticket_price'):
        return jsonify({"error": "Invalid input"}), 400
    
    if data.get('info') == 'None':
        data['info'] = None

    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute(
            "INSERT INTO events (event, local, data, start_time, end_time, info, ticket_total, ticket_available, ticket_price) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s) RETURNING id;",
            (data['event'], data['local'], data['data'], data['start_time'], data['end_time'], data['info'], data['ticket_total'], data['ticket_available'], data['ticket_price'])
        )

        event_id = cursor.fetchone()[0]
        conn.commit()
        cursor.close()
        conn.close()

        return jsonify({"id": event_id, "event": data['event'], "local":data['local'], "data": data['data'], "start_time": data['start_time'], "end_time": data['end_time'], "info":data['info'], "ticket_total": data['ticket_total'], "ticket_available": data['ticket_available'], "ticket_price": data['ticket_price']}), 201
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/management', methods=['GET'])
def get_events():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)

        cursor.execute("SELECT * FROM events;")
        events = cursor.fetchall()
        cursor.close()
        conn.close()

        return jsonify(events), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    
@app.route('/management/<int:event_id>', methods=['GET'])
def get_event_by_id(event_id):
    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)
        
        cursor.execute("SELECT * FROM events WHERE id = %s;", (event_id,))
        event = cursor.fetchone()
        cursor.close()
        conn.close()

        if event:
            return jsonify(event), 200
        else:
            return jsonify({"error": "Event not found"}), 404
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    
@app.route('/management', methods=['POST'])
def update_event_ticket():
    data = request.json
    if not data or not data.get('event_id') or not data.get('ticket_available'):
        return jsonify({"error": "Invalid input"}), 400
    
    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute(
            """
            UPDATE events
            SET ticket_available = %s WHERE id = %s RETURNING id;
            """,
            (data['ticket_available'], data['event_id'])
        )

        event_id = cursor.fetchone()[0]
        conn.commit()
        cursor.close()
        conn.close()

        return jsonify({"id": event_id,"ticket_available": data['ticket_available']}), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500



if __name__ == '__main__':

    initialize_database()
    app.run(host='0.0.0.0', port=5000)