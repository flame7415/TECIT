<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = [
        'complaint_id',
        'user_id',
        'barangay_id',
        'category_id',
        'description',
        'image_path',
        'vulnerability_flag',
        'priority_score',
        'priority_level',
        'status',
        'resolution_notes',
        'resolved_at',
        'escalated_to_municipal',
    ];

    protected $casts = [
        'priority_score' => 'float',
        'resolved_at' => 'datetime',
        'escalated_to_municipal' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Calculate and update priority score based on formula:
     * Priority Score = (category weight * 0.5) + (Vulnerability weight * 0.3) + (Duration weight * 0.2)
     */
    public function calculatePriorityScore(): float
    {
        // Category weight (0-10)
        $categoryWeight = $this->category ? $this->category->weight : 5;

        // Vulnerability weight (0-10)
        $vulnerabilityWeights = [
            'elderly' => 10,
            'PWD' => 10,
            'pregnant' => 8,
            'none' => 0,
        ];
        $vulnerabilityWeight = $vulnerabilityWeights[$this->vulnerability_flag] ?? 0;

        // Duration weight based on days since creation (0-10)
        $daysCreated = $this->created_at->diffInDays(now());
        $durationWeight = min($daysCreated * 1.5, 10);

        // Calculate priority score
        $score = ($categoryWeight * 0.5) + ($vulnerabilityWeight * 0.3) + ($durationWeight * 0.2);

        $this->priority_score = round($score, 2);
        $this->determinePriorityLevel();

        return $this->priority_score;
    }

    /**
     * Determine priority level based on score
     */
    public function determinePriorityLevel(): string
    {
        $score = $this->priority_score;

        if ($score >= 8) {
            $this->priority_level = 'critical';
        } elseif ($score >= 6) {
            $this->priority_level = 'high';
        } elseif ($score >= 4) {
            $this->priority_level = 'medium';
        } else {
            $this->priority_level = 'low';
        }

        return $this->priority_level;
    }

    /**
     * Check if complaint should be escalated to municipal
     * Escalation Logic:
     * - status = unresolved AND Days > 7 OR priority = high
     */
    public function checkEscalation(): bool
    {
        $daysCreated = $this->created_at->diffInDays(now());
        $isUnresolved = in_array($this->status, ['pending', 'acknowledged', 'in_progress']);
        $isHighPriority = in_array($this->priority_level, ['high', 'critical']);

        if ($isUnresolved && ($daysCreated > 7 || $isHighPriority)) {
            $this->escalated_to_municipal = true;
            return true;
        }

        return false;
    }
}
