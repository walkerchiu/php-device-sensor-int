<?php

namespace WalkerChiu\DeviceSensor\Models\Forms;

use WalkerChiu\Core\Models\Forms\FormRequest;

class DeviceDeleteFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        return [
            'id' => ['required','integer','min:1','exists:'.config('wk-core.table.device-sensor.devices').',id']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required' => trans('php-core::validation.required'),
            'id.integer'  => trans('php-core::validation.integer'),
            'id.min'      => trans('php-core::validation.min'),
            'id.exists'   => trans('php-core::validation.exists')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
    }
}
