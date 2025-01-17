from flask import Blueprint, request, jsonify
from model import Order
import requests

payment_blueprint = Blueprint('payment', __name__)