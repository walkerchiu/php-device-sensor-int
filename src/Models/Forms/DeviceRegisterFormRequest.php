<?php

namespace WalkerChiu\DeviceSensor\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class DeviceRegisterFormRequest extends FormRequest
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
            $data['id'] = (int) $request->id;
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
            'device_id'   => trans('php-device-sensor::device.device_id'),
            'serial'      => trans('php-device-sensor::device.serial'),
            'identifier'  => trans('php-device-sensor::device.identifier'),
            'mean'        => trans('php-device-sensor::device.mean'),
            'data_type'   => trans('php-device-sensor::device.data_type'),
            'order'       => trans('php-device-sensor::device.order'),
            'is_enabled'  => trans('php-device-sensor::device.is_enabled'),

            'name'        => trans('php-device-sensor::device.name'),
            'description' => trans('php-device-sensor::device.description')
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
            'serial'      => '',
            'identifier'  => 'required|string|max:255',
            'mean'        => 'required|string',
            'data_type'   => ['required', Rule::in(config('wk-core.class.core.dataType')::getCodes())],
            'order'       => 'nullable|numeric|min:0',
            'is_enabled'  => 'boolean',

            'name'        => 'required|string|max:255',
            'description' => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:'.config('wk-core.table.device-sensor.devices_registers').',id']]);
        } elseif ($request->isMethod('post')) {
            $rules = array_merge($rules, ['id' => ['nullable','integer','min:1','exists:'.config('wk-core.table.device-sensor.devices_registers').',id']]);
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
            'id.integer'          => trans('php-core::validation.integer'),
            'id.min'              => trans('php-core::validation.min'),
            'id.exists'           => trans('php-core::validation.exists'),
            'device_id.required'  => trans('php-core::validation.required'),
            'device_id.integer'   => trans('php-core::validation.integer'),
            'device_id.min'       => trans('php-core::validation.min'),
            'device_id.exists'    => trans('php-core::validation.exists'),
            'identifier.required' => trans('php-core::validation.required'),
            'identifier.string'   => trans('php-core::validation.string'),
            'identifier.max'      => trans('php-core::validation.max'),
            'mean.required'       => trans('php-core::validation.required'),
            'mean.string'         => trans('php-core::validation.string'),
            'data_type.required'  => trans('php-core::validation.required'),
            'data_type.in'        => trans('php-core::validation.in'),
            'order.numeric'       => trans('php-core::validation.numeric'),
            'order.min'           => trans('php-core::validation.min'),
            'is_enabled.boolean'  => trans('php-core::validation.boolean'),

            'name.required'       => trans('php-core::validation.required'),
            'name.string'         => trans('php-core::validation.string'),
            'name.max'            => trans('php-core::validation.max')
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
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if (isset($data['identifier'])) {
                $result = config('wk-core.class.device-sensor.deviceRegister')::where('identifier', $data['identifier'])
                                ->when(isset($data['device_id']), function ($query) use ($data) {
                                    return $query->where('device_id', $data['device_id']);
                                  })
                                ->when(isset($data['id']), function ($query) use ($data) {
                                    return $query->where('id', '<>', $data['id']);
                                  })
                                ->exists();
                if ($result)
                    $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-device-sensor::deviceRegister.identifier')]));
            }
        });
    }
}
