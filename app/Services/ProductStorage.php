<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ProductStorage
{
    protected $file = 'products.json';

    public function all()
    {
        if (!Storage::exists($this->file)) {
            Storage::put($this->file, json_encode([]));
        }

        return json_decode(Storage::get($this->file), true);
    }

    public function save(array $data)
    {
        Storage::put($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }
}
