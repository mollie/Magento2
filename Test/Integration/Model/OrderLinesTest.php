<?php

namespace Mollie\Payment\Model;

use Mollie\Payment\Test\Integration\IntegrationTestCase;

class OrderLinesTest extends IntegrationTestCase
{
    public function testGetOrderLinesProductCalculation()
    {
        $this->loadFixture('Magento/Sales/order_item_list.php');

        $order = $this->loadOrderById('100000001');

        /** @var OrderLines $instance */
        $instance = $this->objectManager->get(OrderLines::class);

        $result = $instance->getOrderLines($order);

        // We expect 3 rows: the first product comes from the order creation, the second is our custom product.
        // The 3rd row is the shipping fee.
        $this->assertCount(3, $result);

        $row = $result[1]; // Custom created item
        $this->assertEquals(2, $row['quantity']);
        $this->assertEquals('physical', $row['type']);
        $this->assertEquals(100, $row['unitPrice']['value']);
        $this->assertEquals(200, $row['totalAmount']['value']);
        $this->assertEquals(34.71, $row['vatAmount']['value']);
    }

    public function testGetOrderLinesShippingFeeCalculation()
    {
        $this->loadFixture('Magento/Sales/order_item_list.php');

        $order = $this->loadOrderById('100000001');
        $order->setShippingAmount(10);
        $order->setBaseShippingAmount(10);
        $order->setShippingTaxAmount(2.1);
        $order->setBaseShippingTaxAmount(2.1);

        /** @var OrderLines $instance */
        $instance = $this->objectManager->get(OrderLines::class);

        $result = $instance->getOrderLines($order);

        // We expect 3 rows: the first product comes from the order creation, the second is our custom product.
        // The 3rd row is the shipping fee.
        $this->assertCount(3, $result);

        $row = $result[2]; // Shipping fee
        $this->assertEquals('shipping_fee', $row['type']);
        $this->assertEquals(12.1, $row['unitPrice']['value']);
        $this->assertEquals(12.1, $row['totalAmount']['value']);
        $this->assertEquals(2.1, $row['vatAmount']['value']);
    }
}
