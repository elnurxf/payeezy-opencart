<?php

class ControllerExtensionPaymentPayeezy extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/payeezy');
        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_payeezy', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['gateway'])) {
            $data['error_gateway'] = $this->error['gateway'];
        } else {
            $data['error_gateway'] = '';
        }

        if (isset($this->error['login'])) {
            $data['error_login'] = $this->error['login'];
        } else {
            $data['error_login'] = '';
        }

        if (isset($this->error['transaction_key'])) {
            $data['error_transaction_key'] = $this->error['transaction_key'];
        } else {
            $data['error_transaction_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/payeezy', 'user_token=' . $this->session->data['user_token'], true),
        );

        $data['action'] = $this->url->link('extension/payment/payeezy', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_payeezy_x_gateway_id'])) {
            $data['payment_payeezy_x_gateway_id'] = $this->request->post['payment_payeezy_x_gateway_id'];
        } else {
            $data['payment_payeezy_x_gateway_id'] = $this->config->get('payment_payeezy_x_gateway_id');
        }

        if (isset($this->request->post['payment_payeezy_x_login'])) {
            $data['payment_payeezy_x_login'] = $this->request->post['payment_payeezy_x_login'];
        } else {
            $data['payment_payeezy_x_login'] = $this->config->get('payment_payeezy_x_login');
        }

        if (isset($this->request->post['payment_payeezy_transaction_key'])) {
            $data['payment_payeezy_transaction_key'] = $this->request->post['payment_payeezy_transaction_key'];
        } else {
            $data['payment_payeezy_transaction_key'] = $this->config->get('payment_payeezy_transaction_key');
        }

        $data['callback'] = HTTP_CATALOG . 'index.php?route=extension/payment/payeezy/callback';

        if (isset($this->request->post['payment_payeezy_total'])) {
            $data['payment_payeezy_total'] = $this->request->post['payment_payeezy_total'];
        } else {
            $data['payment_payeezy_total'] = $this->config->get('payment_payeezy_total');
        }

        if (isset($this->request->post['payment_payeezy_order_status_id'])) {
            $data['payment_payeezy_order_status_id'] = $this->request->post['payment_payeezy_order_status_id'];
        } else {
            $data['payment_payeezy_order_status_id'] = $this->config->get('payment_payeezy_order_status_id');
        }

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_payeezy_status'])) {
            $data['payment_payeezy_status'] = $this->request->post['payment_payeezy_status'];
        } else {
            $data['payment_payeezy_status'] = $this->config->get('payment_payeezy_status');
        }

        if (isset($this->request->post['payment_payeezy_sort_order'])) {
            $data['payment_payeezy_sort_order'] = $this->request->post['payment_payeezy_sort_order'];
        } else {
            $data['payment_payeezy_sort_order'] = $this->config->get('payment_payeezy_sort_order');
        }

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/payeezy', $data));
    }

    protected function validate()
    {

        if (!$this->user->hasPermission('modify', 'extension/payment/payeezy')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_payeezy_x_gateway_id']) {
            $this->error['gateway'] = $this->language->get('error_gateway');
        }

        if (!$this->request->post['payment_payeezy_x_login']) {
            $this->error['login'] = $this->language->get('error_login');
        }

        if (!$this->request->post['payment_payeezy_transaction_key']) {
            $this->error['transaction_key'] = $this->language->get('error_transaction_key');
        }

        return !$this->error;
    }
}
