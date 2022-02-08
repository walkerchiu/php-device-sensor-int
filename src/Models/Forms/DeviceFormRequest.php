<?php

namespace WalkerChiu\DeviceSensor\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use WalkerChiu\Core\Models\Forms\FormRequest;

class DeviceFormRequest extends FormRequest
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
            'serial'        => trans('php-device-sensor::device.serial'),
            'identifier'    => trans('php-device-sensor::device.identifier'),
            'order'         => trans('php-device-sensor::device.order'),
            'is_enabled'    => trans('php-device-sensor::device.is_enabled'),

            'slave_id'      => trans('php-device-sensor::device.slave_id'),
            'ip'            => trans('php-device-sensor::device.ip'),
            'port'          => trans('php-device-sensor::device.port'),
            'scan_interval' => trans('php-device-sensor::device.scan_interval'),
            'sync_at'       => trans('php-device-sensor::device.sync_at'),
            'is_multiplex'  => trans('php-device-sensor::device.is_multiplex'),

            'name'          => trans('php-device-sensor::device.name'),
            'description'   => trans('php-device-sensor::device.description'),
            'location'      => trans('php-device-sensor::device.location')
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
            'serial'        => '',
            'identifier'    => 'required|string|max:255',
            'order'         => 'nullable|numeric|min:0',
            'is_enabled'    => 'boolean',

            'slave_id'      => 'required|integer|between:1,255',
            "ip"            => 'required|ip',
            'port'          => 'required|integer|between:1,65535',
            'scan_interval' => 'nullable|integer|between:1,65535',
            'sync_at'       => 'string|max:6',
            'is_multiplex'  => 'boolean',

            'name'          => 'required|string|max:255',
            'description'   => '',
            'location'      => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:'.config('wk-core.table.device-sensor.devices').',id']]);
        } elseif ($request->isMethod('post')) {
            $rules = array_merge($rules, ['id' => ['nullable','integer','min:1','exists:'.config('wk-core.table.device-sensor.devices').',id']]);
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
            'id.integer'            => trans('php-core::validation.integer'),
            'id.min'                => trans('php-core::validation.min'),
            'id.exists'             => trans('php-core::validation.exists'),
            'identifier.required'   => trans('php-core::validation.required'),
            'identifier.string'     => trans('php-core::validation.required'),
            'identifier.max'        => trans('php-core::validation.max'),
            'order.numeric'         => trans('php-core::validation.numeric'),
            'order.min'             => trans('php-core::validation.min'),
            'is_enabled.boolean'    => trans('php-core::validation.boolean'),

            'slave_id.required'     => trans('php-core::validation.required'),
            'slave_id.integer'      => trans('php-core::validation.integer'),
            'slave_id.between'      => trans('php-core::validation.between'),
            'ip.required'           => trans('php-core::validation.required'),
            'ip.ip'                 => trans('php-core::validation.ip'),
            'port.required'         => trans('php-core::validation.required'),
            'port.integer'          => trans('php-core::validation.integer'),
            'port.between'          => trans('php-core::validation.between'),
            'scan_interval.integer' => trans('php-core::validation.integer'),
            'scan_interval.between' => trans('php-core::validation.between'),
            'sync_at.string'        => trans('php-core::validation.string'),
            'sync_at.max'           => trans('php-core::validation.max'),
            'is_multiplex.boolean'  => trans('php-core::validation.boolean'),

            'name.required'         => trans('php-core::validation.required'),
            'name.string'           => trans('php-core::validation.string'),
            'name.max'              => trans('php-core::validation.max')
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
                $result = config('wk-core.class.device-sensor.device')::where('identifier', $data['identifier'])
                                ->when(isset($data['id']), function ($query) use ($data) {
                                    return $query->where('id', '<>', $data['id']);
                                  })
                                ->exists();
                if ($result)
                    $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-device-sensor::device.identifier')]));
            }
        });
    }
}
