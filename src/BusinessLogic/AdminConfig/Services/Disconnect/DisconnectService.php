<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Disconnect;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\LogSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\PayByLinkSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\PaymentSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Repositories\ConnectionConfigRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Disconnect\DisconnectTaskEnqueuerInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Payment\ShopPaymentService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Repositories\PaymentConfigRepositoryInterface;
/**
 * Class DisconnectService
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Disconnect
 */
class DisconnectService
{
    protected ShopPaymentService $shopPaymentService;
    protected ConnectionConfigRepositoryInterface $connectionConfigRepository;
    protected PaymentSettingsRepositoryInterface $paymentSettingsRepository;
    protected LogSettingsRepositoryInterface $logSettingsRepository;
    protected PaymentConfigRepositoryInterface $paymentMethodConfigRepository;
    protected PayByLinkSettingsRepositoryInterface $payByLinkSettingsRepository;
    protected DisconnectTaskEnqueuerInterface $disconnectTaskEnqueuer;
    /**
     * @param ShopPaymentService $shopPaymentService
     * @param ConnectionConfigRepositoryInterface $connectionConfigRepository
     * @param PaymentSettingsRepositoryInterface $paymentSettingsRepository
     * @param LogSettingsRepositoryInterface $logSettingsRepository
     * @param PaymentConfigRepositoryInterface $paymentMethodConfigRepository
     * @param PayByLinkSettingsRepositoryInterface $payByLinkSettingsRepository
     * @param DisconnectTaskEnqueuerInterface $disconnectTaskEnqueuer
     */
    public function __construct(ShopPaymentService $shopPaymentService, ConnectionConfigRepositoryInterface $connectionConfigRepository, PaymentSettingsRepositoryInterface $paymentSettingsRepository, LogSettingsRepositoryInterface $logSettingsRepository, PaymentConfigRepositoryInterface $paymentMethodConfigRepository, PayByLinkSettingsRepositoryInterface $payByLinkSettingsRepository, DisconnectTaskEnqueuerInterface $disconnectTaskEnqueuer)
    {
        $this->shopPaymentService = $shopPaymentService;
        $this->connectionConfigRepository = $connectionConfigRepository;
        $this->paymentSettingsRepository = $paymentSettingsRepository;
        $this->logSettingsRepository = $logSettingsRepository;
        $this->paymentMethodConfigRepository = $paymentMethodConfigRepository;
        $this->payByLinkSettingsRepository = $payByLinkSettingsRepository;
        $this->disconnectTaskEnqueuer = $disconnectTaskEnqueuer;
    }
    public function disconnect(): void
    {
        try {
            $activeConnection = $this->connectionConfigRepository->getConnection();
            if (null === $activeConnection) {
                return;
            }
            $this->disconnectIntegration((string) $activeConnection->getMode());
            $this->deleteAllData((string) $activeConnection->getMode());
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * @param string $mode
     *
     * @return void
     */
    public function disconnectIntegration(string $mode): void
    {
        $this->shopPaymentService->deletePaymentMethods($mode);
        $this->paymentSettingsRepository->deleteByMode($mode);
        $this->logSettingsRepository->deleteByMode($mode);
        $this->paymentMethodConfigRepository->deleteByMode($mode);
        $this->payByLinkSettingsRepository->deleteByMode($mode);
        $this->connectionConfigRepository->disconnect();
    }
    public function deleteAllData(string $mode): void
    {
        $this->disconnectTaskEnqueuer->enqueueDisconnectTask($mode);
    }
}
