require 'rubygems'
require 'pushmeup'
GCM.host = 'https://android.googleapis.com/gcm/send'
GCM.format = :json
GCM.key = "AIzaSyCf2vsP-y0xUiQ4ef0GXbzOQ9CG7_M1myc"
destination = ["APA91bEHe0NW9gZdmqKle4reBtrYqerSr82BvHw6CiHbpflpXPuawIfTY2qiK9VwH2a2x4LZOyz6l8AVFgW4--LTuOErDOVuFtB9-fadyF478bm7TvIz-h1D-s8QDluIBnJmTE1AgiAM"]
data = {:message => "PhoneGap Build rocks!", :msgcnt => "1", :soundname => "beep.wav"}

#destination = ["APA91bH7CZU5MdsSGUVt8o36pK89yTGlW88sUzvpPMSLymzTFikSN6JdwBkpyyo9oCoI4MPvARUtaBoba622lD7nNg7EAlTSw7siU0SEbqWFZiijqr16nGmTwNHZ1dQbI_T1ZrS3oGiT"]
#data = {:message => "Se ha creado una orden de servicio para su vehículo", :msgcnt => "1", :soundname => "beep.wav", :type => 1, :title => "Orden de servicio", :image => "www/images/service_order.png", :service_order_id => 70}

GCM.send_notification( destination, data)
