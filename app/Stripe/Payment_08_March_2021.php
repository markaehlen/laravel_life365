<?php

namespace App\Stripe;

use Stripe\Stripe;

class Payment
{
    const DEFAULT_API_URL       = 'https://api.escrow-sandbox.com/2017-09-01/';
    private $apiKey;
    public $sendbox             = false;

    function __construct($is_sendbox = null)
    {
        if ($is_sendbox === true || $is_sendbox === false) {
            $this->sendbox = $is_sendbox;
        } else {
            $this->sendbox = true;
        }
        //$stripe_secret_key = config("get.STRIPE_PUBLISHER_KEY");

        $stripe_secret_key = "sk_test_tyFK05M4FxPPV6aQVVkpARff00v84zhSyY";

        \Stripe\Stripe::setApiKey($stripe_secret_key);
    }

    /**
     * Process Stripe Payment.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function processStripePayment($itemInfoArr = array())
    {
        $outputArr = array();
        $charge = \Stripe\Charge::create($itemInfoArr);
        $outputArr['transaction_id'] = (isset($charge->id)) ? $charge->id : 0;
        $outputArr['transaction_response'] = $charge;
        return $outputArr;
    }

    /**
     * Process Stripe Payment.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function processTransfer($itemInfoArr = array())
    {

        try {
            $charge = \Stripe\Transfer::create($itemInfoArr);
        } catch (\Stripe\Error\Base $e) {
            $charge['status'] = false;
            $charge['message'] = $e->getMessage();
        } catch (Exception $e) {
            $charge['status'] = false;
            $charge['message'] = $e->getMessage();
        }
        return $charge;
    }

    /**
     * Process Stripe Payment.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function transferFromConnected($itemInfoArr = array(), $stripe_account)
    {
        //dd($itemInfoArr);
        //dd($stripe_account);
        try {
            $charge = \Stripe\Transfer::create($itemInfoArr, ["stripe_account" => $stripe_account]);
        } catch (\Stripe\Error\Base $e) {
            $charge['status'] = false;
            $charge['message'] = $e->getMessage();
        } catch (Exception $e) {
            $charge['status'] = false;
            $charge['message'] = $e->getMessage();
        }
        return $charge;
    }

    /**
     * retrieveAmount.
     *
     */
    public function refundCharge($charge_id, $amount)
    {
        try {
            $refund = \Stripe\Refund::create([
                "charge" => $charge_id,
                "reverse_transfer" => true,
                "amount" => $amount,
            ]);
        } catch (\Stripe\Error\Base $e) {
            $refund['status'] = false;
            $refund['message'] = $e->getMessage();
        } catch (Exception $e) {
            $refund['status'] = false;
            $refund['message'] = $e->getMessage();
        }
        return $refund;
    }

    /**
     * retrieveAmount.
     *
     */
    public function createReversal($transfer_id)
    {
        return \Stripe\Transfer::createReversal($transfer_id);
    }

