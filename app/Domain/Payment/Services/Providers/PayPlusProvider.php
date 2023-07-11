<?php

namespace App\Domain\Payment\Services\Providers;

use App\Domain\Helpers\EnvService;
use App\Domain\Helpers\LogService;
use Exception;
use Illuminate\Support\Str;
use App\Domain\Orders\Models\Order;
use Illuminate\Support\Facades\Http;
use App\Domain\Users\Services\UserService;
use App\Domain\Payment\Interfaces\IPaymentProvider;

class PayPlusProvider implements IPaymentProvider
{
    const ID                                    = 1;
    const PAYMENT_METHOD_CHARGE                 = 1;
    const DEFAULT_CHARGE_METHOD                 = 'credit-card';
    const PAGE_GENERATION_PATH                  = 'PaymentPages/generateLink';
    const INVOICE_PATH                          = 'books/docs/new/purchase';
    const CURRENCY_CODE                         = 'ILS';
    const INVOICE_QUANTITY                      = 1;
    const INVOICE_TRANSACTION_TYPE               = 'normal';
    const INVOICE_NUMBER_OF_PAYMENTS            = 1;

    private Order $order;

    private $transaction_response;

    private $invoice_response;

    private LogService $log_service;

    private $provider_browser = 'PayPlus';

    private array $page_generation_payload = [
        'payment_page_uid'          => '',
        'charge_method'             => self::PAYMENT_METHOD_CHARGE,
        'charge_default'            => self::DEFAULT_CHARGE_METHOD,
        'hide_other_charge_methods' => true,
        'amount'                    => null,
        'currency_code'             => self::CURRENCY_CODE,
        'sendEmailApproval'         => true,
        'sendEmailFailure'          => true,
        'sendEmailApproval'         => true,
        'create_hash'               => true,
        'refURL_success'            => '',
        'refURL_failure'            => '',
        'refURL_callback'           => '',
        'refURL_cancel'             => '',
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
        $this->setProviderBrowser(); 
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

        $this->setPageUuid()
            ->setCallbackUrls()
            ->setCustomer()
            ->setItem();

        return $this;
    }

    /**
     * Sends a request to the provider and set the response
     *  
     * @return void
     */
    public function startTransaction()
    {
        $this->log_service->info('Send a request to Payplus provider for transaction', $this->page_generation_payload);
        $response = Http::withHeaders([
            'Authorization' => $this->getAuthorization()
            ])->post(config('payment.payplus.address') . self::PAGE_GENERATION_PATH, $this->page_generation_payload);
        $this->transaction_response = json_decode($response->body());
        $this->log_service->info('Response from Payplus provider for transaction', (array) $this->transaction_response);
    }

    /**
     * @param Order $order
     * @return void
     */
    public function sendInvoice(Order $order)
    {
        $this->order = $order;
        $this->log_service->info('Send a request to Payplus provider for invoice');
        $response = Http::withHeaders([
            'Authorization' => $this->getAuthorization()
            ])->post(config('payment.payplus.address') . self::INVOICE_PATH, $this->getInvoicePayload());
        $this->invoice_response = json_decode($response->body());
        $this->log_service->info('Response from Payplus provider for invoice', (array) $this->invoice_response);
    }

    /**
     * check if the payment is finished successfully
     *
     * @return bool
     */
    public function isTransactionValid(): bool
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
     * check if the payment invoice is finished successfully
     *
     * @return bool
     */
    public function isInvoiceValid(): bool
    {
        try {
            if ($this->invoice_response->status !== 'success') {
                throw new Exception('The response status from the invoice indicates for an error');
            }

            if (empty($this->invoice_response->details->docUID) || !Str::isUuid($this->invoice_response->details->docUID)) {
                throw new Exception('The response docUID from the invoice is invalid');
            }

            return true;
        } catch (Exception $ex) {
            $this->log_service->critical($ex);
            return false;
        }
    }

