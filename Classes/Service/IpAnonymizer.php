<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Service;

/**
 * Anonymize a given IP
 *
 * Inspired by https://github.com/geertw/php-ip-anonymizer
 */
class IpAnonymizer
{

    /**
     * @var string IPv4 netmask used to anonymize IPv4 address.
     */
    const IP_4_MASK = '255.255.255.0';

    /**
     * @var string IPv6 netmask used to anonymize IPv6 address.
     */
    const IP_6_MASK = 'ffff:ffff:ffff:ffff:0000:0000:0000:0000';

    /**
     * Anonymize an IPv4 or IPv6 address.
     *
     * @param $address string IP address that must be anonymized
     * @return string The anonymized IP address. Returns an empty string when the IP address is invalid.
     */
    public static function anonymizeIp(string $address): string
    {
        if (empty($address)) {
            return '';
        }
        $packedAddress = inet_pton($address);
        $length = strlen($packedAddress);

        if ($length === 4) {
            return inet_ntop(inet_pton($address) & inet_pton(self::IP_4_MASK));
        }
        if ($length === 16) {
            return inet_ntop(inet_pton($address) & inet_pton(self::IP_6_MASK));
        }

        return '';
    }
}
