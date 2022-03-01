<?php

namespace App\Events;

use App\Notifications;
use App\Visitors;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusLiked implements ShouldBroadcast {

	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $user;
	public $customer;
	public $message;
	public $date;
	public $type;
	public $username;
	public $visitDetails;
	public $visitCustName;


    /**
     * StatusLiked constructor.
     * @param $user
     * @param $type
     */
	public function __construct($user, $type) {

	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn() {
		return ['status-liked'];
	}
}