<?php

namespace App\Services\JobBoard;

use App\Models\JobPost;
use App\Models\User;
use App\Models\JobApplication;
use Illuminate\Support\Facades\DB;

class JobMatchingService
{
    /**
     * Calculate match score between a crew member and a job post
     */
    public function calculateMatchScore(User $crew, JobPost $jobPost): float
    {
        $score = 0;
        $maxScore = 100;

        // Essential Requirements Check (must pass or return 0)
        if (!$this->meetsEssentialRequirements($crew, $jobPost)) {
            return 0;
        }

        // Experience matching (25 points)
        $score += $this->calculateExperienceScore($crew, $jobPost) * 0.25;

        // Location proximity (20 points)
        $score += $this->calculateLocationScore($crew, $jobPost) * 0.20;

        // Vessel size experience (15 points)
        $score += $this->calculateVesselSizeScore($crew, $jobPost) * 0.15;

        // Compensation alignment (15 points)
        $score += $this->calculateCompensationScore($crew, $jobPost) * 0.15;

        // Certification extras (10 points)
        $score += $this->calculateCertificationScore($crew, $jobPost) * 0.10;

        // Language requirements (10 points)
        $score += $this->calculateLanguageScore($crew, $jobPost) * 0.10;

        // Special skills match (5 points)
        $score += $this->calculateSkillsScore($crew, $jobPost) * 0.05;

        // Quality indicators bonus
        $score += $this->calculateQualityBonus($crew, $jobPost);

        return min(100, max(0, round($score, 2)));
    }

