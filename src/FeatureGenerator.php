<?php


namespace CodeceptionTestsGenerator;

use CodeceptionTestsGenerator\helper\Helper;

class FeatureGenerator
{
    use Helper;

    private static $httpsUrlXpath = '/[\w0-9-]+=/';
    private static $beforeQuery   = '/=+[\w0-9-]+/';
    private static $argXpath      = '/<[\w0-9-]+?>/';
    private static $https         = '/((http)[s]*:\/\/{{[\w]*}})\//';
    private static $apiKey        = '/([\w]*={{[\w]*}}){0,}/';
    private static $apiKeyXpath   = '/^((http)[s]{0,1}:\/\/{{[\w]*}}\/)([\w\d\/\-*]+[?]{0,})[&]{0,}([\w\d\-]*[=]{1}[\w\d\-%*]*[&]{1,})*([\w]*={{[\w]*}}){1,}[&]{0,}([\w\d\-]*[=]{1}[\w\d\-]*[&]{0,})*$/';
    private        $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
        $this->postmanCollectionValidate($collection);
    }

    private function creator()
    {
        foreach ($this->jsonFile()['item'] as $items) {
            $httpsUrl = $items['request']['url']['raw'];
            $method   = $items['request']['method'];

            if (array_key_exists('description', $items['request'])) {
                $description = $items['request']['description'];
            } else {
                $description = null;
            }

            if ($this->featureNameValidator($items['name']) !== 0) {
                if ($this->urlValidator($httpsUrl) !== 0) {

                    $testName            = preg_replace('/(\W)+/', '-', str_replace(' ', '-', $items['name']));
                    $featureName = ucfirst($testName);
                    $httpsUrl            = $items['request']['url']['raw'];

                    $headerKey   = $this->columnBy($items, 'key');
                    $headerValue = $this->columnBy($items, 'value');
                    $pos         = preg_match(self::$apiKeyXpath, $httpsUrl);

                    $headerData      = str_replace('"', '', str_replace(['["', '"]'], '', json_encode($headerKey)));
                    $headerDataArray = explode(',', $headerData . ',' . $headerData);
                    sort($headerDataArray);
                    $header = str_replace('&', '>|', rtrim(str_replace('=', ':<', http_build_query(array_combine($headerDataArray, $headerDataArray)) . '>')));

                    $explode = explode(' ',
                        rtrim(str_replace(['/', '?', '&'], ' ', preg_replace([self::$https, self::$apiKey], '', $httpsUrl)), ' '));

                    $filter = array_filter(array_merge(array(0), $explode));

                    foreach ($filter as $key => $value) {
                        if (preg_match(self::$httpsUrlXpath, $value) === 1) {
                            $beforeValue  = preg_replace(self::$beforeQuery, '', $value);
                            $parameters[] = $beforeValue . ':<' . $beforeValue . '>';
                        } else {
                            $parameters[] = 'path_arg' . $key . ':<path_arg' . $key . '>';
                        }
                    }

                    $params   = $this->implodeData('|', $parameters);
                    $template = $this->feature($featureName, $description, $testName, $params, $header, $method);
                    $string   = '';

                    $queryParameters = str_replace(':', "\t\t", preg_replace(self::$argXpath, '', $this->implodeData('|', $parameters)));
                    $pathParameters  = str_replace(':', "\t\t", preg_replace(self::$httpsUrlXpath, '', $this->implodeData('|', $explode)));

                    if (!empty($items['response'])) {
                        $schema = $items['response'][0]['body'];
                        $this->saveSchema($testName, $schema);
                    }

                    if (!empty($headerKey)) {
                        $headerTable   = str_replace(':', "\t\t", preg_replace(static::$argXpath, '', $this->implodeData('|', $headerKey)));
                        $tableExamples = "\t| " . $queryParameters . " |" . $headerTable . " |" . PHP_EOL . "\t| " . $pathParameters . " |" . $this->implodeData('|',
                                $headerValue) . " |";
                        if ($pos === 1) {
                            unset($template["\tWhen I request \"{$featureName}\""]);
                        }
                        if ($pos !== 1) {
                            unset($template["\tWhen I request secured \"{$featureName}\""]);
                        }
                        if (empty($items['response'])) {
                            unset($template["\tAnd the response matches"]);
                        }
                        if (!empty($description)) {
                            unset($template["As a consumer "]);
                        } else {
                            unset($template[""]);
                        }
                        foreach ($template as $key => $val) {
                            $string .= "$key $val\n";
                        }
                        $string .= $tableExamples;
                    } else {
                        if (empty($headerKey)) {
                            $tableExamples = "\t| " . $queryParameters . " |" . PHP_EOL . "\t| " . $pathParameters . " |";
                            unset($template["\tAnd"]);
                            if ($pos === 1) {
                                unset($template["\tWhen I request \"{$featureName}\""]);
                            }
                            if ($pos !== 1) {
                                unset($template["\tWhen I request secured \"{$featureName}\""]);
                            }
                            if (empty($items['response'])) {
                                unset($template["\tAnd the response matches"]);
                            }
                            if (!empty($description)) {
                                unset($template["As a consumer "]);
                            } else {
                                unset($template[""]);
                            }
                            foreach ($template as $key => $val) {
                                $string .= "$key $val\n";
                            }
                            $string .= $tableExamples;
                        }
                    }

                    $this->saveFeature($testName, $string);
                    unset($parameters);
                } else {
                    $store[] = 'Incorrect request url - ' . $httpsUrl;
                    $this->logging($store);
                }
            } else {
                $store[] = 'Incorrect request name - ' . $items['name'];
                $this->logging($store);
            }
        }
    }
}