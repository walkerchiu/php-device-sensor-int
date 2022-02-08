<?php

namespace WalkerChiu\DeviceSensor\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class DataRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.device-sensor.data'));
    }

    /**
     * @param String  $code
     * @param Array   $data
     * @param Bool    $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(string $code, array $data, $auto_packing = false)
    {
        $instance = $this->instance;

        $data = array_map('trim', $data);
        $repository = $instance->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['device_id']), function ($query) use ($data) {
                                                return $query->where('device_id', $data['device_id']);
                                            })
                                            ->unless(empty($data['register_id']), function ($query) use ($data) {
                                                return $query->where('register_id', $data['register_id']);
                                            })
                                            ->unless(empty($data['card_id']), function ($query) use ($data) {
                                                return $query->where('card_id', $data['card_id']);
                                            })
                                            ->unless(empty($data['identifier']), function ($query) use ($data) {
                                                return $query->where('identifier', $data['identifier']);
                                            })
                                            ->unless(empty($data['log']), function ($query) use ($data) {
                                                return $query->where('log', $data['log']);
                                            })
                                            ->unless(empty($data['trigger_at']), function ($query) use ($data) {
                                                return $query->where('trigger_at', $data['trigger_at']);
                                            });
                                })
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-device-sensor.output_format'), config('wk-device-sensor.pagination.pageName'), config('wk-device-sensor.pagination.perPage'));
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Data          $instance
     * @param Array|String  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
    }
}
