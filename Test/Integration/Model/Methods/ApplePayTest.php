<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 *  * See COPYING.txt for license details.
 */

namespace Mollie\Payment\Test\Integration\Model\Methods;

use Mollie\Api\Endpoints\MethodEndpoint;
use Mollie\Api\MollieApiClient;
use Mollie\Payment\Model\Methods\ApplePay;
use Mollie\Payment\Model\MollieConfigProvider;

class ApplePayTest extends AbstractMethodTest
{
    protected $instance = ApplePay::class;

    protected $code = 'applepay';

    public function testTheIncludeWalletsParameterIsUsed()
    {
        $mollieHelperMock = $this->createMock(\Mollie\Payment\Helper\General::class);
        $mollieHelperMock->method('getOrderAmountByQuote')->willReturn(['value' => 100, 'currency' => 'EUR']);

        $mollieApiClient = $this->createMock(MollieApiClient::class);
        $mollieApiClient->methods = $this->createMock(MethodEndpoint::class);

        $mollieApiClient->methods->expects($this->once())->method('allActive')->with($this->callback(function ($arguments) {
            $this->assertArrayHasKey('includeWallets', $arguments);
            $this->assertEquals('applepay', $arguments['includeWallets']);

            return true;
        }))->willReturn([]);

        /** @var MollieConfigProvider $instance */
        $instance = $this->objectManager->create(MollieConfigProvider::class, [
            'mollieHelper' => $mollieHelperMock,
        ]);
        $instance->getActiveMethods($mollieApiClient);
    }
}
