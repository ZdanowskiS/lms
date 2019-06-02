# -*- coding: utf-8 -*-
from PyQt5.QtWidgets import (QApplication, QComboBox, QDialog,
QDialogButtonBox, QFormLayout, QGridLayout, QGroupBox, QHBoxLayout,
QLabel, QLineEdit, QMenu, QMenuBar, QPushButton, QSpinBox, QTextEdit,
QVBoxLayout, QLabel,QTabWidget,QWidget, QTextBrowser, QTableWidget,QTableWidgetItem,QMessageBox,QPlainTextEdit)

from PyQt5.QtWidgets import QMainWindow, QApplication, QPushButton, QWidget, QAction, QTabWidget,QVBoxLayout
from PyQt5.QtGui import QIcon
from PyQt5.QtCore import pyqtSlot
from PyQt5.QtGui import QIcon, QPixmap
from datetime import datetime

from ConfigParser import SafeConfigParser
import json
import requests

from imutils.video import VideoStream
from pyzbar import pyzbar
import argparse

import imutils
import time
import cv2
from matplotlib import pyplot as plt
import time

import connection

import sys
reload(sys)
sys.setdefaultencoding('utf8')

class Dialog(QDialog):

    def __init__(self,connection):
        super(Dialog, self).__init__()

        self.parser = SafeConfigParser()
        self.parser.read('config.py')

        self.connection=connection
        title = 'LMS - helpdesk/events v0.1'
        left = 0
        top = 0
        width = int(self.parser.get('WINDOW', 'width'))
        height = int(self.parser.get('WINDOW', 'height'))
        self.setWindowTitle(title)
        self.setGeometry(left, top, width,height)

        self.interfejs()

    def interfejs(self):
        self.tabs = QTabWidget()
        helpdesk = QWidget()
        events = QWidget()
        self.customer = QWidget()
        hosts = QWidget()
        barcode = QWidget()
        self.tabs.addTab(helpdesk,"helpdesk")
        self.tabs.addTab(events,"events")
        self.tabs.addTab(self.customer,"customer")
        self.tabs.addTab(hosts,"hosts")
        self.tabs.addTab(barcode,"BarCode")
       #elementy helpdesk
        BtnGetHelp = QPushButton("Get")
        self.tableWidget = QTableWidget()

        self.comBox = QComboBox(self)
        self.comBox.addItem("all")
        self.comBox.addItem("new")#RT_NEW
        self.comBox.addItem("open")#RT_OPEN
        self.comBox.addItem("resolved")#RT_RESOLVED
        control=QHBoxLayout()

        control.addWidget(BtnGetHelp)
        control.addWidget(self.comBox)
       #LAYOUT
        helpdesk.layout = QVBoxLayout(self)
        helpdesk.layout.addLayout(control)
        helpdesk.layout.addWidget(self.tableWidget)
        helpdesk.setLayout(helpdesk.layout)
       #Events
        BtnGetEvents = QPushButton("Get")
        self.QTEventList = QTableWidget()

        self.eventBox = QComboBox(self)
        self.eventBox.addItem("all")
        self.eventBox.addItem("open")#RT_NEW
        self.eventBox.addItem("private")#RT_OPEN
        event_control=QHBoxLayout()

        event_control.addWidget(BtnGetEvents)
        event_control.addWidget(self.eventBox)
       #Events layout
        events.layout = QVBoxLayout(self)
        events.layout.addLayout(event_control)
        events.layout.addWidget(self.QTEventList)
        events.setLayout(events.layout)

       #Customer
        label_name = QLabel("Name:", self)
        self.label_name_val = QLabel("", self)
        label_address = QLabel("Address", self)
        self.label_address_val = QLabel("", self)
        self.label_address_val.setWordWrap(1)

        label_phone = QLabel("Phone", self)
        self.label_phone_val = QLabel("", self)
        self.label_phone_val.setWordWrap(1)

        label_tariffs = QLabel("Tariffs", self)
        self.table_tariffs_val = QTableWidget()
       #Customer layput
        self.customer.layout = QGridLayout()
        self.customer.layout.addWidget(label_name, 0, 0)
        self.customer.layout.addWidget(self.label_name_val, 0, 1)
        self.customer.layout.addWidget(label_address, 1, 0)
        self.customer.layout.addWidget(self.label_address_val, 1, 1)
        self.customer.layout.addWidget(label_phone, 2, 0)
        self.customer.layout.addWidget(self.label_phone_val, 2, 1)
        self.customer.layout.addWidget(label_tariffs, 3, 0)
        self.customer.layout.addWidget(self.table_tariffs_val, 3, 1)
        self.customer.setLayout(self.customer.layout)

       #Hosts
        BtnGetHost = QPushButton("Get")
        self.tableHosts = QTableWidget()
       #Host layout
        hosts.layout = QGridLayout()
        hosts.layout.addWidget(BtnGetHost)
        hosts.layout.addWidget(self.tableHosts)
        hosts.setLayout(hosts.layout)

       #Barcode
        self.label_barcode_val = QLabel("", self)
        barcode.layout = QGridLayout()
        self.central_label = QLabel(self)
        barcode.layout.addWidget(self.label_barcode_val)
        BtnGetBarcode = QPushButton("Get")
        barcode.layout.addWidget(BtnGetBarcode)
        barcode.layout.addWidget(self.central_label)
        barcode.setLayout(barcode.layout)

       #Main layout add tabs
        mainLayout = QGridLayout()
        mainLayout.addWidget(self.tabs)
        self.setLayout(mainLayout)

       #helpdesk customers actions
        BtnGetHelp.clicked.connect(self.getTicketList)
        BtnGetEvents.clicked.connect(self.getEventList)

        self.tableWidget.cellClicked.connect(self.clickTicketList)
        self.tableHosts.cellClicked.connect(self.reloadHost)

       #events actions
        self.QTEventList.cellClicked.connect(self.clickEventList)
       #hosts actions
        BtnGetHost.clicked.connect(self.hostlist)
       #barcode actions
        BtnGetBarcode.clicked.connect(self.getbarcode)

    def getTicketList(self):
        response =self.connection.getTicketListByOwner(self.comBox.currentText())
        y=self.connection.toJson(response)
        self.tableWidget.setRowCount(0);
        if y is not None:
           print y
           self.tableWidget.setRowCount(len(y))
           self.tableWidget.setColumnCount(4) 
           self.tableWidget.setHorizontalHeaderLabels(['id', 'customerid', 'subject', 'createtime']) 
           for idx, val in enumerate(y):
              self.tableWidget.setItem(idx,0, QTableWidgetItem(val['id']))
              self.tableWidget.setItem(idx,1, QTableWidgetItem(val['customerid']))
              self.tableWidget.setItem(idx,2, QTableWidgetItem(val['subject']))
              self.tableWidget.setItem(idx,3, QTableWidgetItem(datetime.utcfromtimestamp(int(val['createtime'])).strftime('%Y-%m-%d %H:%M')))

           self.tableWidget.resizeColumnToContents(0)
           self.tableWidget.resizeColumnToContents(1)
           self.tableWidget.resizeColumnToContents(2)
           self.tableWidget.resizeColumnToContents(3)

    def clickTicketList(self, row, column):
        item = self.tableWidget.item(row, column)
        self.ID = item.text()
        if column==1 and self.ID>0:#if customer
           self.tabs.setCurrentWidget(self.customer)
           response =self.connection.getCustomerById(self.ID)
           y=self.connection.toJson(response)
           if y is not None:
              self.label_name_val.setText(y[0]['name'] + " " + y[0]['lastname'])
              self.label_address_val.setText(y[0]['full_address'])

              self.table_tariffs_val.setRowCount(len(y[0]['tariffs']))
              self.table_tariffs_val.setColumnCount(4) 
              self.table_tariffs_val.setHorizontalHeaderLabels(['name', 'downrate', 'uprate', 'value'])
              for idx, val in enumerate(y[0]['tariffs']):
                 self.table_tariffs_val.setItem(idx,0, QTableWidgetItem(val['name']))
                 self.table_tariffs_val.setItem(idx,1, QTableWidgetItem(val['downrate']))
                 self.table_tariffs_val.setItem(idx,2, QTableWidgetItem(val['uprate']))
                 self.table_tariffs_val.setItem(idx,3, QTableWidgetItem(val['value']))

              self.label_phone_val.setText("")
              if y[0]['phones'] is not None:
                 for idx, val in enumerate(y[0]['phones']):
                    self.label_phone_val.setText(self.label_phone_val.text() + val['name']+":"+val['contact']);

        if column==2 or column==0:#If ticket
           ticket_tabs = QTabWidget()
           ticketInfo_tab = QWidget()
           ticketAdd_tab = QWidget()

           ticket_tabs.addTab(ticketInfo_tab,"Info")
           ticket_tabs.addTab(ticketAdd_tab,"Add note")

           ticketInfo_tab.layout = QVBoxLayout()
           ticketAdd_tab.layout = QVBoxLayout()
           item = self.tableWidget.item(row, 0)
           self.ID = item.text()
          #Get ticket data
           response =self.connection.getTicketById(self.ID)
           y=self.connection.toJson(response)

           self.userDialog = QDialog(self)
           self.userDialog.setWindowTitle("Ticket info")
           width = int(self.parser.get('WINDOW', 'width'))-10
           height = int(self.parser.get('WINDOW', 'height'))-10
           self.userDialog.setGeometry(10, 10, width,height)
          #Ticket info layout
           info_layout = QGridLayout()

           label_ticket = QLabel("Ticket:", self)
           label_ticket_val = QLabel(str(y[0]['ticketid']),self)
           info_layout.addWidget(label_ticket,0,0)
           info_layout.addWidget(label_ticket_val,0,1)

           label_subject = QLabel("Subject:", self)
           label_subject_val = QLabel(str(y[0]['subject']),self)
           info_layout.addWidget(label_subject,1,0)
           info_layout.addWidget(label_subject_val,1,1)

           label_customer = QLabel("Customer:", self)
           label_customer_val = QLabel(str(y[0]['customername']),self)
           info_layout.addWidget(label_customer,2,0)
           info_layout.addWidget(label_customer_val,2,1)

           label_requestor = QLabel("Zgłaszający:", self)
           label_requestor_val = QLabel(str(y[0]['requestor_username']),self)
           info_layout.addWidget(label_requestor,3,0)
           info_layout.addWidget(label_requestor_val,3,1)

           label_owner = QLabel("Owner:", self)
           label_owner_val = QLabel(str(y[0]['ownername']),self)
           info_layout.addWidget(label_owner,0,2)
           info_layout.addWidget(label_owner_val,0,3)

           label_address = QLabel("Address:", self)
           label_address_val = QLabel(str(y[0]['location']),self)
           label_address_val.setWordWrap(1)
           info_layout.addWidget(label_address,1,2)
           info_layout.addWidget(label_address_val,1,3)

           QTTicketMessages = QTableWidget()
           QTTicketMessages.setRowCount(len(y[0]['messages']))
           QTTicketMessages.setColumnCount(2) 
           QTTicketMessages.setHorizontalHeaderLabels(['date', 'message'])

           for idx, val in enumerate(y[0]['messages']):
              QTTicketMessages.setItem(idx,0, QTableWidgetItem(datetime.fromtimestamp(int(val['createtime'])).strftime('%Y-%m-%d %H:%M')))
              QTTicketMessages.setItem(idx,1, QTableWidgetItem(val['body']))
              QTTicketMessages.setColumnWidth(1,width-90)
              QTTicketMessages.resizeColumnToContents(0)
              QTTicketMessages.resizeRowToContents(idx)

           ticketInfo_tab.layout.addLayout(info_layout)
           ticketInfo_tab.layout.addWidget(QTTicketMessages)
           ticketInfo_tab.setLayout(ticketInfo_tab.layout)

          #Add note layout
           add_layout = QGridLayout()

           self.textbox = QPlainTextEdit(self)
           add_layout.addWidget(self.textbox)

           BtnAddNote = QPushButton("Add")
           add_layout.addWidget(BtnAddNote)

           add_layout.addWidget(BtnAddNote);
           ticketAdd_tab.layout.addLayout(add_layout)
           ticketAdd_tab.setLayout(ticketAdd_tab.layout)

          #Main layout
           mainLayout = QGridLayout()
           mainLayout.addWidget(ticket_tabs)

           self.userDialog.setLayout(mainLayout)
           self.userDialog.show()
          #Add note action
           BtnAddNote.clicked.connect(self.AddTicketNote)

    def clickEventList(self, row, column):
        item = self.QTEventList.item(row, 0)
        self.ID = item.text()

        if self.ID>0:
          #Get event to display
           response =self.connection.getEventById(self.ID)
           y=self.connection.toJson(response)

           event_tabs = QTabWidget()
           eventInfo_tab = QWidget()
           eventEdit_tab = QWidget()

           event_tabs.addTab(eventInfo_tab,"Info")
           event_tabs.addTab(eventEdit_tab,"Edit")

           eventInfo_tab.layout = QVBoxLayout()
           eventEdit_tab.layout = QVBoxLayout()

           self.QDevent = QDialog(self)
           self.QDevent.setWindowTitle("Ticket info")
           width = int(self.parser.get('WINDOW', 'width'))-10
           height = int(self.parser.get('WINDOW', 'height'))-10
           self.QDevent.setGeometry(10, 10, width,height)

          #Event info layout
           info_layout = QGridLayout()

           label_title = QLabel("Title:", self)
           label_title_val = QLabel(str(y[0]['title']),self)
           info_layout.addWidget(label_title,0,0)
           info_layout.addWidget(label_title_val,0,1)

           label_date = QLabel("Start Time:", self)
           label_date_val = QLabel(str(datetime.fromtimestamp(int(y[0]['date'])).strftime('%Y-%m-%d'))+" "+str(time.strftime("%H:%M", time.localtime(float(y[0]['begintime'])))),self)
           info_layout.addWidget(label_date,1,0)
           info_layout.addWidget(label_date_val,1,1)

           label_date = QLabel("End Time:", self)
           label_date_val = QLabel(str(datetime.fromtimestamp(int(y[0]['enddate'])).strftime('%Y-%m-%d'))+" "+str(time.strftime("%H:%M", time.localtime(float(y[0]['endtime'])))),self)
           info_layout.addWidget(label_date,2,0)
           info_layout.addWidget(label_date_val,2,1)

           label_description = QLabel(y[0]['description'], self)
           label_note = QLabel(y[0]['note'], self)

           eventInfo_tab.layout.addLayout(info_layout)
           eventInfo_tab.layout.addWidget(label_description)
           eventInfo_tab.layout.addWidget(label_note)

           eventInfo_tab.setLayout(eventInfo_tab.layout)
          #edit elements
           self.QPEventNote = QPlainTextEdit(self)
           self.QPEventNote.setPlainText(y[0]['note']);
           eventEdit_tab.layout.addWidget(self.QPEventNote)

          #buttons
           BtnUpdateNote = QPushButton("Update")
           BtnCloseEvent = QPushButton("Close event")
          #button layout
           eventEditControlLayout = QHBoxLayout()
           eventEditControlLayout.addWidget(BtnUpdateNote)
           eventEditControlLayout.addWidget(BtnCloseEvent)

           eventEdit_tab.layout.addLayout(eventEditControlLayout)

           eventEdit_tab.setLayout(eventEdit_tab.layout)

           mainLayout = QGridLayout()
           mainLayout.addWidget(event_tabs)
           self.QDevent.setLayout(mainLayout)
           self.QDevent.show()

       #Event edit actions
        BtnUpdateNote.clicked.connect(self.UpdateEventNote)
        BtnCloseEvent.clicked.connect(self.CloseEvent)

    def AddTicketNote(self):
        response =self.connection.AddTicketNote(self.ID,self.textbox.toPlainText())
        y=self.connection.toJson(response)

    def CloseEvent(self):
        response =self.connection.CloseEvent(self.ID)
        y=self.connection.toJson(response)

    def UpdateEventNote(self):
        response =self.connection.UpdateEventNote(self.ID,self.QPEventNote.toPlainText())
        y=self.connection.toJson(response)

    def getEventList(self,status):
        response =self.connection.getEventList(self.eventBox.currentText())
        y=self.connection.toJson(response)    

        if y is not None:
           self.QTEventList.setRowCount(len(y))
           self.QTEventList.setColumnCount(4) 
           self.QTEventList.setHorizontalHeaderLabels(['id', 'title', 'date', 'begintime'])
           for idx, val in enumerate(y):
              self.QTEventList.setItem(idx,0, QTableWidgetItem(val['id']))
              self.QTEventList.setItem(idx,1, QTableWidgetItem(val['title']))
              self.QTEventList.setItem(idx,2, QTableWidgetItem(datetime.fromtimestamp(int(val['date'])).strftime('%x')))
              self.QTEventList.setItem(idx,3, QTableWidgetItem(time.strftime("%H:%M", time.localtime(float(val['begintime'])))))

           self.QTEventList.resizeColumnToContents(0)
           self.QTEventList.resizeColumnToContents(1)
           self.QTEventList.resizeColumnToContents(2)
           self.QTEventList.resizeColumnToContents(3)

    def hostlist(self):
           response =self.connection.getHostList()

           y=self.connection.toJson(response)

           if(y!=1):
              self.tableHosts.setRowCount(len(y))
              self.tableHosts.setColumnCount(4) 
              self.tableHosts.setHorizontalHeaderLabels(['id', 'name', 'time', 'reload'])
              for idx, val in enumerate(y):
                 self.tableHosts.setItem(idx,0, QTableWidgetItem(val['id']))
                 self.tableHosts.setItem(idx,1, QTableWidgetItem(val['name']))
                 if(int(val['lastreload'])>0):
                    self.tableHosts.setItem(idx,2, QTableWidgetItem(datetime.fromtimestamp(int(val['lastreload'])).strftime('%Y-%m-%d %H:%M')))
                 else:
                  self.tableHosts.setItem(idx,2, QTableWidgetItem("-"))
                 self.tableHosts.setItem(idx,3, QTableWidgetItem("reload"))
              self.tableHosts.resizeColumnToContents(0)
              self.tableHosts.resizeColumnToContents(1)
              self.tableHosts.resizeColumnToContents(2)

    def reloadHost(self, row, column):
       item = self.tableHosts.item(row, 0)
       hostID = item.text()
       if column==3 and hostID>0:
           response =self.connection.reloadHost(hostID)
           self.hostlist() 

    def getbarcode(self):
        #https://www.pyimagesearch.com/2018/03/19/reading-barcodes-with-python-and-openmv/
        #https://www.learnopencv.com/barcode-and-qr-code-scanner-using-zbar-and-opencv/
        #start_window = StartWindow()
        #start_window.show()

        cap = cv2.VideoCapture(0)
        ret, frame = cap.read()

        cv2.imwrite("frame.jpg", frame )
        im = cv2.imread('frame.jpg')
        #cv2.imwrite("frame.jpg", frame)
        pixmap=QPixmap("frame.jpg")
        self.central_label.setPixmap(pixmap)

        decodedObjects = pyzbar.decode(im)
        # Print results
        for obj in decodedObjects:
           print('Type : ', obj.type)
           print('Data : ', obj.data,'\n')

        try:
           self.label_barcode_val.setText(str(obj.data))
        except:
           QMessageBox.about(QWidget(), "Error", "No code found")
        #plt.imshow(im)
        #plt.show()

    def display(self, im, decodedObjects):
    # Loop over all decoded objects
        for decodedObject in decodedObjects: 
            points = decodedObject.polygon
 
        # If the points do not form a quad, find convex hull
        if len(points) > 4 : 
            hull = cv2.convexHull(np.array([point for point in points], dtype=np.float32))
            hull = list(map(tuple, np.squeeze(hull)))
        else : 
              hull = points;
     
        # Number of points in the convex hull
        n = len(hull)
 
        # Draw the convext hull
        for j in range(0,n):
            cv2.line(im, hull[j], hull[ (j+1) % n], (255,0,0), 3)
 
        # Display results 
        cv2.imshow("Results", im);
        cv2.waitKey(0);

if __name__ == '__main__':
    p1 = connection.connection()

    app = QApplication(sys.argv)
    dialog = Dialog(p1)
    sys.exit(dialog.exec_())
