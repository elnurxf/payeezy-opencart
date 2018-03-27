<?php

class ControllerExtensionPaymentPayeezy extends Controller
{
    public function index()
    {
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        // Purchase order or other number used by merchant’s customer to track the order.
        $x_cust_id = $this->session->data['order_id'];

        // Take from Payment Page ID in Payment Pages interface
        $x_gateway_id = $this->config->get('payment_payeezy_x_gateway_id');

        // Take from Payment Page ID in Payment Pages interface
        $x_login = $this->config->get('payment_payeezy_x_login');

        // Take from Payment Pages configuration interface
        $transaction_key = $this->config->get('payment_payeezy_transaction_key');

        $x_amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

        // Needs to agree with the currency of the payment page
        $x_currency_code = $order_info['currency_code'];

        // initialize random generator for x_fp_sequence
        srand(time());
        $x_fp_sequence = rand(1000, 100000) + 123456;

        // needs to be in UTC. Make sure webserver produces UTC
        $x_fp_timestamp = time();

        // The values that contribute to x_fp_hash
        $hmac_data = $x_login . "^" . $x_fp_sequence . "^" . $x_fp_timestamp . "^" . $x_amount . "^" . $x_currency_code;
        $x_fp_hash = hash_hmac('MD5', $hmac_data, $transaction_key);

        $data                    = [];
        $data['action']          = 'https://checkout.globalgatewaye4.firstdata.com/payment';
        $data['x_gateway_id']    = $x_gateway_id;
        $data['x_login']         = $x_login;
        $data['x_amount']        = $x_amount;
        $data['x_fp_sequence']   = $x_fp_sequence;
        $data['x_fp_timestamp']  = $x_fp_timestamp;
        $data['x_fp_hash']       = $x_fp_hash;
        $data['x_currency_code'] = $x_currency_code;
        $data['x_cust_id']       = $x_cust_id;
        $data['button_confirm']  = $this->language->get('button_confirm');

        return $this->load->view('extension/payment/payeezy', $data);
    }

    public function callback()
    {
        $data = [];

        $x_response_code = null;
        if (isset($this->request->post['x_response_code'])) {
            $x_response_code = $this->request->post['x_response_code'];
        }

        $exact_ctr = null;
        if (isset($this->request->post['exact_ctr'])) {
            $exact_ctr = $this->request->post['exact_ctr'];
        }
        $data['exact_ctr'] = $exact_ctr;

        $x_cust_id = null;
        if (isset($this->request->post['x_cust_id'])) {
            $x_cust_id = $this->request->post['x_cust_id'];
        }

        if ($x_response_code == '1' && !is_null($x_cust_id)) {

            $this->load->model('checkout/order');

            // Set order to payed if reponse code is 1
            $this->model_checkout_order->addOrderHistory($x_cust_id, $this->config->get('payment_payeezy_order_status_id'));

            $this->response->redirect($this->url->link('checkout/success', '', true));
        } else {
            $this->response->redirect($this->url->link('checkout/failure', '', true));
        }
    }
}
