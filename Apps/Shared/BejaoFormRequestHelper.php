<?php

namespace Apps\Shared;

use Bejao\Shared\Infrastructure\Http\FormRequestHelper;

final class BejaoFormRequestHelper extends FormRequestHelper
{

    public function __construct(AbstractBejaoFormRequest $formRequest)

    {
        parent::__construct($formRequest);
    }


}
