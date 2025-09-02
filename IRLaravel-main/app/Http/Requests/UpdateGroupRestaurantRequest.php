<?php
namespace App\Http\Requests;

use App\Models\GroupRestaurant;
use InfyOm\Generator\Request\APIRequest;

class UpdateGroupRestaurantRequest extends APIRequest
{
    public function rules()
    {
        return array_merge(
            GroupRestaurant::$rule,
            [
                'name' => 'required|max:255'
            ]
        );
    }

    public function authorize()
    {
        return true;
    }
}