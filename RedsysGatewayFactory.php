<?php
namespace Crevillo\Payum\Redsys;

use Crevillo\Payum\Redsys\Action\NotifyAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Payum\Core\GatewayFactory as CoreGatewayFactory;
use Crevillo\Payum\Redsys\Action\CaptureAction;
use Crevillo\Payum\Redsys\Action\ConvertPaymentAction;
use Crevillo\Payum\Redsys\Action\StatusAction;
use Payum\Core\GatewayFactoryInterface;

class RedsysGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config = ArrayObject::ensureArrayObject($config);
        $config->defaults($this->defaultConfig);
        $config->defaults($this->coreGatewayFactory->createConfig());

        $config->defaults(array(
            'payum.factory_name' => 'redsys',
            'payum.factory_title' => 'Redsys',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.status' => new StatusAction(),
        ));

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'merchant_code' => '',
                'terminal' => '',
                'secret_key' => '',
                'sandbox' => true,
            );

            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = array('merchant_code', 'terminal', 'secret_key');

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $redsysConfig = array(
                    'merchant_code' => $config['merchant_code'],
                    'terminal' => $config['terminal'],
                    'secret_key' => $config['secret_key'],
                    'sandbox' => $config['sandbox'],
                );

                return new Api($redsysConfig);
            };
        }

        return (array) $config;
    }
}