    /**
     * Check if crew meets essential requirements
     */
    private function meetsEssentialRequirements(User $crew, JobPost $jobPost): bool
    {
        // Check position match
        if ($jobPost->position_title && !$this->matchesPosition($crew, $jobPost->position_title)) {
            return false;
        }

        // Check required certifications
        if ($jobPost->required_certifications) {
            $crewCerts = $crew->certifications ?? [];
            foreach ($jobPost->required_certifications as $requiredCert) {
                if (!in_array($requiredCert, $crewCerts)) {
                    // Check if cert exists in documents via certificates relationship
                    // Documents have type='certificate' and status='approved'
                    // Certificates are related to documents and have certificate_type_id
                    // CertificateType has the 'name' field we need to match
                    $hasCert = $crew->documents()
                        ->where('type', 'certificate')
                        ->where('status', 'approved')
                        ->whereHas('certificates.type', function($q) use ($requiredCert) {
                            $q->where('name', 'like', '%' . $requiredCert . '%')
                              ->orWhere('name', '=', $requiredCert);
                        })
                        ->exists();
                    
                    if (!$hasCert) {
                        return false;
                    }
                }
            }
        }

        // Check availability alignment (for temporary work)
        if ($jobPost->isTemporary()) {
            $availability = $crew->crewAvailability;
            if (!$availability || $availability->status === 'not_available') {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate experience score
     */
    private function calculateExperienceScore(User $crew, JobPost $jobPost): float
    {
        $crewYears = $crew->years_experience ?? 0;
        $requiredYears = $jobPost->min_years_experience ?? 0;

        if ($requiredYears === 0) {
            return 1.0; // No requirement
        }

        if ($crewYears >= $requiredYears) {
            $excess = $crewYears - $requiredYears;
            // Bonus for exceeding requirement, but cap at 1.2
            return min(1.2, 1.0 + ($excess * 0.05));
        }

        // Penalty for insufficient experience
        return max(0, $crewYears / $requiredYears);
    }

    /**
     * Calculate location proximity score
     */
    private function calculateLocationScore(User $crew, JobPost $jobPost): float
    {
        if (!$crew->latitude || !$crew->longitude || !$jobPost->latitude || !$jobPost->longitude) {
            return 0.5; // Neutral if no location data
        }

        $distance = $crew->getDistanceTo($jobPost->latitude, $jobPost->longitude);

        // 0-5km: 1.0, 5-20km: 0.8, 20-50km: 0.6, 50-100km: 0.4, 100km+: 0.2
        if ($distance <= 5) {
            return 1.0;
        } elseif ($distance <= 20) {
            return 0.8;
        } elseif ($distance <= 50) {
            return 0.6;
        } elseif ($distance <= 100) {
            return 0.4;
        } else {
            return 0.2;
        }
    }

    /**
     * Calculate vessel size experience score
     */
    private function calculateVesselSizeScore(User $crew, JobPost $jobPost): float
    {
        $requiredSize = $jobPost->min_vessel_size_experience ?? 0;
        if ($requiredSize === 0) {
            return 1.0;
        }

        // Check previous yachts for size match
        $previousYachts = $crew->previous_yachts ?? [];
        foreach ($previousYachts as $yacht) {
            // This would need yacht size data - simplified for now
            if (isset($yacht['size']) && $yacht['size'] >= $requiredSize) {
                return 1.0;
            }
        }

        // Check current yacht
        if ($crew->current_yacht) {
            // Would need to check yacht model for size
            return 0.8; // Assume current yacht is relevant
        }

        return 0.5; // Neutral if no clear match
    }

    /**
     * Calculate compensation alignment score
     */
    private function calculateCompensationScore(User $crew, JobPost $jobPost): float
    {
        if ($jobPost->isPermanent()) {
            // Would need to get crew salary expectations from profile
            // For now, return neutral
            return 0.8;
        } else {
            // Temporary work - check day rate alignment
            $availability = $crew->crewAvailability;
            if ($availability && $availability->day_rate_min && $jobPost->day_rate_max) {
                if ($availability->day_rate_min <= $jobPost->day_rate_max) {
                    return 1.0;
                }
                // If crew rate is higher, penalty
                $difference = $availability->day_rate_min - $jobPost->day_rate_max;
                return max(0, 1.0 - ($difference / 50)); // Reduce score based on difference
            }
        }

        return 0.8; // Neutral
    }

    /**
     * Calculate certification score
     */
    private function calculateCertificationScore(User $crew, JobPost $jobPost): float
    {
        $preferredCerts = $jobPost->preferred_certifications ?? [];
        if (empty($preferredCerts)) {
            return 1.0;
        }

        $crewCerts = $crew->certifications ?? [];
        $matched = 0;
        foreach ($preferredCerts as $cert) {
            if (in_array($cert, $crewCerts)) {
                $matched++;
            }
        }

        return $matched / count($preferredCerts);
    }

    /**
     * Calculate language score
     */
    private function calculateLanguageScore(User $crew, JobPost $jobPost): float
    {
        $requiredLanguages = $jobPost->required_languages ?? [];
        $preferredLanguages = $jobPost->preferred_languages ?? [];

        if (empty($requiredLanguages) && empty($preferredLanguages)) {
            return 1.0;
        }

        $crewLanguages = $crew->languages ?? [];
        $score = 0.5; // Base score

        // Check required languages
        foreach ($requiredLanguages as $lang) {
            if (in_array($lang, $crewLanguages)) {
                $score += 0.3;
            } else {
                $score -= 0.2; // Penalty for missing required
            }
        }

        // Check preferred languages
        foreach ($preferredLanguages as $lang) {
            if (in_array($lang, $crewLanguages)) {
                $score += 0.2;
            }
        }

        return min(1.0, max(0, $score));
    }

    /**
     * Calculate special skills score
     */
    private function calculateSkillsScore(User $crew, JobPost $jobPost): float
    {
        $preferredSkills = $jobPost->preferred_skills ?? [];
        if (empty($preferredSkills)) {
            return 1.0;
        }

        $crewSkills = $crew->specializations ?? [];
        $matched = 0;
        foreach ($preferredSkills as $skill) {
            if (in_array($skill, $crewSkills)) {
                $matched++;
            }
        }

        return $matched / count($preferredSkills);
    }

    /**
     * Calculate quality bonus points
     */
    private function calculateQualityBonus(User $crew, JobPost $jobPost): float
    {
        $bonus = 0;

        // Rating bonus (up to +5 points)
        if ($crew->rating) {
            if ($crew->rating >= 4.5) {
                $bonus += 5;
            } elseif ($crew->rating >= 4.0) {
                $bonus += 3;
            } elseif ($crew->rating >= 3.5) {
                $bonus += 1;
            }
        }

        // Completion rate bonus (for temp work)
        if ($jobPost->isTemporary()) {
            $availability = $crew->crewAvailability;
            if ($availability && $availability->completion_rate_percentage === 100) {
                $bonus += 2;
            } elseif ($availability && $availability->completion_rate_percentage >= 90) {
                $bonus += 1;
            }
        }

        return min(7, $bonus); // Cap bonus at 7 points
    }

    /**
     * Check if crew matches position
     */
    private function matchesPosition(User $crew, string $positionTitle): bool
    {
        // This would check crew's position preferences or experience
        // Simplified check for now
        $specializations = $crew->specializations ?? [];
        $positionLower = strtolower($positionTitle);
        
        foreach ($specializations as $spec) {
            if (stripos($positionLower, strtolower($spec)) !== false) {
                return true;
            }
        }

        return true; // Default to true if no clear check
    }

    /**
     * Find matching crew for a job post
     */
    public function findMatchingCrew(JobPost $jobPost, int $limit = 20): array
    {
        $query = User::query();

        // Basic filters
        if ($jobPost->position_title) {
            // Would filter by position - simplified
        }

        // Location filter if specified
        if ($jobPost->latitude && $jobPost->longitude) {
            // Would use location-based query - simplified for now
        }

        $crewMembers = $query->where('looking_for_work', true)
            ->limit($limit * 2) // Get more than needed to filter
            ->get();

        $matches = [];
        foreach ($crewMembers as $crew) {
            $score = $this->calculateMatchScore($crew, $jobPost);
            if ($score >= 60) { // Only include matches above 60%
                $matches[] = [
                    'user' => $crew,
                    'score' => $score,
                ];
            }
        }

        // Sort by score descending
        usort($matches, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($matches, 0, $limit);
    }

    /**
     * Find matching jobs for a crew member
     */
    public function findMatchingJobs(User $crew, int $limit = 20, string $jobType = ''): array
    {
        $query = JobPost::published();

        if ($jobType) {
            $query->where('job_type', $jobType);
        }

        $jobPosts = $query->limit($limit * 2)->get();

        $matches = [];
        foreach ($jobPosts as $job) {
            $score = $this->calculateMatchScore($crew, $job);
            if ($score >= 60) {
                $matches[] = [
                    'job' => $job,
                    'score' => $score,
                ];
            }
        }

        // Sort by score descending
        usort($matches, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($matches, 0, $limit);
    }
}

