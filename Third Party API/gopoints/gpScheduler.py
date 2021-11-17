from sqlalchemy.sql.functions import user
import schedule
import os
from datetime import date
import time
from sqlalchemy import create_engine, select, MetaData, Table, Column, Integer, String, Enum
from sqlalchemy.orm import Session
from sqlalchemy.ext.declarative import declarative_base
import enum
import shutil
import requests

engine = create_engine("mysql://root:@localhost:3306/gopoints", echo=False, future=True)
Base = declarative_base()

class accountStatus(enum.Enum):
    ACTIVE = 'ACTIVE'
    CLOSED = 'CLOSED'
    SUSPENDED = 'SUSPENDED'
    INELIGIBLE = 'INELIGIBLE'

class Member(Base):
    __tablename__ = 'memberdata'

    memberId = Column(String(30), primary_key=True)
    firstName = Column('firstName', String(255))
    lastName = Column(String(255))
    accountStatus = Column(Enum(accountStatus))
    pointsAmount = Column(String(10))

session = Session(engine, future=True)


def processFile(filePrefix):
    print("starting process accrual files.")
    uploadFolder = filePrefix + "uploads/"
    handbackFolder = filePrefix + "handbacks/"
    processedFolder = filePrefix + "processed/"
    for file in os.listdir(uploadFolder):
        print('Processing file ' + file)
        fileData = []
        with open(uploadFolder + file, "r") as f:
            for line in f:
                fileData.append(line.strip().split(","))
        result = [['Transfer Data', 'Amount', 'Reference number', 'Outcome code']]
        partnerCode = fileData[1][7]
        for line in fileData[1:]:
            outcome = None
            if line != [""]:
                lineData = line
                currMemberId = lineData[1]
                stmt = select(Member).filter_by(memberId=currMemberId)
                stmtexc = session.execute(stmt).scalars().all()
                outcome = '0001'
                if len(stmtexc) == 1:
                    userData = stmtexc[0]
                    if userData.firstName != lineData[2] or userData.lastName != lineData[3]:
                        outcome = '0002'
                    elif userData.accountStatus == accountStatus.CLOSED:
                        outcome = '0003'
                    elif userData.accountStatus == accountStatus.SUSPENDED:
                        outcome = '0004'
                    elif userData.accountStatus == accountStatus.INELIGIBLE:
                        outcome = '0005'
                    if outcome == None:
                        try:
                            currentPoints = userData.pointsAmount
                            newPoints = int(currentPoints) + int(lineData[5])
                            userData.pointsAmount = newPoints
                            session.commit()
                            outcome = '0000'
                        except:
                            outcome = '0099'
                    currentResult = [lineData[4], lineData[5], lineData[6], outcome]
                    result.append(currentResult)
        fileName = partnerCode.strip() + "_" + str(date.today().strftime('%Y%m%d')) + '.HANDBACK.txt'
        print(fileName)
        with open(handbackFolder + fileName, 'w') as f:
            f.writelines("%s\n" % (',').join(row) for row in result)
        shutil.move(uploadFolder + file, processedFolder + file)
        handbackFile = open(handbackFolder + fileName, "rb")
        try :
            res = requests.put("https://z8lnzkjnyf.execute-api.us-east-1.amazonaws.com/production/file/handback", files = {"handback_file": handbackFile}, headers={"X-API-KEY": "bv34XeV2TN1ta2ZAuTXlqU5eA0piseRBbHJCXih0"})
        except:
            res = requests.put("http://localhost:5002/file/handback", files = {"handback_file": handbackFile}, headers={"X-API-KEY": "bv34XeV2TN1ta2ZAuTXlqU5eA0piseRBbHJCXih0"})
        print(res)
    print("Complete.")


filePrefix = "./GP"

if not os.path.exists('./GPuploads'):
    os.makedirs('./GPuploads')
if not os.path.exists('./GPhandbacks'):
    os.makedirs('./GPhandbacks')
if not os.path.exists('./GPprocessed'):
    os.makedirs('./GPprocessed')

processFile(filePrefix)

# schedule.every(10).seconds.do(processFile, filePrefix)

# while True:
#     schedule.run_pending()
#     time.sleep(1)