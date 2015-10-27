<?php
use Carbon\Carbon;
use Sly\NotificationPusher\PushManager,
    Sly\NotificationPusher\Adapter\Gcm as GcmAdapter,
    Sly\NotificationPusher\Adapter\Apns as ApnsAdapter,
    Sly\NotificationPusher\Collection\DeviceCollection,
    Sly\NotificationPusher\Model\Device,
    Sly\NotificationPusher\Model\Message,
    Sly\NotificationPusher\Model\Push;
/**
* 
*/
class SendNotification
{   
	public function fire($job, $data)
	{
        $images = ['service_order','service_diagnostic','service_quote','service_delivery','km_capture','scheduled_service'];
        try {
            $pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);
            $gcmAdapter  = new GcmAdapter(array(
                'apiKey' => 'AIzaSyDt5cYNhJSqQ3JpWoVDcTE5bXVMn_DrCvU',
            ));
            $apnsAdapter = new ApnsAdapter(array(
                'certificate' => '/Users/Cesar/Sites/carservices-server/app/commands/certificates-apns/development/development_com.hqh.carservice.pem',
                'passPhrase'  => 'hqh121213',
            ));
            $androidDevices = new DeviceCollection(
                Token::where('car_owner_id', $data['car_owner_id'])
                     ->where('platform', 'Android')
                     ->where('active', 1)
                     ->get()->map(function($token) {
                        return new Device($token->token);
                })->toArray()
            );
            $iosDevices = new DeviceCollection(
                Token::where('car_owner_id', $data['car_owner_id'])
                     ->where('platform', 'iOS')
                     ->where('active', 1)
                     ->get()->map(function($token) {
                        return new Device($token->token);
                })->toArray()
            );
            $message = new Message($data['message'], array(
                'title' => $data['title'],
                'image' => 'www/images/' . $images[$data['type']] . '.png',
                'data'  => $data['data'],
                'type'  => $data['type']
            ));
            $pushManager->add(new Push($gcmAdapter, $androidDevices, $message));
            $message = new Message($data['message'], array(
                'title'  => $data['title'],
                'badge'  => 1,
                'custom' => array(
                    'data'  => $data['data'],
                    'type'  => $data['type'],
                ),
            ));
            $pushManager->add(new Push($apnsAdapter,  $iosDevices  , $message));
            $pushManager->push();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
		$job->delete();
	}
}

?>