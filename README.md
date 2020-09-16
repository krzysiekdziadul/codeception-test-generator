# Codeception test generator #

Package generates tests base on [ Postman ](https://www.postman.com/collection/) collection. Collection must be in v2.1.0 and passed [validation](#Sample-valid-collection-object).
Generator supports GET, POST, PUT, DELETE method's. Script will create AcceptanceTester.php file contain step's definition. Tests saved in feature files and based on [Gherkin](https://docs.behat.org/en/v2.5/guides/1.gherkin.html) syntax.
####
**Sample valid request** 
```
https://{{host}}/v1/weather/country/GB?city=London&date=23–03-2020&api_key={{api_key}}
```
```
https://{{host}}/v1/weather/country/GB?city=London
```
```
https://{{host}}/v1/weather/all-country-list
```
```
http://{{host}}/v1/weather/all-country-list
```
#### Sample valid collection object

```
		{
			"name": "Weather in London",
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
			"response": []
		}
```
**Pay atention for naming conventionse**  
Please make sure, don't use special characters when named collection's.   
#####
**Sample valid name**
```some name``` ```some_name``` ```some-name some``` ```some-name-1```  
###
**Validation log**  
Available in ```tests/_output/collection-log.json```
####
- - -
### Four steps to run ###
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
    "dziadul/codeception-test-generator": "dev-master"
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
HOST=example.com/
API_KEY=1234567890
```
### 4. Run test generator

1. paste your's postman collection in to ```tests/_data/collection``` folder
2. run ```php vendor/bin/codecept generate:feature```
3. run ```php vendor/bin/codecept run acceptance```  
###
- - -
### Feature example ##
**Example GET method without api access key** 
```
https://{{host}}/v1/weather/country/GB?city=London    
```
```
Feature: London weather.  
  As a consumer of the API, I want an API that provides with data about London weather.
  So that I can use this for my application.

  Scenario Outline: London weather.  
    Given the parameters "path_arg1:<path_arg1>| path_arg2:<path_arg2> | path_arg3:<path_arg3> | city:<city>"  
    And  the header "Accept:<Accept>"  
    When I request url by "https" protocol and "GET" method 
    Then I see response status code is "200"  

    Examples:
      | path_arg1 | path_arg2 | path_arg3  | city       |  Accept           |
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
    Given the parameters "path_arg1:<path_arg1>| path_arg2:<path_arg2> | path_arg3:<path_arg3> | city:<city> | date:<date>"  
    And  the header "Accept:<Accept>"  
    When I request secured url by "https" protocol and "POST" method
    Then I see response status code is "200"  

    Examples:
      | path_arg1 | path_arg2 | path_arg3  | city       | date       |  Accept           |
      | v1        | weather   | GB         | London     | 23-03-2020 |  application/xml  |
```
**Feel free to change ```path_arg*``` name**  
```    
Given the parameters "api_version:<api_version>| api_name:<api_name> | country:<country> | city:<city> | date:<date>"  
```  
```

    Examples:
      | api_version | api_name | country  | city       | date       |  Accept           |
      | v1          | weather  | GB       | London     | 23-03-2020 |  application/xml  |
```
