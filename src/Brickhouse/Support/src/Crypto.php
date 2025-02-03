<?php

namespace Brickhouse\Support;

use Brickhouse\Support\Exceptions\DecryptionException;
use Brickhouse\Support\Exceptions\EncryptionException;

class Crypto
{
    public const string CIPHER = 'aes-256-gcm';

    public function encryptString(#[\SensitiveParameter] string $value): string
    {
        return $this->encrypt($value, serialize: false);
    }

    public function decryptString(#[\SensitiveParameter] string $value): string
    {
        return $this->decrypt($value, deserialize: false);
    }

    /**
     * Encrypts the given value with the AES-256-GCM cipher.
     *
     * @param mixed     $value          Value to encrypt.
     * @param bool      $serialize      Whether to serialize the value before encrypting.
     *
     * @return string
     *
     * @throws EncryptionException      Thrown if the given value couldn't be encrypted.
     */
    public function encrypt(#[\SensitiveParameter] mixed $value, bool $serialize = true): string
    {
        if ($serialize) {
            $value = serialize($value);
        }

        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));
        $value = openssl_encrypt($value, self::CIPHER, $this->readApplicationKey(), 0, $iv, $tag);

        if ($value === false) {
            throw new EncryptionException('Failed to encrypt data.');
        }

        // To decrypt the string again, we need the original IV and tag.
        // Therefore, we encode it into the message before returning it.
        $iv = base64_encode($iv);
        $tag = base64_encode($tag);

        $concat = json_encode(compact('iv', 'tag', 'value'), JSON_UNESCAPED_SLASHES);

        return base64_encode($concat);
    }

    /**
     * Decrypts the given value with the AES-256-GCM cipher.
     *
     * @param string    $value          Cipter-text to decrypt.
     * @param bool      $deserialize    Whether to deserialize the value before decrypting.
     *
     * @return mixed
     */
    public function decrypt(#[\SensitiveParameter] string $value, bool $deserialize = true): mixed
    {
        $payload = base64_decode($value);
        $payload = json_decode($payload, associative: true);

        foreach (['iv', 'value', 'tag'] as $key) {
            if (!isset($payload[$key]) || !is_string($payload[$key])) {
                throw new DecryptionException("Cipher-text payload is invalid: {$key} not given.");
            }
        }

        ['iv' => $iv, 'value' => $value, 'tag' => $tag] = $payload;

        // Validate length of the IV against the expected length for the cipher
        $iv = base64_decode($iv, strict: true);

        $ivLength = strlen($iv);
        $expectedLength = openssl_cipher_iv_length(self::CIPHER);
        if ($ivLength !== $expectedLength) {
            throw new DecryptionException("Invalid IV length. Expected {$expectedLength}, got {$ivLength}.");
        }

        // Validate tag size for the cipher
        $tag = base64_decode($tag);

        if (strlen($tag) !== 16) {
            throw new DecryptionException('Invalid tag length. Expected 16, got ' . strlen($tag) . '.');
        }

        $decrypted = openssl_decrypt($value, self::CIPHER, $this->readApplicationKey(), 0, $iv, $tag);
        if ($decrypted === false) {
            throw new DecryptionException("Failed to decrypt data.");
        }

        if ($deserialize) {
            $decrypted = unserialize($decrypted);
        }

        return $decrypted;
    }

    /**
     * Reads the application key from the current environment and returns it.
     *
     * @return string
     *
     * @throws \RuntimeException    Thrown if the application key environment variable (`APP_KEY`) is unset, empty or invalid.
     * @throws \RuntimeException    Thrown if the application key is in an invalid format.
     * @throws \RuntimeException    Thrown if the application key is not a base64-compatible string.
     */
    private function readApplicationKey(): string
    {
        $applicationKey = getenv('APP_KEY');
        if ($applicationKey === false || empty($applicationKey) || is_array($applicationKey)) {
            throw new \RuntimeException(
                "No application key defined; use environment variable APP_KEY to define an application key."
            );
        }

        if (!str_starts_with($applicationKey, "base64:")) {
            throw new \RuntimeException(
                "Invalid application key; must be base64-encoded value, prefixed with 'base64:'"
            );
        }

        $applicationKey = substr($applicationKey, strlen('base64:'));

        $decodedApplicationKey = base64_decode($applicationKey);
        if ($decodedApplicationKey === false) {
            throw new \RuntimeException(
                "Invalid application key; could not be base64-decoded."
            );
        }

        return $decodedApplicationKey;
    }
}
