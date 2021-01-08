# Codeception test generator #

Module generate tests base on [ Postman ](https://www.postman.com/collection/) collection and is dedicated for Codeception framework.
Generator supports GET, POST, PUT, DELETE, PATCH method's. Script overwrite AcceptanceTester class with a generated step's definition. Tests save in feature files and are based on [Gherkin](https://docs.behat.org/en/v2.5/guides/1.gherkin.html) syntax.
####
## How to start ? ##
1.Prepare collection follow by Postman [collection guide ](https://learning.postman.com/docs/sending-requests/intro-to-collections/)     
2.Install Codeception   
3.Install codeception-test-generator

## Notice ##
Export Postman collection as 2.1.0 version.  
For create step with json schema matches, please add `Examples` in collection and fill witch response schema.[Example](#Sample-valid-collection-object)        
**Use valid request url** 
```
https://{{host}}/v1/weather/country/GB?city=London&date=23–03-2020&key={{key}}

https://{{host}}/v1/weather/country/GB?city=London

https://{{host}}/v1/weather/all-country-list
```
**Pay atention for naming conventionse**  
Please make sure, don't use special characters when named collection's.   
#####
**Sample valid name**
```some name``` ```some_name``` ```some-name some``` ```some-name-1```  
###
**Validation log** available: ```tests/_output/collection-log.json```
####
- - -
## Four steps to run ##
1.[Install Codeception framework.](#1-Preinstaled-and-configured-codeception-framework)   
2.[Install codeception-test-generator package.](#2-Install-codeception-test-generator)   
3.[Setup Codecepion.](#3-Setup-codeception)   
4.[Run test generator.](#4-Run-test-generator)   

### 1. Preinstaled and configured codeception framework  
Please follow by official [Codeception](https://codeception.com/quickstart) framework guide.
### Required packages ###
```
"codeception/module-rest": "^1.2"
"vlucas/phpdotenv": "^3.3"
```
### 2. Install codeception-test-generator
**composer.json**
```
  "require-dev": {
    "dziadul/codeception-test-generator": "~1.0"
  },
  "autoload": {
    "psr-4": {
      "CodeceptionTestsGenerator\\": "src/"
    }
  },
  "scripts": {
    "post-update-cmd": [
      "CodeceptionTestsGenerator\\PostCmd::postUpdate"
    ]
  }
```
run ```composer update```

### 3. Setup codeception
**codeception.yml**
```
bootstrap: _bootstrap.php
extensions:
    commands:
        - Tests\_support\Command\TestCreateCommand
params:
    - .env
```
**acceptance.suite.yml**
```
actor: AcceptanceTester
modules:
    enabled:
        - REST:
             depends: PhpBrowser
        - \Helper\Acceptance
    step_decorators: ~  
```
**create ```.env``` file, locate it in main project folder and setup credentials**
```
HOST=https://example.com/
KEY=API_KEY=1234567890
```
### 4. Run test generator

1. paste your's postman collection in to ```tests/_data/collection``` folder
2. run ```php vendor/bin/codecept generate:feature-test```
3. run ```php vendor/bin/codecept run acceptance```  
###
- - -  
## Feature example #
**Example GET method without api access key** 
```
https://{{host}}/v1/weather/country/GB?city=London    
```
```
Feature: London weather.  
  As a consumer of the API, I want an API that provides with data about London weather.
  So that I can use this for my application.

  Scenario Outline: London weather.  
    Given the parameters "path_arg1:<value1>| path_arg2:<value2> | path_arg3:<value3> | city:<city>"  
    And  the header "Accept:<Accept>"  
    When I request url created from params by "GET" method 
    Then I see response status code is "200"  

    Examples:
      | value1    | value2    | value3     | city       |  Accept           |
      | v1        | weather   | GB         | London     |  application/json |
```
**Example GET method with json schema matches** 
```
https://{{host}}/v1/weather/country/GB?city=London    
```
```
Feature: London weather.  
  As a consumer of the API, I want an API that provides with data about London weather.
  So that I can use this for my application.

  Scenario Outline: London weather.  
    Given the parameters "path_arg1:<value1>| path_arg2:<value2> | path_arg3:<value3> | city:<city>"  
    And  the header "Accept:<Accept>"  
    When I request url created from params by "GET" method 
    Then I see response status code is "200"  
    And the response matches "London-weather" json schema

    Examples:
      | value1    | value2    | value3     | city       |  Accept           |
      | v1        | weather   | GB         | London     |  application/json |
```
**Example POST method with api access key** 
```
https://{{host}}/v1/weather/country/GB?city=London&date=23–03-2020&key={{key}}    
```
```
Feature: London weather.  
  As a consumer of the API, I want an API that provides with data about London weather.
  So that I can use this for my application.

  Scenario Outline: London weather.  
    Given the parameters "path_arg1:<value1>| path_arg2:<value2> | path_arg3:<value3> | city:<city> | date:<date>"  
    And  the header "Accept:<Accept>"  
    When I request secured url created from params by "POST" method
    Then I see response status code is "200"  

    Examples:
      | value1    | value2    | value3     | city       | date       |  Accept           |
      | v1        | weather   | GB         | London     | 23-03-2020 |  application/xml  |
```
**Feel free to change ```value*``` name**  
## Upgrade Feature by validation example #

You can also add response validation rules by [specifying postman collection examples](https://learning.postman.com/docs/sending-requests/examples/) each feature.
####
**How it is work ?** 

1. Open saved request from collection
2. Press ```Examples``` on right corner Postman tool
3. Press ```Add Examples```
4. On Body section paste requests validation roles eg.

```
{
      "url": [
        "https://{{host}}/v1/weatherxx/country/GB?city=London&key={{key}}",
        "https://{{host}}/v1/weather/country/xx?city=London&key={{key}}",
        "https://{{host}}/v1/weather/country/GB?city=yyyy&key={{key}}"
      ]
}
```
5. Name it eg ```validation 404``` and save

**Pay atention for naming conventionse**  
Please make sure, use ```validation``` + empty space + ```code```.

**Example GET method with json schema matches and validation with api access key**

```
https://{{host}}/v1/weather/country/GB?city=London&key={{key}}    
```
```
Feature: London weather.  
  As a consumer of the API, I want an API that provides with data about London weather.
  So that I can use this for my application.

  Scenario Outline: London weather.  
    Given the parameters "path_arg1:<value1>| path_arg2:<value2> | path_arg3:<value3> | city:<city>"  
    And  the header "Accept:<Accept>"  
    When I request url created from params by "GET" method 
    Then I see response status code is "200"  
    And the response matches "London-weather" json schema

    Examples:
      | value1  | value2   | value3  | city       |  Accept           |
      | v1      | weather  | GB      | London     |  application/json |

  Scenario Outline: London weather - 404 validation.  
    Given the parameters "path_arg1:<value1>| path_arg2:<value2> | path_arg3:<value3> | city:<city>"  
    And  the header "Accept:<Accept>"  
    When I request url created from params by "GET" method 
    Then I see response status code is "404"  

    Examples:
      | value1    | value2    | value3     | city       |  Accept           |
      | v1        | weatherxx | GB         | London     |  application/json |
      | v1        | weather   | xx         | xx         |  application/json |
      | v1        | weather   | GB         | yyyy       |  application/json |
```


**Feel free to change ```value*``` name**  
```    
Given the parameters "path_arg1:<api_version>| path_arg2:<api_name> | country:<country> | city:<city> | date:<date>"  
```  
```

    Examples:
      | api_version | api_name | country  | city       | date       |  Accept           |
      | v1          | weather  | GB       | London     | 23-03-2020 |  application/xml  |
```
#### Sample valid collection object

```
		{
          "name": "London weather",
          "request": {
            "method": "GET",
            "header": [
              {
                "key": "Accept",
                "value": "application/json"
              }
            ],
            "url": {
              "raw": "https://{{host}}/v1/weather/country/GB?city=London&date=23–03-2020",
              "protocol": "https",
              "host": [
                "{{host}}"
              ],
              "path": [
                "v1",
                "weather",
                "country",
                "GB"
              ],
              "query": [
                {
                  "key": "city",
                  "value": "London"
                },
                {
                  "key": "date",
                  "value": "23–03-2020"
                }
              ]
            }
          },
          "response": [
            {
              "$schema": "http://json-schema.org/draft-04/schema#",
              "type": "object",
              "properties": {
                "weather": {
                  "type": "array",
                  "items": [
                    {
                      "type": "object",
                      "properties": {
                        "id": {
                          "type": "integer"
                        },
                        "main": {
                          "type": "string"
                        },
                        "description": {
                          "type": "string"
                        },
                        "icon": {
                          "type": "string"
                        }
                      }
                    }
                  ]
                }
              }
            }
          ]
        }
```