    /**
     * check if the payment callback is finished successfully
     *
     * @param array $response
     * @return bool
     */
    public function isPaymentCallbackValid(array $response): bool
    {
        if (!is_string($response['approval_number'])) {
            $this->log_service->error('The approval number is invalid', ['approval_number' => $response['approval_number']]);
            return false;
        }
        
        if ($response['browser'] !== $this->provider_browser) {
            $this->log_service->error('The response user agent is invalid', ['browser' => $response['browser']]);
            return false;
        }

        if(!$this->isHashValid($response['hash'])) {
            $this->log_service->error('The response hash is invalid', ['hash' => $response['hash']]);
            return false;
        }

        return true;
    }

    /**
     * @return self
     */
    private function setItem(): self
    {
        $this->page_generation_payload['amount']            = 0.1;
        $this->page_generation_payload['items'][0]['name']  = $this->order->course->name;
        $this->page_generation_payload['items'][0]['price'] = 0.1;
        // $this->page_generation_payload['amount']            = $this->order->price;
        // $this->page_generation_payload['items'][0]['name']  = $this->order->course->name;
        // $this->page_generation_payload['items'][0]['price'] = $this->order->price;
        return $this;
    }

    /**
     * @return self
     */
    private function setPageUuid(): self
    {
        $this->page_generation_payload['payment_page_uid'] = config('payment.payplus.page_uuid');
        return $this;
    }

    /**
     * @return self
     */
    private function setCallbackUrls(): self
    {
        $this->page_generation_payload['refURL_success']     = config('app.client_url') . '/orders/success';
        $this->page_generation_payload['refURL_failure']     = config('app.client_url') . '/orders/failure';
        $this->page_generation_payload['refURL_callback']    = config('app.url') . '/api/payment/callback';
        $this->page_generation_payload['refURL_cancel']      = config('app.client_url');
        return $this;
    }

    /**
     * @return self
     */
    private function setCustomer(): self
    {
        $this->page_generation_payload['customer']['customer_name'] = $this->order->user->details->fullName;
        $this->page_generation_payload['customer']['email']         = $this->order->user->email;
        return $this;
    }

    /**
     * builds and return the invoice payload
     * 
     * @return array
     */
    private function getInvoicePayload(): array
    {
        return [
            "doc_date"      => now()->format('Y-m-d'),
            "totalAmount"   => $this->order->price,
            "customer"      => [
                "name"  => $this->order->user->details->fullName,
                "email" => $this->order->user->email,
                "phone" => $this->order->user->details->phone,
            ],
            "items"         => [
                [
                    "name"      => $this->order->course->name,
                    "quantity"  => self::INVOICE_QUANTITY,
                    "price"     => $this->order->price,
                ]
            ],
            "payments"      => [
                [
                    "payment_type"      => self::DEFAULT_CHARGE_METHOD,
                    "amount"            => $this->order->price,
                    "transaction_type"  => self::INVOICE_TRANSACTION_TYPE,
                    "payments"          => self::INVOICE_NUMBER_OF_PAYMENTS,
                    "first_payment"     => $this->order->price
                ]
            ]
        ];
    }

    /**
     * @param string $hash
     * @return bool
     */
    private function isHashValid(string $hash): bool
    {
        $message = request()->getContent();
        if(!$message) {
            $this->log_service->error('Hash is invalid, no message found');
            return false;
        }
        
        $this->log_service->info('Hash Validation message', ['message' => $message]);
        $message        = json_encode(json_decode($message, true));
        $this->log_service->info('Hash Validation message as string', ['message' => $message]);
        $genHash        = hash_hmac('sha256', $message, config('payment.payplus.secret_key'), true);
        $this->log_service->info('Hash Validation gen hash', ['hash' => $genHash]);
        $genHash_base64 = base64_encode($genHash);
        $this->log_service->info('Hash Validation base64 gen hash', ['hash' => $genHash_base64]);
    
        return $genHash_base64 === $hash;
    }

    /**
     * @return void
     */
    private function setProviderBrowser()
    {
        if(EnvService::isLocal()) {
            $this->provider_browser = 'PostmanRuntime/7.32.3';
        }
    }

    /**
     * @return string
     */
    private function getAuthorization(): string
    {
        return json_encode(["api_key" => config('payment.payplus.api_key'), "secret_key" => config('payment.payplus.secret_key')]);
    }
}
