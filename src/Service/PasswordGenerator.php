<?php
namespace App\Service;

class PasswordGenerator
{
    /**
     * Génération du JWT
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param int $validity
     * @return string
     */
    public function generate(
        $length = 9,
        $add_dashes = false,
        $available_sets = "luds"
    ): string {
        $sets = [];
        if (strpos($available_sets, "l") !== false) {
            $sets[] = "abcdefghjkmnpqrstuvwxyz";
        }
        if (strpos($available_sets, "u") !== false) {
            $sets[] = "ABCDEFGHJKMNPQRSTUVWXYZ";
        }
        if (strpos($available_sets, "d") !== false) {
            $sets[] = "23456789";
        }
        if (strpos($available_sets, "s") !== false) {
            $sets[] = '!@#$%&*?';
        }

        $all = "";
        $password = "";
        foreach ($sets as $set) {
            $password .= $set[$this->tweak_array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[$this->tweak_array_rand($all)];
        }

        $password = str_shuffle($password);

        if (!$add_dashes) {
            return $password;
        }

        $dash_len = floor(sqrt($length));
        $dash_str = "";
        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . "-";
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    private function tweak_array_rand($array)
    {
        if (function_exists("random_int")) {
            return random_int(0, count($array) - 1);
        } elseif (function_exists("mt_rand")) {
            return mt_rand(0, count($array) - 1);
        } else {
            return array_rand($array);
        }
    }
}
