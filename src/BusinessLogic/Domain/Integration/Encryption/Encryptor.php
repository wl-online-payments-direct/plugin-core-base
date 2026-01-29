<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Encryption;

/**
 * Interface Encryptor
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Integration\Encryption
 */
interface Encryptor
{
    /**
     * Encrypts a given string.
     *
     * @param string $data
     *
     * @return string
     */
    public function encrypt(string $data): string;
    /**
     * Decrypts a given string.
     *
     * @param string $encryptedData
     *
     * @return string
     */
    public function decrypt(string $encryptedData): string;
}
