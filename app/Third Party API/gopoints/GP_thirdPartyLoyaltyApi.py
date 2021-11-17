from flask import Flask, flash, request, redirect, url_for, send_from_directory
from flask.helpers import send_file
from flask.wrappers import Response
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
import os
from werkzeug.utils import secure_filename
import json
import requests
import csv
import io
import codecs
import enum
from datetime import date

from werkzeug.middleware.shared_data import SharedDataMiddleware

UPLOAD_FOLDER = '.\\GPuploads'
ALLOWED_EXTENSIONS = {'txt', 'csv'}

if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
CORS(app)

app.add_url_rule('/uploads/<filename>', 'uploaded_file',
                 build_only=True)
app.wsgi_app = SharedDataMiddleware(app.wsgi_app, {
    '/uploads':  app.config['UPLOAD_FOLDER']
})

def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

@app.route('/', methods=['GET', 'POST'])
def upload_file():
    if request.method == 'POST':
        if 'file' not in request.files:
            flash('No file part')
            return redirect(request.url)
        file = request.files['file']
        if file.filename == '':
            flash('No selected file')
            return redirect(request.url)
        if file and allowed_file(file.filename):
            filename = secure_filename(file.filename)
            file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
            return Response(status=200)
    return '''
    <!doctype html>
    <title>Upload new File</title>
    <h1>Upload new File</h1>
    <form method=post enctype=multipart/form-data>
      <input type=file name=file>
      <input type=submit value=Upload>
    </form>
    '''


@app.route('/uploads/<filename>')
def uploaded_file(filename):
    return send_from_directory(app.config['UPLOAD_FOLDER'],
                               filename)

@app.route('/receive_accrual', methods=['PUT'])
def receive_accrual():
    message = ""
    if 'accrual_file' not in request.files:
        message = "No accrual_file key in request"
    else:
        file = request.files['accrual_file']
        file_name = file.filename
        if file_name == '':
            message = "No accrual file selected"
        else:
            message = "Received accrual file with filename: " + str(file_name)
    print(message)
    return message

if __name__ == '__main__':
    app.run(debug=False)
