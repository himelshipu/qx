<?php

namespace App\Traits;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasNotifications
{
    /**
     * Get all notifications for this model
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /**
     * Notify a user about this model
     *
     * @param  \App\Models\User           $user
     * @param  string                     $type
     * @param  string                     $title
     * @param  string                     $body
     * @param  array                      $data
     * @return \App\Models\Notification
     */
    public function notifyUser($user, string $type, string $title, string $body, array $data = []): Notification
    {
        return Notification::create([
            'user_id'         => is_numeric($user) ? $user : $user->id,
            'type'            => $type,
            'title'           => $title,
            'body'            => $body,
            'data_json'       => $data,
            'notifiable_type' => static::class,
            'notifiable_id'   => $this->id,
            'is_read'         => false
        ]);
    }

    /**
     * Notify multiple users about this model
     *
     * @param  array   $userIds
     * @param  string  $type
     * @param  string  $title
     * @param  string  $body
     * @param  array   $data
     * @return array
     */
    public function notifyUsers(array $userIds, string $type, string $title, string $body, array $data = []): array
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = $this->notifyUser($userId, $type, $title, $body, $data);
        }

        return $notifications;
    }

    /**
     * Notify all users with a specific role
     *
     * @param  string  $role
     * @param  string  $type
     * @param  string  $title
     * @param  string  $body
     * @param  array   $data
     * @return array
     */
    public function notifyRole(string $role, string $type, string $title, string $body, array $data = []): array
    {
        $userIds = \App\Models\User::role($role)->pluck('id')->toArray();

        return $this->notifyUsers($userIds, $type, $title, $body, $data);
    }

    /**
     * Notify all admins
     *
     * @param  string  $type
     * @param  string  $title
     * @param  string  $body
     * @param  array   $data
     * @return array
     */
    public function notifyAdmins(string $type, string $title, string $body, array $data = []): array
    {
        return $this->notifyRole('admin', $type, $title, $body, $data);
    }
}
