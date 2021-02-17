<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class test extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            "key","1a5d42a5s4da",
            "val","87awqwdas2dfs4x2f"
        ];
    }
}
