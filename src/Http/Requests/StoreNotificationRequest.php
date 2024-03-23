<?php

namespace IICN\Notification\Http\Requests;

use Carbon\Carbon;
use IICN\Subscription\CouponConditionInterface;
use IICN\Subscription\Models\SubscriptionCoupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::guard(config('notification.guard'))->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:150',
            'content' => 'required|string|max:300',
            'image_url' => 'nullable|string',
            'link' => 'required_if:action,link|string',
            'platform' => 'nullable|string|in:android,ios',
            'version' => 'nullable|string|present_if:platform,android,ios',
            'languages' => 'nullable|array',
            'languages.*' => 'required|string',
            'destinations' => 'nullable|array',
            'destinations.*' => 'required|string',
            'action' => 'nullable|string',
            'name' => 'nullable|string',
            'ayeh' => 'nullable|string',
            'special_users' => 'required|boolean',
            'users' => 'nullable|array',
            'users.*' => 'required|string|email|distinct',
            'received_time' => 'required|boolean',
            'send_date' => 'string|date_format:Y-m-d H:i',
            'test' => 'boolean|nullable',
        ];
    }
}
