<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionModeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class ConnectionMode.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Connection
 */
class ConnectionMode
{
    private const LIVE = 'live';
    private const TEST = 'test';
    private string $mode;
    private function __construct(string $mode)
    {
        $this->mode = $mode;
    }
    /**
     * @throws InvalidConnectionModeException
     */
    public static function parse(string $mode): ConnectionMode
    {
        if (!in_array($mode, [self::LIVE, self::TEST], \true)) {
            throw new InvalidConnectionModeException(new TranslatableLabel(sprintf('Connection mode is invalid %s.', $mode), 'connection.invalidConnectionMode', [$mode]));
        }
        return new self($mode);
    }
    public function __toString(): string
    {
        return $this->mode;
    }
    public function equals(ConnectionMode $mode): bool
    {
        return $this->mode === $mode->mode;
    }
    public static function live(): ConnectionMode
    {
        return new self(self::LIVE);
    }
    public static function test(): ConnectionMode
    {
        return new self(self::TEST);
    }
}
