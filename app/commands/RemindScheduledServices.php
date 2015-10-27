<?php

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Carbon\Carbon;

class RemindScheduledServices extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'reminder:scheduledservices';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remind to user do car service';

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
		return $scheduler
			->daily()
			->hours(12);
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$now =  Carbon::now('America/Mexico_City');
		ScheduledService::
		  where('date', '<=', $now)
		->whereNull('service_order_id')
		->where('notified', 0)->get()
		->map(function($scheduledService){
			$data = [
                'car_owner_id' => $scheduledService->car->carOwner->id,
                'type'         => NotificationType::REMIND_SERVICETIME,
                'title'        => 'Servicio programado', 
                'message'      => 'Es tiempo de realizar servicio técnico a su vehículo',
                'data'         => $scheduledService->id
            ];
            $notification = new Notification(array_merge($data, array('date' => Carbon::now('America/Mexico_City'))));
            $notification->save();
            Queue::push('SendNotification', $data);
            $scheduledService->notified = 1;
            $scheduledService->save();
		});
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
