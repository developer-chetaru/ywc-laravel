<?php

namespace App\Services\JobBoard;

use App\Models\JobPost;
use App\Models\User;
use App\Models\JobApplication;
use App\Services\JobBoard\JobMatchingService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class JobNotificationService
{
    protected $matchingService;

    public function __construct(JobMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Notify matching crew about a new job posting
     */
    public function notifyMatchingCrew(JobPost $jobPost): void
    {
        if (!$jobPost->notify_matching_crew) {
            return;
        }

        $matches = $this->matchingService->findMatchingCrew($jobPost, 50);

        foreach ($matches as $match) {
            $crew = $match['user'];
            $score = $match['score'];

            // Determine notification type based on match score
            if ($score >= 95) {
                $this->sendPerfectMatchNotification($crew, $jobPost, $score);
            } elseif ($score >= 85) {
                $this->sendExcellentMatchNotification($crew, $jobPost, $score);
            } elseif ($score >= 75) {
                $this->sendGoodMatchNotification($crew, $jobPost, $score);
            }
        }
    }

    /**
     * Notify crew about application status change
     */
    public function notifyApplicationStatusChange(JobApplication $application): void
    {
        $crew = $application->user;
        $jobPost = $application->jobPost;

        if (!$application->notify_on_status_change) {
            return;
        }

        switch ($application->status) {
            case 'viewed':
                $this->sendApplicationViewedNotification($crew, $jobPost);
                break;
            case 'shortlisted':
                $this->sendShortlistedNotification($crew, $jobPost);
                break;
            case 'interview_requested':
                $this->sendInterviewRequestNotification($crew, $jobPost);
                break;
            case 'offer_sent':
                $this->sendOfferNotification($crew, $jobPost);
                break;
            case 'declined':
                $this->sendDeclinedNotification($crew, $jobPost, $application->decline_feedback);
                break;
        }
    }

    /**
     * Notify captain about new application
     */
    public function notifyNewApplication(JobApplication $application): void
    {
        $jobPost = $application->jobPost;
        $captain = $jobPost->user;

        // Send notification to captain
        // This would integrate with your notification system
        Log::info('New application notification', [
            'captain_id' => $captain->id,
            'job_post_id' => $jobPost->id,
            'application_id' => $application->id,
            'match_score' => $application->match_score,
        ]);

        // Would send actual notification here
        // Notification::send($captain, new NewJobApplicationNotification($application));
    }

    /**
     * Notify captain about new temporary work application
     */
    public function notifyTemporaryWorkApplication(\App\Models\TemporaryWorkBooking $booking): void
    {
        $jobPost = $booking->jobPost;
        $captain = $booking->bookedBy;

        // Send notification to captain
        Log::info('New temporary work application notification', [
            'captain_id' => $captain->id,
            'job_post_id' => $jobPost->id,
            'booking_id' => $booking->id,
            'crew_id' => $booking->user_id,
        ]);

        // Would send actual notification here
        // Notification::send($captain, new NewTemporaryWorkApplicationNotification($booking));
    }

    /**
     * Notify crew about booking confirmation
     */
    public function notifyBookingConfirmed(\App\Models\TemporaryWorkBooking $booking): void
    {
        $crew = $booking->user;
        $jobPost = $booking->jobPost;

        Log::info('Booking confirmed notification', [
            'crew_id' => $crew->id,
            'booking_id' => $booking->id,
            'job_post_id' => $jobPost->id,
        ]);

        // Would send actual notification here
        // Notification::send($crew, new BookingConfirmedNotification($booking));
    }

    /**
     * Notify about urgent temporary work
     */
    public function notifyUrgentTemporaryWork(JobPost $jobPost): void
    {
        if (!$jobPost->isTemporary() || $jobPost->urgency_level === 'normal') {
            return;
        }

        $matches = $this->matchingService->findMatchingCrew($jobPost, 20);

        foreach ($matches as $match) {
            $crew = $match['user'];
            $availability = $crew->crewAvailability;

            if ($availability && $availability->status === 'available_now') {
                $this->sendUrgentWorkNotification($crew, $jobPost);
            }
        }
    }

    // Private notification methods

    private function sendPerfectMatchNotification(User $crew, JobPost $jobPost, float $score): void
    {
        // Perfect match - immediate push notification
        Log::info('Perfect match notification', [
            'crew_id' => $crew->id,
            'job_post_id' => $jobPost->id,
            'score' => $score,
        ]);

        // Would send push notification + email
        // Notification::send($crew, new PerfectJobMatchNotification($jobPost, $score));
    }

    private function sendExcellentMatchNotification(User $crew, JobPost $jobPost, float $score): void
    {
        Log::info('Excellent match notification', [
            'crew_id' => $crew->id,
            'job_post_id' => $jobPost->id,
            'score' => $score,
        ]);

        // Would send push notification
        // Notification::send($crew, new ExcellentJobMatchNotification($jobPost, $score));
    }

    private function sendGoodMatchNotification(User $crew, JobPost $jobPost, float $score): void
    {
        Log::info('Good match notification', [
            'crew_id' => $crew->id,
            'job_post_id' => $jobPost->id,
            'score' => $score,
        ]);

        // Would send email only (or daily digest)
        // Notification::send($crew, new GoodJobMatchNotification($jobPost, $score));
    }

    private function sendApplicationViewedNotification(User $crew, JobPost $jobPost): void
    {
        Log::info('Application viewed notification', [
            'crew_id' => $crew->id,
            'job_post_id' => $jobPost->id,
        ]);

        // Notification::send($crew, new ApplicationViewedNotification($jobPost));
    }

    private function sendShortlistedNotification(User $crew, JobPost $jobPost): void
    {
        Log::info('Shortlisted notification', [
            'crew_id' => $crew->id,
            'job_post_id' => $jobPost->id,
        ]);

        // Notification::send($crew, new ShortlistedNotification($jobPost));
    }

    private function sendInterviewRequestNotification(User $crew, JobPost $jobPost): void
    {
        Log::info('Interview request notification', [
            'crew_id' => $crew->id,
            'job_post_id' => $jobPost->id,
        ]);

        // Notification::send($crew, new InterviewRequestNotification($jobPost));
    }

    private function sendOfferNotification(User $crew, JobPost $jobPost): void
    {
        Log::info('Offer notification', [
            'crew_id' => $crew->id,
            'job_post_id' => $jobPost->id,
        ]);

        // Notification::send($crew, new OfferNotification($jobPost));
    }

    private function sendDeclinedNotification(User $crew, JobPost $jobPost, ?string $feedback): void
    {
        Log::info('Declined notification', [
            'crew_id' => $crew->id,
            'job_post_id' => $jobPost->id,
        ]);

        // Notification::send($crew, new ApplicationDeclinedNotification($jobPost, $feedback));
    }

    private function sendUrgentWorkNotification(User $crew, JobPost $jobPost): void
    {
        Log::info('Urgent work notification', [
            'crew_id' => $crew->id,
            'job_post_id' => $jobPost->id,
        ]);

        // Notification::send($crew, new UrgentTemporaryWorkNotification($jobPost));
    }
}

