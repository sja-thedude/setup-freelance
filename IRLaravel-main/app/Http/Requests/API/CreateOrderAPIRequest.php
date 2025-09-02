<?php

namespace App\Http\Requests\API;

use App\Models\Order;
use Illuminate\Validation\Rule;

class CreateOrderAPIRequest extends MyAPIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(Order::$rules_raw, [
            'workspace_id' => [
                'required',
                'integer',
                // Validate exist workspace
                Rule::exists('workspaces', 'id')->where(function ($query) {
                    /** @var  \Illuminate\Database\Eloquent\Builder $query */
                    $query
                        // Only get active record
                        ->where('active', true)
                        // withoutTrashed()
                        ->whereNull('deleted_at');
                }),
            ],
        ]);
    }

    /**
     * Prepare the data for validation.
     *
     * @overwrite
     * @return void
     */
    protected function prepareForValidation()
    {
        $userId = null;

        if (!empty($this->user())) {
            $userId = $this->user()->getKey();
        }

        $metaData = null;

        if (!$this->isFromBrowser($this)) {
            $metaData = json_encode($this->all(), JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES);
        }

        // Raw data
        $this->merge($this->json()->all());

        $this->merge([
            // User logged in
            'user_id' => $userId,
            // Meta data
            'meta_data' => $metaData,
        ]);
    }

}
