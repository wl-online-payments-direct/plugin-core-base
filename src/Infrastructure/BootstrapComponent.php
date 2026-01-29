<?php

namespace WOP\OnlinePayments\Core\Infrastructure;

use WOP\OnlinePayments\Core\Infrastructure\Configuration\ConfigurationManager;
use WOP\OnlinePayments\Core\Infrastructure\Http\CurlHttpClient;
use WOP\OnlinePayments\Core\Infrastructure\Http\HttpClient;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\AsyncProcessStarterService;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Events\QueueItemStateTransitionEventBus;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\AsyncProcessService;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\TaskRunnerManager;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\TaskRunnerStatusStorage;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\TaskRunnerWakeup;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueService;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\RunnerStatusStorage;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskRunner;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskRunnerWakeupService;
use WOP\OnlinePayments\Core\Infrastructure\Utility\Events\EventBus;
use WOP\OnlinePayments\Core\Infrastructure\Utility\GuidProvider;
use WOP\OnlinePayments\Core\Infrastructure\Utility\TimeProvider;
/**
 * Class BootstrapComponent.
 *
 * @package OnlinePayments\Core\Infrastructure
 */
class BootstrapComponent
{
    /**
     * Initializes infrastructure components.
     */
    public static function init()
    {
        static::initServices();
        static::initRepositories();
        static::initEvents();
    }
    /**
     * Initializes services and utilities.
     */
    protected static function initServices()
    {
        ServiceRegister::registerService(ConfigurationManager::CLASS_NAME, function () {
            return ConfigurationManager::getInstance();
        });
        ServiceRegister::registerService(TimeProvider::CLASS_NAME, function () {
            return TimeProvider::getInstance();
        });
        ServiceRegister::registerService(GuidProvider::CLASS_NAME, function () {
            return GuidProvider::getInstance();
        });
        ServiceRegister::registerService(EventBus::CLASS_NAME, function () {
            return EventBus::getInstance();
        });
        ServiceRegister::registerService(HttpClient::CLASS_NAME, function () {
            return new CurlHttpClient();
        });
        ServiceRegister::registerService(EventBus::CLASS_NAME, function () {
            return EventBus::getInstance();
        });
        ServiceRegister::registerService(AsyncProcessService::CLASS_NAME, function () {
            return AsyncProcessStarterService::getInstance();
        });
        ServiceRegister::registerService(QueueService::CLASS_NAME, function () {
            return new QueueService();
        });
        ServiceRegister::registerService(TaskRunnerWakeup::CLASS_NAME, function () {
            return new TaskRunnerWakeupService();
        });
        ServiceRegister::registerService(TaskRunner::CLASS_NAME, function () {
            return new TaskRunner();
        });
        ServiceRegister::registerService(TaskRunnerStatusStorage::CLASS_NAME, function () {
            return new RunnerStatusStorage();
        });
        ServiceRegister::registerService(TaskRunnerManager::CLASS_NAME, function () {
            return new TaskExecution\TaskRunnerManager();
        });
        ServiceRegister::registerService(QueueItemStateTransitionEventBus::CLASS_NAME, function () {
            return QueueItemStateTransitionEventBus::getInstance();
        });
    }
    /**
     * Initializes repositories.
     */
    protected static function initRepositories()
    {
    }
    /**
     * Initializes events.
     */
    protected static function initEvents()
    {
    }
}
