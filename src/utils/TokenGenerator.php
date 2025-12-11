<?php

class TokenGenerator {
    public static function generate() {
        return bin2hex(random_bytes(32));
    }
}
