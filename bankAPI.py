from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from os import environ
import os
import requests
import json
import sys
import datetime

app = Flask(__name__)

app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root:root@localhost:3306/abcbank'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)
CORS(app)

PARTNERCODE = "ABCSG"
EXTENSION = ".txt"
FILEURL = 'https://api.cs301-g7.com//file/accrual'

class User(db.Model):
    __tablename__ = 'user'

    userid = db.Column(db.String(128), primary_key=True)
    password = db.Column(db.String(128), nullable=False)
    miles = db.Column(db.Integer(), nullable=False)
    name = db.Column(db.String(128), nullable=False)

    def __init__(self, userid, password, miles, name):
        self.userid = userid
        self.password = password
        self.miles = miles
        self.name = name

    def json(self):
        return {"userid": self.userid, "password": self.password, "miles": self.miles, "name": self.name}

@app.route("/updateMiles", methods=['POST'])
def updateMiles():
    if (not (request.is_json)):
        result = request.get_data()
        status = 400 # Bad Request
        replymessage = json.dumps({"status": status, "message": "Miles information should be in JSON", "data": result}, default=str)
        return replymessage

    result = request.get_json()
    user = User.query.filter_by(userid=result["userid"]).first()
    dbMiles = user.miles

    status = 201

    try:
        user.miles = dbMiles - float(result["miles"])
        db.session.commit()

    except Exception as e:
        status = 500
        result = {"status": status, "message": "An error occurred when updating the user in DB.", "error": str(e)}
        return result
    return {"status": status}

if __name__ == '__main__': 
    app.run(debug=False)