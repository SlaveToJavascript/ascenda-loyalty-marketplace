from flask import Flask, request, jsonify, Response
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from os import environ
import os
import requests
import json
import sys
import datetime
import boto3

app = Flask(__name__)

CORS(app)

def connectDynamoDBClient():
    client = boto3.client('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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' , region_name='us-east-1')

    try:
        response_table = client.describe_table(TableName='ITSA-US-EAST-1-LOYALTY-DB')
        loyalty_table = "ITSA-US-EAST-1-LOYALTY-DB"
        region = "us-east-1"
    except client.exceptions.ResourceNotFoundException:
        loyalty_table = "ITSA-AP-EAST-1-LOYALTY-DB"
        region = "ap-east-1"
        client = boto3.client('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name='ap-east-1')
    
    return client, region, loyalty_table

def connectDynamoDBResource(region):
    dynamodb = boto3.resource('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name=region)
    return dynamodb

@app.route("/", methods = ["GET"])
def health():
    return Response(status=200)

#Retrieve loyalty program partner code
@app.route("/loyaltyprogram/partnercodes", methods = ["GET"])
def get_loyalty_program_partner_code():
    client, region, loyalty_table = connectDynamoDBClient()
    dynamodb = connectDynamoDBResource(region)
    table = dynamodb.Table(loyalty_table)
    response = table.scan()

    results = {}
    for result in response["Items"]:
        if("loyaltyProgramCode" in result):
            results[result["loyaltyProgramId"]] = result["loyaltyProgramCode"]
    
    return {"status": 201, "PARTNERCODEMAPPING" : results}

# Retrieve loyalty program by ID
@app.route("/loyaltyprogram/<string:loyaltyProgramID>")
def find_loyalty_program_by_ID(loyaltyProgramID):
    client, region, loyalty_table = connectDynamoDBClient()

    response = client.get_item(
            TableName=loyalty_table,
            Key = {
                'loyaltyProgramId': {'S': loyaltyProgramID.upper()}
            }
        )
    
    if ("Item" not in response):
        status = 400
        result = {"status": status, "message": "Invalid Loyalty Program ID"}
        return result
    
    result = {"status": 201, "message": response["Item"]}
    return result

# Retrieve loyalty program by bank
@app.route("/loyaltyprogram/bank/<string:loyaltyProgramBank>")
def find_loyalty_program_by_Bank(loyaltyProgramBank):
    client, region, loyalty_table = connectDynamoDBClient()
    dynamodb = connectDynamoDBResource(region)
    
    table = dynamodb.Table(loyalty_table)
    response = table.scan()
    results = []

    for result in response["Items"]:
        if(result["loyaltyProgramBank"] == loyaltyProgramBank):
            results.append(result)

    result_message = {"status": 201, "Items": results}
    return result_message

# Create new Loyalty Program
@app.route("/loyaltyprogram/create", methods = ["POST"])
def create_loyaltyprogram():
    json = request.get_json()
    loyaltyProgramID = json['loyaltyProgramId'].upper()
    loyaltyProgramDescription = json['description']
    enrollment = json['enrollmentLink']
    currency_name = json['loyaltyCurrencyName']
    program_name = json['loyaltyProgramName']
    processing_time = json['processingTime']
    terms_and_conditions = json['termsAndConditionsLink']
    points = json['loyaltyPoints']
    bank = json['loyaltyProgramBank']
    program_code = json['loyaltyProgramCode']

    result = {"status": 201, "message": "Loyalty Program Created!"}
    try:
        client, region, loyalty_table = connectDynamoDBClient()
        response = client.put_item(
                TableName=loyalty_table,
                Item = {
                    "loyaltyProgramId" : {'S': loyaltyProgramID},
                    "description" : {'S' : loyaltyProgramDescription},
                    "enrollmentLink" : {'S' : enrollment},
                    "loyaltyCurrencyName" : {'S' : currency_name},
                    "loyaltyProgramName" : {'S' : program_name},
                    "processingTime" : {'S' : processing_time},
                    "termsAndConditionsLink" : {'S' : terms_and_conditions},
                    "loyaltyPoints" : {'S' : points},
                    "loyaltyProgramBank" : {'S' : bank},
                    "loyaltyProgramCode" : {'S' : program_code}
                },
                ConditionExpression='attribute_not_exists(loyaltyProgramId)'
            )
        
    except client.exceptions.ClientError as e:
        if e.response['Error']['Code'] == 'ConditionalCheckFailedException':
            result = {"status": 400, "message": "Loyalty Program Exists!"}
    return result

# Delete loyalty program
@app.route("/loyaltyprogram/delete", methods = ["DELETE"])
def delete_loyaltyprogram():
    json = request.get_json()
    loyaltyProgramID = json['loyaltyProgramId'].upper()

    result = {"status": 201, "message": "Loyalty Program Deleted!"}
    try:
        client, region, loyalty_table = connectDynamoDBClient()
        response = client.delete_item(
                TableName=loyalty_table,
                Key = {
                    "loyaltyProgramId" : {'S': loyaltyProgramID}
                },
                ConditionExpression='attribute_exists(loyaltyProgramId)'
            )
        
    except client.exceptions.ClientError as e:
        if e.response['Error']['Code'] == 'ConditionalCheckFailedException':
            result = {"status": 400, "message": "Loyalty Program does not exists!"}
    return result

# Update loyalty program
@app.route("/loyaltyprogram/update", methods = ["PUT"])
def update_loyaltyprogram():
    json = request.get_json()
    loyaltyProgramID = json['loyaltyProgramId'].upper()
    loyaltyProgramDescription = json['description']
    enrollment = json['enrollmentLink']
    currency_name = json['loyaltyCurrencyName']
    program_name = json['loyaltyProgramName']
    processing_time = json['processingTime']
    terms_and_conditions = json['termsAndConditionsLink']
    points = json['loyaltyPoints']
    bank = json['loyaltyProgramBank']
    program_code = json['loyaltyProgramCode']

    result = {"status": 201, "message": "Loyalty Program Updated!"}
    try:
        client, region, loyalty_table = connectDynamoDBClient()
        response = client.update_item(
                TableName=loyalty_table,
                Key = {
                    "loyaltyProgramId" : {'S': loyaltyProgramID},
                },
                UpdateExpression='SET description = :loyaltyProgramDescription, enrollmentLink = :enrollment, loyaltyCurrencyName = :currency_name, loyaltyProgramName = :program_name, processingTime = :processing_time, termsAndConditionsLink = :terms_and_conditions , loyaltyPoints = :points, loyaltyProgramBank = :bank, loyaltyProgramCode = :program_code',
                ExpressionAttributeValues={
                    ":loyaltyProgramDescription" : {'S' : loyaltyProgramDescription},
                    ":enrollment" : {'S' : enrollment},
                    ":currency_name" : {'S' : currency_name},
                    ":program_name" : {'S' : program_name},
                    ":processing_time" : {'S' : processing_time},
                    ":terms_and_conditions" : {'S' : terms_and_conditions},
                    ":points" : {'S' : points},
                    ":bank" : {'S' : bank},
                    ":program_code" : {'S' : program_code}
                },
                ConditionExpression='attribute_exists(loyaltyProgramId)'
            )
        
    except client.exceptions.ClientError as e:
        if e.response['Error']['Code'] == 'ConditionalCheckFailedException':
            result = {"status": 400, "message": "Loyalty Program does not exists!"}
    return result

@app.route("/loyaltyprogram/convertCurrencies", methods = ["POST"])
def deduct_loyalty_points():
    json = request.get_json()
    loyaltyProgramID = str(json['loyaltyProgramId'])
    balance = json["balance"]
    miles = json["miles"]
    points = json["points"]

    #Get loyalty program by ID
    client, region, loyalty_table = connectDynamoDBClient()
    response = client.get_item(
            TableName=loyalty_table,
            Key = {
                'loyaltyProgramId': {'S': loyaltyProgramID}
            }
        )
    
    if ("Item" not in response):
        status = 400
        result = {"status": status, "message": "Invalid Loyalty Program ID"}
        return result
    else:
        loyaltyCurrencyName = response["Item"]["loyaltyCurrencyName"]["S"]
        loyaltyPoints = response["Item"]["loyaltyPoints"]["S"]

    convertedPoints = ""
    remaining_points = float(miles)
    errorMilesMessage = ""
    convertedPointsMessage = ""

    if (miles != "" and points != ""):
        remaining_points = float(miles) - float(points)

    if (remaining_points < 0):
        errorMilesMessage = "Insufficient Balance!"
        status = 400
        result = {"status": status, "remaining_points": remaining_points, "errorMilesMessage" : errorMilesMessage, "convertedPointsMessage": convertedPointsMessage}
        return result
    elif (points != ""):
        convertedPoints = int(points) / 1000 * int(loyaltyPoints)
        convertedPointsMessage = "Equates to " + str(convertedPoints) + " " + str(loyaltyCurrencyName)
        status = 201
        result = {"status": status, "remaining_points": remaining_points, "errorMilesMessage" : errorMilesMessage, "convertedPointsMessage": convertedPointsMessage, "convertedPoints": convertedPoints}
        return result
    status = 400
    result = {"status": status, "remaining_points": remaining_points, "errorMilesMessage" : errorMilesMessage, "convertedPointsMessage": convertedPointsMessage}
    return result

if __name__ == '__main__':
    app.run(debug=False)