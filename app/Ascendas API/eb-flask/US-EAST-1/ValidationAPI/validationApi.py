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
import uuid
from botocore.client import ClientError

app = Flask(__name__)

CORS(app)

PARTNER_CODE = ["DBSSG", "AL", "ABCSG"]
COLUMN_NAME_HANDBACK = ["Transfer date", "Amount", "Reference number", "Outcome code"]

def connectDynamoDBClient():
    client = boto3.client('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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' , region_name='us-east-1')

    try:
        response_table = client.describe_table(TableName='ITSA-US-EAST-1-VALIDATION-DB')
        validation_table = 'ITSA-US-EAST-1-VALIDATION-DB'
        region= 'us-east-1'
    except client.exceptions.ResourceNotFoundException:
        validation_table = 'ITSA-AP-EAST-1-VALIDATION-DB'
        region= 'ap-east-1'
        dynamodb = boto3.client('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name='ap-east-1')
    return validation_table, region, client

def connectDynamoDBFileClient():
        dynamodb = boto3.client('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name='us-east-1')

        try:
            response_table_file = dynamodb.describe_table(TableName='ITSA-US-EAST-1-FILE-DB')
            region="us-east-1"
            file_table = 'ITSA-US-EAST-1-FILE-DB'
        except dynamodb.exceptions.ResourceNotFoundException:
            region="ap-east-1"
            file_table = 'ITSA-AP-EAST-1-FILE-DB'
            dynamodb = boto3.resource('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name='ap-east-1')
        return file_table, dynamodb, region
    
def connectS3Client():
    s3 = boto3.client("s3",aws_access_key_id='AKIAWGZOGTL2WLBFP5IB',aws_secret_access_key='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')
    bucket_name = "itsa-us-east-1-file-s3"
    try:
        s3.head_bucket(Bucket=bucket_name)
        s3 = boto3.client('s3', region_name="us-east-1",  config=boto3.session.Config(s3={'addressing_style': 'virtual'}, signature_version='s3v4'),aws_access_key_id='AKIAWGZOGTL2WLBFP5IB',aws_secret_access_key='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')
    except ClientError:
        bucket_name = "itsa-ap-east-1-file-s3"
        s3_client = boto3.client('s3', region_name="ap-east-1",  config=boto3.session.Config(s3={'addressing_style': 'virtual'}, signature_version='s3v4'),aws_access_key_id='AKIAWGZOGTL2WLBFP5IB',aws_secret_access_key='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')
    return s3

def connectS3Resource():
    s3 = boto3.client("s3",aws_access_key_id='AKIAWGZOGTL2WLBFP5IB',aws_secret_access_key='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')
    bucket_name = "itsa-us-east-1-file-s3"
    try:
        s3.head_bucket(Bucket=bucket_name)
        s3_resource = boto3.resource('s3', region_name="us-east-1",  config=boto3.session.Config(s3={'addressing_style': 'virtual'}, signature_version='s3v4'),aws_access_key_id='AKIAWGZOGTL2WLBFP5IB',aws_secret_access_key='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')
    except ClientError:
        bucket_name = "itsa-ap-east-1-file-s3"
        s3_resource = boto3.resource('s3', region_name="ap-east-1",  config=boto3.session.Config(s3={'addressing_style': 'virtual'}, signature_version='s3v4'),aws_access_key_id='AKIAWGZOGTL2WLBFP5IB',aws_secret_access_key='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')
    return s3_resource

@app.route("/", methods = ["GET"])
def health():
    return Response(status=200)

@app.route("/validation/members")
def get_all():
    validation_table, region, client = connectDynamoDBClient()
    dynamodb = boto3.resource('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name=region)
    table = dynamodb.Table(validation_table)
    response = table.scan()

    members = []
    for data in response['Items']:
        if("memberFirstName" in data and "memberLastName" in data):
            member_name = data['memberFirstName'] +" " + data['memberLastName']
            if(member_name not in members):
                members.append(member_name)
    return {"status": 201, "Items": members}

@app.route("/validation/accrual", methods=['POST'])
def validateAccrual():
    status = 400
    if (not (request.is_json)):
        result = request.get_data()
        replymessage = json.dumps({"status": status, "message": "Transfer information should be in JSON", "data": result}, default=str)
        return replymessage

    dict_result = request.get_json()

    # Check whether the fields exist in the file
    if "Member ID" not in dict_result:
        result = {"status": status, "message": "Member ID does not exist."}
        return result
    elif "Member first name" not in dict_result:
        result = {"status": status, "message": "Member first name does not exist."}
        return result
    elif "Member last name" not in dict_result:
        result = {"status": status, "message": "Member last name does not exist."}
        return result
    elif "Transfer date" not in dict_result:
        result = {"status": status, "message": "Transfer date does not exist."}
        return result
    elif "Amount" not in dict_result:
        result = {"status": status, "message": "Amount does not exist."}
        return result
    elif "Partner code" not in dict_result:
        result = {"status": status, "message": "Partner code does not exist."}
        return result
    elif "Loyalty Program" not in dict_result:
        result = {"status": status, "message": "Loyalty program does not exist."}
        return result

    validDate = True
    try:
        datetime.datetime.strptime(dict_result["Transfer date"], '%Y-%m-%d')
    except ValueError:
        validDate = False

    # Check whether the field samples are valid
    if (not (str(dict_result["Amount"]).isnumeric())):
        result = {"status": status, "message": "Invalid amount"}
        return result
    elif not validDate:
        result = {"status": status, "message": "Invalid transfer date"}
        return result
    elif dict_result["Partner code"] not in PARTNER_CODE:
        result = {"status": status, "message": "Invalid partner code"}
        return result
    
    # Check whether Loyalty program exists in DB
    programExistResponse = requests.get("https://api.cs301-g7.com/loyaltyprogram/" + str(dict_result["Loyalty Program"]), headers={"X-API-KEY": "tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D"})
    if (programExistResponse.json()["status"] != 201):
        result = {"status": status, "message": "Invalid loyalty program"}
        return result

    # check whether Member ID, first name and last name are valid with DB
    validation_table, region, client = connectDynamoDBClient()
    response = client.get_item(
        TableName=validation_table,
        Key = {
            'userId': {'S': dict_result["Member ID"]}
        }
    )

    if ("Item" in response):
        if ("memberFirstName" in response["Item"] and "memberLastName" in response["Item"]):
            if (response["Item"]["memberFirstName"].get("S") == dict_result["Member first name"] and response["Item"]["memberLastName"].get("S") == dict_result["Member last name"]):
                status = 201
                systemID = str(uuid.uuid4()).replace("-", "")
                result = {"status": status, "message": "Valid", "Reference number": systemID}
                return result
    
    result = {"status": status, "message": "Invalid - Membership details does not exist in database."}
    return result

@app.route("/validation/handback", methods=['POST'])
def validateHandback():
    status = 400
    if 'handback_file' not in request.files:
        message = "No handback_file key in request"
        return {"status": status, "message": message}
    
    file = request.files['handback_file']
    file_name = file.filename

    if file_name == '':
        message= "No handback file selected"
        return {"status": status, "message": message}
    
    # Validate filename
    if file_name.split(".")[1] != "HANDBACK":
        return {"status": status, "message": "Filename is not in the correct format."}
    filename_validation = validateFileName(file_name)
    if filename_validation["status"] != 201:
        return filename_validation
    
    dict_results = decode_file_obj(file, COLUMN_NAME_HANDBACK)

    for dict_result in dict_results:
        validDate = True
        try:
            datetime.datetime.strptime(dict_result["Transfer date"], '%Y-%m-%d')
        except ValueError:
            validDate = False

        if not validDate:
            result = {"status": status, "message": "Invalid transfer date in handback file."}
            return result
        elif not dict_result["Amount"].isnumeric():
            result = {"status": status, "message": "Invalid amount in handback file."}
            return result
        elif not dict_result["Reference number"].isnumeric():
            result = {"status": status, "message": "Invalid reference number in handback file."}
            return result 
        elif not dict_result["Outcome code"].isnumeric():
            result = {"status": status, "message": "Invalid outcome code in handback file."}
            return result 
        elif dict_result["Outcome code"] not in ["0000", "0001", "0002", "0003", "0004", "0005", "0099"]:
            result = {"status": status, "message": "Invalid outcome code in handback file."}
            return result 
    
    result = {"status": 201, "message": "Valid accrual file."}
    return result      

# Check whether the fields exist in the file
@app.route("/validation/membership", methods=['POST'])
def validateMembership():
    if (not (request.is_json)):
        result = request.get_data()
        status = 400 # Bad Request
        replymessage = json.dumps({"status": status, "message": "Membership information should be in JSON", "data": result}, default=str)
        return replymessage

    result = request.get_json()

    loyaltyProgramID = ""
    userID = ""

    if ("loyaltyProgramID" in result):
        loyaltyProgramID = result["loyaltyProgramID"]
    if ("userID" in result):
        userID = result["userID"]

    validMembership = False

    # check whether the format of the membership is valid
    if (loyaltyProgramID == "GOPOINTS"):
        validMembership = validateGoJek(userID)
    elif (loyaltyProgramID == "INDOPACIFIC"):
        validMembership = validateIndoPacific(userID)
    elif (loyaltyProgramID == "EMINENT AIRWAYS GUEST"):
        validMembership = validateEminentAirways(userID)
    elif (loyaltyProgramID == "QUANTUM AIRLINES QFLYER"):
        validMembership = validateQuantumAirlines(userID)
    elif (loyaltyProgramID == "CONRAD X CLUB"):
        validMembership = validateConradXClub(userID)
    elif (loyaltyProgramID == "MILLENNIUM REWARDS"):
        validMembership = validateMillennium(userID)
    else:
        status = 400
        result = {"status": status, "message": "Loyalty Program '{}' is invalid.".format(loyaltyProgramID)}
        return result

    if (validMembership):
        # check whether it exists in the database
        validation_table, region, client = connectDynamoDBClient()
        response = client.get_item(
            TableName=validation_table,
            Key = {
                'userId': {'S': userID}
            }
        )

        isValid = False
        if ("Item" in response):
            if ("loyaltyProgramId" in response["Item"]):
                if (response["Item"]["loyaltyProgramId"].get("S") == loyaltyProgramID):
                    status = 201
                    result = {"status": status, "message": "Valid Loyalty Program ID and User ID"}
                    return result

        if (not isValid):
            status = 400
            result = {"status": status, "message": "Invalid Loyalty Program ID and/or User ID"}
            return result
    else:
        status = 400
        result = {"status": status, "message": "User ID '{}' has invalid format.".format(userID)}
        return result

#Delete PII
@app.route("/validation/deletePII", methods = ['DELETE'])
def delete_PII():
    json = request.get_json()
    memberFirstName = json['memberFirstName']
    memberLastName = json['memberLastName'].upper()
    bankID = json['bankID'].upper()
    
    #get transactions
    get_transactions_by_member(memberFirstName,memberLastName)

    #delete information in accrual and handback files
    delete_accrual_by_user(memberFirstName, memberLastName, bankID)

    #delete from file table
    delete_reference_number(memberFirstName, memberLastName)

    #delete from validation table
    delete_by_user_id(memberFirstName, memberLastName)

    return {"status": 201, "message":"User information deleted!"}

def delete_by_user_id(memberFirstName, memberLastName):
    validation_table, region, client = connectDynamoDBClient()

    dynamodb = boto3.resource('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name='us-east-1')

    table = dynamodb.Table(validation_table)
    response = table.scan()
    for result in response["Items"]:
        if("memberFirstName" in result and "memberLastName" in result):
            if(result["memberFirstName"] == memberFirstName and result["memberLastName"] == memberLastName):
                delete_PII_validation(result["userId"])

def delete_PII_validation(user_Id):
    validation_table, region, client = connectDynamoDBClient()

    try:
        #retrieve information from validation table
        response = client.get_item(
            TableName=validation_table,
            Key={
                'userId': {'S': user_Id}
            }
        )
        if ("Item" in response):
            if ("loyaltyProgramId" in response["Item"]):
                loyaltyProgramId = response["Item"]["loyaltyProgramId"].get("S")

                #Delete member from validation table
                response = client.delete_item(
                    TableName=validation_table,
                    Key={
                        'userId': {'S': user_Id}
                    }
                )

                userUUID = str(uuid.uuid4().hex)
                #Insert into validation table
                response = client.put_item(
                    TableName=validation_table,
                    Item={
                            'userId': {'S': userUUID},
                            'loyaltyProgramId' : {'S' : loyaltyProgramId}
                        }
                )

                return {"status": 201, "message":"User information removal succesful"}
            else:
                return {"status": 400, "message":"User information removal failed"}
    except client.exceptions.ClientError as e:
        if e.response['Error']['Code'] == "ConditionalCheckFailedException":
            return {"status": 400, "message":"User information removal failed"}
    return {"status": 400, "message":"User information removal failed"}

def get_transactions_by_member(memberFirstName,memberLastName):
    file_table, dynamodb, region = connectDynamoDBFileClient()
    
    dynamodb = boto3.resource('dynamodb',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB', aws_secret_access_key='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', region_name=region)

    #get transfer dates, reference numbers
    table = dynamodb.Table(file_table)
    response = table.scan()
    transfer_dates = []
    reference_numbers = []

    for result in response["Items"]:
        if("Member first name" in result and "Member last name" in result):
            if(result["Member first name"] == memberFirstName and (result["Member last name"] == memberLastName)):
                if(result['Transfer date'] not in transfer_dates):
                    transfer_dates.append(result['Transfer date'])
                if(result['Reference number'] not in reference_numbers):
                    reference_numbers.append(result['Reference number'])

    result_message = {"status": 200, "Items":{"transfer_dates" : transfer_dates, "reference_numbers" : reference_numbers}}
    return result_message

def delete_reference_number(memberFirstName,memberLastName):
    reference_numbers = get_transactions_by_member(memberFirstName,memberLastName)['Items']["reference_numbers"]
    for reference_number in reference_numbers:
        delete_PII_file(reference_number)

def delete_PII_file(reference_number):
    file_table, client, region = connectDynamoDBFileClient()

    try:
        memberFirstName = ""
        memberLastName = ""
        memberId = ""
        response = client.update_item(
                TableName=file_table,
                Key = {
                    "Reference number" : {'S': reference_number},
                },
                ExpressionAttributeNames= {
                    "#fn":"Member first name",
                    "#ln":"Member last name",
                    "#mid":"Member ID"
                },
                UpdateExpression='SET #fn = :memberFirstName, #ln = :memberLastName, #mid = :memberId',
                ExpressionAttributeValues={
                    ":memberFirstName" : {'S' : memberFirstName},
                    ":memberLastName" : {'S' : memberLastName},
                    ":memberId" : {'S' : memberId},
                }
            )
        return {"status": 201, "message":"User information removal succesful"}
    except client.exceptions.ClientError as e:
        return {"status": 400, "message":"User information removal failed"}

def get_file_names(bank, transfer_dates):
    file_names = []
    for date in transfer_dates:
        date = date.split('-')
        date = ''.join(date)
        file_name_accrual = bank + '_' + date +'.txt'
        file_names.append(file_name_accrual)
    return file_names

def delete_contents(bucket, file_name, reference_numbers, region):
    #read the file


    s3_client = connectS3Client()

    s3_object = s3_client.get_object(Bucket=bucket, Key=file_name)
    data = s3_object['Body'].read().decode('utf-8')
    data = data.split('\n')

    header = data[0].strip('\r')
    content = [header]
    for i in range (1, len(data) - 1):
        line = data[i].strip('\r')
        line_split = line.split(',')
        if(line_split[6] not in reference_numbers):
            line_split[1] = ""
            line_split[2] = ""
            line_split[3] = ""
            line = ",".join(line_split)
            content.append(line)

    # delete file from s3 bucket
    s3 = connectS3Resource()
    s3.Object(bucket, file_name).delete()

    if(len(content) > 1):
        #create new file
        with open(file_name, 'w') as f:
            f.writelines("%s\n" % row for row in content)

        #upload file to s3
        try:
            with open(file_name, "rb") as f:
                s3_client.upload_fileobj(f, bucket, file_name)
        except botocore.exceptions.ClientError as error:
            return {"status": 400, "message":"upload to S3 bucket failed"}

        return {"status": 201, "message":"successful upload to S3 bucket"}

    return {"status": 201, "message":"PII deleted successfully in file"}

def delete_accrual_by_user(memberFirstName, memberLastName, bankID):
    json = request.get_json()
    memberFirstName = json['memberFirstName']
    memberLastName = json['memberLastName'].upper()
    bankID = json['bankID'].upper()
    s3 = boto3.resource('s3',aws_access_key_id='AKIAWGZOGTL2WLBFP5IB',aws_secret_access_key='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')
    
    if(s3.Bucket('itsa-us-east-1-file-s3') not in s3.buckets.all() and s3.Bucket('itsa-ap-east-1-file-s3') not in s3.buckets.all()):
        return {"status": 400, "message":"Buckets does not exist"}
    
    result = get_transactions_by_member(memberFirstName, memberLastName)
    transfer_dates = result['Items']['transfer_dates']
    reference_numbers = result['Items']['reference_numbers']
    
    file_names = get_file_names(bankID, transfer_dates)
    if(s3.Bucket('itsa-us-east-1-file-s3') in s3.buckets.all()):
        bucket_us_east_1 = s3.Bucket('itsa-us-east-1-file-s3')

        for obj in bucket_us_east_1.objects.all():
            file_name_us_east_1 = obj.key
            if(file_name_us_east_1 in file_names):
                delete_contents('itsa-us-east-1-file-s3', file_name_us_east_1 , reference_numbers, 'us-east-1')
    else:
        s3_client = boto3.resource('s3', region_name='ap-east-1',  config=boto3.session.Config(s3={'addressing_style': 'virtual'}, signature_version='s3v4'),aws_access_key_id='AKIAWGZOGTL2WLBFP5IB',aws_secret_access_key='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')
        bucket_ap_east_1 = s3_client.Bucket('itsa-ap-east-1-file-s3')

        for obj in bucket_ap_east_1.objects.all():
            file_name_ap_east_1 = obj.key
            if(file_name_ap_east_1 in file_names):
                delete_contents('itsa-ap-east-1-file-s3', file_name_ap_east_1 , reference_numbers, 'ap-east-1')

    return {"status": 201, "message":"Sucessfully deleted!"}

def validateFileName(filename):
    if (filename.find("_") == -1):
        status = 400
        result = {"status": status, "message": "Filename is not in the correct format."}
        return result
    elif (filename.split("_")[0] not in PARTNER_CODE):
        status = 400
        result = {"status": status, "message": "Filename does not contain valid partner code."}
        return result
    elif(str(datetime.datetime.today().date()).replace('-', '') != filename.split("_")[1][:filename.split("_")[1].index(".")]):
        status = 400
        result = {"status": status, "message": "Filename time is not the current time."}
        return result
    elif (filename.split("_")[1].find(".txt") == -1):
        status = 400
        result = {"status": status, "message": "Filename does not have the correct extension."}
        return result
    else:
        status = 201
        result = {"status": status, "message": "Filename is valid"}
        return result

def decode_file_obj(file, column_name):
    content = file.read().decode("utf-8")
    dict_array = []
    for i in range(1, len(content.split("\n")) - 1):
        dict_result = {}
        line_list = content.split("\n")[i].split(",")
        if (len(line_list) != len(column_name)):
            return {"status": 400, "message": "Missing values in file."}

        for j in range(len(line_list)):
            dict_result[column_name[j]] = line_list[j]
        dict_array.append(dict_result)   
    return dict_array

def validateGoJek(membership):
    if (len(membership) < 10 or len(membership) > 16):
        return False
    return True

def validateIndoPacific(membership):
    if (len(membership) != 10):
        return False
    return True

def validateEminentAirways(membership):
    if (len(membership) != 12):
        return False
    return True

def validateQuantumAirlines(membership):
    if (len(membership) != 10):
        return False
    return True

def validateConradXClub(membership):
    if (len(membership) != 9):
        return False
    return True

def validateMillennium(membership):
    if (len(membership) != 10):
        return False
    elif (not membership[:len(membership)-1].isnumeric()):
        return False
    elif (not membership[-1].isalpha()):
        return False
    return True

if __name__ == '__main__': 
    app.run(debug=False)