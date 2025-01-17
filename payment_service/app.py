from flask import Flask, request, jsonify
import psycopg2
from psycopg2.extras import RealDictCursor

app = Flask(__name__)

DB_CONFIG = {
    "dbname": "payment_db",
    "user": "user",
    "password": "password",
    "host": "postgres_payment",
    "port": 5432,
}

def initialize_database():
    create_table_query = """
    CREATE TABLE IF NOT EXISTS payment (
        id SERIAL PRIMARY KEY,
        event_id INTEGER NOT NULL,
        client_id INTEGER NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        name VARCHAR(120) NOT NULL,
        card_number VARCHAR(50),
        validation_date VARCHAR(50),
        cvv VARCHAR(5),
        paypal_email VARCHAR(120)
    );
    """
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute(create_table_query)
        conn.commit()
        cursor.close()
        conn.close()
        print("Tabela 'payment' inicializada com sucesso.")
    except Exception as e:
        print(f"Erro ao inicializar a tabela: {e}")

def get_db_connection():
    return psycopg2.connect(**DB_CONFIG)

@app.route('/payment', methods=['POST'])
def create_payment():
    data = request.json
    if not data or not data.get('event_id') or not data.get('client_id') or not data.get('payment_method') or not data.get('name'):
        return jsonify({"error": "Entrada inválida"}), 400
    else:
        if (data.get('payment_method') == 'visa' or data.get('payment_method') == 'mastercard') and (not data.get('card_number') or not data.get('validation_date') or not data.get('cvv')):
            return jsonify({"error": "Informações de cartão de crédito inválidas"}), 400
        
        elif data.get('payment_method') == 'paypal' and not data.get('paypal_email'):
            return jsonify({"error": "Informações de paypal inválidas"}), 400
        
        else:
            try:
                conn = get_db_connection()
                cursor = conn.cursor()

                cursor.execute(
                    "INSERT INTO payment (event_id, client_id, payment_method, name, card_number, validation_date, cvv, paypal_email) VALUES (%s, %s, %s, %s, %s, %s, %s, %s) RETURNING id;",
                    (data['event_id'], data['client_id'], data['payment_method'], data['name'], data['card_number'], data['validation_date'], data['cvv'], data['paypal_email'])
                )
                payment_id = cursor.fetchone()[0]
                conn.commit()

                return jsonify({"id": payment_id, "event_id": data['event_id'], "client_id": data['client_id'], "payment_method": data['payment_method'], "name": data['name'], "card_number": data['card_number'], "validation_date": data['validation_date'], "cvv": data['ccv'], "paypal_email": data['paypal_email']}), 201
            except Exception as e:
                return jsonify({"error": str(e)}), 500
            finally:
                conn.close()


@app.route('/payment', methods=['GET'])
def get_payment():
    try:
        conn = get_db_connection()
        cursor = conn.cursor(cursor_factory=RealDictCursor)
        cursor.execute("SELECT * FROM payment")
        users = cursor.fetchall()

        return jsonify(users), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

if __name__ == "__main__":

    initialize_database()
    app.run(host="0.0.0.0", port=4000)
