from PyQt5.QtWidgets import (QWidget,QMessageBox)
from ConfigParser import SafeConfigParser
import json
import requests

class connection:
   def __init__(self):
      self.parser = SafeConfigParser()
      self.parser.read('config.py')

   def getApi(self,payload):
      try:
         response = requests.get(self.parser.get('CONNECT', 'url')+'&override=1&loginform[login]='+self.parser.get('CONNECT', 'user')+'&loginform[pwd]='+self.parser.get('CONNECT', 'pwd'), params=payload, verify=False)
         return response
      except:
         QMessageBox.about(QWidget(), "Error", "No connection")
      return 1;

   def getTicketListByOwner(self,status):
      payload = {'api':'1','action':'getTicketListByOwner','status':status}
      return self.getApi(payload)

   def getTicketById(self,rtticketid):
      payload = {'api':'1','action':'getTicketById','id':rtticketid}
      return self.getApi(payload)

   def getCustomerById(self,customerid):
      payload = {'api':'1','action':'getCustomerById','id':customerid}
      return self.getApi(payload)

   def getHostList(self):
      payload = {'api':'1','action':'getHostList'}
      return self.getApi(payload)

   def reloadHost(self, hostid):
      payload = {'api':'1','action':'reloadHost','id':hostid}
      return self.getApi(payload)

   def AddTicketNote(self,rtticketid,data):
      payload = {'api':'1','action':'AddTicketNote','id':rtticketid,'data':data}
      return self.getApi(payload)

   def getEventList(self,status):
      payload = {'api':'1','action':'getEventList','status':status}
      return self.getApi(payload)

   def getEventById(self,eventid):
      payload = {'api':'1','action':'getEventById','id':eventid}
      return self.getApi(payload)

   def CloseEvent(self,eventid):
      payload = {'api':'1','action':'CloseEvent','id':eventid}
      return self.getApi(payload)

   def UpdateEventNote(self,eventid,data):
      payload = {'api':'1','action':'UpdateEventNote','id':eventid,'data':data}
      return self.getApi(payload)

   def toJson(self,data):
      try:
         return json.loads(data.content.decode('utf-8'));
      except:
         QMessageBox.about(QWidget(), "Error", "No data found")
         return 1;
