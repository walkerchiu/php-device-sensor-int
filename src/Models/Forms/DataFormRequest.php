<?php

namespace WalkerChiu\DeviceSensor\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use WalkerChiu\Core\Models\Forms\FormRequest;

class DataFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (string) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'device_id'   => trans('php-device-sensor::card.device_id'),
            'register_id' => trans('php-device-sensor::card.register_id'),
            'value'       => trans('php-device-sensor::card.value'),
            'trigger_at'  => trans('php-device-sensor::card.trigger_at')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'device_id'   => ['required','integer','min:1','exists:'.config('wk-core.table.device-sensor.devices').',id'],
            'register_id' => ['nullable','integer','min:1','exists:'.config('wk-core.table.device-sensor.devices_registers').',id'],
            'value'       => 'required|string',
            'trigger_at'  => 'required|date|date_format:Y-m-d H:i:s'
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.device-sensor.data').',id']]);
        } elseif ($request->isMethod('post')) {
            $rules = array_merge($rules, ['id' => ['nullable','string','exists:'.config('wk-core.table.device-sensor.data').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.string'              => trans('php-core::validation.string'),
            'id.exists'              => trans('php-core::validation.exists'),
            'device_id.required'     => trans('php-core::validation.required'),
            'device_id.integer'      => trans('php-core::validation.integer'),
            'device_id.min'          => trans('php-core::validation.min'),
            'device_id.exists'       => trans('php-core::validation.exists'),
            'register_id.required'   => trans('php-core::validation.required'),
            'register_id.integer'    => trans('php-core::validation.integer'),
            'register_id.min'        => trans('php-core::validation.min'),
            'register_id.exists'     => trans('php-core::validation.exists'),
            'value.required'         => trans('php-core::validation.required'),
            'value.string'           => trans('php-core::validation.string'),
            'trigger_at.required'    => trans('php-core::validation.required'),
            'trigger_at.date'        => trans('php-core::validation.date'),
            'trigger_at.date_format' => trans('php-core::validation.date_format')
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
