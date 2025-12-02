<?php

namespace App\Livewire\IndustryReview;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Broker;

#[Layout('layouts.app')]
class BrokerComparison extends Component
{
    public $broker1Id = null;
    public $broker2Id = null;
    public $broker3Id = null;
    public $broker1 = null;
    public $broker2 = null;
    public $broker3 = null;

    public function mount()
    {
        // Load brokers if IDs are provided
        if ($this->broker1Id) {
            $this->broker1 = Broker::withCount('reviews')->find($this->broker1Id);
        }
        if ($this->broker2Id) {
            $this->broker2 = Broker::withCount('reviews')->find($this->broker2Id);
        }
        if ($this->broker3Id) {
            $this->broker3 = Broker::withCount('reviews')->find($this->broker3Id);
        }
    }

    public function selectBroker($position, $brokerId)
    {
        if ($position === 1) {
            $this->broker1Id = $brokerId;
            $this->broker1 = Broker::withCount('reviews')->find($brokerId);
        } elseif ($position === 2) {
            $this->broker2Id = $brokerId;
            $this->broker2 = Broker::withCount('reviews')->find($brokerId);
        } elseif ($position === 3) {
            $this->broker3Id = $brokerId;
            $this->broker3 = Broker::withCount('reviews')->find($brokerId);
        }
    }

    public function removeBroker($position)
    {
        if ($position === 1) {
            $this->broker1Id = null;
            $this->broker1 = null;
        } elseif ($position === 2) {
            $this->broker2Id = null;
            $this->broker2 = null;
        } elseif ($position === 3) {
            $this->broker3Id = null;
            $this->broker3 = null;
        }
    }

    public function render()
    {
        $availableBrokers = Broker::orderBy('name')->get();

        return view('livewire.industry-review.broker-comparison', [
            'availableBrokers' => $availableBrokers,
        ]);
    }
}
