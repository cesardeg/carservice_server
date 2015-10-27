<?php

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Carbon\Carbon;

class RemindKmCapture extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'reminder:kmcapture';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remind to user to capture km of their car';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * When a command should run
	 *
	 * @param Scheduler $scheduler
	 * @return \Indatus\Dispatcher\Scheduling\Schedulable
	 */
	public function schedule(Schedulable $scheduler)
	{
		return $scheduler;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$now =  Carbon::now('America/Mexico_City');
		$reminders = Reminder::where('next_reminder', '<=', $now)
			->where('subject', 'KmCapture')
			->where('remind', 1)->get();
		foreach ($reminders as $reminder) 
		{
			$reminder->next_reminder = new Carbon($reminder->next_reminder, 'America/Mexico_City');
			switch ($reminder->time_unit) {
                case 'Minutos':
                	$periods = ceil($now->diffInMinutes($reminder->next_reminder)/$reminder->frequency);
                    $reminder->next_reminder->addMinutes($reminder->frequency * $periods);
                    if ($now->gte($reminder->next_reminder)) 
                    	$reminder->next_reminder->addMinutes($reminder->frequency);
                    break;
                case 'Horas':
                	$periods = ceil($now->diffInHours($reminder->next_reminder)/$reminder->frequency);	
                    $reminder->next_reminder->addHours($reminder->frequency * $periods);
                    if ($now->gte($reminder->next_reminder)) 
                    	$reminder->next_reminder->addHours($reminder->frequency * $periods);
                    break;
                case 'Días':
                	$periods = ceil($now->diffInDays($reminder->next_reminder)/$reminder->frequency);
                    $reminder->next_reminder->addDays($reminder->frequency * $periods);
                    if ($now->gte($reminder->next_reminder)) 
                    	$reminder->next_reminder->addDays($reminder->frequency * $periods);
                    break;
                case 'Semanas':
                	$periods = ceil($now->diffInWeeks($reminder->next_reminder)/$reminder->frequency);
                    $reminder->next_reminder->addWeeks($reminder->frequency * $periods);
                    if ($now->gte($reminder->next_reminder)) 
                    	$reminder->next_reminder->addWeeks($reminder->frequency * $periods);
                    break;
                case 'Meses':
                	$periods = ceil($now->diffInMonths($reminder->next_reminder)/$reminder->frequency);
                    $reminder->next_reminder->addMonths($reminder->frequency * $periods);
                    if ($now->gte($reminder->next_reminder)) 
                    	$reminder->next_reminder->addMonths($reminder->frequency * $periods);
                    break;
                case 'Años':
                	$periods = ceil($now->diffInYears($reminder->next_reminder)/$reminder->frequency);
                    $reminder->next_reminder->addYears($reminder->frequency * $periods);
                    if ($now->gte($reminder->next_reminder)) 
                    	$reminder->next_reminder->addYears($reminder->frequency * $periods);
                    break;
                default:
                	$reminder->remind = 0;
            }
            $reminder->save();
			if ($reminder->next_reminder->gt($now) && !$reminder->car->service_order_id) {
				Notification::where('car_owner_id', $reminder->car->car_owner_id)
                ->where('data', $reminder->car_id)
                ->where('active', 1)
                ->where('type', NotificationType::REMIND_KMCAPTURE)
                ->update(array('active' => 0));
				$data = [
	                'car_owner_id' => $reminder->car->car_owner_id,
	                'type'         => NotificationType::REMIND_KMCAPTURE,
	                'title'        => 'Captura de KM', 
	                'message'      => 'Favor de capturar el km de su vehículo',
	                'data'         => $reminder->car_id
	            ];
				$notification = new Notification(array_merge($data, array('date' => $now)));
	            $notification->save();
	            Queue::push('SendNotification', $data);
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
