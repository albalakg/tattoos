<?php

namespace App\Domain\Payment\Services\Providers;

use App\Domain\Helpers\LogService;
use Exception;
use Illuminate\Support\Str;
use App\Domain\Orders\Models\Order;
use Illuminate\Support\Facades\Http;
use App\Domain\Users\Services\UserService;
use App\Domain\Payment\Interfaces\IPaymentProvider;

class PayPlusProvider implements IPaymentProvider
{
    const ID                    = 1;
    const PAYMENT_METHOD_CHARGE = 1;
    const DEFAULT_CHARGE_METHOD = 'credit-card';
    const PAGE_GENERATION_PATH  = 'PaymentPages/generateLink';
    const CURRENCY_CODE         = 'ILS';

    private Order $order;

    private $transaction_response;

    private LogService $log_service;

    private array $payment_payload = [
        'payment_page_uid'          => '',
        'charge_method'             => self::PAYMENT_METHOD_CHARGE,
        'charge_default'            => self::DEFAULT_CHARGE_METHOD,
        'hide_other_charge_methods' => true,
        'amount'                    => null,
        'currency_code'             => self::CURRENCY_CODE,
        'sendEmailApproval'         => true,
        'sendEmailFailure'          => true,
        'sendEmailApproval'         => true,
        'refURL_success'            => '',
        'refURL_failure'            => '',
        'refURL_callback'           => '',
        'customer'                  => [
            'customer_name'         => '',
            'email'                 => '',
        ],
        'items'                     => [
            [
                'name'              => '',
                'quantity'          => 1,
                'price'             => null,
            ]
        ],
    ];

    public function __construct()
    {
        $this->log_service = new LogService('payment');
    }

    /**
     * @return int
     */
    public function getProviderID(): int
    {
        return self::ID;
    }

    /**
     * @return void
     */
    public function getTransactionResponse()
    {
        return $this->transaction_response;
    }

    /**
     * @return string
     */
    public function getGeneratedPageToken(): string
    {
        return $this->transaction_response->data->page_request_uid;
    }

    /**
     * @return string
     */
    public function getGeneratedPageLink(): string
    {
        return $this->transaction_response->data->payment_page_link;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function buildPayment(Order $order)
    {
        $this->order = $order;

        $this->setPrice()
            ->setPageUuid()
            ->setCallbackUrls()
            ->setCustomer()
            ->setItem();

        return $this;
    }

    /**
     * @return void
     */
    public function startTransaction()
    {
        // $this->transaction_response = (object) [
        //     "results"=> (object) [
        //       "status"=> "success",
        //       "code"=> 0,
        //       "description"=> "payment page link is been generated"
        //     ],
        //     "data"=> (object) [
        //       "page_request_uid"=> "f33f7a1f-5ea7-4857-992a-2da95b369f53",
        //       "payment_page_link"=> "https://payments.payplus.co.il/f33f7a1f-5ea7-4857-992a-2da95b369f53",
        //       "qr_code_image"=> "https://restapi.payplus.co.il/api/payment-pages/payment-request/f33f7a1f-5ea7-4857-992a-2da95b369f53/qr-code"
        //     ]
        // ];
        // $response = Http::withHeaders([
        //     'Authorization' => $this->getAuthorization()
        // ])->post(config('payment.payplus.address') . self::PAGE_GENERATION_PATH, $this->payment_payload);
        // $this->transaction_response = json_decode($response->body());
        $response = Http::withHeaders([
            'Authorization' => $this->getAuthorization()
        ])->get('https://server.goldensacademy.com/api/orders/callback?name=123&price=123&id=92929');
        $this->transaction_response = json_decode($response->body());
    }

    /**
     * check if the payment is finished successfully
     *
     * @return bool
     */
    public function isValid(): bool
    {
        try {
            if ($this->transaction_response->results->status !== 'success') {
                throw new Exception('The response status from the transaction indicates for an error');
            }

            if (empty($this->transaction_response->data->payment_page_link) || !is_string($this->transaction_response->data->payment_page_link)) {
                throw new Exception('The response page link from the transaction is invalid');
            }

            return true;
        } catch (Exception $ex) {
            $this->log_service->critical($ex);
            return false;
        }
    }

    /**
     * @return self
     */
    private function setItem(): self
    {
        $this->payment_payload['amount']            = $this->order->price;
        $this->payment_payload['items'][0]['name']  = $this->order->course->name;
        $this->payment_payload['items'][0]['price'] = $this->order->price;
        return $this;
    }

    /**
     * @return self
     */
    private function setPageUuid(): self
    {
        $this->payment_payload['payment_page_uid'] = config('payment.payplus.page_uuid');
        return $this;
    }

    /**
     * @return self
     */
    private function setCallbackUrls(): self
    {
        $this->payment_payload['refURL_success']    = 'https://goldensacademy.com/api/orders/success';
        $this->payment_payload['refURL_failure']    = 'https://goldensacademy.com/api/orders/failure';
        $this->payment_payload['refURL_callback']   = 'https://server.goldensacademy.com/api/orders/callback';
        // $this->payment_payload['refURL_success']    = config('app.url') . '/api/orders/success';
        // $this->payment_payload['refURL_failure']    = config('app.url') . '/api/orders/failure';
        // $this->payment_payload['refURL_callback']   = config('app.url') . '/api/orders/callback';
        return $this;
    }

    /**
     * @return self
     */
    private function setCustomer(): self
    {
        $user_service   = new UserService();
        $user           = $user_service->getUserByID($this->order->user_id);
        if (!$user) {
            throw new Exception('User not found');
        }

        $this->payment_payload['customer']['customer_name'] = $user->details->fullName;
        $this->payment_payload['customer']['email']         = $user->email;
        return $this;
    }

    /**
     * @return self
     */
    private function setPrice(): self
    {
        $this->payment_payload['price'] = $this->order->price;
        return $this;
    }

    /**
     * @return string
     */
    private function getAuthorization(): string
    {
        return json_encode(["api_key" => config('payment.payplus.api_key'), "secret_key" => config('payment.payplus.secret_key')]);
    }
}
