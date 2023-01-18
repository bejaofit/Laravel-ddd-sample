<?php

namespace Apps\Shared;

use Bejao\Shared\Infrastructure\Http\FormRequestHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class AbstractBejaoFormRequest extends FormRequest
{


    protected ?BejaoFormRequestHelper $helper = null;

    public function getHelper(): BejaoFormRequestHelper
    {
        if ($this->helper === null) {
            /** @var BejaoFormRequestHelper $helper */
            $helper = App::makeWith(BejaoFormRequestHelper::class,['formRequest' => $this]);
            $this->helper = $helper;
        }
        return $this->helper;
    }


    /**
     * @return array<string,mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
