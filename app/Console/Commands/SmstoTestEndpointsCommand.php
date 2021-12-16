<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intergo\SmsTo\Facades\SmsToContact;
use Intergo\SmsTo\Facades\SmsToNumberLookup;
use Intergo\SmsTo\Facades\SmsToShortlink;
use Intergo\SmsTo\Facades\SmsToSms;
use Intergo\SmsTo\Facades\SmsToTeam;
use Intergo\SmsTo\Module\Sms\Message\CampaignMessage;
use Intergo\SmsTo\Module\Sms\Message\PersonalizedMessage;
use Intergo\SmsTo\Module\Sms\Message\SingleMessage;

class SmstoTestEndpointsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms-to:test-endpoints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to test all endpoints provide by SMS.to';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Running all tests');
        if(env('SMSTO_TEST_SMS')) {
            $this->handleSms();
        }
        if(env('SMSTO_TEST_TEAM')) {
            $this->handleTeam();
        }
        if(env('SMSTO_TEST_CONTACT')) {
            $this->handleContact();
        }
        if(env('SMSTO_TEST_SHORTLINK')) {
            $this->handleShortlink();
        }
        if(env('SMSTO_TEST_NUMBER_LOOKUP')) {
            $this->handleNumberLookup();
        }
    }

    private function handleSms()
    {
        $response = SmsToSms::getCampaigns();
        $this->respond($response, 'Campaign List', 'current_page');

        $response = SmsToSms::getMessages();
        $this->respond($response, 'Message List', 'current_page');

        $this->normalSms();
    }

    private function normalSms()
    {
        $message = new SingleMessage();
        $message->setTo(getenv('SMSTO_SINGLE_PHONE'))->setMessage('This is test from package');
        $response = SmsToSms::estimate($message);
        $this->respond($response, 'Estimate single message', 'estimated_cost');

        $message = new CampaignMessage();
        $message->setTo(explode(',', getenv('SMSTO_MULTIPLE_PHONE')))->setMessage('This is test from package');
        $response = SmsToSms::estimate($message);
        $this->respond($response, 'Estimate campaign message', 'estimated_cost');

        $message = new PersonalizedMessage();
        $message->setMessages([['message' => 'This is test from package', 'to' => getenv('SMSTO_SINGLE_PHONE')]]);
        $response = SmsToSms::estimate($message);
        $this->respond($response, 'Estimate personalized message', 'estimated_cost');

        $message = new SingleMessage();
        $message->setTo(getenv('SMSTO_SINGLE_PHONE'))->setMessage('This is test from package');
        $response = SmsToSms::send($message);
        $this->respond($response, 'Send single message', 'success', true);

        $message = new CampaignMessage();
        $message->setTo(explode(',', getenv('SMSTO_MULTIPLE_PHONE')))->setMessage('This is test from package');
        $response = SmsToSms::send($message);
        $this->respond($response, 'Send campaign message', 'success', true);

        $message = new PersonalizedMessage();
        $message->setMessages([['message' => 'This is test from package', 'to' => getenv('SMSTO_SINGLE_PHONE')]]);
        $response = SmsToSms::send($message);
        $this->respond($response, 'Send personalized message', 'success', true);
    }

    private function handleShortlink()
    {
        $response = SmsToShortlink::all();
        $this->respond($response, 'Shortlink List', 'data');
        $name = uniqid('shortlink-');
        $url = 'https://sms.to';
        $response = SmsToShortlink::create($name, $url);
        $this->respond($response, 'New Shortlink', 'data');
        $id = $response['data']['id'];
        $response = SmsToShortlink::getByID($id);
        $this->respond($response, 'Shortlink Report', 'meta');
        $response = SmsToShortlink::deleteByID($id);
        $this->respond($response, 'Delete Shortlink', 'message');
    }

    private function handleTeam()
    {
        $response = SmsToTeam::allMembers();
        $this->respond($response, 'Team Member List', 'success', true);

        $response = SmsToTeam::allInvitations();
        $this->respond($response, 'Team Invitation List', 'success', true);

        $response = SmsToTeam::generateMember();
        $this->respond($response, 'Generate new member', 'success', true);
        $id = $response['data']['id'];

        $email = uniqid('sms-to') . '@sms.to';
        $response = SmsToTeam::inviteMemberByEmail($email);
        $this->respond($response, 'Invite new member', 'success', true);

        $response = SmsToTeam::disableMemberByID($id);
        $this->respond($response, 'Disable existing member', 'success', true);

        $response = SmsToTeam::enableMemberByID($id);
        $this->respond($response, 'Enable existing member', 'success', true);

        $response = SmsToTeam::creditMemberByID($id, 5);
        $this->respond($response, 'Crediting existing member', 'success', true);

        $response = SmsToTeam::debitMemberByID($id, 5);
        $this->respond($response, 'Debiting existing member', 'success', true);

        $response = SmsToTeam::deleteMemberByID($id, 5);
        $this->respond($response, 'Deleting existing member', 'success', true);
    }

    private function handleContact()
    {
        $phone = getenv('SMSTO_SINGLE_PHONE');
        $response = SmsToContact::allList();
        $this->respond($response, 'Contact List', 'current_page');
        $listName = uniqid('test-list-');

        $response = SmsToContact::createList($listName, 'Test Description from package');
        $this->respond($response, 'New Contact List', 'success', true);
        $id = $response['data']['id'];

        $response = SmsToContact::create($phone, $id);
        $this->respond($response, 'New Contact in List', 'success', true);

        $response = SmsToContact::getContactListByListID($id);
        $this->respond($response, 'All Contact in List', 'current_page');


        $response = SmsToContact::optinByPhone($phone, [$id]);
        $this->respond($response, 'Optin contact in List', 'success', true);

        $response = SmsToContact::optoutByPhone($phone, [$id]);
        $this->respond($response, 'Optout contact in List', 'success', true);

        $response = SmsToContact::deleteListByID($id);
        $this->respond($response, 'Delete List', 'success', true);
    }

    private function handleNumberLookup()
    {
        $phone = getenv('SMSTO_SINGLE_PHONE');
        $response = SmsToNumberLookup::estimate($phone);
        $this->respond($response, 'Numberlookup Estimate', 'success', true);

        $response = SmsToNumberLookup::verify($phone);
        $this->respond($response, 'Numberlookup Verify', 'phone');
    }

    private function respond($response, $message, $checkForField = 'success', $checkFieldValue = null)
    {
        if(isset($response[$checkForField])) {
            if(!empty($checkFieldValue) && $response[$checkForField] == $checkFieldValue) {
                $this->info("Testing $message PASSED");
            } else {
                $this->info("Testing $message PASSED");
            }
        } else {
            $this->info("Testing $message FAILED");
            $this->error(json_encode($response));
        }
    }
}
