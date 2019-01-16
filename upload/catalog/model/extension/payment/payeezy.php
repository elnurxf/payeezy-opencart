<?php

class ModelExtensionPaymentPayeezy extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/payeezy');

        $min = (float) $this->config->get('payment_payeezy_total');

        if ($min == 0 || ($min > 0 && $min < $total)) {
            return [
                'code'       => 'payeezy',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_payeezy_sort_order'),
            ];
        }

        return [];
    }
}
