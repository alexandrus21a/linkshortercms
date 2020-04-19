<?php

namespace App\Http\Requests;

use Common\Core\BaseFormRequest;
use Common\Core\HttpClient;
use Common\Settings\Settings;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class CrupdateLinkRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        $ruleRequired = $this->getMethod() === 'POST' ? 'required' : '';
        $longUrlRequired = $this->getMethod() === 'POST' ? 'required_without:multiple_urls' : '';
        $except = $this->getMethod() === 'PUT' ? $this->route('link')->id : '';

        $rules = [
            'alias' => "nullable|string|min:5|max:10|unique:links,alias,$except",
            'long_url' => "$longUrlRequired|int|max:250", // TODO: can't use "url" validation on 7.3 until laravel is upgraded
            'domain_id' => "nullable|integer",
            'password' => 'nullable|string|max:250',
            'disabled' => 'nullable|boolean',
            'description' => 'nullable|string|max:250',
            'expires_at' => 'nullable|date_format:Y-m-d H:i:s',
            'pixels.*' => 'required|int',
            'rules' => 'array',
            'rules.*.key' => "$ruleRequired|string|max:250",
            'rules.*.value' => "$ruleRequired|string|max:250",
            'rules.*.type' => "$ruleRequired|string|max:250",
        ];

        if ($this->getMethod() === 'POST') {
            $rules['multiple_urls'] = 'required_without:long_url|array|max:10';
            $rules['multiple_urls.*'] = 'required';  // TODO: can't use "url" validation on 7.3 until laravel is upgraded
        }

        return $rules;
    }

    protected function withValidator(Validator $validator)
    {
        return $validator->after(function(Validator $validator) {
            if ($validator->errors()->has('multiple_urls.*')) {
                $validator->errors()->add('multiple_urls', 'One of the urls is not valid.');
                // base "url" validation failed, can bail
                return;
            }

            if ($multipleUrls = $this->get('multiple_urls')) {
                foreach ($multipleUrls as $url) {
                    $this->runCustomValidations($url, $validator, 'multiple_urls');
                }
            } else if ($longUrl = $this->get('long_url')) {
                $this->runCustomValidations($longUrl, $validator, 'long_url');
            }
        });
    }

    private function runCustomValidations($url, Validator $validator, $errorKey)
    {
        $this->validateAgainstBlacklist($url, $validator, 'keywords', $errorKey);
        $this->validateAgainstBlacklist($url, $validator, 'domains', $errorKey);
        $this->validateAgainstGoogleSafeBrowsing($url, $validator, $errorKey);
        $this->validateAgainstPhishtank($url, $validator, $errorKey);
        $this->validateOriginPolicy($url, $validator, $errorKey);
    }

    private function validateOriginPolicy($url, Validator $validator, $errorKey)
    {
        $type = $this->get('type') ?: $this->route('link.type');
        if ($type !== 'frame' && $type !== 'overlay') return;

        $blacklist = [
            'x-frame-options: deny',
            'x-frame-options: sameorigin',
            'x-frame-options: allow-from',
        ];

        try {
            $headers = get_headers($url);
        } catch (Exception $e) {
            $headers = [];
        }

        $cantIframe = collect($headers)
            ->first(function($header) use($blacklist) {
                $header = strtolower($header);
                return array_search($header, $blacklist) !== false;
            });

        if ($cantIframe) {
            $start = $errorKey === 'long_url' ? 'This URL' : 'One of the urls';
            $validator->errors()->add($errorKey, __("$start does not allow framing. Please select a different type."));
        }
    }

    /**
     * @param string $url
     * @param Validator $validator
     * @param string $listName
     * @param string $errorKey
     */
    private function validateAgainstBlacklist($url, Validator $validator, $listName, $errorKey)
    {
        // key specified blacklist (keyword or domain)
        $list = collect(explode(',', app(Settings::class)->get("links.blacklist.$listName")));
        $list->transform(function($item) {
            return trim($item);
        });

        // check if url matches any blacklist item
        $match = $list->first(function($item) use($url) {
            return str_contains($url, $item);
        });
        if ($match) {
            if ($listName === 'keywords') {
                $validator->errors()->add($errorKey, __('URLs can\'t contain the word ":word".', ['word' => $match]));
            } else {
                $validator->errors()->add($errorKey, __('URLs from ":domain" domain can\'t be shortened.', ['domain' => $match]));
            }
        }
    }

    private function validateAgainstGoogleSafeBrowsing($url, Validator $validator, $errorKey)
    {
        $key = app(Settings::class)->get('links.google_safe_browsing_key');
        if ( ! $key) return;

        $body = [
            'client' => [
                'clientId' => config('app.name'),
                'clientVersion' => config('common.site.version')
            ],
            'threatInfo' => [
                'threatTypes' => ["MALWARE", "SOCIAL_ENGINEERING","THREAT_TYPE_UNSPECIFIED"],
                'platformTypes' => ['ANY_PLATFORM'],
                'threatEntryTypes' => ['URL'],
                'threatEntries' => [
                    ['url' => $url],
                ]
            ]
        ];

        $response = app(HttpClient::class)
            ->post("https://safebrowsing.googleapis.com/v4/threatMatches:find?key=$key", [
                'headers' => [ 'Content-Type' => 'application/json' ],
                'json' => $body
            ]);

        if (Arr::get($response, 'matches.0.threatType')) {
            $start = $errorKey === 'long_url' ? 'This URL' : 'One of the urls';
            $validator->errors()->add($errorKey, __("$start can't be shortened, because it is unsafe."));
        }
    }

    private function validateAgainstPhishtank($url, Validator $validator, $errorKey)
    {
        $key = app(Settings::class)->get('links.phishtank_key');
        if ( ! $key) return;

        $response = app(HttpClient::class)
            ->post('https://checkurl.phishtank.com/checkurl/', [
                'form_params' => [
                    'format' => 'json',
                    'app_key' => $key,
                    'url' => $url
                ]
            ]);

        if (Arr::get($response, 'results.valid') === false) {
            $start = $errorKey === 'long_url' ? 'This URL' : 'One of the urls';
            $validator->errors()->add($errorKey, __("$start can't be shortened, because it is used for phising."));
        }
    }
}
