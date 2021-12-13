# Demo App for testing SDK and endpoints

This demo app will provide a command that will help users test their integrations with our SDK (SMS.to SDK)[https://github.com/intergo/sms.to-laravel-lumen]

## Setup

- Clone this repo
- Setup the environment variables

```dotenv
### SMSTO Credentials
SMSTO_AUTH_MODE=api_key
SMSTO_API_KEY=sjkdhfgsdfsdf

## Comment above if using oauth
SMSTO_CLIENT_ID=sdfdsfsdfsdfsd
SMSTO_CLIENT_SECRET=aksdjashkdjashdkjashdkjhasgdjk

### SMSTO Demo Data
SMSTO_TEST_SMS=true
SMSTO_TEST_CONTACT=true
SMSTO_TEST_SHORTLINK=true
SMSTO_TEST_TEAM=true
SMSTO_TEST_NUMBER_LOOKUP=true
SMSTO_INVITE_EMAIL="test-smsto@sms.to"
SMSTO_SINGLE_PHONE="+RandomNumber"
SMSTO_MULTIPLE_PHONE="+RandomNumber,+RandomNumber1"

```

**Run command**

> php artisan sms-to:test-endpoints

The command will execute all endpoints and determine if the endpoints are working
