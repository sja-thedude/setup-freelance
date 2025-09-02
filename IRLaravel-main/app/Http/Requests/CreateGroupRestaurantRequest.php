<?php
namespace App\Http\Requests;

use App\Models\GroupRestaurant;
use InfyOm\Generator\Request\APIRequest;

class CreateGroupRestaurantRequest extends APIRequest
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

    public function rules()
    {
        return array_merge(
            GroupRestaurant::$rule,
            [
                'name' => 'required|max:255'
            ]
        );
    }
}