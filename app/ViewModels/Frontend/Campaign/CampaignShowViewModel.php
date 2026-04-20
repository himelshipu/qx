<?php

declare(strict_types=1);

namespace App\ViewModels\Frontend\Campaign;

use App\Models\Campaign;
use App\Models\CampaignApplication;
use Illuminate\Support\Collection;

class CampaignShowViewModel
{
    public function __construct(
        protected Campaign $campaign,
        protected Collection $workProgress,
        protected Collection $assignmentByInfluencer,
        protected ?CampaignApplication $influencerApplication,
    ) {}

    public function toArray(): array
    {
        return [
            'applicationStats' => $this->applicationStats(),
            'workProgressCards' => $this->workProgressCards(),
            'applicationUi' => $this->applicationUi(),
            'influencerApplicationUi' => $this->influencerApplicationUi(),
        ];
    }

    private function applicationStats(): array
    {
        return [
            'invited' => $this->campaign->applications->where('status', 'invited')->count(),
            'applied' => $this->campaign->applications->whereIn('status', ['applied', 'countered_by_brand', 'countered_by_influencer'])->count(),
            'approved' => $this->campaign->applications->whereIn('status', ['approved', 'completed'])->count(),
            'rejected' => $this->campaign->applications->whereIn('status', ['rejected', 'declined_by_brand', 'declined_by_influencer'])->count(),
        ];
    }

    private function workProgressCards(): Collection
    {
        return $this->workProgress->map(function (array $item): array {
            $rawStatusKey = (string) ($item['status_key'] ?? 'pending');
            $statusKey = match ($rawStatusKey) {
                'accepted' => 'in_progress',
                'on_review' => 'delivered',
                'completed' => 'approved',
                default => $rawStatusKey,
            };

            $item['status_key'] = $statusKey;

            $item['progress_bar_class'] = match ($statusKey) {
                'approved' => 'bg-emerald-500',
                'delivered' => 'bg-indigo-500',
                'in_progress' => 'bg-blue-500',
                'rejected' => 'bg-rose-500',
                'pending' => 'bg-amber-500',
                'cancelled' => 'bg-red-500',
                default => 'bg-gray-400',
            };

            $item['status_badge_class'] = match ($statusKey) {
                'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                'delivered' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
                'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                'rejected' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
                'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
            };

            return $item;
        });
    }

    private function applicationUi(): array
    {
        $progressByApplication = $this->workProgress->keyBy('application_id');

        return $this->campaign->applications
            ->mapWithKeys(function (CampaignApplication $application) use ($progressByApplication): array {
                $progressEntry = $progressByApplication->get($application->id, []);
                $rawWorkStatusKey = (string) ($progressEntry['status_key'] ?? $application->work_status ?? '');
                $workStatusKey = match ($rawWorkStatusKey) {
                    'accepted' => 'in_progress',
                    'on_review' => 'delivered',
                    'completed' => 'approved',
                    default => $rawWorkStatusKey,
                };

                if ($workStatusKey === '' && $application->status === 'completed') {
                    $workStatusKey = 'approved';
                }

                $workStatusLabelMap = [
                    'pending' => 'Order Pending',
                    'in_progress' => 'In Progress',
                    'delivered' => 'Delivered',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ];

                $workStatus = $workStatusKey !== ''
                    ? ($workStatusLabelMap[$workStatusKey] ?? ucfirst(str_replace('_', ' ', $workStatusKey)))
                    : null;

                $workStatusStyle = match ($workStatus) {
                    'Approved' => 'background-color: rgba(209, 250, 229, 1); color: rgb(4, 120, 87);',
                    'Delivered' => 'background-color: rgba(224, 231, 255, 1); color: rgb(67, 56, 202);',
                    'In Progress' => 'background-color: rgba(219, 234, 254, 1); color: rgb(29, 78, 216);',
                    'Rejected' => 'background-color: rgba(254, 226, 226, 1); color: rgb(185, 28, 28);',
                    default => 'background-color: rgba(254, 243, 199, 1); color: rgb(180, 83, 9);',
                };

                [$statusLabel, $statusClass] = match ((string) $application->status) {
                    'approved' => ['Approved', 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'],
                    'completed' => ['Completed', 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300'],
                    'rejected' => ['Rejected', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
                    'declined_by_brand' => ['Declined by Brand', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
                    'declined_by_influencer' => ['Declined by Influencer', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
                    'countered_by_brand' => ['Countered by Brand', 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
                    'countered_by_influencer' => ['Countered by Influencer', 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'],
                    'applied' => ['Applied', 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
                    default => ['Invited', 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
                };

                $maxFollowers = (int) ($application->influencer?->platformStats?->max('follower_count') ?? 0);
                $currency = strtoupper((string) ($this->campaign->currency ?? 'USD'));

                return [
                    $application->id => [
                        'status_label' => $statusLabel,
                        'status_class' => $statusClass,
                        'max_followers' => $maxFollowers,
                        'work_status' => $workStatus,
                        'work_status_style' => $workStatusStyle,
                        'currency' => $currency,
                    ],
                ];
            })
            ->all();
    }

    private function influencerApplicationUi(): ?array
    {
        if (! $this->influencerApplication) {
            return null;
        }

        $status = (string) $this->influencerApplication->status;

        $statusClass = [
            'invited' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'applied' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            'countered_by_brand' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
            'countered_by_influencer' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'declined_by_brand' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'declined_by_influencer' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        ][$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';

        $statusLabel = match ($status) {
            'invited' => 'Invited',
            'applied' => 'Applied',
            'countered_by_brand' => 'Countered by Brand',
            'countered_by_influencer' => 'Counter sent',
            'approved' => 'Approved',
            'rejected' => 'Not Selected',
            'declined_by_brand' => 'Declined by Brand',
            'declined_by_influencer' => 'Declined by You',
            'completed' => 'Work Completed',
            default => 'Unknown',
        };

        return [
            'status_class' => $statusClass,
            'status_label' => $statusLabel,
            'can_withdraw' => ! in_array($status, ['rejected', 'completed', 'approved', 'declined_by_brand', 'declined_by_influencer'], true),
            'currency' => strtoupper((string) ($this->campaign->currency ?? 'USD')),
        ];
    }
}
