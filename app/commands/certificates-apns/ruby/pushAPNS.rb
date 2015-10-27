require 'rubygems'
require 'pushmeup'


APNS.host = 'gateway.sandbox.push.apple.com' 
APNS.port = 2195 
APNS.pem  = '/Users/Cesar/Sites/carservices-server/app/commands/certificates-apns/CarServicesCert.pem'
APNS.pass = 'hqh121213'

device_token = '684405a7e0b682bfb9ef36095c3858ed85118fdca7a0a1cf629c9b548ecb1b70'
#APNS.send_notification(device_token, 'Hello iPhone!' )
APNS.send_notification(device_token, :alert => 'PushPlugin works!!', :badge => 1, :sound => 'beep.wav')
