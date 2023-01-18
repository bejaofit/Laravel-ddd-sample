<?php

namespace Bejao\Shared\Infrastructure\Http;

use Apps\Shared\AbstractBejaoFormRequest;
use Bejao\Shared\Domain\Enums\LanguageEnum;
use Bejao\Shared\Domain\Exceptions\InvalidRequestException;
use Bejao\Shared\Framework\MixedHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
use RuntimeException;

class FormRequestHelper
{
    protected AbstractBejaoFormRequest $formRequest;

    public function __construct(AbstractBejaoFormRequest $formRequest)
    {
        $this->formRequest = $formRequest;
    }


    public function getLocaleLanguage(): LanguageEnum
    {
        $locale = Lang::locale();
        return LanguageEnum::fromValue($locale);
    }

    public function getBoolean(string $attribute, ?bool $default = null): bool
    {
        $value = $this->formRequest->input($attribute, $default);
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int)$value === 1;
        }
        if (is_string($value)) {
            return $value === '1' || $value === 'true' || $value === 'on' || $value === 'yes';
        }
        if ($value === null) {
            return false;
        }
        throw new RuntimeException('Invalid Value ' . $attribute . ' ' . $value);
    }

    public function getStringOrNull(string $attribute): ?string
    {
        $value = $this->formRequest->input($attribute);
        if ($value === null) {
            return null;
        }
        return $this->getString($attribute);
    }

    public function getIntOrNull(string $attribute): ?int
    {
        $value = $this->formRequest->input($attribute);
        if ($value === null) {
            return null;
        }
        return $this->getInt($attribute);
    }

    public function getFloatOrNull(string $attribute): ?float
    {
        $value = $this->formRequest->input($attribute);
        if ($value === null) {
            return null;
        }
        return $this->getFloat($attribute);
    }


    public function getDateFromUnixTimeStamp(string $attribute, ?int $default = null): Carbon
    {
        $value = $this->formRequest->input($attribute, $default);
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }
        if (is_string($value)) {
            return Carbon::createFromTimestamp((int)$value);
        }
        throw new RuntimeException('Invalid Value ' . $attribute . ' ' . $value);
    }

    public function getDateTimeStampFromString(string $attribute, ?int $default = null): int
    {
        $value = $this->formRequest->input($attribute, $default);

        if (is_string($value)) {
            return (new Carbon($value))->getTimestamp();
        }
        throw new RuntimeException('Invalid Value ' . $attribute . ' ' . $value);
    }

    public function getTimeStampOrNull(string $attribute, ?int $default = null): ?int
    {
        $value = $this->formRequest->input($attribute, $default);

        if ($value === '' || $value === null) {
            return null;
        }

        if (is_string($value)) {
            return (new Carbon($value))->getTimestamp();
        }
        throw new RuntimeException('Invalid Value ' . $attribute . ' ' . $value);
    }

    public function getInt(string $attribute, ?int $default = null): int
    {
        $value = $this->formRequest->input($attribute, $default);
        if (is_numeric($value)) {
            return (int)$value;
        }
        if (is_string($value)) {
            return (int)$value;
        }
        throw new RuntimeException('Invalid Value ' . $attribute . ' ' . $value);
    }


    public function getFloat(string $attribute, ?int $default = null): float
    {
        $value = $this->formRequest->input($attribute, $default);
        if (is_numeric($value)) {
            return (float)$value;
        }
        if (is_string($value)) {
            return (float)$value;
        }
        throw new RuntimeException('Invalid Value ' . $attribute . ' ' . $value);
    }

    public function getString(string $attribute, ?string $default = null): string
    {
        $value = $this->formRequest->input($attribute, $default);
        if (is_string($value)) {
            return $value;
        }
        throw new RuntimeException('Invalid Value ' . $attribute . ' ' . $value);
    }

    public function getRequiredString(string $attribute, ?string $default = null): string
    {
        $value = $this->formRequest->input($attribute, $default);
        if (null === $value) {
            throw InvalidRequestException::fromMessage('The attribute ' . $attribute . ' is required.');
        }

        return $this->getString($attribute, $default);
    }


    /**
     * @param string $string
     * @return string
     */
    public function routeString(string $string): string
    {
        $value = $this->formRequest->route($string);
        if (is_string($value)) {
            return $value;
        }
        throw new RuntimeException('Wrong route value ' . json_encode($value));
    }

    public function routeUUID(string $value): string
    {
        $value = $this->routeString($value);
        return trim(str_replace(['/', '.', '\\'], '', $value));
    }

    public function queryString(string $string): string
    {
        $value = $this->formRequest->query($string);
        if (is_string($value)) {
            return $value;
        }
        throw new RuntimeException('Wrong query value ' . json_encode($value));
    }

    public function queryStringOrNull(string $string): ?string
    {
        $value = $this->formRequest->query($string);

        if (null === $value) {
            return null;
        }
        return $this->queryString($string);
    }


    /**
     * @param Request $request
     * @return ?int
     */
    public function getPageSize(Request $request): ?int
    {
        $pageSize = $request->input('perPage');
        if (is_numeric($pageSize) || is_string($pageSize)) {
            return (int)$pageSize;
        }
        return null;
    }

    /**
     * @return array<string,string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @param string $attribute
     * @param array<mixed>|null $default
     * @return array
     * @phpstan-ignore-next-line
     */
    public function getArray(string $attribute, ?array $default = null): array
    {
        $value = $this->formRequest->input($attribute, $default);

        if ($value === null) {
            return [];
        }
        if (is_array($value)) {
            return $value;
        }
        throw new RuntimeException('Invalid Value ' . $attribute . ' ' . print_r($value, true));
    }

    public function getHeaderString(string $item): string
    {
        $value = $this->formRequest->header($item);
        if (is_string($value)) {
            return $value;
        }
        throw new RuntimeException('Wrong header value ' . print_r($value, true));
    }


    public function routeInt(string $string): int
    {
        /** @var string|object|null|int $value */
        $value = $this->formRequest->route($string);
        if (is_string($value)) {
            return (int)$value;
        }
        if (is_numeric($value)) {
            return $value;
        }

        throw new RuntimeException('Wrong route value ' . json_encode($value));
    }

    /**
     * @param string $attribute
     * @return int[]
     */
    public function getIntArray(string $attribute): array
    {
        $array = $this->getArray($attribute);

        return array_map(static function ($item) {
            return MixedHelper::getInt($item);
        }, $array);
    }

    /**
     * @param string $attribute
     * @return string[]
     */
    public function getStringArray(string $attribute): array
    {
        $array = $this->getArray($attribute);
        return array_map(static function ($item) {
            return MixedHelper::getString($item);
        }, $array);
    }

    public function getBooleanOrNull(string $attribute): ?bool
    {
        $value = $this->formRequest->input($attribute);
        if (null === $value) {
            return null;
        }
        return $this->getBoolean($attribute);
    }
}
