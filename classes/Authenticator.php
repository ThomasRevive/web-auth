<?php
use Selective\Base32\Base32;

class Authenticator {
    private $bytes;

    public function __construct($secret = null, $length = 16) {
        if (!empty($secret)) {
            $base32 = new Base32();
            $this->bytes = $base32->decode($secret);
        }
        else {
            $this->bytes = random_bytes($length);
        }
    }

    public function generateSecret() {
        $base32 = new Base32();
        return $base32->encode($this->bytes);
    }

    public function generateOPTCode($site, $user) {
        $secret = $this->generateSecret();
        $name = "{$site}:{$user}";

        $auth_url = urlencode('otpauth://totp/' . $name . '?secret=' . $secret);
        $auth_url .= urlencode('&issuer=' . urlencode($site));

        return $auth_url;
    }

    public function generateQrCode($optUrl) {
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . $optUrl;
    }

    public function getBytes() {
        return $this->bytes;
    }

    public function getSecret() {
        $base32 = new Base32();
        return $base32->decode($base32->encode($this->bytes));
    }

    private function calculateTOTP($stepCount = false, $digits = 6, $timeStep = 30) {
        // echo $this->getSecret() . '<br/>';
        // echo $this->bytes . '<br/>';

        $timeStep = intval($timeStep);

        if ($stepCount === false) {
            $stepCount = floor(time() / $timeStep);
        }

        // SHA256 is 32 in length
        $secret = str_pad($this->getSecret(), intval(32), STR_PAD_RIGHT);

        echo $secret . '<br/>';

        $timestamp = pack('J', $stepCount);
        $hash = hash_hmac('sha256', $timestamp, $secret, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0xf;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % pow(10, $digits);

        return str_pad($code, $digits, '0', STR_PAD_LEFT);
    }

    public function verifyAuthCode($authCode): bool {
        $timeStep = 30;
        $maxTicks = 4;

        echo $authCode . '<br/><br/>';

        // Array of all ticks to allow, sorted using absolute value to test closest match first.
        $ticks = range(-$maxTicks, $maxTicks);
        usort($ticks, function ($a, $b) {
            $a = abs($a);
            $b = abs($b);
            if ($a === $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        $time = time() / 30;
        $digits = strlen($authCode);

        foreach ($ticks as $offset) {
            $logTime = $time + $offset;

            $calc = $this->calculateTOTP($logTime, $digits, $timeStep);

            echo $calc . '<br/>';

            if ($calc === $authCode) {
                return true;
            }
        }

        return false;
    }
}