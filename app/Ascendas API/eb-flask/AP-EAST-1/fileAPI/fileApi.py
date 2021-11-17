from flask import Flask, request, jsonify, Response
from flask_cors import CORS
from os import environ
import os
import ast
import requests
import json
import boto3
import botocore
from io import BytesIO
from datetime import datetime
from flask.helpers import send_file
from botocore.client import ClientError


app = Flask(__name__)

CORS(app)
validationURL = "https://api.cs301-g7.com/validation/" 
EXTENSION = ".txt"

all_partner_codes = requests.get("https://api.cs301-g7.com/loyaltyprogram/partnercodes", headers={"X-API-KEY": "tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D"})
try:
    PARTNERCODEMAPPING = all_partner_codes.json()["PARTNERCODEMAPPING"]
except:
    PARTNERCODEMAPPING = {'CONRAD X CLUB': 'CXC', 'EMINENT AIRWAYS GUEST': 'EAG', 'GOPOINTS': 'GP', 'INDOPACIFIC': 'IP', 'MILLENNIUM REWARDS': 'MR', 'QUANTUM AIRLINES QFLYER': 'QAQ'}

LOYALTYPARTNERURLMAPPING = {
    "GP": "http://Gopointsthirdparty-env.eba-rya8ymsd.us-east-1.elasticbeanstalk.com",
    "IP": "http://Indopointsthirdparty-env.eba-kgewyjh5.us-east-1.elasticbeanstalk.com"
}

