<?php

namespace App\Http\Requests;

use Auth;
use Common\Core\BaseFormRequest;
use Illuminate\Validation\Rule;

class CrupdateLinkOverlayRequest extends BaseFormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        $required = $this->getMethod() === 'POST' ? 'required' : '';
        $ignore = $this->getMethod() === 'PUT' ? $this->route('link_overlay')->id : '';
        $userId = $this->route('link_overlay') ? $this->route('link_overlay')->user_id : Auth::id();

        return [
            'name' => [
                $required, 'string', 'min:3',
                Rule::unique('link_overlays')->where('user_id', $userId)->ignore($ignore)
            ],
            'message' => "$required|string|max:250",
            'label' => "nullable|string|max:250",
            'btn_link' => "nullable|string|max:250",
            'btn_text' => "nullable|string|max:250",
            'colors' => "$required|array",
        ];
    }
}