    /**
     * Process Stripe Payment.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function menualTransfer($itemInfoArr = array(), $stripe_account_id)
    {

        try {
            $payout = \Stripe\Payout::create($itemInfoArr, ['stripe_account' => $stripe_account_id]);
        } catch (\Stripe\Error\Base $e) {
            $payout['status'] = false;
            $payout['message'] = $e->getMessage();
        } catch (Exception $e) {
            $payout['status'] = false;
            $payout['message'] = $e->getMessage();
        }
        return $payout;
    }

    /**
     * Process Stripe Payment.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function refundAmount($refundArr)
    {
        try {
            $refund = \Stripe\Refund::create($refundArr);
        } catch (\Stripe\Error\Base $e) {
            $refund['status'] = false;
            $refund['message'] = $e->getMessage();
        } catch (Exception $e) {
            $refund['status'] = false;
            $refund['message'] = $e->getMessage();
        }
        return $refund;
    }

    /**
     * Process Stripe Payment.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function source_transaction($itemInfoArr = array())
    {
        $outputArr = array();
        $charge = \Stripe\Charge::create($itemInfoArr);
        $outputArr['transaction_id'] = (isset($charge->id)) ? $charge->id : 0;
        $outputArr['transaction_response'] = $charge;
        return $outputArr;
    }

    /**
     * Process Stripe Payment.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function captureAmount($charge_id)
    {
        $charge = \Stripe\Charge::retrieve($charge_id);
        $charge->capture();
        return $charge;
    }

    /**
     * Process Destination Stripe Payment.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function processDestinationStripePayment($itemInfoArr = array())
    {
        $outputArr = array();
        try {
            $charge = \Stripe\Charge::create(array(
                'amount' => ($itemInfoArr['total_amount'] + (!empty($itemInfoArr['shipping_amount']) ? $itemInfoArr['shipping_amount'] : 0) + (!empty($itemInfoArr['credit_card_amount']) ? $itemInfoArr['credit_card_amount'] : 0)) * 100,
                'currency' => $itemInfoArr['config']['currency'],
                'source' => $itemInfoArr['config']['token'],
                "destination" => array(
                    "amount" => $itemInfoArr['service_provider_amount'] * 100,
                    "account" => $itemInfoArr['stripe_account_id'],
                ),
            ));
            $outputArr['transaction_id'] = (isset($charge->id)) ? $charge->id : 0;
            $outputArr['transaction_response'] = $charge;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
        }
        return $outputArr;
    }

    /**
     * Create Connected Stripe Account.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function createConnectedStripeAccount($itemInfoArr = array())
    {
        $outputArr = array();
        //dd($itemInfoArr);
        try {
            $acct = \Stripe\Account::create($itemInfoArr);
            $outputArr['account_id'] = (isset($acct->id)) ? $acct->id : '';
            $outputArr['data'] = $acct;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            die('c');
            $outputArr['message'] = $e->getMessage();
        } catch (Exception $e) {
            die('e');
            $outputArr['message'] = $e->getMessage();
        }

        return $outputArr;
    }


    /**
     * Create Connected Stripe Account.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function updateConnectedStripeAccount($account_id, $itemInfoArr = array())
    {
        $outputArr = array();
        unset($itemInfoArr['type']);
        unset($itemInfoArr['country']);
        //dd($itemInfoArr);die;
        try {
            $acct = \Stripe\Account::update($account_id, $itemInfoArr);
            $outputArr['account_id'] = (isset($acct->id)) ? $acct->id : '';
            $outputArr['data'] = $acct;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
        }

        return $outputArr;
    }

    /** This api not used
     * Create Connected Stripe Account.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function updateConnectedStripeAccount_old($account_id, $itemInfoArr = array(), $business_type)
    {
        $outputArr = array();
        try {
            $acct = \Stripe\Account::retrieve($account_id);

            if (!empty($itemInfoArr[$business_type]['dob']['day'])) {
                $acct->$business_type->dob->day = $itemInfoArr[$business_type]['dob']['day'];
                $acct->$business_type->dob->month = $itemInfoArr[$business_type]['dob']['month'];
                $acct->$business_type->dob->year = $itemInfoArr[$business_type]['dob']['year'];
            }

            if (!empty($itemInfoArr[$business_type]['first_name'])) {
                $acct->$business_type->first_name = $itemInfoArr[$business_type]['first_name'];
            }

            if (!empty($itemInfoArr[$business_type]['last_name'])) {
                $acct->$business_type->last_name = $itemInfoArr[$business_type]['last_name'];
            }


            if (!empty($itemInfoArr[$business_type]['business_type'])) {
                $acct->$business_type->business_type = $itemInfoArr[$business_type]['business_type'];
            }

            if (!empty($itemInfoArr[$business_type]['address']['line1'])) {
                $acct->$business_type->address['line1'] = $itemInfoArr[$business_type]['address']['line1'];
            }
            if (!empty($itemInfoArr[$business_type]['address']['line2'])) {
                $acct->$business_type->address['line2'] = $itemInfoArr[$business_type]['address']['line2'];
            }

            if (!empty($itemInfoArr[$business_type]['address']['postal_code'])) {
                $acct->$business_type->address['postal_code'] = $itemInfoArr[$business_type]['address']['postal_code'];
            }

            if (!empty($itemInfoArr[$business_type]['address']['city'])) {
                $acct->$business_type->address['city'] = $itemInfoArr[$business_type]['address']['city'];
            }

            if (!empty($itemInfoArr[$business_type]['address']['state'])) {
                $acct->$business_type->address['state'] = $itemInfoArr[$business_type]['address']['state'];
            }

            if (isset($itemInfoArr[$business_type]['ssn_last_4']) && !empty($itemInfoArr[$business_type]['ssn_last_4'])) {
                $acct->$business_type->ssn_last_4 = $itemInfoArr[$business_type]['ssn_last_4'];
            }

            $result = $acct->save();
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
        }

        return $outputArr;
    }

    public function updateBankAccountDetails($account_id, $bankId, $paymentInfo = array())
    {
        $outputArr = array();
        try {
            if (!empty($bankId)) {
                $acct = \Stripe\Account::updateExternalAccount(
                    $account_id,
                    $bankId,
                    $paymentInfo
                );
            } else {
                $acct = \Stripe\Account::createExternalAccount(
                    $account_id,
                    ["external_account" => [
                        "object" => "bank_account",
                        "country" => $paymentInfo['country'],
                        "currency" => $paymentInfo['currency'],
                        "routing_number" => $paymentInfo['routing_number'],
                        "account_number" => $paymentInfo['account_number'],
                    ]]
                );
            }
            $outputArr['status'] = true;
            $outputArr['data'] = $acct;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['status'] = false;
            $outputArr['message'] = $e->getMessage();
        } catch (Exception $e) {
            $outputArr['status'] = false;
            $outputArr['message'] = $e->getMessage();
        }
        return $outputArr;
    }


    public function addBankAccountByToken($account_id, $token)
    {
        $outputArr = array();
        //dd($paymentInfo);
        try {
            $bank_account = \Stripe\Account::createExternalAccount(
                $account_id,
                [
                    'external_account' => $token,
                ]
            );

            $outputArr['status'] = true;
            $outputArr['content'] = $bank_account;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['status'] = false;
            $outputArr['message'] = $e->getMessage();
        } catch (Exception $e) {
            $outputArr['status'] = false;
            $outputArr['message'] = $e->getMessage();
        }
        return $outputArr;
    }


    public function createStripePlan($createPlanInfoArr = [])
    {
        $ConfigSettings = Configure::read('Setting');
        $outputArr = array();
        $this->configuration();
        try {
            $plan = \Stripe\Plan::create(array(
                "product" => 'prod_DBBHXHMhVv6hOC',
                "nickname" => $createPlanInfoArr['plan_name'],
                "id" => $createPlanInfoArr['planID'],
                "interval" => $createPlanInfoArr['stripe_plan_interval'],
                "currency" => $createPlanInfoArr['currency'],
                "amount" => $createPlanInfoArr['final_price'],
            ));
            $outputArr['plan_id'] = (isset($plan->id)) ? $plan->id : 0;
            $outputArr['plan_response'] = $plan;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
        }
        return $outputArr;
    }

    public function createStripeCustomer($createCustomerInfoArr = [])
    {
        $outputArr = [];
        $this->configuration();
        try {
            $customer = \Stripe\Customer::create(array(
                'source' => $createCustomerInfoArr['source'],
                'email' => $createCustomerInfoArr['email']
            ));
            $outputArr['customerID'] = (isset($customer->id)) ? $customer->id : 0;
            $outputArr['customer_response'] = $customer;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            $outputArr['message'] = $e->getMessage();
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
        }

        return $outputArr;
    }

    public function createStripeSubscription($createSubscriptionInfoArr = [])
    {
        $outputArr = [];
        $this->configuration();
        try {
            $subscription = \Stripe\Subscription::create(array(
                "customer" => $createSubscriptionInfoArr['customerID'],
                "plan" => $createSubscriptionInfoArr['stripe_plan_id'],
            ));
            $outputArr['subscription_id'] = (isset($subscription->id)) ? $subscription->id : 0;
            $outputArr['subscription_response'] = $subscription;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            $outputArr['message'] = $e->getMessage();
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
        }
        return $outputArr;
    }

    public function initialRequirements($user, $account_id = null)
    {
        if (!isset($user->location->iso_alpha2_code)) {
            return false;
        }
        $business_type                                          = "company";
        $company['business_type']                               = $business_type;
        $company['country']                                     = $user->location->iso_alpha2_code; //$user->user_profile->location->iso_alpha2_code;
        $company['type']                                        = 'custom';
        $company['email']                                       = $user->email;
        $company['company']['name']                             = $user->company_name;
        $company['settings']['payouts']['schedule']['interval'] = 'manual';

        if (!empty($user->defaultAddress->address_line_1)) {
            $company['company']['address']['line1'] = $user->defaultAddress->address_line_1;
        } else {
            $company['company']['address']['line1'] = $user->address;
        }
        if (!empty($user->defaultAddress->address_line_2)) {
            $company['company']['address']['line2'] = $user->defaultAddress->address_line_2;
        }
        $company['company']['address']['postal_code'] = $user->defaultAddress->postal_code;

        if (!empty($user->defaultAddress->city)) {
            $company['company']['address']['city'] = $user->defaultAddress->city;
        }

        if (!empty($user->defaultAddress->state->title)) {
            $company['company']['address']['state'] = $user->defaultAddress->state->title;
        }

        if (!empty($user->mobile)) {
            $company['company']['phone'] = $user->mobile;
        }

        if (in_array($company['country'], ["US", "JP", "GB", "AU", "BE"])) {
            if (!empty($user->company_tax_id)) {
                $company['company']['tax_id'] = $user->company_tax_id;
            }
        }

        if ($company['country'] == "US" || $company['country'] == "GB") {
            if (!empty($user->company_registration_no)) {
                // $company['company']['tax_id_registrar'] = $user->company_registration_no;
            }
        }

        if ($company['country'] == "US") {
            //$company['requested_capabilities'] = ['platform_payments']; // need to discuss with stripe support
            $company['requested_capabilities'] = [
                'card_payments',
            ];
        }
        $company['requested_capabilities'] = ['card_payments', 'transfers'];
        $company['business_profile']['mcc'] = '5399';
        $company['business_profile']['name'] = null;
        $company['business_profile']['url'] = $user->company_url;
        $company['business_profile']['product_description'] = $user->about; //'I-Whiz is designed to offer buyers and suppliers a platform to do business with complete freedom, peace of mind and security.';
        $company['business_profile']['support_email'] = $user->email;
        $company['business_profile']['support_phone'] = $user->mobile;

        $company['tos_acceptance']['date'] = time();
        $company['tos_acceptance']['ip'] = !empty($user->ip) ? $user->ip : request()->ip();
        $company['tos_acceptance']['user_agent'] = $user->user_agent;

        if ($account_id != null) {
            $result = $this->updateConnectedStripeAccount($account_id, $company);
        } else {
            $result = $this->createConnectedStripeAccount($company);
        }
        //dump($company['business_profile']['url']);
        return $result;
    }

    public function createAccountPersons($account_id, $person_id, $user, $type = null)
    {
        if ($type == "owner" || $type == "director") {
            $owner = explode(" ", $user->owner_name);
            if (count($owner) > 1) {
                $person['first_name'] = $owner[0];
                $person['last_name'] = $owner[1];
            } else {
                $person['first_name'] = $user->owner_name;
                $person['last_name'] = $user->last_name;
            }
        } else {
            $person['first_name'] = $user->first_name;
            $person['last_name'] = $user->last_name;
        }

        if (!empty($user->mobile) && ($user->location->iso_alpha2_code == "US" || $user->location->iso_alpha2_code == "GB")) {
            $person['phone'] = $user->mobile;
        }


        $existAddr = [];
        if (!empty($user->address->address_line_1))
            $existAddr[] = $user->address->address_line_1;
        if (!empty($user->address->address_line_2))
            $existAddr[] = $user->address->address_line_1;
        if (!empty($user->address->city))
            $existAddr[] = $user->address->city;
        if (!empty($user->address->state->title))
            $existAddr[] = $user->address->state->title;
        if (!empty($user->address->postal_code))
            $existAddr[] = $user->address->postal_code;
        if (!empty($user->address->country->title))
            $existAddr[] = $user->address->country->title;

        if ($user->location->iso_alpha2_code == "US" && $user->profile) {
            $person['ssn_last_4'] = $user->profile->ssn_last_4;
        }
        if ($user->location->iso_alpha2_code == "US not idea what it used") {
            $person['address'] = implode(", ", $existAddr);
            // $person['ssn_last_4'] = $user->ssn_last_4;
        } else {
            if (!empty($user->address->address_line_1)) {
                $person['address']['line1'] = $user->address->address_line_1;
            } else {
                $person['address']['line1'] = implode(", ", $existAddr);
            }
            $person['address']['postal_code'] = $user->address->postal_code ?? "";
            $person['address']['city'] = $user->address->city ?? "";
        }

        if (!empty($user->address->state->title)) {
            $person['address']['state'] = $user->address->state->title;
        }

        // dump($user->id);
        if (!empty($user->date_of_birth)) {
            $person['dob']['day'] = date('d', strtotime($user->date_of_birth));
            $person['dob']['month'] = date('m', strtotime($user->date_of_birth));
            $person['dob']['year'] = date('Y', strtotime($user->date_of_birth));
        }
        $person['email'] = $user->email;
        if (!$type) {
            $person['relationship']['account_opener'] = true;
            $person['relationship']['owner'] = true;
            $person['relationship']['director'] = true;
            $person['relationship']['percent_ownership'] =  100;
        } else {
            $person['relationship'][$type] = true;
        }

        if ($type == "owner") {
            $person['relationship']['director'] = true;
            $person['relationship']['percent_ownership'] =  100;
            $person['relationship']['title'] = "CEO";
        } else {
            $person['relationship']['title'] = $user->company_designation;
        }
        //dd($person);
        try {
            if ($person_id) {
                $return = \Stripe\Account::updatePerson($account_id, $person_id, $person);
            } else {
                $return = \Stripe\Account::createPerson($account_id, $person);
            }
            $outputArr['data'] = $return;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        }
        return $outputArr;
    }

    public function getPersons($account_id)
    {
        $persons = \Stripe\Account::allPersons($account_id);
        return $persons;
    }

    public function uploadFiles($account_id, $filePath)
    {
        try {
            // $file = \Stripe\FileUpload::create(
            //     [
            //         'purpose' => 'identity_document',
            //         'file' => fopen($filePath, 'r')
            //     ],
            //     ['stripe_account' => $account_id]
            // );
            $file = \Stripe\File::create(
                [
                    'purpose' => 'identity_document',
                    'file' => fopen($filePath, 'r')
                ],
                ['stripe_account' => $account_id]
            );
            $outputArr['data'] = $file;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        }
        return $outputArr;
    }

    public function updateDocument($accountId, $personId, $data)
    {
        try {
            $return = \Stripe\Account::updatePerson($accountId, $personId, $data);
            $outputArr['data'] = $return;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        }
        return $outputArr;
    }

    public function createCustomer($data, $cuid)
    {
        try {
            $return = \Stripe\Customer::create($data);
            $outputArr['data'] = $return;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        }
        return $outputArr;
    }

    public function retrieveAccount($account_id)
    {
        $account = \Stripe\Account::retrieve($account_id);
        return $account;
    }

    public function createConnectedAccount($user, $account_id = null)
    {
        $itemInfoArr = [];
        $itemInfoArr['country'] = $user->location->iso_alpha2_code; //$user->user_profile->location->iso_alpha2_code;
        $itemInfoArr['type'] = 'custom';
        $itemInfoArr['email'] = $user->email;
        if ($user->role_id == 1) {
            $business_type = "individual";

            if (!empty($user->date_of_birth)) {
                $itemInfoArr[$business_type]['dob']['day'] = date('d', strtotime($user->date_of_birth));
                $itemInfoArr[$business_type]['dob']['month'] = date('m', strtotime($user->date_of_birth));
                $itemInfoArr[$business_type]['dob']['year'] = date('Y', strtotime($user->date_of_birth));
            }
            $itemInfoArr[$business_type]['first_name'] = $user->first_name;
            $itemInfoArr[$business_type]['last_name'] = $user->last_name;
            if ($itemInfoArr['country'] == "US") {
                $itemInfoArr[$business_type]['ssn_last_4'] = $user->ssn_last_4;
            }
        } else {
            $business_type = "company";
            $itemInfoArr[$business_type]['name'] = $user->company_name;
            // $itemInfoArr['settings']['payouts']['schedule']['delay_days'] = 30;
            $itemInfoArr['settings']['payouts']['schedule']['interval'] = 'manual';
            if ($itemInfoArr['country'] == "US") {
                $itemInfoArr['business_url'] = 'https://law-whiz.com';
            }
        }
        $itemInfoArr['business_type'] = $business_type;
        // $itemInfoArr[$business_type]['phone'] = $user->mobile;
        if (!empty($user->address_line_1)) {
            $itemInfoArr[$business_type]['address']['line1'] = $user->address_line_1;
        }
        if (!empty($user->address_line_2)) {
            $itemInfoArr[$business_type]['address']['line2'] = $user->address_line_2;
        }

        $itemInfoArr[$business_type]['address']['postal_code'] = $user->postal_code;

        if (!empty($user->city)) {
            $itemInfoArr[$business_type]['address']['city'] = $user->city;
        }

        if (!empty($user->state)) {
            $itemInfoArr[$business_type]['address']['state'] = $user->state;
        }
        if ($itemInfoArr['country'] == "US" || $itemInfoArr['country'] == "GB" || $itemInfoArr['country'] == "BE") {
            $itemInfoArr[$business_type]['tax_id'] = $user->company_tax_id;
        }
        if ($itemInfoArr['country'] == "US" || $itemInfoArr['country'] == "GB") {
            $itemInfoArr[$business_type]['ssn_last_4'] = $user->ssn_last_4;
        }


        // if (!empty($user->company_registration_no)) {
        //     $itemInfoArr[$business_type]['tax_id_registrar'] = $user->company_registration_no;
        // }

        $itemInfoArr['tos_acceptance']['date'] = time();
        $itemInfoArr['tos_acceptance']['ip'] = $user->ip;
        $itemInfoArr['tos_acceptance']['user_agent'] = $user->user_agent;
        if ($account_id != null) {
            $result = $this->updateConnectedStripeAccount($account_id, $itemInfoArr);
        } else {
            $result = $this->createConnectedStripeAccount($itemInfoArr);
            if ($user->role_id == 2 && (isset($result['account_id']) && !empty($result['account_id']))) {
                $itemInfoArr[$business_type]['first_name'] = $user->first_name;
                $itemInfoArr[$business_type]['last_name'] = $user->last_name;
                $comapnyData = $itemInfoArr['company'];
                if (isset($comapnyData['name'])) {
                    unset($comapnyData['name']);
                }
                if (!empty($user->date_of_birth)) {
                    $comapnyData['dob']['day'] = date('d', strtotime($user->date_of_birth));
                    $comapnyData['dob']['month'] = date('m', strtotime($user->date_of_birth));
                    $comapnyData['dob']['year'] = date('Y', strtotime($user->date_of_birth));
                }
                $comapnyData['email'] = $user->email;
                // $comapnyData['relationship']['account_opener'] = true;
                // $comapnyData['relationship']['owner'] = false;
                $comapnyData['relationship']['title'] = $user->company_designation;
                // if($itemInfoArr['country'] == "US" || $itemInfoArr['country'] == "GB" || $itemInfoArr['country'] == "AU" || $itemInfoArr['country'] == "BE"){
                $comapnyData['relationship']['account_opener']['first_name'] = $user->first_name;
                $comapnyData['relationship']['account_opener']['last_name'] = $user->last_name;
                $comapnyData['relationship']['account_opener']['dob']['day'] = date('d', strtotime($user->date_of_birth));
                $comapnyData['relationship']['account_opener']['dob']['month'] = date('m', strtotime($user->date_of_birth));
                $comapnyData['relationship']['account_opener']['dob']['year'] = date('Y', strtotime($user->date_of_birth));
                if ($itemInfoArr['country'] == "US") {
                    $comapnyData['relationship']['account_opener']['address'] = $user->full_address;
                    $comapnyData['relationship']['account_opener']['ssn_last_4'] = $user->ssn_last_4;
                } else {
                    if (!empty($user->address_line_1)) {
                        $comapnyData['relationship']['account_opener']['address']['line1'] = $user->address_line_1;
                    }
                    $comapnyData['relationship']['account_opener']['address']['postal_code'] = $user->postal_code;
                    $comapnyData['relationship']['account_opener']['address']['city'] = $user->city;
                }
                $comapnyData['relationship']['account_opener']['email'] = $user->email;
                $comapnyData['relationship']['account_opener']['phone'] = $user->phone;

                $comapnyData['relationship']['owner']['first_name'] = $user->first_name;
                $comapnyData['relationship']['owner']['last_name'] = $user->last_name;
                $comapnyData['relationship']['owner']['dob']['day'] = date('d', strtotime($user->date_of_birth));
                $comapnyData['relationship']['owner']['dob']['month'] = date('m', strtotime($user->date_of_birth));
                $comapnyData['relationship']['owner']['dob']['year'] = date('Y', strtotime($user->date_of_birth));
                if ($itemInfoArr['country'] == "US") {
                    $comapnyData['relationship']['owner']['address'] = $user->full_address;
                    $comapnyData['relationship']['owner']['ssn_last_4'] = $user->ssn_last_4;
                } else {
                    if (!empty($user->address_line_1)) {
                        $comapnyData['relationship']['owner']['address']['line1'] = $user->address_line_1;
                    }
                    $comapnyData['relationship']['owner']['address']['postal_code'] = $user->postal_code;
                    $comapnyData['relationship']['owner']['address']['city'] = $user->city;
                }
                $comapnyData['relationship']['owner']['email'] = $user->email;
                $comapnyData['relationship']['owner']['phone'] = $user->phone;

                //if($itemInfoArr['country'] == "GB" || $itemInfoArr['country'] == "BE"){
                $comapnyData['relationship']['director']['first_name'] = $user->first_name;
                $comapnyData['relationship']['director']['last_name'] = $user->last_name;
                //  }

                //}
                dump($comapnyData);
                $this->createPerson($result['account_id'], $comapnyData);
            }
        }
        return $result;
    }

    public function createPerson($account_id, array $data)
    {
        try {
            $person = \Stripe\Account::createPerson($account_id, $data);
            $outputArr['data'] = $person;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        }
        return $outputArr;
    }

    /**
     * Delete Connected Stripe Account.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function deleteAccount($account_id)
    {
        $account = \Stripe\Account::retrieve($account_id);
        $account->delete();
        return $account;
    }

    /**
     * Get add Connected Stripe Bank Account.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function getAllBankAccounts($account_id)
    {
        $bank_accounts = \Stripe\Account::allExternalAccounts(
            $account_id,
            [
                'limit' => 3,
                'object' => 'bank_account',
            ]
        );
        return $bank_accounts;
    }

    /**
     * Get add Connected Stripe Bank Account.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function createPlan($data, $account_id = null)
    {

        try {
            $plan = \Stripe\Plan::create($data);;
            $outputArr['data'] = $plan;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        }
        return $outputArr;
    }

    /**
     * Get add Connected Stripe Bank Account.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function updatePlan($plan_id, $data)
    {
        try {
            $plan = \Stripe\Plan::update($plan_id, $data);
            $outputArr['data'] = $plan;
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        }
        return $outputArr;
    }

    /**
     * Get add Connected Stripe Bank Account.
     *
     * @param array    $itemInfoArr
     * @return \Cake\Http\Response|null
     * @throws \Stripe\Exception\StripeConnectionException When connection not found.
     */
    public function deletePlan($product_id, $plan_id = null)
    {
        try {
            if ($plan_id) {
                $plan = \Stripe\Plan::retrieve($plan_id);
                $plan->delete();
            }
            $product = \Stripe\Product::retrieve($product_id);
            $product->delete();
            $outputArr['status'] = true;
        } catch (\Stripe\Error\Base $e) {
            // Code to do something with the $e exception object when an error occurs
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        } catch (Exception $e) {
            $outputArr['message'] = $e->getMessage();
            $outputArr['status'] = false;
        }
        return $outputArr;
    }
}