def connectDynamoDBClient():
    client = boto3.client('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name='ap-east-1')

    try:
        response_table = client.describe_table(TableName='ITSA-AP-EAST-1-FILE-DB')
        file_table = 'ITSA-AP-EAST-1-FILE-DB'
        region = 'ap-east-1'
    except client.exceptions.ResourceNotFoundException:
        file_table = 'ITSA-US-EAST-1-FILE-DB'
        client = boto3.client('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name='us-east-1')
        region = 'us-east-1'
    return client, region, file_table
    
def connectS3Client():
    s3 = boto3.client("s3",aws_access_key_id='AKIAWGZOGTL2WLBFP5IB',aws_secret_access_key='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')
    bucket_name = "itsa-ap-east-1-file-s3"
    try:
        s3.head_bucket(Bucket=bucket_name)
    except ClientError:
        bucket_name = "itsa-us-east-1-file-s3"
    return s3, bucket_name

@app.route("/", methods = ["GET"])
def health():
    return Response(status=200)

#accrual details from bank
@app.route("/file/addTransferDetails", methods=['PUT'])
def addTransfer():
    status = 400
    if (not (request.is_json)):
        result = request.get_data()
        replymessage = json.dumps({"status": status, "message": "Membership information should be in JSON", "data": result}, default=str, headers={"X-API-KEY": "tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D"})
        return replymessage

    result = request.get_json()
    result = json.loads(json.dumps(result, default=str))
    r = requests.post(validationURL + "accrual", json = result, headers={"X-API-KEY": "tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D"})

    validated_result = r.json()
    if (validated_result["status"] != 201):
        return {"status": status, "message": validated_result["message"]}

    try:
        client, region, file_table = connectDynamoDBClient()
        response = client.put_item(
                TableName=file_table,
                Item = {
                    "Member ID" : {'S': result["Member ID"]},
                    "Member first name" : {'S' : result["Member first name"]},
                    "Member last name" : {'S' : result["Member last name"]},
                    "Transfer date" : {'S' : result["Transfer date"]},
                    "Amount" : {'S' : result["Amount"]},
                    "Partner code" : {'S' :result["Partner code"]},
                    "Reference number" : {'S' : validated_result["Reference number"]},
                    "Loyalty Program" : {'S' : result["Loyalty Program"]}
                }
            )
    except client.exceptions.ClientError as e:
        if e.response['Error']['Code'] == 'ConditionalCheckFailedException':
            result = {"status": status, "message": "Reference number exists."}
            return result

    return {"status": 201, "message": "Successsfully added to database", "Reference number": validated_result["Reference number"]}

@app.route("/file/getDate", methods=['POST'])
def getDate():
    status = 400
    if (not (request.is_json)):
        result = request.get_data()
        replymessage = json.dumps({"status": status, "message": "Membership information should be in JSON", "data": result}, default=str)
        return replymessage

    result = request.get_json()
    referenceNumber = result["transactionID"]
    firstName = result['Member first name']
    lastName = result['Member last name']

    client, region, file_table = connectDynamoDBClient()
    response = client.get_item(
            TableName=file_table,
            Key = {
                'Reference number': {'S': referenceNumber}
            }
        )
    
    if ("Item" not in response):
        result = {"status": status, "message": "Reference number not found"}
        return result
    else:
        retrievedFirstName = response["Item"]["Member first name"]["S"]
        retrievedLastName = response["Item"]["Member last name"]["S"]

        if (retrievedFirstName != firstName or retrievedLastName != lastName):
            result = {"status": status, "message": "User does not have access to this reference number."}
            return result
    
    result = {"status": 201, "message": response["Item"]}
    return result

#handback file from 3rd party loyalty programme
@app.route("/file/handback", methods=['PUT'])
def receive_handback():
    isValid = False
    status = ""
    file_name = ""

    if 'handback_file' not in request.files:
        status= "No handback_file key in request"
        return {"isValid": isValid, "status": status}
    
    handback_file = request.files['handback_file']
    file_name = handback_file.filename

    if file_name == '':
        status= "No handback file selected"
        return {"isValid": isValid, "status": status}
    
    if not (handback_file and allowed_file_type(file_name)):
        status= "File not of txt format"
        return {"isValid": isValid, "status": status}

    #validation of handback
    r = requests.post(validationURL + "handback", files = {"handback_file": handback_file}, headers={"X-API-KEY": "tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D"})
    validationResult = r.json()
    
    if validationResult["status"] == 201:
        isValid = True
        uploadResult = uploadS3(handback_file, file_name)

        status = "processing"
        if uploadResult["status"] != 201:
            isValid = False
            status = uploadResult["message"]
        
    else:
        status = "Failed due to " + validationResult["message"].lower()
    
    returnJson = {"isValid": isValid, "status": status}
    
    return returnJson

#bank polling for transaction enquiry
@app.route("/file/getOutcomeCode", methods=['POST'])
def transaction_enquiry():
    enq_decode ={'0000':"success", "0001": "member not found", "0002": "member name mismatch",
                "0003": "member account closed", "0004": "member account suspended", 
                "0005" : "member ineligible for accrual",
                "0099": "unable to process, please contact support for more information"}
    
    if (not (request.is_json)):
        result = request.get_data()
        replymessage = json.dumps({"status": 400, "message": "Information should be in JSON", "data": result}, default=str)
        return replymessage

    result = request.get_json()
    date = result['transferDate']
    date = date.replace("-","")
    refNum = result['transactionID']
    bankCode = result['bankCode']

    filename = bankCode +"_"+ date + ".HANDBACK.txt"
    return_data = downloadS3(filename)

    if hasattr(return_data,"read"):
        result_dict = decode_file_obj(return_data)
        data = result_dict[refNum]
        code = data[2]
        if code == "0000":
            returnJson = {"status": 201, "message":enq_decode[code]}
        else:
            returnJson = {"status": 400, "message":enq_decode[code]}

        return returnJson

    return return_data

def downloadS3(obj_key):
    s3, bucket_name = connectS3Client()
    try:
        response = s3.get_object(Bucket=bucket_name, Key=obj_key)
        body = response['Body']
        return body

    except botocore.exceptions.ClientError as error:
        raise error
        return {"status": 400, "message":"download from S3 bucket failed"}

def allowed_file_type(filename):
    extensions = {'txt'}
    return '.' in filename and \
        filename.rsplit('.', 1)[1].lower() in extensions

def uploadS3(file, obj_name):
    s3, bucket_name = connectS3Client()
    try:
        s3.put_object(Body=file.getvalue(), Bucket=bucket_name, Key=obj_name)
    except botocore.exceptions.ClientError as error:
        return {"status": 400, "message":"upload to S3 bucket failed"}

    return {"status": 201, "message":"successful upload to S3 bucket"}

def decode_file_obj(file):
    dict_result = {}
    content = file.read().decode("utf-8")
    for r in content.split("\n")[1:]:
        if ("," in r):
            r = r.split(",")
            dict_result[r[2]] = [r[0], r[1], r[3].strip("\r")]

    return dict_result

# Retrieve file transfers by date and partnerCode
def find_file_transfers_by_date_and_partnerCode(partnerCode, date):
    client, region, file_table = connectDynamoDBClient()

    dynamodb = boto3.resource('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name=region)
    table = dynamodb.Table(file_table)
    response = table.scan()

    results = []
    if ("Items" in response):
        for result in response["Items"]:
            if(result["Partner code"] == partnerCode and result["Transfer date"] == date):
                results.append(result)
        result_message = {"status": 200, "Items": results}
        return result_message

    return {"status": 400, "message": "Unable to get file table."}


@app.route("/file/sendAccrual", methods=['PUT'])
def send_accrual():
    status = 400
    if (not (request.is_json)):
        result = request.get_data()
        replymessage = json.dumps({"status": status, "message": "Information should be in JSON", "data": result}, default=str)
        return replymessage

    result = request.get_json()
    partnerCode = ""
    if "partnerCode" in result:
        partnerCode = result["partnerCode"]
    date = datetime.today().strftime('%Y-%m-%d')
    results = find_file_transfers_by_date_and_partnerCode(partnerCode, date)

    if (results["status"] != 200):
        return results
    else:
        dict_result = results["Items"]
        new_dict_result = {}
        for transfer in dict_result:
            if transfer["Loyalty Program"] not in new_dict_result:
                new_dict_result[transfer["Loyalty Program"]] = [transfer]
            else:
                new_dict_result[transfer["Loyalty Program"]].append(transfer)
        
        for loyaltyPartner in new_dict_result:
            result = new_dict_result[loyaltyPartner]
            code = PARTNERCODEMAPPING[loyaltyPartner]

            index = 1
            filename = code + "_" + "".join(date.split("-")) + EXTENSION
            f = open("./file/" + filename, "w")
            f.writelines("index,Member ID,Member first name,Member last name,Transfer date,Amount,Reference number,Partner code\n")

            for transfer in result:
                index = index
                memberID = transfer["Member ID"]
                firstName = transfer["Member first name"]
                lastName = transfer["Member last name"]
                transferDate = transfer["Transfer date"]
                amount = transfer["Amount"]
                referenceNumber = transfer["Reference number"]
                partnerCode = transfer["Partner code"]

                f.writelines(str(index) + "," + str(memberID) + "," + str(firstName) + "," + str(lastName) + "," + str(transferDate) + "," + str(amount) +  "," + str(referenceNumber) + "," + str(partnerCode) + "\n")
                index = index + 1

            f.close()

            with open("./file/" + filename, "rb") as f:
                try:
                    s3, bucket_name = connectS3Client()
                    s3.upload_fileobj(f, "itsa-ap-east-1-file-s3", filename)
                except botocore.exceptions.ClientError as error:
                    return {"status": 400, "message": "Upload to S3 bucket failed"}
            
            files = {'accrual_file': open("./file/" + filename, 'rb')}
            loyaltyPartnerURL = LOYALTYPARTNERURLMAPPING[code]
            try:
                r = requests.post(loyaltyPartnerURL +  "/receive_accrual", files=files, headers={"X-API-KEY": "tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D"})
                print(r)
            except requests.exceptions.ConnectionError as e:
                return {"status": 400, "message": loyaltyPartner + " is not available"}

    return {"status": 201, "message":"Successfully uploaded to S3 bucket and sent to third party loyalty programs"}

if __name__ == '__main__': 
    app.run(debug=False)
    